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

    /**
     * Disco donde está la constancia. Las nuevas se guardan en `local` (privado); las anteriores pueden seguir
     * en `public` hasta que se mueva la carpeta `licencias` a `storage/app/private`. Null si el archivo no está.
     */
    public function discoDeLaConstancia(): ?string
    {
        if (! $this->archivo) {
            return null;
        }

        foreach (['local', 'public'] as $disco) {
            if (Storage::disk($disco)->exists($this->archivo)) {
                return $disco;
            }
        }

        return null;
    }
}
