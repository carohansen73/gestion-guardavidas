<?php

namespace App\Models;

use App\Models\Concerns\TieneTemporada;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CambioDeTurno extends Model
{
    /** @use HasFactory<\Database\Factories\CambioDeTurnoFactory> */
    use HasFactory, TieneTemporada;

    protected $fillable = [
        'fecha',
        'turno_nuevo',
        'guardavida_id',
        'playa_id',
        'puesto_id',
        'funcion',
        'detalles',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function guardavida()
    {
        return $this->belongsTo(Guardavida::class);
    }

    public function playa()
    {
        return $this->belongsTo(Playa::class);
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }
}
