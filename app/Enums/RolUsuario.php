<?php
namespace App\Enums;

use App\Models\User;

enum RolUsuario: string {

    case Superadmin = 'superadmin';
    case Admin = 'admin';
    case Encargado = 'encargado';
    case Guardavida = 'guardavida';

    /**
     * Rol "principal" a mostrar en la UI para un usuario que puede tener más
     * de un rol asignado (ej. admin + superadmin, agregado a mano por SQL).
     * El orden de los cases() define la prioridad: superadmin > admin >
     * encargado > guardavida.
     */
    public static function principal(User $user): self
    {
        foreach (self::cases() as $rol) {
            if ($user->hasRole($rol->value)) {
                return $rol;
            }
        }

        return self::Guardavida;
    }

    public function texto(): string
    {
        return match ($this) {
            self::Superadmin => 'Superadmin',
            self::Admin => 'Admin',
            self::Encargado => 'Encargado',
            self::Guardavida => 'Guardavida',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Superadmin => 'text-fuchsia-600 dark:text-fuchsia-400',
            self::Admin => 'text-sky-600 dark:text-sky-400',
            self::Encargado => 'text-amber-600 dark:text-amber-400',
            self::Guardavida => 'text-emerald-600 dark:text-emerald-400',
        };
    }

    public function fondo(): string
    {
        return match ($this) {
            self::Superadmin => 'bg-fuchsia-100 dark:bg-fuchsia-900/40',
            self::Admin => 'bg-sky-100 dark:bg-sky-900/40',
            self::Encargado => 'bg-amber-100 dark:bg-amber-900/40',
            self::Guardavida => 'bg-emerald-100 dark:bg-emerald-900/40',
        };
    }

    /**
     * Nombre lógico del ícono a renderizar; el SVG en sí vive en el
     * componente Blade (resources/views/components/role-badge.blade.php).
     */
    public function icono(): string
    {
        return match ($this) {
            self::Superadmin => 'sparkles',
            self::Admin => 'shield-check',
            self::Encargado => 'star',
            self::Guardavida => 'lifebuoy',
        };
    }
}
