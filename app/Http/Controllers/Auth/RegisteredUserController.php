<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guardavida;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Prfil actualizado por el propio usuario
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => ['required','email','max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        // Si hay una nueva contraseña, la encripta
        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            //elimina password
            unset($validated['password']);
        }

        unset($validated['current_password']);

        $user->update($validated);

         //sincroniza nombre en Guardavida
        if($user->guardavida) {
            $user->guardavida->update([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
            ]);
        }

        return redirect()->route('perfil.edit')->with('success', 'Perfil actualizado correctamente.');
    }

    /***
     * Perfil de usuario actualizado por el admin
     */
    public function updateUserByAdmin(Request $request, User $user){

        //solo un admin puede editar a otro admin
        if (Auth::user()->hasRole('admin') === false && $user->hasRole('admin')) {
            abort(403, "No tiene permisos para editar un usuario administrador.");
        }

        //valido los campos
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        //actualiza Usuario
        $user->name = $validated['nombre'];
        $user->lastname = $validated['apellido'];
        $user->email = $validated['email'];

        if(!empty($validated['password'])){
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        //sincroniza nombre en Guardavida
        if($user->guardavida) {
            $user->guardavida->update([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
            ]);
        }

        return redirect()->back()->with('success', 'Usuario actualizado correctamente.');
        // return redirect()->route('guardavida.edit', [$user->guardavida->id, 'tab' => 'profile'])
        // ->with('success', 'Usuario actualizado correctamente.');
    }
}
