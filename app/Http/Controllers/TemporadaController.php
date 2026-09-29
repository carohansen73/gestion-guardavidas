<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemporadaRequest;
use App\Http\Requests\UpdateTemporadaRequest;
use App\Models\Temporada;

class TemporadaController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Temporada::class, 'temporada');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $temporadas = Temporada::orderByDesc('fecha_inicio')->get();

        return view('ui.temporadas.index', compact('temporadas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $temporada = null;

        return view('ui.temporadas.fields', compact('temporada'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTemporadaRequest $request)
    {
        Temporada::create($request->validated());

        return redirect()->route('temporada.index')->with('success', 'Temporada creada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Temporada $temporada)
    {
        return view('ui.temporadas.fields', compact('temporada'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTemporadaRequest $request, Temporada $temporada)
    {
        $temporada->update($request->validated());

        return redirect()->route('temporada.index')->with('success', 'Temporada actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Temporada $temporada)
    {
        $temporada->delete();

        return redirect()->route('temporada.index')->with('success', 'Temporada eliminada correctamente.');
    }
}
