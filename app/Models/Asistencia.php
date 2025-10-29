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
    }/*
    public static function asistenciaPorPlaya(){

        $asistenciaPorPlaya = Asistencia::where

    }


    public static function asistenciaPorTurno($turno){

        $asistenciaPorTurno = Asistencia::
    }
        */

}
