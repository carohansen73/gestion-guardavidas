<?php

namespace App\Policies;

use App\Models\Postulacion;
use App\Models\User;

/**
 * Autoriza las pantallas de ADMIN sobre inscripciones. El área del propio
 * postulante (/postulacion) no pasa por acá: un postulante no tiene ningún
 * permiso, solo accede a lo suyo (ver PostulacionController).
 */
class PostulacionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver_postulacion');
    }

    public function view(User $user, Postulacion $postulacion): bool
    {
        return $user->can('ver_postulacion');
    }

    /** Aceptar / rechazar / dejar pendiente + observaciones. */
    public function revisar(User $user, Postulacion $postulacion): bool
    {
        return $user->can('revisar_postulacion');
    }

    /** Selección en lote de postulantes (alta de guardavidas), deshacer y cierre. */
    public function seleccionar(User $user): bool
    {
        return $user->can('seleccionar_postulacion');
    }
}
