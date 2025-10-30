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

        $cambiosDeTurno = CambioDeTurno::with(['guardavida', 'playa', 'puesto'])
        ->latest()
        ->get();

        $playas = Playa::all();

        return view('ui.cambios-de-turno.index')
        ->with('registros', $cambiosDeTurno)
        ->with('playas', $playas)
        ->with('user', $user);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $guardavidas = Guardavida::with('playa', 'puesto')->get();
        $cambioDeTurno = null;

        return view('ui.cambios-de-turno.fields', compact(
            'guardavidas', 'cambioDeTurno'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCambioDeTurnoRequest $request)
    {
          $guardavida = Guardavida::findOrFail($request->guardavida_id);

        //AUTOCOMPLETE DEL GUARDAVIDA
        $cambioDeTurno = new CambioDeTurno();
        $cambioDeTurno->guardavida_id = $request->guardavida_id;
        if ($guardavida->turno === 'M') {
            $cambioDeTurno->turno_nuevo = 'T';
        } else{
            $cambioDeTurno->turno_nuevo = 'M';
        }
        $cambioDeTurno->playa_id = $guardavida->playa_id;
        $cambioDeTurno->puesto_id = $guardavida->puesto_id;
        $cambioDeTurno->funcion = $guardavida->funcion;

        // Campos ingresados por el usuario
        $cambioDeTurno->fecha = $request->fecha;
        $cambioDeTurno->detalles = $request->detalles;

        $cambioDeTurno->save();
        return redirect()->route('cambio-de-turno.index')->with('success', 'Cambio de turno registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CambioDeTurno $cambioDeTurno)
    {
        return view('ui.cambios-de-turno.show-fields', compact(
            'cambioDeTurno'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CambioDeTurno $cambioDeTurno)
    {
        $user = Auth::user();
        $guardavidaAuth = $user->guardavida;

        $guardavidas = Guardavida::with('playa', 'puesto')->get();
        $playas = Playa::with('puestos')->get();

        return view('ui.cambios-de-turno.fields', compact(
            'guardavidaAuth', 'guardavidas', 'cambioDeTurno', 'playas'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCambioDeTurnoRequest $request, CambioDeTurno $cambioDeTurno)
    {
        $cambioDeTurno->update($request->validated());

        return redirect()->route('cambio-de-turno.index')->with('success', 'Cambio de turno actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CambioDeTurno $cambioDeTurno)
    {
        $cambioDeTurno->delete();

        return redirect()->route('cambio-de-turno.index')->with('success', 'Cambio de turno eliminado correctamente.');
    }
}
