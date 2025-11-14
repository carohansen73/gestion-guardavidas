<?php

namespace App\Policies;

use App\Models\Intervencion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

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
     *
     * Determine whether the user can view the model.
     */
    public function view(User $user, Intervencion $intervencion): bool
    {
       //TODO ver roles y permisos!
    //    return $user->role === 'admin' || $user->role === 'guardavida'  || $user->role === 'timonel';
       return $user->can('ver_intervencion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //TODO ver roles y permisos!
        //return $user->role === 'admin' || $user->role === 'guardavida'  || $user->role === 'timonel';
        return $user->can('agregar_intervencion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Intervencion $intervencion): bool
    {
         return $user->can('editar_intervencion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Intervencion $intervencion): bool
    {
        // return $user->id === $intervencion->user_id || $user->rol === 'encargado' || $user->rol === 'jefePlaya'
        // || $user->role === 'admin' ;

        return $user->can('eliminar_intervencion');
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

    public function before(User $user, string $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }
}
