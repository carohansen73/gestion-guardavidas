<?php

namespace App\Enums;

/**
 * Paleta de colores que puede elegir un rol (columna `roles.color`). Las clases van escritas completas acá
 * (y no armadas con el nombre del color) para que Tailwind las encuentre al compilar.
 */
enum ColorRol: string
{
    case Fuchsia = 'fuchsia';
    case Sky = 'sky';
    case Teal = 'teal';
    case Amber = 'amber';
    case Emerald = 'emerald';
    case Violet = 'violet';
    case Rose = 'rose';
    case Slate = 'slate';

    public function texto(): string
    {
        return match ($this) {
            self::Fuchsia => 'text-fuchsia-600 dark:text-fuchsia-400',
            self::Sky => 'text-sky-600 dark:text-sky-400',
            self::Teal => 'text-teal-600 dark:text-teal-400',
            self::Amber => 'text-amber-600 dark:text-amber-400',
            self::Emerald => 'text-emerald-600 dark:text-emerald-400',
            self::Violet => 'text-violet-600 dark:text-violet-400',
            self::Rose => 'text-rose-600 dark:text-rose-400',
            self::Slate => 'text-slate-600 dark:text-slate-300',
        };
    }

    public function fondo(): string
    {
        return match ($this) {
            self::Fuchsia => 'bg-fuchsia-100 dark:bg-fuchsia-900/40',
            self::Sky => 'bg-sky-100 dark:bg-sky-900/40',
            self::Teal => 'bg-teal-100 dark:bg-teal-900/40',
            self::Amber => 'bg-amber-100 dark:bg-amber-900/40',
            self::Emerald => 'bg-emerald-100 dark:bg-emerald-900/40',
            self::Violet => 'bg-violet-100 dark:bg-violet-900/40',
            self::Rose => 'bg-rose-100 dark:bg-rose-900/40',
            self::Slate => 'bg-slate-100 dark:bg-slate-700/60',
        };
    }
}
