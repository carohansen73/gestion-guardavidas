<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Mismo esquema de rate limiting que el login web normal
        // (LoginRequest::ensureIsNotRateLimited), clave por email+IP para no
        // bloquear a todo el mundo si alguien intenta fuerza bruta contra
        // una sola cuenta desde varias IPs.
        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'success' => false,
                'message' => "Demasiados intentos. Probá de nuevo en {$seconds} segundos.",
            ], 429);
        }

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user) {
            RateLimiter::hit($throttleKey);

            // El email no existe
            return response()->json([
                'success' => false,
                'message' => 'El correo electrónico no está registrado.',
            ], 401);
        }

        if (! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey);

            // Contraseña incorrecta
            return response()->json([
                'success' => false,
                'message' => 'La contraseña es incorrecta.',
            ], 401);
        }

        // Mismo chequeo que LoginRequest::authenticate(): un usuario
        // deshabilitado no puede loguearse ni obtener un token nuevo, así
        // no puede seguir fichando offline después de darlo de baja.
        if (! $user->enabled) {
            RateLimiter::hit($throttleKey);

            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta fue deshabilitada. Contactá al administrador.',
            ], 403);
        }

        RateLimiter::clear($throttleKey);

        // Si llega acá, email y password son válidos
        Auth::login($user);
        $request->session()->regenerate();

        // Vence a los 120 días (~una temporada completa): hay playas sin
        // internet, así que el token tiene que aguantar toda la temporada
        // sin que el guardavida necesite volver a loguearse con wifi para
        // poder sincronizar lo fichado. Es el único createToken() de la
        // app, así que esto no afecta a ningún otro uso de Sanctum.
        $token = $user->createToken('sw-token', ['*'], now()->addDays(120))->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'token' => $token,
        ]);
    }
}
