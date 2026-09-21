<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuardavidaFrancoHistorial extends Model
{
    protected $table = 'guardavida_franco_historial';

    protected $fillable = [
        'guardavida_id',
        'dias_franco',
        'vigente_desde',
        'vigente_hasta',
        'creado_por_user_id',
    ];

    protected $casts = [
        'dias_franco' => 'array',
        'vigente_desde' => 'date',
        'vigente_hasta' => 'date',
    ];

    public function guardavida()
    {
        return $this->belongsTo(Guardavida::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por_user_id');
    }

    /** Nombres de los días de este esquema, ej. "Sábado, Domingo". */
    public function getDiasNombresAttribute(): string
    {
        return Guardavida::nombresDeDias($this->dias_franco);
    }
}
