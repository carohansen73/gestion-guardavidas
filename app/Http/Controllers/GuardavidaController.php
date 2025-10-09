<?php

namespace App\Http\Controllers;

use App\Models\Guardavida;
use App\Http\Requests\StoreGuardavidaRequest;
use App\Http\Requests\UpdateGuardavidaRequest;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;


class GuardavidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $guardavidas = Guardavida::select('guardavidas.*')
                ->join('playas', 'playas.id', '=', 'guardavidas.playa_id')
                ->join('puestos', 'puestos.id', '=', 'guardavidas.puesto_id')
                ->join('users', 'users.id', '=', 'guardavidas.user_id')
                ->orderBy('playas.nombre')
                ->orderBy('puestos.nombre')
                ->orderBy('guardavidas.apellido')
                ->orderBy('guardavidas.nombre')
                ->with(['playa', 'puesto', 'user'])
                ->paginate(20);
        } else {
            //solo de su playa? ->preg! ver el tema claro/Dunamar
            $guardavidas = Guardavida::select('guardavidas.*')
                ->join('playas', 'playas.id', '=', 'guardavidas.playa_id')
                ->join('puestos', 'puestos.id', '=', 'guardavidas.puesto_id')
                ->join('users', 'users.id', '=', 'guardavidas.user_id')
                ->orderBy('playas.nombre')
                ->orderBy('puestos.nombre')
                ->orderBy('guardavidas.apellido')
                ->orderBy('guardavidas.nombre')
                ->with(['playa', 'puesto', 'user'])
                ->paginate(20);
        }
        return view('ui.guardavidas.index')->with('registros', $guardavidas);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $playas = Playa::all();
        $puestos = Puesto::orderBy('nombre')->get();

        $guardavida = null;

        return view('ui.guardavidas.fields', compact(
            'playas', 'puestos', 'guardavida'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuardavidaRequest $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(Guardavida $guardavida)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guardavida $guardavida)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuardavidaRequest $request, Guardavida $guardavida)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guardavida $guardavida)
    {
        //
    }

    public function getAll(){
        $guardavidas = Guardavida::select('id', 'nombre', 'apellido')->get();

        return response()->json($guardavidas);
    }
}
