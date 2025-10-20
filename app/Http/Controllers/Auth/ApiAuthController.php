<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class ApiAuthController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        // El email no existe
        return response()->json([
            'success' => false,
            'message' => 'El correo electrónico no está registrado.'
        ], 401);
    }

    if (!Hash::check($request->password, $user->password)) {
        // Contraseña incorrecta
        return response()->json([
            'success' => false,
            'message' => 'La contraseña es incorrecta.'
        ], 401);
    }

    // Si llega acá, email y password son válidos
    Auth::login($user);
    $request->session()->regenerate();

    return response()->json([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
        ]
    ]);
}

}

