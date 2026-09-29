<?php

namespace App\Http\Middleware;

use App\Models\Temporada;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if (! Temporada::activa()) {
            abort(403, 'No hay una temporada activa en este momento. Esta acción solo está disponible durante la temporada.');
        }

        return $next($request);
    }
}
