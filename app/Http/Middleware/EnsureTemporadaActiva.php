<?php

namespace App\Http\Middleware;

use App\Models\Temporada;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea creación/edición/eliminación de intervenciones, banderas,
 * licencias, novedades de materiales y cambios de turno para guardavida/
 * encargado cuando no hay una Temporada con ventana operativa activa.
 * Admin/superadmin no tienen esta restricción (siguen gestionando todo el
 * año). No se aplica a Asistencia (fichaje) — ver CLAUDE.md/plan de
 * temporadas: el fichaje queda siempre disponible (hay personal, como el
 * de aeródromo, que usa el sistema solo para fichar todo el año).
 */
class EnsureTemporadaActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ($user->hasRole('admin') || $user->hasRole('superadmin'))) {
            return $next($request);
        }

        $activa = Temporada::activa();
        if (! $activa) {
            abort(403, 'No hay una temporada activa en este momento. Esta acción solo está disponible durante la temporada.');
        }

        // La fecha de lo que se carga tiene que caer dentro de la temporada en curso: así un registro
        // no termina asignado a una temporada cerrada (que es historial y solo toca un admin).
        // PARA DESBLOQUEAR cargas/ediciones en temporadas pasadas para guardavida/encargado: sacar este
        // bloque (y ver TieneTemporada::modificablePor).
        foreach (['fecha', 'fecha_inicio'] as $campo) {
            $valor = $request->input($campo);
            if (! is_string($valor) || $valor === '') {
                continue;
            }

            try {
                $fecha = Carbon::parse($valor);
            } catch (\Throwable) {
                continue; // fecha inválida: la rechaza la validación del formulario
            }

            if ($fecha->lt($activa->fecha_inicio->copy()->startOfDay()) || $fecha->gt($activa->fecha_fin->copy()->endOfDay())) {
                throw ValidationException::withMessages([
                    $campo => "La fecha tiene que estar dentro de la temporada en curso ({$activa->fecha_inicio->format('d/m/Y')} al {$activa->fecha_fin->format('d/m/Y')}).",
                ]);
            }
        }

        return $next($request);
    }
}
