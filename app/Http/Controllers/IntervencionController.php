<?php

namespace App\Http\Controllers;

use App\Models\Intervencion;
use App\Models\BanderaTipo;
use App\Models\Guardavida;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\Fuerza;
use App\Models\User;
use App\Http\Requests\StoreIntervencionRequest;
use App\Http\Requests\UpdateIntervencionRequest;
use Illuminate\Support\Facades\Auth;

class IntervencionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $intervenciones = Intervencion::all();
        return view('ui.intervenciones.index')
        ->with('intervenciones', $intervenciones);
    }

    /**
     * Show the form for creating a new resource.
     * TODO: Acomodar input fecha y hora xq se sale de la ventana en mobile!!!
     */
    public function create()
    {
        $user = Auth::user();
        $guardavidaAuth = $user->guardavida;

        $guardavidas = Guardavida::orderBy('nombre')->get();
        $banderas = BanderaTipo::all();
        $playas = Playa::all();
        $puestos = Puesto::orderBy('nombre')->get();
        $fuerzas = Fuerza::all();

        $intervencion = null;

        return view('ui.intervenciones.create', compact(
            'guardavidas', 'banderas', 'playas', 'puestos', 'fuerzas', 'guardavidaAuth', 'intervencion'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIntervencionRequest $request)
    {
        $user_id = Auth::id();

        //Se crea la intervencion con datos validados + user_id
        $intervencion = Intervencion::create([
            ...$request->validated(),
            'user_id' => $user_id,
        ]);

        //chequea que lleguen fuerzas y guardavidas y los sincroniza
        if ( $request->has('fuerzas')){
            $intervencion->fuerzas()->sync($request->fuerzas);
        }

        if ( $request->has('guardavidas')){
            $guardavidas_ids = $request->input('guardavidas');

            // Sincroniza los guardavidas con la intervención
            $intervencion->guardavidas()->sync($guardavidas_ids);
        }

        return redirect()->route('intervencion.index')
                     ->with('success', 'Intervención creada');

    }

    /**
     * Display the specified resource.
     */
    public function show(Intervencion $intervencion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Intervencion $intervencion)
    {
        $user = Auth::user();
        $guardavidaAuth = $user->guardavida;

        $guardavidas = Guardavida::orderBy('nombre')->get();
        $banderas = BanderaTipo::all();
        $playas = Playa::all();
        $puestos = Puesto::orderBy('nombre')->get();
        $fuerzas = Fuerza::all();


        return view('ui.intervenciones.create', compact(
            'guardavidas', 'banderas', 'playas', 'puestos', 'fuerzas', 'guardavidaAuth', 'intervencion'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIntervencionRequest $request, Intervencion $intervencion)
    {

        //actualizo los datos
        $intervencion->update($request->validated());

        //Sincroniza fuerzas
        if($request->has('fuerzas')){
            $intervencion->fuerzas()->sync($request->fuerzas);
        } else {
            // Si no vienen, se limpian
            $intervencion->fuerzas()->sync([]);
        }

        //Sincroniza guardavidas
        if($request->has('guardavidas')){
            $intervencion->guardavidas()->sync($request->guardavidas);
        } else {
            $intervencion->guardavidas()->sync([]);
        }

        return redirect()->route('intervencion.index')
        ->with('success', 'Intervención actualizada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Intervencion $intervencion)
    {

         $user = Auth::user();

        //solo lo puede eliminar el suaurio que lo creó o el encargado, jefe de playa o admin
        //TODO cuanod haga el control por Policy
        //$this->authorize('delete', $intervencion);
        if ($intervencion->user_id !== $user->id && $user->rol !== 'encargado' && $user->rol !== 'jefe' && $user->rol !== 'admin') {
            return redirect()->route('intervencion.index')
            ->with('error', 'No tienes permiso para eliminar esta intervención.');
        }
        // Limpia las relaciones de tablas relacionales
        $intervencion->fuerzas()->detach();
        $intervencion->guardavidas()->detach();

        $intervencion->delete();

          return redirect()->route('intervencion.index')
        ->with('success', 'Intervención eliminada');
    }
}
