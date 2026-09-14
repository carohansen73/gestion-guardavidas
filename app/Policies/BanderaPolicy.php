<?php

namespace App\Policies;

use App\Models\Bandera;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BanderaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_bandera');
    }

    /**
     * Un guardavida solo ve banderas de su propia playa.
     * encargado y admin ven cualquiera.
     */
    public function view(User $user, Bandera $bandera): bool
    {
        if (!$user->can('ver_bandera')) {
            return false;
        }

        if ($user->hasRole('guardavida')) {
            return $user->guardavida?->playa_id === $bandera->playa_id;
        }

        return true;
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
        return $user->id === $bandera->user_id || $user->hasAnyRole(['encargado', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bandera $bandera): bool
    {
        return $user->id === $bandera->user_id || $user->hasAnyRole(['encargado', 'admin']);
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
