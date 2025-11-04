<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Novedad extends Model
{
    protected $table = 'novedades';


     protected $fillable = [
        'fecha',
        'tipo',
        'titulo',
        'descripcion',
        'playa_id',
        'referencia_modelo',
        'icono',
        'color',
        'referencia_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',  // 👈 convierte automáticamente a Carbon
    ];

     public function playa() {
        return $this->belongsTo(Playa::class);
    }

    public function referencia() {
        return $this->morphTo(null, 'referencia_modelo', 'referencia_id');
    }
}
