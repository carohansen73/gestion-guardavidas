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
    protected $appends = ['asistencias_count', 'intervenciones_count','licencias_count'];


    ////------------------------------------------ Relaciones -------------------------------------------------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function playa()
    {
        return $this->belongsTo(Playa::class);
    }

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

    public function licencias()
    {
        return $this->hasMany(Licencia::class, 'guardavida_id');
    }

    // ******************** Contadores ******************************************
    public function getAsistenciasCountAttribute()
    {
        return $this->asistencias()->count();
    }

    public function getIntervencionesCountAttribute()
    {
        return $this->intervenciones()->count();
    }

    public function getLicenciasCountAttribute(){
        return $this->licencias()->count();
    }

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


}
