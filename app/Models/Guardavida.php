<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardavida extends Model
{

    use HasFactory;

    protected $perPage = 10;
    protected $table = 'guardavidas'; // tu tabla real

    protected $fillable = [
        'funcion',
        'nombre',
        'apellido',
        'dni',
        'telefono',
        'direccion',
        'numero',
        'piso_dpto',
        'user_id',
        'playa_id',
        'puesto_id',
        'turno',
    ];
    // Agregar accessor para contar asistencias
    protected $appends = ['asistencias_count', 'intervenciones_count'];

    public function getAsistenciasCountAttribute()
    {
        return $this->asistencias()->count();
    }

    public function getIntervencionesCountAttribute()
    {
        return $this->intervenciones()->count();
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }
/*
    public function playa()
    {
        return $this->belongsTo(Playa::class);
    }*/

    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }


    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'guardavidas_id');
    }

    public function intervenciones()
    {
        return $this->belongsToMany(Intervencion::class, 'guardavidas_intervenciones', 'guardavida_id', 'intervencion_id');
    }


    //***  Relaciones que se mencionan en la vista pero no estaban definidas agregar cuando se definan los turnos de cada usuario***
    /*
    public function turnos()
    {
        return $this->belongsToMany(Turno::class, 'guardavida_turno');
    }
        */

    //vi que habia como atributo pero falta la tabla para definirlos por grupos de funciones asi es mas facil filtrar y seleccionar
    /*
    public function funciones()
    {
        return $this->belongsToMany(Funcion::class, 'guardavida_funcion');
    }
*/
    public static function obtenerGuardavidas($idUser, $idPlaya)
    {
        return self::with(['puesto.playa'])
            ->where('user_id', $idUser)
            ->whereHas('puesto.playa', fn($q) => $q->where('id', $idPlaya))
            ->first();
    }


    public static function showGuardavidaId($id)
    {
        $guardavida = Guardavida::where('id', $id)->first();
        return $guardavida ?? null;
    }

    public function playa()
    {
        return $this->hasOneThrough(Playa::class, Puesto::class, 'id', 'id', 'puesto_id', 'playa_id');
    }
}
