<?php

namespace App\Policies;

use App\Models\Bandera;
use App\Models\User;

class BanderaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_bandera');
    }

    public function view(User $user, Bandera $bandera): bool
    {
        return $user->can('ver_bandera');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('agregar_bandera');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bandera $bandera): bool
    {
        // Temporada abierta (o gestionar_temporada_cerrada) y, además, que sea suyo o pueda gestionar los ajenos (ver TieneAutor).
        return $bandera->modificablePor($user) && $user->can('editar_bandera') && $bandera->esPropioOGestionaAjenos($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bandera $bandera): bool
    {
        // Temporada abierta (o gestionar_temporada_cerrada) y, además, que sea suyo o pueda gestionar los ajenos (ver TieneAutor).
        return $bandera->modificablePor($user) && $user->can('eliminar_bandera') && $bandera->esPropioOGestionaAjenos($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bandera $bandera): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bandera $bandera): bool
    {
        return false;
    }
}
