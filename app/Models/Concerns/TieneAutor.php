<?php

namespace App\Models\Concerns;

use App\Models\User;

/**
 * Registro operativo que recuerda quién lo cargó (columna `user_id`).
 *
 * El autor se completa solo al crear, con el usuario logueado. Editar o borrar un registro es cosa de su autor;
 * para tocar los de otros hace falta el permiso `gestionar_ajenos_<recurso>` (cada modelo declara cuál en
 * `$permisoAjenos`). Los registros anteriores a esta columna no tienen autor: solo los toca quien tenga ese permiso.
 *
 * OJO: igual que TieneTemporada, el autor se completa en un evento del modelo; un insert por Query Builder no lo hace.
 */
trait TieneAutor
{
    public static function bootTieneAutor(): void
    {
        static::creating(function ($modelo) {
            if ($modelo->user_id === null && auth()->check()) {
                $modelo->user_id = auth()->id();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** ¿Lo cargó este usuario? */
    public function esDe(?User $usuario): bool
    {
        return $usuario !== null && $this->user_id !== null && (int) $this->user_id === (int) $usuario->id;
    }

    /** ¿Puede tocar este registro por ser suyo o por poder gestionar los ajenos? (El permiso editar_/eliminar_ se pide aparte.) */
    public function esPropioOGestionaAjenos(?User $usuario): bool
    {
        return $this->esDe($usuario) || ($usuario?->can($this->permisoAjenos) ?? false);
    }
}
