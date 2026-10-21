<?php

namespace App\Services;

use App\Enums\RolUsuario;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Qué significa cada rol para el sistema, leído de la tabla `roles` (una sola consulta por request).
 * Es el único lugar que responde "¿quién es plantel?", "¿a quién no toca la selección?" o "¿qué roles puede
 * asignar este usuario?": ningún otro código tiene que conocer nombres de roles. Se registra como `scoped`
 * (AppServiceProvider), así que se recarga solo en cada request y en cada test.
 */
class CatalogoRoles
{
    /** @var Collection<string, Rol>|null */
    private ?Collection $roles = null;

    /** Todos los roles, indexados por nombre y de mayor a menor rango. */
    public function todos(): Collection
    {
        return $this->roles ??= Rol::query()->orderByDesc('rango')->orderBy('name')->get()->keyBy('name');
    }

    /** Vuelve a leer la tabla (después de crear o editar un rol en el mismo request). */
    public function olvidar(): void
    {
        $this->roles = null;
    }

    public function rol(string $nombre): ?Rol
    {
        return $this->todos()->get($nombre);
    }

    // ------------------------------------------------------------------------------------ plantel

    /** Roles con ficha de guardavidas (guardavida, encargado, personal…). */
    public function plantel(): array
    {
        return $this->nombres(fn (Rol $r) => $r->es_plantel);
    }

    /** Roles de plantel que pasan por la postulación y la selección cada temporada. */
    public function plantelConTemporada(): array
    {
        return $this->nombres(fn (Rol $r) => $r->es_plantel && $r->usa_temporada);
    }

    /** Roles de plantel fijo: no usan temporada y se dan de baja deshabilitando el usuario. */
    public function plantelSinTemporada(): array
    {
        return $this->nombres(fn (Rol $r) => $r->es_plantel && ! $r->usa_temporada);
    }

    /** Roles que ni la selección ni el cierre de temporada pueden cambiar. */
    public function intocables(): array
    {
        return $this->nombres(fn (Rol $r) => $r->intocable);
    }

    /** Roles administrativos: sin ficha de guardavidas y fuera de la selección (admin, superadmin, rrhh…). */
    public function administrativos(): array
    {
        return $this->nombres(fn (Rol $r) => $r->intocable && ! $r->es_plantel);
    }

    public function esPlantel(string $nombre): bool
    {
        return (bool) $this->rol($nombre)?->es_plantel;
    }

    public function usaTemporada(string $nombre): bool
    {
        return (bool) $this->rol($nombre)?->usa_temporada;
    }

    public function userEsPlantelSinTemporada(User $user): bool
    {
        return $user->hasAnyRole($this->plantelSinTemporada());
    }

    public function userEsAdministrativo(User $user): bool
    {
        return $user->hasAnyRole($this->administrativos());
    }

    public function userEsIntocable(User $user): bool
    {
        return $user->hasAnyRole($this->intocables());
    }

    /** ¿Está hoy en el plantel? Los de temporada, por rol; los fijos, mientras su usuario siga habilitado. */
    public function userEstaEnPlantel(User $user): bool
    {
        return $user->hasAnyRole($this->plantelConTemporada())
            || ($user->enabled && $this->userEsPlantelSinTemporada($user));
    }

    // ------------------------------------------------------------------ quién puede gestionar a quién

    /** Rango más alto entre los roles del usuario (0 si no tiene ninguno). */
    public function rangoDe(User $user): int
    {
        return (int) $user->getRoleNames()
            ->map(fn (string $nombre) => $this->rol($nombre)?->rango ?? 0)
            ->max();
    }

    /** Roles que este usuario puede elegir al dar de alta o cambiar el rol de alguien: asignables y de rango igual o menor. */
    public function asignablesPara(User $user): Collection
    {
        $rango = $this->rangoDe($user);

        return $this->todos()->filter(fn (Rol $r) => $r->asignable && $r->rango <= $rango)->values();
    }

    /** ¿Puede este usuario modificar a ese otro (nombre, email, contraseña, rol)? Nunca a un superadmin, ni a alguien de mayor rango. */
    public function puedeGestionar(User $actor, User $objetivo): bool
    {
        if ($objetivo->hasRole(RolUsuario::Superadmin->value)) {
            return false;
        }

        return $this->rangoDe($objetivo) <= $this->rangoDe($actor);
    }

    // ---------------------------------------------------------------------------------- presentación

    /** Rol principal de un usuario (el de mayor rango), para mostrar su etiqueta. */
    public function principal(User $user): ?Rol
    {
        $nombres = $user->getRoleNames();

        return $this->todos()->first(fn (Rol $r) => $nombres->contains($r->name));
    }

    /** @param  callable(Rol): bool  $filtro */
    private function nombres(callable $filtro): array
    {
        return $this->todos()->filter($filtro)->keys()->all();
    }
}
