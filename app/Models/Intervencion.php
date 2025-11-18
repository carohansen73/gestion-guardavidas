<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervencion extends Model
{
    /** @use HasFactory<\Database\Factories\IntervencionFactory> */
    use HasFactory;

       protected $fillable = [
        'fecha',
        'tipo_intervencion',
        'victimas',
        'codigo',
        'bandera_id',
        'traslado',
        'playa_id',
        'puesto_id',
        'detalles',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'traslado' => 'boolean',
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

    public function bandera()
    {
        return $this->belongsTo(Bandera::class);
    }

    //many to many
    public function guardavidas()
    {
        return $this->belongsToMany(Guardavida::class, 'guardavidas_intervenciones', 'intervencion_id', 'guardavida_id');
    }
    //many to many
    public function fuerzas()
    {
        return $this->belongsToMany(Fuerza::class, 'fuerzas_intervenciones', 'intervencion_id', 'fuerza_id');
    }
}
