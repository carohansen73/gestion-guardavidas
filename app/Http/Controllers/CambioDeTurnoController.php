<?php

namespace App\Http\Controllers;

use App\Models\CambioDeTurno;
use App\Http\Requests\StoreCambioDeTurnoRequest;
use App\Http\Requests\UpdateCambioDeTurnoRequest;
use App\Models\Guardavida;
use App\Models\Playa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
        ->paginate(20);

        $playas = Playa::all();

        return view('ui.cambios-de-turno.index')
        ->with('registros', $cambiosDeTurno)
        ->with('playas', $playas)
        ->with('user', $user);
    }

    /**
     * Panel admin independiente (/admin/turnos)

      Filtros funcionales

      Paginación

      En el blade estilos básicos que se adaptan a las pantallas
     */
    public function indexAdmin(Request $request)
    {
        $query = CambioDeTurno::with(['guardavida', 'playa', 'puesto'])
            ->orderBy('fecha', 'desc');

        if ($request->filled('playa_id')) {
            $query->where('playa_id', $request->playa_id);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        $registros = $query->paginate(10)->withQueryString();
        $playas = Playa::all();

        return view('admin.usuarios.listadoTurnos', compact('registros', 'playas'));
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
        //
        $guardavida->update(['turno' => $cambioDeTurno->turno_nuevo]);

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
