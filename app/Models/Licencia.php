<?php

namespace App\Models;

use App\Models\Concerns\TieneTemporada;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Licencia extends Model
{
    /** @use HasFactory<\Database\Factories\LicenciaFactory> */
    use HasFactory, TieneTemporada;

    /** Fecha que determina la temporada del registro. */
    protected string $campoFechaTemporada = 'fecha_inicio';

    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'tipo_licencia',
        'en_tiempo',
        'turno',
        'guardavida_id',
        'playa_id',
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

    /** Link a la constancia: pasa por la aplicación (login + permiso), no por una URL pública del disco. */
    public function getArchivoUrlAttribute()
    {
        return $this->archivo ? route('licencia.constancia', $this) : null;
    }

    /** Disco privado (`storage/app/private`) donde se guardan las constancias: no tiene URL pública. */
    public const DISCO_CONSTANCIAS = 'local';

    /** ¿Tiene constancia y el archivo está en el disco? */
    public function constanciaDisponible(): bool
    {
        return $this->archivo && Storage::disk(self::DISCO_CONSTANCIAS)->exists($this->archivo);
    }
}
