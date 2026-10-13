<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    protected $fillable = [
        'nombre',
        'fecha_inicio_postulacion',
        'fecha_fin_postulacion',
        'fecha_inicio',
        'fecha_fin',
        'declaracion_jurada_modelo',
    ];

    protected $casts = [
        'fecha_inicio_postulacion' => 'date',
        'fecha_fin_postulacion' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function postulaciones()
    {
        return $this->hasMany(Postulacion::class);
    }

    /** ¿Hoy cae dentro de la ventana de postulación de esta temporada? */
    public function postulacionAbierta(): bool
    {
        $hoy = Carbon::today();

        return $this->fecha_inicio_postulacion->lte($hoy) && $this->fecha_fin_postulacion->gte($hoy);
    }

    /** La temporada cuya ventana operativa incluye esa fecha (null si ninguna). */
    public static function enLaFecha($fecha): ?self
    {
        $fecha = Carbon::parse($fecha)->toDateString();

        return static::whereDate('fecha_inicio', '<=', $fecha)
            ->whereDate('fecha_fin', '>=', $fecha)
            ->first();
    }

    /** Primer día del ciclo completo de la temporada: cuando abre la inscripción (o, si fuera antes, la temporada). */
    public function cicloDesde(): Carbon
    {
        return $this->fecha_inicio_postulacion->lt($this->fecha_inicio) ? $this->fecha_inicio_postulacion : $this->fecha_inicio;
    }

    /** Último día del ciclo completo de la temporada. */
    public function cicloHasta(): Carbon
    {
        return $this->fecha_fin->gt($this->fecha_fin_postulacion) ? $this->fecha_fin : $this->fecha_fin_postulacion;
    }

    /**
     * La temporada a la que pertenece una fecha. Un día pertenece a una temporada desde que abre su
     * inscripción hasta que termina (inscripción, tramo entre la inscripción y el comienzo, y temporada
     * en sí). Si la fecha no cae en ninguna, la temporada más cercana (la que empieza o terminó más
     * cerca; si hay empate, la más nueva). Es null solo si no hay ninguna temporada cargada.
     * La usan las altas fuera de la selección (alta manual, reincorporación).
     */
    public static function paraFecha($fecha): ?self
    {
        $fecha = Carbon::parse($fecha)->startOfDay();

        // La operativa manda: si dos ciclos se pisan (inscripción de la próxima mientras corre la actual),
        // un día de temporada pertenece a la temporada en curso.
        if ($operativa = static::enLaFecha($fecha)) {
            return $operativa;
        }

        $todas = static::all();
        if ($todas->isEmpty()) {
            return null;
        }

        $distancia = function (self $t) use ($fecha): int {
            if ($fecha->lt($t->cicloDesde())) {
                return (int) abs($t->cicloDesde()->diffInDays($fecha));
            }

            return $fecha->gt($t->cicloHasta()) ? (int) abs($fecha->diffInDays($t->cicloHasta())) : 0;
        };

        return $todas->sort(function (self $a, self $b) use ($distancia) {
            return [$distancia($a), $b->fecha_inicio->timestamp] <=> [$distancia($b), $a->fecha_inicio->timestamp];
        })->first();
    }

    /**
     * La temporada a la que pertenece un registro operativo (intervención, bandera, licencia, fichaje...)
     * según SU fecha: la última que ya había empezado ese día. Si la fecha es anterior a todas, la
     * primera. Así un registro cargado entre dos temporadas queda en la que acaba de terminar y nunca
     * queda "sin temporada" (solo es null si no hay ninguna cargada).
     * Es distinta de paraFecha(), que incluye la inscripción y se usa para las altas del plantel.
     */
    public static function paraDato($fecha): ?self
    {
        $fecha = Carbon::parse($fecha)->toDateString();

        return static::whereDate('fecha_inicio', '<=', $fecha)->orderByDesc('fecha_inicio')->first()
            ?? static::orderBy('fecha_inicio')->first();
    }

    /**
     * La temporada que muestran por defecto el panel y los listados: la activa; si todavía no empezó
     * la nueva (o ya terminó la anterior), la última que empezó; si ninguna empezó, la primera.
     */
    public static function paraListados(): ?self
    {
        return static::activa()
            ?? static::whereDate('fecha_inicio', '<=', Carbon::today())->orderByDesc('fecha_inicio')->first()
            ?? static::orderBy('fecha_inicio')->first();
    }

    /**
     * La temporada elegida con `?temporada=` en un listado: un id, "todas" (null) o, sin parámetro,
     * la de paraListados(). Un id inexistente cae en el valor por defecto.
     *
     * @return array{0: \Illuminate\Support\Collection, 1: ?self} [todas las temporadas (más nueva primero), elegida (null = todas)]
     */
    public static function seleccionada(?string $parametro): array
    {
        $temporadas = static::orderByDesc('fecha_inicio')->get();

        if ($parametro === 'todas') {
            return [$temporadas, null];
        }

        return [$temporadas, $temporadas->firstWhere('id', (int) $parametro) ?? static::paraListados()];
    }

    /** Cantidad de registros operativos asociados, por tabla (para el borrado y la pantalla de Temporadas). */
    public function registrosAsociados(): array
    {
        $conteo = [];
        foreach (\App\Services\ReasignadorTemporadas::TABLAS as $tabla => $columna) {
            $conteo[$tabla] = \Illuminate\Support\Facades\DB::table($tabla)->where('temporada_id', $this->id)->count();
        }

        return $conteo;
    }

    /**
     * La temporada cuya ventana OPERATIVA (fecha_inicio/fecha_fin) incluye
     * hoy. Es la que rige para cargar intervenciones/banderas/etc. y para
     * la restricción de escritura de guardavida/encargado fuera de
     * temporada.
     */
    public static function activa(): ?self
    {
        $hoy = Carbon::today();

        return static::whereDate('fecha_inicio', '<=', $hoy)
            ->whereDate('fecha_fin', '>=', $hoy)
            ->first();
    }

    /**
     * La temporada cuya ventana de POSTULACIÓN incluye hoy — puede ser una
     * temporada distinta a la operativa activa, ya que la postulación se
     * hace meses antes de que la temporada arranque.
     */
    public static function conPostulacionAbierta(): ?self
    {
        $hoy = Carbon::today();

        return static::whereDate('fecha_inicio_postulacion', '<=', $hoy)
            ->whereDate('fecha_fin_postulacion', '>=', $hoy)
            ->first();
    }
}
