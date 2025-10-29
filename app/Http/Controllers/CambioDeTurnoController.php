<?php

namespace App\Http\Controllers;

use App\Models\CambioDeTurno;
use App\Http\Requests\StoreCambioDeTurnoRequest;
use App\Http\Requests\UpdateCambioDeTurnoRequest;
use App\Models\Guardavida;
use App\Models\Playa;
use Illuminate\Support\Facades\Auth;

class CambioDeTurnoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $cambiosTurno = CambioDeTurno::with(['guardavida', 'playa', 'puesto'])
        ->latest()
        ->get();

        $playas = Playa::all();

        return view('ui.cambios-de-turno.index')
        ->with('registros', $cambiosTurno)
        ->with('playas', $playas)
        ->with('user', $user);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $guardavidas = Guardavida::with('playa', 'puesto')->get();
        $cambioTurno = null;

        return view('ui.cambios-de-turno.fields', compact(
            'guardavidas', 'cambioTurno'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCambioDeTurnoRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CambioDeTurno $cambioDeTurno)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CambioDeTurno $cambioDeTurno)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCambioDeTurnoRequest $request, CambioDeTurno $cambioDeTurno)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CambioDeTurno $cambioDeTurno)
    {
        //
    }
}
