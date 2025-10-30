<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

 /**
     * Modifica el estado habilitado/deshabilitado
     *
     * @param User $guardavida
     * @return void
     */
    public function toggle(User $user){

        $user->enabled  = !$user->enabled ;
        $user->save();

        return back()->with('success', 'El estado del usuario fue actualizado correctamente.');
    }

    public function verPuestoUsuario(Request $request){
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'puesto_id' => 'required|integer|exists:puestos,id',
        ]);
        $idUser = $validated['user_id'];
        $idPuesto = $validated['puesto_id'];

        $guardavida = User::obtenerPuesto($idUser, $idPuesto);
        if (is_null($guardavida)) {
            return response()->json([
                'success' => false,
                'data' => 'Escaneo en puesto incorrecto'
            ]);
        }
        else{
             return response()->json([
                'success' => true,
                'data' => 'Escaneo en puesto correcto'
            ]);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
