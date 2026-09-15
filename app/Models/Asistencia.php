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

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];



    public static function nuevaAsistencia($longitud, $latitud, $precision, $puesto_id, $guardavidas_id, $fecha_hora){
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




    /*
    public static function asistenciaPorPlaya(){

        $asistenciaPorPlaya = Asistencia::where

    }


    public static function asistenciaPorTurno($turno){

        $asistenciaPorTurno = Asistencia::
    }
        */
}
