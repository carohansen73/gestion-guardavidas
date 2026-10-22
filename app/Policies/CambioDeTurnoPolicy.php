<?php

namespace App\Policies;

use App\Models\CambioDeTurno;
use App\Models\User;

class CambioDeTurnoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_cambio_turno');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CambioDeTurno $cambioDeTurno): bool
    {
        return $user->can('ver_cambio_turno');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('agregar_cambio_turno');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CambioDeTurno $cambioDeTurno): bool
    {
        // Temporada abierta (o gestionar_temporada_cerrada) y, además, que sea suyo o pueda gestionar los ajenos (ver TieneAutor).
        return $cambioDeTurno->modificablePor($user) && $user->can('editar_cambio_turno') && $cambioDeTurno->esPropioOGestionaAjenos($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CambioDeTurno $cambioDeTurno): bool
    {
        // Temporada abierta (o gestionar_temporada_cerrada) y, además, que sea suyo o pueda gestionar los ajenos (ver TieneAutor).
        return $cambioDeTurno->modificablePor($user) && $user->can('eliminar_cambio_turno') && $cambioDeTurno->esPropioOGestionaAjenos($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CambioDeTurno $cambioDeTurno): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CambioDeTurno $cambioDeTurno): bool
    {
        return false;
    }
}
