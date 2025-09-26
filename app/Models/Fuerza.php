<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fuerza extends Model
{
    protected $fillable = ['nombre'];

     public function intervenciones()
    {
        return $this->belongsToMany(Intervencion::class, 'fuerzas_intervenciones', 'fuerza_id', 'intervencion_id');
    }
}
