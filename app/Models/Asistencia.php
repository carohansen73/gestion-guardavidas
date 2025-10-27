<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
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



    public static function nuevaAsistencia($longitud, $latitud, $precision, $puesto_id, $guardavidas_id, $fecha_hora){
        $asistencia = Asistencia::create([
            "longitud" => $longitud,
            "latitud" => $latitud,
            "precision" => $precision,
            'puesto_id' => $puesto_id,
            'guardavidas_id' => $guardavidas_id,
            'fecha_hora' => $fecha_hora,
        ]);

        return $asistencia;
    }
}
