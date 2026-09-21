<?php

namespace App\Services;

use App\Models\Asistencia;
use App\Models\FrancoExcepcion;
use App\Models\Licencia;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

/**
 * Resumen agregado de asistencia por guardavida para el panel de RRHH
 * (presentismo): días asistidos, faltas, licencias, francos y fichajes
 * fuera de rango en un período.
 *
 * El esquema de franco (uno o varios días fijos por semana) se evalúa día
 * por día contra Guardavida::diasFrancoVigentesEn(), o sea contra el
 * esquema que regía EN ESA FECHA según guardavida_franco_historial — si
 * alguien cambió de franco en medio del rango consultado, no se le aplica
 * retroactivamente el esquema actual a fechas viejas.
 *
 * Además, si una semana no tiene ningún día de franco explicado por el
 * esquema vigente ni por un cambio puntual (FrancoExcepcion) — típicamente
 * porque todavía no configuró nada — igual se le reconoce 1 franco por
 * semana por defecto: el primer día sin asistencia/licencia de esa semana
 * se cuenta como franco (no como falta), y recién el segundo día sin
 * explicar en la misma semana es falta. Esto es solo la red de seguridad
 * para el caso "sin configurar"; alguien con esquema de 2+ días (ej.
 * aeródromo, franco sábado y domingo) ya tiene sus francos cubiertos por el
 * esquema explícito y no necesita esta inferencia.
 *
 * A propósito NO reutiliza HistorialAsistenciaService (ese arma el
 * historial día por día con relaciones cargadas, pensado para el detalle
 * individual y los excels) — acá alcanza con conjuntos de fechas y un
 * recorrido liviano por día, sin tocar esa lógica compartida.
 */
class ResumenAsistenciaService
{
    /**
     * @param  Collection<int,\App\Models\Guardavida>  $guardavidas
     * @return array<int,array{dias_totales:int,dias_habiles:int,asistencias:int,faltas:int,licencias:int,francos:int,fuera_de_rango:int,porcentaje:?float}>
     */
    public function generar(Collection $guardavidas, Carbon $inicio, Carbon $fin): array
    {
        $ids = $guardavidas->pluck('id');

        if ($ids->isEmpty()) {
            return [];
        }

        // Se carga una sola vez para todos, así diasFrancoVigentesEn() no
        // dispara una consulta por cada día del rango. load() es propio de
        // la Collection de Eloquent, no de la Collection genérica — por eso
        // se envuelve acá, para que este método funcione sin importar qué
        // tipo de Collection le haya pasado el caller.
        \Illuminate\Database\Eloquent\Collection::make($guardavidas)->load('francoHistorial');

        // Días distintos con fichaje por guardavida, y si alguno de esos
        // fichajes quedó marcado fuera de rango.
        $asistenciasPorDia = Asistencia::whereIn('guardavidas_id', $ids)
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->selectRaw('guardavidas_id, DATE(fecha_hora) as dia, MAX(CASE WHEN estado_validacion = "fuera_de_rango" THEN 1 ELSE 0 END) as fuera_de_rango')
            ->groupBy('guardavidas_id', 'dia')
            ->get()
            ->groupBy('guardavidas_id');

        $licenciasPorGuardavida = Licencia::whereIn('guardavida_id', $ids)
            ->where(function ($q) use ($inicio, $fin) {
                $q->whereBetween('fecha_inicio', [$inicio, $fin])
                    ->orWhereBetween('fecha_fin', [$inicio, $fin])
                    ->orWhere(function ($q2) use ($inicio, $fin) {
                        $q2->where('fecha_inicio', '<=', $inicio)
                            ->where('fecha_fin', '>=', $fin);
                    });
            })
            ->get()
            ->groupBy('guardavida_id');

        $excepcionesPorGuardavida = FrancoExcepcion::whereIn('guardavida_id', $ids)
            ->whereBetween('fecha', [$inicio, $fin])
            ->get()
            ->groupBy('guardavida_id');

        // Lista de fechas del rango (una sola vez, se reutiliza para todos).
        $fechas = collect(CarbonPeriod::create($inicio->copy()->startOfDay(), $fin->copy()->startOfDay()))
            ->map(fn (Carbon $d) => $d->toDateString());

        $resumen = [];

        foreach ($guardavidas as $guardavida) {
            $diasAsistencia = $asistenciasPorDia->get($guardavida->id, collect())
                ->keyBy('dia');

            $diasLicencia = [];
            foreach ($licenciasPorGuardavida->get($guardavida->id, collect()) as $licencia) {
                $desde = Carbon::parse($licencia->fecha_inicio)->max($inicio)->startOfDay();
                $hasta = Carbon::parse($licencia->fecha_fin)->min($fin)->startOfDay();
                foreach (CarbonPeriod::create($desde, $hasta) as $d) {
                    $diasLicencia[$d->toDateString()] = true;
                }
            }

            $excepciones = $excepcionesPorGuardavida->get($guardavida->id, collect())
                ->keyBy(fn ($e) => $e->fecha->toDateString());

            // Primera pasada: clasificación "dura" día por día. 'pendiente'
            // son los días sin asistencia/licencia/franco explicado — ahí es
            // donde entra en juego el franco semanal de abajo.
            $clasificacion = [];
            $fueraDeRangoPorFecha = [];
            $semanaDe = [];

            foreach ($fechas as $fechaStr) {
                // Semana de lunes a domingo — no nos importa a qué semana de
                // franco real corresponde, solo agrupar para el descuento.
                $semanaDe[$fechaStr] = Carbon::parse($fechaStr)->startOfWeek(Carbon::MONDAY)->toDateString();

                if ($diasAsistencia->has($fechaStr)) {
                    $clasificacion[$fechaStr] = 'asistencia';
                    $fueraDeRangoPorFecha[$fechaStr] = (int) $diasAsistencia[$fechaStr]->fuera_de_rango === 1;

                    continue;
                }

                if (isset($diasLicencia[$fechaStr])) {
                    $clasificacion[$fechaStr] = 'licencia';

                    continue;
                }

                $excepcion = $excepciones->get($fechaStr);
                if ($excepcion && $excepcion->tipo === 'agregado') {
                    $clasificacion[$fechaStr] = 'franco';

                    continue;
                }

                if ($excepcion && $excepcion->tipo === 'cancelado') {
                    // Se le corrió el franco a otra fecha esa semana: este día debía trabajar.
                    $clasificacion[$fechaStr] = 'falta';

                    continue;
                }

                // Esquema vigente EN ESA FECHA puntual, no el actual — si
                // cambió de franco en el medio del rango consultado, cada
                // día se evalúa contra lo que regía en ese momento.
                $diasFrancoVigentes = $guardavida->diasFrancoVigentesEn($fechaStr);
                $esDiaFrancoFijo = in_array((int) Carbon::parse($fechaStr)->dayOfWeek, $diasFrancoVigentes, true);

                $clasificacion[$fechaStr] = $esDiaFrancoFijo ? 'franco' : 'pendiente';
            }

            // Segunda pasada: todos los guardavidas tienen derecho a 1 franco
            // semanal, sepamos o no qué día es. Si esa semana ya tiene un
            // franco explícito (día fijo configurado o cambio puntual
            // cargado), no se toca nada. Si no, el primer día "pendiente" de
            // la semana se toma como ese franco semanal, y el resto de los
            // pendientes de esa misma semana sí quedan como falta.
            $pendientesPorSemana = [];
            $francosExplicitosPorSemana = [];
            foreach ($clasificacion as $fechaStr => $estado) {
                $semana = $semanaDe[$fechaStr];
                if ($estado === 'pendiente') {
                    $pendientesPorSemana[$semana][] = $fechaStr;
                } elseif ($estado === 'franco') {
                    $francosExplicitosPorSemana[$semana] = ($francosExplicitosPorSemana[$semana] ?? 0) + 1;
                }
            }

            foreach ($pendientesPorSemana as $semana => $fechasPendientes) {
                sort($fechasPendientes);

                if (($francosExplicitosPorSemana[$semana] ?? 0) === 0) {
                    $clasificacion[array_shift($fechasPendientes)] = 'franco';
                }

                foreach ($fechasPendientes as $fechaStr) {
                    $clasificacion[$fechaStr] = 'falta';
                }
            }

            // Tercera pasada: conteo final.
            $asistencias = 0;
            $faltas = 0;
            $licencias = 0;
            $francos = 0;
            $fueraDeRango = 0;

            foreach ($clasificacion as $fechaStr => $estado) {
                match ($estado) {
                    'asistencia' => $asistencias++,
                    'licencia' => $licencias++,
                    'franco' => $francos++,
                    default => $faltas++,
                };

                if ($estado === 'asistencia' && ($fueraDeRangoPorFecha[$fechaStr] ?? false)) {
                    $fueraDeRango++;
                }
            }

            $diasTotales = $fechas->count();
            $diasHabiles = $diasTotales - $licencias - $francos;

            $resumen[$guardavida->id] = [
                'dias_totales' => $diasTotales,
                'dias_habiles' => max($diasHabiles, 0),
                'asistencias' => $asistencias,
                'faltas' => $faltas,
                'licencias' => $licencias,
                'francos' => $francos,
                'fuera_de_rango' => $fueraDeRango,
                'porcentaje' => $diasHabiles > 0 ? round(($asistencias / $diasHabiles) * 100) : null,
            ];
        }

        return $resumen;
    }
}
