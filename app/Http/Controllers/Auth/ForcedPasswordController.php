<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;

class ForcedPasswordController extends Controller
{
    public function edit(){
        return view('auth.force-password');
    }

    public function update(Request $request){
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = FacadesAuth::user();
        $user->password = Hash::make($request->password);

        $user->must_change_psw = false;
        $user->save();

        return redirect()->route('home')
            ->with('status', 'Contraseña actualizada correctamente.');
    }
}
