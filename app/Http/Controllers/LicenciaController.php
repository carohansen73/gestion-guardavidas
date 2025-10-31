<?php

namespace App\Http\Controllers;

use App\Models\Licencia;
use App\Http\Requests\StoreLicenciaRequest;
use App\Http\Requests\UpdateLicenciaRequest;
use App\Models\Guardavida;
use App\Models\Playa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LicenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $licencias = Licencia::with(['guardavida', 'playa', 'puesto'])
        ->latest()
        ->get();

        $playas = Playa::all();

        return view('ui.licencias.index')
        ->with('registros', $licencias)
        ->with('playas', $playas)
        ->with('user', $user);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $guardavidas = Guardavida::with('playa', 'puesto')->get();
        $licencia = null;

        return view('ui.licencias.fields', compact(
            'guardavidas', 'licencia'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLicenciaRequest $request)
    {
        //$user_id = Auth::id();

        $guardavida = Guardavida::findOrFail($request->guardavida_id);

        //AUTOCOMPLETE DEL GUARDAVIDA
        $licencia = new Licencia();
        $licencia->guardavida_id = $request->guardavida_id;
        $licencia->playa_id = $guardavida->playa_id;
        $licencia->puesto_id = $guardavida->puesto_id;
        $licencia->turno = $guardavida->turno;

        // Campos ingresados por el usuario
        $licencia->tipo_licencia = $request->tipo_licencia;
        $licencia->fecha_inicio = $request->fecha_inicio;
        $licencia->fecha_fin = $request->fecha_fin;
        $licencia->en_tiempo = $request->boolean('en_tiempo');
        $licencia->detalle = $request->detalle;

        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('licencias', 'public');
            $licencia->archivo = $path;
        }
        $licencia->save();

        $licencia->save();
        return redirect()->route('licencia.index')->with('success', 'Licencia registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Licencia $licencia)
    {
        return view('ui.licencias.show-fields', compact(
            'licencia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Licencia $licencia)
    {
        $user = Auth::user();
        $guardavidaAuth = $user->guardavida;

        $guardavidas = Guardavida::with('playa', 'puesto')->get();
        $playas = Playa::with('puestos')->get();

        return view('ui.licencias.fields', compact(
            'guardavidaAuth', 'guardavidas', 'licencia', 'playas'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLicenciaRequest $request, Licencia $licencia)
    {
        $licencia->update($request->except('archivo'));

       if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior si existe
            if ($licencia->archivo && Storage::disk('public')->exists($licencia->archivo)) {
                Storage::disk('public')->delete($licencia->archivo);
            }

            $path = $request->file('archivo')->store('licencias', 'public');
            $licencia->archivo = $path;
               $licencia->save();
        }

        return redirect()->route('licencia.index')->with('success', 'Licencia actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Licencia $licencia){

        // Elimina archivo si existe
        if ($licencia->archivo && Storage::disk('public')->exists($licencia->archivo)) {
            Storage::disk('public')->delete($licencia->archivo);
        }

        //Elimina el registro
        $licencia->delete();

        return redirect()->route('licencia.index')->with('success', 'Licencia eliminada correctamente.');
    }


    /**
     * funcion para que de la cantidad de licencias en la seccion licencias del perfil del guardavidas y en el historial de
     * sus licencias
     */

    public function misLicencias()
    {
        $guardavida = auth()->user()->guardavida;
        if (!$guardavida) {
            abort(403, 'No tiene un perfil de guardavida asignado.');
        }

        // Cargar relaciones
        $guardavida->load('licencias.puesto.playa', 'licencias');

        // Pasamos $esAdmin = false para que el Blade detecte que no es vista administrativa
        $esAdmin = false;
        // No necesitamos filtros ni balnearios/puestos para este caso
        return view('admin.asistenciaPorPerfil', compact('guardavida', 'esAdmin'));
    }

}


