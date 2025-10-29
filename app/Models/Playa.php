<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playa extends Model
{
    protected $fillable = ['nombre', 'color'];

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
}
