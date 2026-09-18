<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrancoIntercambio extends Model
{
    protected $table = 'franco_intercambios';

    protected $fillable = [
        'guardavida_solicitante_id',
        'guardavida_destinatario_id',
        'fecha_propia',
        'fecha_deseada',
        'estado',
        'mensaje',
        'respondido_at',
    ];

    protected $casts = [
        'fecha_propia' => 'date',
        'fecha_deseada' => 'date',
        'respondido_at' => 'datetime',
    ];

    public function solicitante()
    {
        return $this->belongsTo(Guardavida::class, 'guardavida_solicitante_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(Guardavida::class, 'guardavida_destinatario_id');
    }
}
