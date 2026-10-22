<?php

namespace App\Models;

use App\Models\Concerns\TieneAutor;
use App\Models\Concerns\TieneTemporada;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CambioDeTurno extends Model
{
    /** @use HasFactory<\Database\Factories\CambioDeTurnoFactory> */
    use HasFactory, TieneAutor, TieneTemporada;

    /** Permiso para editar/borrar los registros que cargó otro usuario (ver TieneAutor). */
    protected string $permisoAjenos = 'gestionar_ajenos_cambio_turno';

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
