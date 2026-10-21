<?php

namespace App\Http\Middleware;

use App\Enums\RolUsuario;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Un postulante no tiene ningún permiso asignado (ver RolesYPermisosSeeder),
 * así que ya queda bloqueado de la mayoría de las pantallas por el propio
 * sistema de @can/Policy — pero hay rutas sin ningún gate de permiso (ej.
 * /home, /activeCamera) que igual lo dejarían pasar. Este middleware corta
 * eso de raíz: cualquier request de un postulante que no sea a su propia
 * área de postulación se redirige ahí, sin importar qué ruta haya pedido.
 * La única excepción es `home`: para el postulante es su inicio (fechas de inscripción), no el panel operativo
 * (ver HomeController::inicioPostulante).
 */
class RedirectPostulante
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->hasRole(RolUsuario::Postulante->value) && ! $request->routeIs('postulacion.*', 'home')) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
