<?php

namespace App\Policies;

use App\Models\Temporada;
use App\Models\User;

class TemporadaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_temporada');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Temporada $temporada): bool
    {
        return $user->can('ver_temporada');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('agregar_temporada');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Temporada $temporada): bool
    {
        return $user->can('editar_temporada');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Temporada $temporada): bool
    {
        return $user->can('eliminar_temporada');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Temporada $temporada): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Temporada $temporada): bool
    {
        return false;
    }
}
