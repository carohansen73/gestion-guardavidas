<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Un período de alta -> baja de una persona en el plantel (ver la migración
 * create_guardavida_periodos_table). Una persona puede tener varios (uno por
 * temporada, o por reincorporación).
 */
class GuardavidaPeriodo extends Model
{
    protected $table = 'guardavida_periodos';

    protected $fillable = ['guardavida_id', 'temporada_id', 'desde', 'hasta', 'motivo'];

    protected $casts = [
        'desde' => 'date',
        'hasta' => 'date',
    ];

    public function guardavida()
    {
        return $this->belongsTo(Guardavida::class);
    }

    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    /** ¿Este período cubre la fecha? (desde/hasta vacíos = sin límite) */
    public function cubre($fecha): bool
    {
        $fecha = \Carbon\Carbon::parse($fecha)->startOfDay();

        return ($this->desde === null || $this->desde->startOfDay()->lte($fecha))
            && ($this->hasta === null || $this->hasta->startOfDay()->gte($fecha));
    }
}
