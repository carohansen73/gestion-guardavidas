<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardavida extends Model
{
    /** @use HasFactory<\Database\Factories\GuardavidaFactory> */
    use HasFactory;

    protected $fillable = [
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
    ];

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

    //many to many
     public function intervenciones()
    {
        return $this->belongsToMany(Intervencion::class, 'guardavidas_intervenciones', 'guardavida_id', 'intervencion_id');
    }

    public static function obtenerGuardavidas($idUser, $idPlaya){
        $datosGuardavidas = Guardavida::select('guardavidas.*', 'puestos.nombre as puesto', 'playas.nombre as playa','playas.id as playas_id')
        ->where('user_id', $idUser)
        ->join('puestos', 'guardavidas.puesto_id', '=','puestos.id')
        ->join('playas', 'puestos.playa_id', '=', 'playas.id')
        ->where('playas.id', $idPlaya)
        ->first();
        
        return !empty($datosGuardavidas) ? $datosGuardavidas : null;
        
    }
}
