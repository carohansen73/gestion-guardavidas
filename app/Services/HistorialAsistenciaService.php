<?php
namespace App\Services;

use App\Models\Asistencia;
use App\Models\Licencia;
use Carbon\Carbon;

class HistorialAsistenciaService{
    public function generar($guardavidaId, $inicio, $fin){

         // Asistencias del rango
        $asistencias = Asistencia::where('guardavidas_id', $guardavidaId)
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->orderBy('fecha_hora')
            ->get()
            ->groupBy(fn($a) => Carbon::parse($a->fecha_hora)->toDateString());

        // Licencias del rango
        $licencias = Licencia::where('guardavida_id', $guardavidaId)
            ->where(function($q) use ($inicio, $fin) {
                $q->whereBetween('fecha_inicio', [$inicio, $fin])
                ->orWhereBetween('fecha_fin', [$inicio, $fin])
                ->orWhere(function ($q2) use ($inicio, $fin) {
                    $q2->where('fecha_inicio', '<=', $inicio)
                        ->where('fecha_fin', '>=', $fin);
                });
            })
            ->get();

        // Construcción del historial día x día
        $historial = [];

        for ($fecha = $inicio->copy(); $fecha->lte($fin); $fecha->addDay()) {

            $dateString = $fecha->toDateString();

            // 1- Verifica si hay licencia ese día
            $licencia = $licencias->first(function ($l) use ($fecha) {
                return $fecha->between($l->fecha_inicio, $l->fecha_fin);
            });

            if ($licencia) {
                $historial[] = [
                    'fecha' => $dateString,
                    'estado' => 'LICENCIA',
                    'detalle' => $licencia->tipo_licencia,
                    'ingreso' => null,
                    'egreso' => null,
                    'puesto' => $licencia->puesto->nombre ?? '-',
                    'fuera_de_rango' => false,
                ];
                continue;
            }

            // 2- Verifica si hay asistencia ese día
            $asistencia = $asistencias->get($dateString);

            if ($asistencia) {
                // Si hubo un solo fichaje ese día, no hay salida registrada
                // de verdad — mostrar la misma hora como "egreso" sugiere que
                // trabajó 0 minutos, cuando en realidad no sabemos a qué hora
                // se fue (pasa en más de la mitad de los días reales).
                $huboEgresoDistinto = $asistencia->count() > 1;

                $historial[] = [
                    'fecha' => $dateString,
                    'estado' => 'ASISTIÓ',
                    'ingreso' => $asistencia->first()->fecha_hora,
                    'egreso' => $huboEgresoDistinto ? $asistencia->last()->fecha_hora : null,
                    'puesto' => $asistencia->first()->puesto->nombre ?? '-',
                    'fuera_de_rango' => $asistencia->contains(fn ($a) => $a->estado_validacion === 'fuera_de_rango'),
                ];
                continue;
            }

            // 3- Si no hay ni licencia ni asistencia → FALTÓ
            $historial[] = [
                'fecha' => $dateString,
                'estado' => 'FALTA',
                'ingreso' => null,
                'egreso' => null,
                'puesto' => '-',
                'fuera_de_rango' => false,
            ];
        }

        return $historial;
    }
}
