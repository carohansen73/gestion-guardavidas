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
use App\Models\Bandera;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



class IntervencionController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Intervencion::class, 'intervencion');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //TODO ver que rol puede ver en tdas las playas o solo algunas
        $user = Auth::user();
        if ($user->hasRole('guardavida')) {
            $intervenciones = Intervencion::where('playa_id', $user->guardavida->playa_id)
            ->with(['guardavidas', 'fuerzas', 'puesto', 'playa'])
            ->latest()
            ->get();
        }  elseif ($user->hasAnyRole(['admin', 'encargado'])) {
            $intervenciones = Intervencion::with(['guardavidas', 'fuerzas', 'puesto', 'playa'])
            ->latest()->get();
        } else {
            abort (403, 'No tienes permisos para ver estas intervenciones');
        }

        $playas = Playa::all();

        return view('ui.intervenciones.index')
        ->with('intervenciones', $intervenciones)
        ->with('playas', $playas)
        ->with('user', $user);
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

        $validated = $request->validated();

        // Buscar la bandera correspondiente
        $bandera = $this->buscarBanderaDelDiaYTurno(
                $validated['playa_id'],
                $validated['fecha']
        );

        //Se crea la intervencion con datos validados + user_id
        $intervencion = Intervencion::create([
            ...$validated,
            'user_id' => $user_id,
            'bandera_id' => $bandera?->id,
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
         return view('ui.intervenciones.show-fields', compact(
           'intervencion'
        ));
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


        return view('ui.intervenciones.edit', compact(
            'guardavidas', 'banderas', 'playas', 'puestos', 'fuerzas', 'guardavidaAuth', 'intervencion'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIntervencionRequest $request, Intervencion $intervencion)
    {

        $validated = $request->validated();

        // Buscar la bandera correspondiente (según playa y fecha)
        $bandera = $this->buscarBanderaDelDiaYTurno(
            $validated['playa_id'],
            $validated['fecha']
        );

        //actualizo los datos
        $intervencion->update([
            ...$validated,
            'bandera_id' => $bandera?->id,
        ]);

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


    public function buscarBanderaDelDiaYTurno($playaId, $fecha){
        if ($fecha) {
            $hora = Carbon::parse($fecha)->format('H');

            // Asignar turno según hora (si cargan fuera de horario queda TT)
            if ($hora >= 5 && $hora < 13) {
                $turno = 'M';
            } else {
                $turno = 'T';
            }
        }


        // 1- Busca bandera en el turno, cargada antes
        $bandera = Bandera::where('playa_id', $playaId)
            ->whereDate('fecha', Carbon::parse($fecha)->toDateString())
            ->whereTime('fecha', '<=', Carbon::parse($fecha)->toTimeString())
            ->where('turno', $turno)
            ->orderBy('fecha', 'desc')
            ->first();

        // 2- busca bandera en el turno, cargada despues
        if (!$bandera) {
            $bandera = Bandera::where('playa_id', $playaId)
                ->whereDate('fecha', Carbon::parse($fecha)->toDateString())
                ->whereTime('fecha', '>', Carbon::parse($fecha)->toTimeString())
                ->where('turno', $turno)
                ->orderBy('fecha', 'asc')
                ->first();
        }

        // 3- Si no hay bandera en ese turno, busca en el otro turno
        if (!$bandera) {
            $otroTurno = $turno === 'M' ? 'T' : 'M';

            $bandera = Bandera::where('playa_id', $playaId)
                ->whereDate('fecha', Carbon::parse($fecha)->toDateString())
                ->whereTime('fecha', '<=', Carbon::parse($fecha)->toTimeString())
                ->where('turno', $otroTurno)
                ->orderBy('fecha', 'desc')
                ->first();

            // Si tampoco hay previa, probamos la siguiente del otro turno
            if (!$bandera) {
                $bandera = Bandera::where('playa_id', $playaId)
                    ->whereDate('fecha',  Carbon::parse($fecha)->toDateString())
                    ->whereTime('fecha', '>', Carbon::parse($fecha)->toTimeString())
                    ->where('turno', $otroTurno)
                    ->orderBy('fecha', 'asc')
                    ->first();
            }
        }

        return $bandera;
    }
}
