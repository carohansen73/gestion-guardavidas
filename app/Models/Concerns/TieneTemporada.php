<?php

namespace App\Models\Concerns;

use App\Models\Temporada;

/**
 * Registro operativo que pertenece a una temporada (columna `temporada_id`).
 *
 * La temporada NUNCA se elige a mano ni sale de "hoy": se calcula siempre a partir de la fecha del propio
 * registro (Temporada::paraDato). Se recalcula al crearlo y cada vez que cambia esa fecha, así un registro
 * cargado con fecha pasada, editado o sincronizado tarde desde un celular offline queda bien asignado.
 *
 * Cada modelo declara su columna de fecha en `$campoFechaTemporada` (por defecto `fecha`).
 *
 * OJO: el cálculo vive en el evento `saving` del modelo. Una escritura que lo saltee (Query Builder,
 * ->update() masivo, insert/upsert) deja el registro sin recalcular: usar siempre el modelo, o llamar a
 * App\Services\ReasignadorTemporadas.
 */
trait TieneTemporada
{
    public static function bootTieneTemporada(): void
    {
        static::saving(function ($modelo) {
            $campo = $modelo->columnaFechaTemporada();

            if ($modelo->temporada_id === null || $modelo->isDirty($campo)) {
                $modelo->temporada_id = $modelo->{$campo} ? Temporada::paraDato($modelo->{$campo})?->id : null;
            }
        });
    }

    /** Columna cuya fecha determina la temporada. */
    public function columnaFechaTemporada(): string
    {
        return property_exists($this, 'campoFechaTemporada') ? $this->campoFechaTemporada : 'fecha';
    }

    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    /**
     * ¿Puede este usuario modificar o borrar el registro? Las temporadas cerradas quedan como historial:
     * solo admin/superadmin las tocan; el resto (guardavida, encargado) solo edita lo de la temporada en curso.
     *
     * PARA DESBLOQUEAR (si en el futuro piden que encargados/guardavidas corrijan temporadas pasadas):
     *   - devolver `true` acá, o agregar el rol que corresponda en la lista de abajo; y
     *   - en App\Http\Middleware\EnsureTemporadaActiva sacar el chequeo de que la fecha cargada
     *     caiga dentro de la temporada en curso.
     * Las políticas (App\Policies\*) y las vistas de los listados ya consultan este método.
     */
    public function modificablePor(?\App\Models\User $usuario): bool
    {
        if ($usuario?->can('gestionar_temporada_cerrada')) {
            return true;
        }

        return $this->temporada_id !== null && $this->temporada_id === Temporada::activa()?->id;
    }

    /** Filtra por temporada. `null` = sin filtro (todas). */
    public function scopeDeTemporada($query, $temporada)
    {
        $id = $temporada instanceof Temporada ? $temporada->id : $temporada;

        return $id ? $query->where($this->getTable().'.temporada_id', $id) : $query;
    }
}
