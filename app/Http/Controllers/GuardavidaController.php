<?php

namespace App\Http\Controllers;

use App\Models\Guardavida;
use App\Http\Requests\StoreGuardavidaRequest;
use App\Http\Requests\UpdateGuardavidaRequest;

use Illuminate\Http\Request;


class GuardavidaController extends Controller
{
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
    public function store(StoreGuardavidaRequest $request)
    {
        //
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
