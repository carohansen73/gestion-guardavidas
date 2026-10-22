<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIntervencionRequest;
use App\Http\Requests\UpdateIntervencionRequest;
use App\Models\Bandera;
use App\Models\BanderaTipo;
use App\Models\Fuerza;
use App\Models\Guardavida;
use App\Models\Intervencion;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
    public function index(Request $request)
    {
        $user = Auth::user();
        [$temporadas, $temporada] = Temporada::seleccionada($request->query('temporada'));

        // El acceso ya está autorizado (ver_intervencion) por authorizeResource(): quien lo tiene ve las de todas las playas.
        $intervenciones = Intervencion::deTemporada($temporada)
            ->with(['guardavidas', 'fuerzas', 'puesto', 'playa'])
            ->latest()
            ->get();

        $playas = Playa::all();

        return view('intervenciones.index')
            ->with('intervenciones', $intervenciones)
            ->with('playas', $playas)
            ->with('temporadas', $temporadas)
            ->with('temporada', $temporada)
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
        $banderas = BanderaTipo::all();
        $fuerzas = Fuerza::all();
        $intervencion = null;

        $playas = Playa::all();
        $puestos = Puesto::orderBy('nombre')->get();
        // Lista completa (sin filtrar por playa a nivel servidor): el
        // admin ve todas las playas, y el select de guardavidas se
        // acota por playa en el cliente (ver fields.blade.php) a medida
        // que cambia el select de playa, sin recargar la página.
        $guardavidas = Guardavida::activos()->with('user:id,name,lastname')
            ->get()
            ->sortBy([
                ['user.lastname', 'asc'],
                ['user.name', 'asc'],
            ], SORT_FLAG_CASE | SORT_NATURAL)
            ->values();

        return view('intervenciones.create', compact(
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

        // Se crea la intervencion con datos validados + user_id
        $intervencion = Intervencion::create([
            ...$validated,
            'user_id' => $user_id,
            'bandera_id' => $bandera?->id,
        ]);

        // chequea que lleguen fuerzas y guardavidas y los sincroniza
        if ($request->has('fuerzas')) {
            $intervencion->fuerzas()->sync($request->fuerzas);
        }

        if ($request->has('guardavidas')) {
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
        $intervencion->load(['playa', 'puesto', 'user', 'bandera.bandera', 'guardavidas.user', 'fuerzas']);

        // Fotos de los guardavidas que intervinieron, en una sola consulta (las lee <x-avatar-guardavida>).
        $fotos = Guardavida::fotosPersonalesDe($intervencion->guardavidas->pluck('user_id'));
        foreach ($intervencion->guardavidas as $guardavida) {
            $guardavida->foto_personal_id = $fotos[$guardavida->user_id] ?? null;
        }

        // Desde el listado se pide por AJAX y va en un modal: solo el contenido. Si no, la página completa.
        return view(request()->ajax() ? 'intervenciones.partials.detalle' : 'intervenciones.show-fields', compact(
            'intervencion'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Intervencion $intervencion)
    {
        $user = Auth::user();
        // Los que ya figuran en esta intervención se mantienen aunque ya no estén en el plantel.
        $idsActuales = $intervencion->guardavidas()->pluck('guardavidas.id')->all();
        $guardavidaAuth = $user->guardavida;
        $banderas = BanderaTipo::all();
        $fuerzas = Fuerza::all();

        $playas = Playa::all();
        $puestos = Puesto::orderBy('nombre')->get();
        // Lista completa (sin filtrar por playa a nivel servidor): ver
        // comentario equivalente en create() — se acota por playa en
        // el cliente.
        $guardavidas = Guardavida::activos($idsActuales)->with('user:id,name,lastname')
            ->get()
            ->sortBy([
                ['user.lastname', 'asc'],
                ['user.name', 'asc'],
            ], SORT_FLAG_CASE | SORT_NATURAL)
            ->values();

        return view('intervenciones.edit', compact(
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

        // actualizo los datos
        $intervencion->update([
            ...$validated,
            'bandera_id' => $bandera?->id,
        ]);

        // Sincroniza fuerzas
        if ($request->has('fuerzas')) {
            $intervencion->fuerzas()->sync($request->fuerzas);
        } else {
            // Si no vienen, se limpian
            $intervencion->fuerzas()->sync([]);
        }

        // Sincroniza guardavidas
        if ($request->has('guardavidas')) {
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
        // El permiso ya lo valida authorizeResource() del constructor contra
        // IntervencionPolicy::delete() (permiso eliminar_intervencion). Antes
        // había acá un chequeo manual con $user->rol, un atributo que no
        // existe en el modelo User (usa Spatie) — esa condición nunca era
        // verdadera, así que en la práctica solo el autor original podía
        // borrar su propia intervención, ni admin ni encargado podían.

        // Limpia las relaciones de tablas relacionales
        $intervencion->fuerzas()->detach();
        $intervencion->guardavidas()->detach();

        $intervencion->delete();

        return redirect()->route('intervencion.index')
            ->with('success', 'Intervención eliminada');
    }

    public function buscarBanderaDelDiaYTurno($playaId, $fecha)
    {
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
        if (! $bandera) {
            $bandera = Bandera::where('playa_id', $playaId)
                ->whereDate('fecha', Carbon::parse($fecha)->toDateString())
                ->whereTime('fecha', '>', Carbon::parse($fecha)->toTimeString())
                ->where('turno', $turno)
                ->orderBy('fecha', 'asc')
                ->first();
        }

        // 3- Si no hay bandera en ese turno, busca en el otro turno
        if (! $bandera) {
            $otroTurno = $turno === 'M' ? 'T' : 'M';

            $bandera = Bandera::where('playa_id', $playaId)
                ->whereDate('fecha', Carbon::parse($fecha)->toDateString())
                ->whereTime('fecha', '<=', Carbon::parse($fecha)->toTimeString())
                ->where('turno', $otroTurno)
                ->orderBy('fecha', 'desc')
                ->first();

            // Si tampoco hay previa, probamos la siguiente del otro turno
            if (! $bandera) {
                $bandera = Bandera::where('playa_id', $playaId)
                    ->whereDate('fecha', Carbon::parse($fecha)->toDateString())
                    ->whereTime('fecha', '>', Carbon::parse($fecha)->toTimeString())
                    ->where('turno', $otroTurno)
                    ->orderBy('fecha', 'asc')
                    ->first();
            }
        }

        return $bandera;
    }
}
