<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
         $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        // Verificar si el usuario existe
        $user = User::where('email', $credentials['email'])->first();
        if($user != null){
            $guards = empty($guards) ? [null] : $guards;
            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()){
                    if ($user->hasRole('admin') && Hash::check($credentials['password'], $user->password) && ($user->email == $credentials['email'])) {
                        return redirect('emprendedores/nuevoEmprendimiento');
                    }
                    else{
                        return back()->withErrors([
                            'error' => 'Credenciales incorrectas.',
                        ]);
                    }
                }
            }
            return $next($request);
        }
        else{
            return back()->withErrors([
                'error' => 'Credenciales incorrectas.',
            ]);
        }
    }
}
