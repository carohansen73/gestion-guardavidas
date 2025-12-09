<?php

namespace App\Http\Controllers;

use App\Enums\TipoNovedad;
use App\Models\NovedadMaterial;
use App\Http\Requests\StoreNovedadMaterialRequest;
use App\Http\Requests\UpdateNovedadMaterialRequest;
use App\Models\Material;
use App\Models\Playa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NovedadMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $novedades = NovedadMaterial::with(['material', 'playa'])
        ->latest()
        ->get();

        $playas = Playa::all();

        return view('ui.novedades-materiales.index')
        ->with('registros', $novedades)
        ->with('playas', $playas)
        ->with('user', $user);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $guardavidaAuth = $user->guardavida;

        if ($user->hasAnyRole(['guardavida', 'encargado']) && $guardavidaAuth){
            $playas = Playa::where('id', $guardavidaAuth->playa_id)->get();
        } else {
            $playas = Playa::all();
        }

        $materiales = Material::all();
        //Enums
        $tipoNovedad = TipoNovedad::values();

        return view('ui.novedades-materiales.fields', compact(
            'guardavidaAuth', 'playas', 'materiales', 'tipoNovedad'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNovedadMaterialRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use (&$validated) {
            //Inserta en caso de ser un nuevo material
            if ($validated['material_id'] === 'otro') {
                $material =  Material::create([
                    'nombre' => $validated['nuevo_material_nombre'],
                    'detalle' => $validated['nuevo_material_detalle'] ?? null,
                ]);
                $validated['material_id'] = $material->id;
            }

            $novedad = NovedadMaterial::create([
                'tipo_novedad' => $validated['tipo_novedad'],
                'material_id' => $validated['material_id'],
                'fecha' => $validated['fecha'],
                'playa_id' => $validated['playa_id'],
                'detalles' => $validated['detalles'] ?? null,
            ]);
        });

        return redirect()->route('novedad-de-material.index')
            ->with('success', 'Novedad registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(NovedadMaterial $novedadDeMaterial)
    {
        //si no me toma el binding automatico: pasar el parametro a ($id) y buscar el registro:
        // $novedadMaterial = NovedadMaterial::findOrFail($id);
        // dd($novedadMaterial);
        return view('ui.novedades-materiales.show-fields', compact(
            'novedadDeMaterial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NovedadMaterial $novedadDeMaterial)
    {
        $user = Auth::user();
        $guardavidaAuth = $user->guardavida;

        if ($user->hasAnyRole(['guardavida', 'encargado']) && $guardavidaAuth){
            $playas = Playa::where('id', $guardavidaAuth->playa_id)->get();
        } else {
            $playas = Playa::all();
        }

        $materiales = Material::all();
        $tipoNovedad = TipoNovedad::values();

        return view('ui.novedades-materiales.fields', compact(
            'guardavidaAuth', 'playas', 'materiales', 'tipoNovedad', 'novedadDeMaterial'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNovedadMaterialRequest $request, NovedadMaterial $novedadDeMaterial)
    {
       $validated = $request->validated();

        //Si es un nuevo material lo inserta
        if ($validated['material_id'] === 'otro') {
            $material =  Material::create([
                'nombre' => $validated['nuevo_material_nombre'],
                'detalle' => $validated['nuevo_material_detalle'] ?? null,
            ]);
            $validated['material_id'] = $material->id;
        }

        $novedadDeMaterial->update($validated);

        return redirect()->route('novedad-de-material.index')
            ->with('success', 'Novedad actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NovedadMaterial $novedadDeMaterial)
    {
        $user = Auth::user();

        //solo lo puede eliminar el encargado o admin
        //TODO cuanod haga el control por Policy
        //$this->authorize('delete', $novedadDeMaterial);
        if (!$user->hasAnyRole(['encargado', 'admin']) ) {
            return redirect()->route('novedad-de-material.index')
            ->with('error', 'No tienes permiso para eliminar esta novedad.');
        }

        $novedadDeMaterial->delete();

        return redirect()->route('novedad-de-material.index')
            ->with('success', 'Registro de novedad eliminada');
    }
}

