<?php

namespace App\Services;

use App\Models\Guardavida;
use App\Models\Postulacion;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Fase 5: selección de postulantes para la temporada.
 *
 * - confirmar(): marca las postulaciones como seleccionadas y deja a la
 *   persona como guardavida (crea la fila de `guardavidas` o actualiza la
 *   que ya tenía) con rol `guardavida` o `encargado`.
 * - deseleccionar(): la saca de la selección y la devuelve a `postulante`.
 * - candidatosCierre()/cerrar(): los guardavidas del plantel que NO fueron
 *   seleccionados en la temporada vuelven a rol `postulante` (se conserva su
 *   fila de `guardavidas` y todo su historial: asistencias, licencias, etc.).
 *
 * Los usuarios con rol admin/superadmin nunca se tocan.
 */
class SeleccionPostulantes
{
    private const ROLES_INTOCABLES = ['admin', 'superadmin'];

    /**
     * @param  array<int,array{postulacion:Postulacion,playa_id:int,puesto_id:?int,turno:?string,encargado:bool}>  $filas
     * @return array{creados:int,actualizados:int}
     */
    public function confirmar(array $filas): array
    {
        $creados = 0;
        $actualizados = 0;

        DB::transaction(function () use ($filas, &$creados, &$actualizados) {
            foreach ($filas as $fila) {
                /** @var Postulacion $postulacion */
                $postulacion = $fila['postulacion'];
                $user = $postulacion->user;

                if ($user->hasAnyRole(self::ROLES_INTOCABLES)) {
                    throw new \DomainException("{$user->lastname}, {$user->name} tiene rol de administrador y no puede pasar a guardavida desde acá.");
                }

                // El puesto es opcional: si no se define, el propio guardavida lo elige al ingresar por primera vez.
                $puesto = $fila['puesto_id'] ? Puesto::findOrFail($fila['puesto_id']) : null;
                $playaId = $puesto?->playa_id ?? $fila['playa_id'];
                $guardavida = $user->guardavida;
                $encargado = (bool) $fila['encargado'];

                // Se respeta una función ya cargada (Timonel, Jefe de playa);
                // marcar "encargado" solo reemplaza a la función genérica.
                $funcion = $guardavida?->funcion ?? 'Guardavida';
                if ($encargado && $funcion === 'Guardavida') {
                    $funcion = 'Encargado';
                } elseif (! $encargado && $funcion === 'Encargado') {
                    $funcion = 'Guardavida';
                }

                $datos = [
                    'playa_id' => $playaId,
                    'puesto_id' => $puesto?->id,
                    'funcion' => $funcion,
                ];
                if (filled($fila['turno'])) {
                    $datos['turno'] = $fila['turno'];
                }

                if ($guardavida) {
                    $guardavida->update($datos);
                    $actualizados++;
                } else {
                    $guardavida = Guardavida::create($datos + ['user_id' => $user->id]);
                    $creados++;
                }

                $user->syncRoles([$encargado ? 'encargado' : 'guardavida']);
                $user->update(['enabled' => true]);

                $postulacion->update([
                    'seleccionado' => true,
                    'playa_asignada_id' => $playaId,
                    'puesto_asignado_id' => $puesto?->id,
                    'turno_asignado' => $guardavida->turno,
                    'funcion_asignada' => $funcion,
                ]);
            }
        });

        return ['creados' => $creados, 'actualizados' => $actualizados];
    }

    /** Saca a la persona de la selección y la devuelve a postulante (conserva su fila de guardavidas). */
    public function deseleccionar(Postulacion $postulacion): void
    {
        DB::transaction(function () use ($postulacion) {
            $postulacion->update([
                'seleccionado' => false,
                'playa_asignada_id' => null,
                'puesto_asignado_id' => null,
                'turno_asignado' => null,
                'funcion_asignada' => null,
            ]);

            $this->volverAPostulante($postulacion->user);
        });
    }

    /** Guardavidas del plantel actual que no fueron seleccionados en la temporada. */
    public function candidatosCierre(Temporada $temporada): Collection
    {
        return Guardavida::activos()
            ->whereDoesntHave('user', fn ($u) => $u->role(self::ROLES_INTOCABLES))
            ->whereDoesntHave('user.postulaciones', fn ($p) => $p->where('temporada_id', $temporada->id)->where('seleccionado', true))
            ->with(['user:id,name,lastname,dni', 'playa', 'puesto'])
            ->get()
            ->sortBy([['user.lastname', 'asc'], ['user.name', 'asc']], SORT_FLAG_CASE | SORT_NATURAL)
            ->values();
    }

    /**
     * Pasa a postulante a los guardavidas indicados (solo si siguen siendo
     * candidatos del cierre: no se puede usar para tocar a cualquiera).
     */
    public function cerrar(Temporada $temporada, array $guardavidaIds): int
    {
        $total = 0;

        DB::transaction(function () use ($temporada, $guardavidaIds, &$total) {
            foreach ($this->candidatosCierre($temporada)->whereIn('id', $guardavidaIds) as $guardavida) {
                $this->volverAPostulante($guardavida->user);
                $total++;
            }
        });

        return $total;
    }

    private function volverAPostulante(User $user): void
    {
        if ($user->hasAnyRole(self::ROLES_INTOCABLES)) {
            return;
        }

        $user->syncRoles(['postulante']);
        // Un celular con la sesión offline guardada no tiene que poder seguir fichando.
        $user->tokens()->delete();
    }
}
