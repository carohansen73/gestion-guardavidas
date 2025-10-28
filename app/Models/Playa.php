<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playa extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    //Un balneario puede estar vinculado a varios puestos
    public function puestos()
    {
        return $this->hasMany(Puesto::class);  // Relación de un-a-muchos
    }

    //  public function guardavidas()
    // {
    //     return $this->hasMany(Guardavida::class);
    // }

    // public function incidentes()
    // {
    //     return $this->hasMany(Incidente::class);
    // }


    // Un balneario puede estar asignado a varios guardavidas
    public function guardavidas()
    {
        return $this->hasMany(Guardavida::class);
    }

    // Un balneario puede estar vinculado a muchas asistencias
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }



}
