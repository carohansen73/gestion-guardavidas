<?php

namespace App\Models;

use App\Models\Concerns\TieneTemporada;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use TieneTemporada;

    /** Fecha que determina la temporada del registro. */
    protected string $campoFechaTemporada = 'fecha_hora';

    public $timestamps = false;

    protected $fillable = [
        'longitud',
        'latitud',
        'precision',
        'estado_validacion',
        'modo_sync',
        'puesto_id',
        'guardavidas_id',
        'fecha_hora',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public static function nuevaAsistencia($longitud, $latitud, $precision, $puesto_id, $guardavidas_id, $fecha_hora, $estado_validacion = 'valido')
    {
        // createOrFirst: intenta crear, y si choca contra el índice único
        // (mismo guardavida + puesto + fecha_hora, típico de un reintento de
        // sincronización offline) devuelve el registro que ya existía en vez
        // de tirar una excepción. Es seguro ante llamadas simultáneas porque
        // la detección del duplicado la hace la base de datos, no una
        // consulta previa desde PHP.
        return Asistencia::createOrFirst(
            [
                'guardavidas_id' => $guardavidas_id,
                'puesto_id' => $puesto_id,
                'fecha_hora' => $fecha_hora,
            ],
            [
                'longitud' => $longitud,
                'latitud' => $latitud,
                'precision' => $precision,
                'estado_validacion' => $estado_validacion,
            ]
        );
    }

    public static function asistenciaPorGuardavidaId($id)
    {
        return self::where('guardavidas_id', $id)->with(['puesto'])->get();
    }

    public static function asistenciasAll()
    {
        return self::with(['guardavida', 'puesto'])->get();
    }

    public static function asistenciaPorPuesto($idPuesto)
    {
        return self::where('puesto_id', $idPuesto)->with('guardavida')->get();
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }

    /** Fórmula de Haversine: distancia en metros entre dos coordenadas. */
    public static function distanciaMetros(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $radioTierra = 6371000; // metros
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($deltaLon / 2) ** 2;

        return $radioTierra * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Metros entre donde se fichó y el puesto, o null si falta alguna de las dos ubicaciones.
     * (Es la misma distancia que se usa para marcar un fichaje como fuera de rango.)
     */
    public function distanciaAlPuesto(): ?int
    {
        $puesto = $this->puesto;

        if (! $puesto || $this->latitud === null || $this->longitud === null || $puesto->latitud === null || $puesto->longitud === null) {
            return null;
        }

        return (int) round(self::distanciaMetros((float) $this->latitud, (float) $this->longitud, (float) $puesto->latitud, (float) $puesto->longitud));
    }
}
