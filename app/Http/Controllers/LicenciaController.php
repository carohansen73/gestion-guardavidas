<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLicenciaRequest;
use App\Http\Requests\UpdateLicenciaRequest;
use App\Models\Guardavida;
use App\Models\Licencia;
use App\Models\Playa;
use App\Models\Temporada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LicenciaController extends Controller
{
    /**
     * authorizeResource()  agrega automaticamente los permisos de la policy
     * a cada método del controller.
     *
     * Según el método, laravel verifica si el usuario tiene permisos en
     * app/Policies/LicenciaPolicy.php. si no tiene permisos devuelve 403
     */
    public function __construct()
    {
        $this->authorizeResource(Licencia::class, 'licencia');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        [$temporadas, $temporada] = Temporada::seleccionada($request->query('temporada'));

        $licencias = Licencia::deTemporada($temporada)
            ->with(['guardavida', 'playa', 'puesto'])
            ->latest()
            ->get();

        $playas = Playa::all();

        return view('licencias.index')
            ->with('registros', $licencias)
            ->with('playas', $playas)
            ->with('temporadas', $temporadas)
            ->with('temporada', $temporada)
            ->with('user', $user);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // apellido/nombre viven en users (Fase 3b) — se ordena en PHP. Se
        // usa la forma "user.lastname" (dot notation) en vez de un closure
        // extractor: sortBy([[callback, dir], ...]) espera un comparador
        // de 2 argumentos, no un extractor de 1 valor.
        $guardavidas = Guardavida::activos()->with(['playa', 'puesto', 'user:id,name,lastname'])
            ->get()
            ->sortBy([
                ['user.lastname', 'asc'],
                ['user.name', 'asc'],
            ], SORT_FLAG_CASE | SORT_NATURAL)
            ->values();
        $licencia = null;

        return view('licencias.fields', compact(
            'guardavidas', 'licencia'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLicenciaRequest $request)
    {
        // $user_id = Auth::id();

        $guardavida = Guardavida::findOrFail($request->guardavida_id);

        // AUTOCOMPLETE DEL GUARDAVIDA
        $licencia = new Licencia;
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
            // Disco privado: la constancia es un dato de salud, solo se abre por licencia.constancia.
            $path = $request->file('archivo')->store('licencias', 'local');
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
        return view('licencias.show-fields', compact(
            'licencia'));
    }

    /**
     * Abre la constancia (foto o PDF) de una licencia. Es un dato de salud: solo la ve quien tiene el permiso
     * `ver_licencia` (admin, encargado) o el propio guardavida de la licencia, siempre con la sesión iniciada.
     */
    public function constancia(Licencia $licencia)
    {
        $user = Auth::user();

        abort_unless(
            $user->can('ver_licencia') || ($licencia->guardavida && $licencia->guardavida->user_id === $user->id),
            403
        );

        $disco = $licencia->discoDeLaConstancia();
        abort_unless($disco, 404, 'La constancia no está disponible.');

        $extension = pathinfo($licencia->archivo, PATHINFO_EXTENSION);

        return Storage::disk($disco)->response($licencia->archivo, "constancia-licencia-{$licencia->id}.{$extension}", [
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Licencia $licencia)
    {
        $user = Auth::user();
        $guardavidaAuth = $user->guardavida;

        $guardavidas = Guardavida::activos([$licencia->guardavida_id])->with(['playa', 'puesto', 'user:id,name,lastname'])
            ->get()
            ->sortBy([
                ['user.lastname', 'asc'],
                ['user.name', 'asc'],
            ], SORT_FLAG_CASE | SORT_NATURAL)
            ->values();
        $playas = Playa::with('puestos')->get();

        return view('licencias.fields', compact(
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
            // Eliminar archivo anterior si existe (esté en el disco privado o en el público de antes)
            if ($disco = $licencia->discoDeLaConstancia()) {
                Storage::disk($disco)->delete($licencia->archivo);
            }

            $path = $request->file('archivo')->store('licencias', 'local');
            $licencia->archivo = $path;
            $licencia->save();
        }

        return redirect()->route('licencia.index')->with('success', 'Licencia actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Licencia $licencia)
    {

        // Elimina archivo si existe
        if ($disco = $licencia->discoDeLaConstancia()) {
            Storage::disk($disco)->delete($licencia->archivo);
        }

        // Elimina el registro
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
        if (! $guardavida) {
            abort(403, 'No tiene un perfil de guardavida asignado.');
        }

        // Cargar relaciones
        $guardavida->load('licencias.puesto.playa', 'licencias');

        // Pasamos $esAdmin = false para que el Blade detecte que no es vista administrativa
        $esAdmin = false;

        // No necesitamos filtros ni balnearios/puestos para este caso
        return view('asistencias.por-perfil', compact('guardavida', 'esAdmin'));
    }
}
