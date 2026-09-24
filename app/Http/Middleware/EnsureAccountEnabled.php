<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Corta con 403 cualquier request autenticada (sesión o token Sanctum) de un
 * usuario deshabilitado. El login (web y /loginIdUser) ya rechaza a un
 * usuario deshabilitado al momento de loguearse, pero un token Sanctum ya
 * emitido sigue siendo válido hasta que expira o se revoca — este
 * middleware es lo que evita que un guardavida dado de baja pueda seguir
 * usando ese token (por ejemplo para fichar offline) después de la baja.
 */
class EnsureAccountEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta fue deshabilitada. Contactá al administrador.',
            ], 403);
        }

        return $next($request);
    }
}
