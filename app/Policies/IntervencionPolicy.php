<?php

namespace App\Policies;

use App\Models\Intervencion;
use App\Models\User;

class IntervencionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_intervencion');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Intervencion $intervencion): bool
    {
        // TODO ver roles y permisos!
        //    return $user->role === 'admin' || $user->role === 'guardavida'  || $user->role === 'timonel';
        return $user->can('ver_intervencion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // TODO ver roles y permisos!
        // return $user->role === 'admin' || $user->role === 'guardavida'  || $user->role === 'timonel';
        return $user->can('agregar_intervencion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Intervencion $intervencion): bool
    {
        // Temporada abierta (o gestionar_temporada_cerrada) y, además, que sea suyo o pueda gestionar los ajenos (ver TieneAutor).
        return $intervencion->modificablePor($user) && $user->can('editar_intervencion') && $intervencion->esPropioOGestionaAjenos($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Intervencion $intervencion): bool
    {
        // Temporada abierta (o gestionar_temporada_cerrada) y, además, que sea suyo o pueda gestionar los ajenos (ver TieneAutor).
        return $intervencion->modificablePor($user) && $user->can('eliminar_intervencion') && $intervencion->esPropioOGestionaAjenos($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Intervencion $intervencion): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Intervencion $intervencion): bool
    {
        return false;
    }
}
