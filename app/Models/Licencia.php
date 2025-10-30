<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licencia extends Model
{
    /** @use HasFactory<\Database\Factories\LicenciaFactory> */
    use HasFactory;

     protected $fillable = [
        'fecha_inicio',
        'fecha_fin' ,
        'tipo_licencia' ,
        'en_tiempo' ,
        'turno',
        'guardavida_id',
        'playa_id' ,
        'puesto_id',
        'detalle',
        'archivo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'en_tiempo' => 'boolean',
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

    public function getArchivoUrlAttribute()
    {
        return $this->archivo ? asset('storage/' . $this->archivo) : null;
    }
}
