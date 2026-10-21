<?php

namespace App\Enums;

/**
 * Solo los roles que el CÓDIGO necesita nombrar porque son parte de un proceso: la selección de postulantes los
 * asigna y quita (postulante ⇄ guardavida/encargado), el alta pública crea postulantes y superadmin es el único
 * que nunca se toca desde la app. Todo lo demás de un rol (etiqueta, color, si es plantel, quién puede asignarlo…)
 * vive en la tabla `roles` — ver App\Models\Rol y App\Services\CatalogoRoles — y sus permisos, en Spatie.
 */
enum RolUsuario: string
{
    case Superadmin = 'superadmin';
    case Admin = 'admin';
    case Encargado = 'encargado';
    case Guardavida = 'guardavida';
    case Postulante = 'postulante';
}
