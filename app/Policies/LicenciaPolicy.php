<?php

namespace App\Policies;

use App\Models\Licencia;
use App\Models\User;

class LicenciaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_licencia');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Licencia $licencia): bool
    {
        return $user->can('ver_licencia');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('agregar_licencia');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Licencia $licencia): bool
    {
        // Temporada abierta (o gestionar_temporada_cerrada) y, además, que sea suyo o pueda gestionar los ajenos (ver TieneAutor).
        return $licencia->modificablePor($user) && $user->can('editar_licencia') && $licencia->esPropioOGestionaAjenos($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Licencia $licencia): bool
    {
        // Temporada abierta (o gestionar_temporada_cerrada) y, además, que sea suyo o pueda gestionar los ajenos (ver TieneAutor).
        return $licencia->modificablePor($user) && $user->can('eliminar_licencia') && $licencia->esPropioOGestionaAjenos($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Licencia $licencia): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Licencia $licencia): bool
    {
        return false;
    }
}
