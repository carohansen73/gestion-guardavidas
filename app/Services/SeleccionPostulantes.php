<?php

namespace App\Services;

use App\Enums\RolUsuario;
use App\Models\Guardavida;
use App\Models\Postulacion;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use App\Notifications\PostulacionSeleccionadaMailNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
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
 * Los usuarios con rol admin/superadmin o `personal` (empleados fijos que solo fichan, sin temporada)
 * nunca se tocan.
 */
class SeleccionPostulantes
{
    public function __construct(private CatalogoRoles $roles) {}

    /**
     * @param  array<int,array{postulacion:Postulacion,playa_id:int,puesto_id:?int,turno:?string,encargado:bool}>  $filas
     * @return array{creados:int,actualizados:int}
     */
    public function confirmar(array $filas, ?Carbon $desde = null): array
    {
        $creados = 0;
        $actualizados = 0;
        $desde ??= now();

        DB::transaction(function () use ($filas, $desde, &$creados, &$actualizados) {
            foreach ($filas as $fila) {
                /** @var Postulacion $postulacion */
                $postulacion = $fila['postulacion'];
                $user = $postulacion->user;

                if ($user->hasAnyRole($this->roles->intocables())) {
                    throw new \DomainException("{$user->lastname}, {$user->name} tiene un rol que la selección no modifica (administrativo o de personal fijo) y no puede pasar a guardavida desde acá.");
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

                $esNuevo = ! $guardavida;
                if ($guardavida) {
                    $guardavida->update($datos);
                    $actualizados++;
                } else {
                    $guardavida = Guardavida::create($datos + ['user_id' => $user->id]);
                    $creados++;
                }

                // Alta en el plantel a partir de la fecha de inicio (para el presentismo).
                $guardavida->darDeAlta($desde, $postulacion->temporada_id, null, $esNuevo);

                $user->syncRoles([($encargado ? RolUsuario::Encargado : RolUsuario::Guardavida)->value]);
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

        // Recién con la selección ya guardada se avisa por mail a cada persona (en cola: confirmar en
        // lote no depende de lo que tarde el servidor de mail, y un mail que falla no deshace nada).
        foreach ($filas as $fila) {
            $this->avisarSeleccion($fila['postulacion'], $desde);
        }

        return ['creados' => $creados, 'actualizados' => $actualizados];
    }

    private function avisarSeleccion(Postulacion $postulacion, Carbon $desde): void
    {
        $postulacion->load(['user', 'temporada', 'playaAsignada', 'puestoAsignado']);

        rescue(fn () => $postulacion->user->notify(new PostulacionSeleccionadaMailNotification(
            $postulacion->temporada->nombre,
            $postulacion->playaAsignada?->nombre ?? '—',
            $postulacion->puestoAsignado?->nombre,
            ['M' => 'Mañana', 'T' => 'Tarde'][$postulacion->turno_asignado] ?? null,
            $postulacion->funcion_asignada,
            $desde->format('d/m/Y'),
        )));
    }

    /**
     * Deja a alguien que entró SIN pasar por la selección (alta manual, reincorporación) igual que a
     * los demás: con una postulación aceptada y seleccionada en esa temporada, con la playa/puesto/
     * turno/función que tiene hoy. Así "seleccionado en la temporada" es la única regla (la usa el
     * cierre, los listados, etc.). No exige datos ni documentos: el admin los puede completar después
     * desde la postulación. Si ya figuraba como seleccionado en esa temporada no la toca.
     */
    public function incluirEnTemporada(Guardavida $guardavida, Temporada $temporada, string $nota = 'Dado/a de alta por la administración'): Postulacion
    {
        $postulacion = Postulacion::firstOrNew(['user_id' => $guardavida->user_id, 'temporada_id' => $temporada->id]);

        if ($postulacion->exists && $postulacion->seleccionado) {
            return $postulacion;
        }

        $postulacion->fill([
            'estado' => Postulacion::ESTADO_ACEPTADA,
            'seleccionado' => true,
            'playa_asignada_id' => $guardavida->playa_id,
            'puesto_asignado_id' => $guardavida->puesto_id,
            'turno_asignado' => $guardavida->turno,
            'funcion_asignada' => $guardavida->funcion,
            'revisado_por_user_id' => Auth::id(),
            'fecha_revision' => now(),
        ]);
        if (blank($postulacion->observaciones)) {
            $postulacion->observaciones = $nota;
        }
        $postulacion->save();

        return $postulacion;
    }

    /**
     * Corrige una selección hecha por error: saca a la persona de la selección, borra el
     * alta de esa temporada y la devuelve a postulante (conserva su fila de guardavidas).
     * Para una baja de alguien que ya trabajó usar darDeBaja().
     */
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

            $postulacion->user->guardavida?->periodos()
                ->where('temporada_id', $postulacion->temporada_id)->whereNull('hasta')->delete();

            $this->volverAPostulante($postulacion->user);
        });
    }

    /**
     * Baja de alguien que ya trabajó (renuncia, etc.): cierra su período en esa fecha y lo
     * devuelve a postulante, así deja de aparecer en listados y asistencias pero puede volver
     * a postularse. Conserva la marca de seleccionado de la temporada y todo su historial.
     */
    public function darDeBaja(Guardavida $guardavida, Carbon $hasta, ?string $motivo = null): void
    {
        DB::transaction(function () use ($guardavida, $hasta, $motivo) {
            $guardavida->darDeBajaDelPeriodo($hasta, $motivo);

            // El personal fijo (rol `personal`) no vuelve a postulante: conserva su rol y se lo deshabilita.
            if ($this->roles->userEsPlantelSinTemporada($guardavida->user)) {
                $guardavida->user->update(['enabled' => false]);
                $guardavida->user->tokens()->delete(); // un celular con la sesión offline guardada no puede seguir fichando

                return;
            }

            $this->volverAPostulante($guardavida->user);
        });
    }

    /**
     * Vuelve a dar de alta a alguien que está fuera del plantel (dado de baja o no seleccionado):
     * abre un período nuevo desde esa fecha y le devuelve el rol (encargado si esa es su función).
     * Mantiene su playa y puesto anteriores. No toca a admin/superadmin.
     */
    public function reincorporar(Guardavida $guardavida, Carbon $desde): void
    {
        $user = $guardavida->user;

        // Personal fijo dado de baja: se vuelve a habilitar con un período nuevo; no pasa por la temporada.
        if ($this->roles->userEsPlantelSinTemporada($user) && ! $this->roles->userEsAdministrativo($user)) {
            DB::transaction(function () use ($guardavida, $user, $desde) {
                $guardavida->darDeAlta($desde, null, 'Reincorporación');
                $user->update(['enabled' => true]);
            });

            return;
        }

        if ($user->hasAnyRole($this->roles->intocables())) {
            throw new \DomainException('Un administrador o un empleado de personal no puede darse de alta como guardavida desde acá.');
        }

        DB::transaction(function () use ($guardavida, $user, $desde) {
            $temporada = Temporada::paraFecha($desde);
            $guardavida->darDeAlta($desde, $temporada?->id, 'Reincorporación');
            if ($temporada) {
                $this->incluirEnTemporada($guardavida, $temporada, 'Reincorporado/a por la administración');
            }
            $user->syncRoles([($guardavida->funcion === 'Encargado' ? RolUsuario::Encargado : RolUsuario::Guardavida)->value]);
            $user->update(['enabled' => true]);
        });
    }

    /** Guardavidas del plantel actual que no fueron seleccionados en la temporada. */
    public function candidatosCierre(Temporada $temporada): Collection
    {
        return Guardavida::activos()
            ->whereDoesntHave('user', fn ($u) => $u->role($this->roles->intocables()))
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
    public function cerrar(Temporada $temporada, array $guardavidaIds, ?Carbon $hasta = null): int
    {
        $total = 0;
        $hasta ??= now();

        DB::transaction(function () use ($temporada, $guardavidaIds, $hasta, &$total) {
            foreach ($this->candidatosCierre($temporada)->whereIn('id', $guardavidaIds) as $guardavida) {
                $guardavida->darDeBajaDelPeriodo($hasta, 'No seleccionado en la temporada');
                $this->volverAPostulante($guardavida->user);
                $total++;
            }
        });

        return $total;
    }

    private function volverAPostulante(User $user): void
    {
        if ($user->hasAnyRole($this->roles->intocables())) {
            return;
        }

        $user->syncRoles([RolUsuario::Postulante->value]);
        // Un celular con la sesión offline guardada no tiene que poder seguir fichando.
        $user->tokens()->delete();
    }
}
