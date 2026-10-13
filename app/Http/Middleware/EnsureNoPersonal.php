<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * El rol `personal` (empleados fijos que solo usan el sistema para fichar) no entra a las pantallas
 * de gestión de guardavidas, exportes ni asistencias de otras personas. Varias de esas rutas no
 * validan un permiso propio (las puede abrir cualquier usuario logueado), así que se las cierra acá.
 */
class EnsureNoPersonal
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('personal') && ! $user->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'Tu usuario solo puede registrar su asistencia.');
        }

        return $next($request);
    }
}
