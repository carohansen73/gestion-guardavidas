<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemporadaRequest;
use App\Http\Requests\UpdateTemporadaRequest;
use App\Models\Temporada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        return view('temporadas.index', compact('temporadas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $temporada = null;

        return view('temporadas.fields', compact('temporada'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTemporadaRequest $request)
    {
        $temporada = Temporada::create($this->datos($request));
        $this->guardarModelo($request, $temporada);

        return redirect()->route('temporada.index')->with('success', 'Temporada creada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Temporada $temporada)
    {
        return view('temporadas.fields', compact('temporada'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTemporadaRequest $request, Temporada $temporada)
    {
        $temporada->update($this->datos($request));
        $this->guardarModelo($request, $temporada);

        return redirect()->route('temporada.index')->with('success', 'Temporada actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Temporada $temporada)
    {
        // No se puede eliminar una temporada con postulaciones porque
        // la FK de postulaciones.temporada_id es restrict.
        if ($temporada->postulaciones()->exists()) {
            return redirect()->route('temporada.index')
                ->with('error', 'No se puede eliminar una temporada que ya tiene postulaciones.');
        }

        $temporada->delete();
        Storage::disk('local')->deleteDirectory("temporadas/{$temporada->id}");

        return redirect()->route('temporada.index')->with('success', 'Temporada eliminada correctamente.');
    }

    /** Un admin ve/descarga el modelo de declaración jurada de la temporada. */
    public function modeloDeclaracion(Temporada $temporada)
    {
        $this->authorize('view', $temporada);

        return self::servirModelo($temporada);
    }

    /**
     * Sirve el PDF modelo de la temporada. Lo usa también el área del
     * postulante (PostulacionController), por eso es estático.
     */
    public static function servirModelo(Temporada $temporada)
    {
        $ruta = $temporada->declaracion_jurada_modelo;
        abort_unless($ruta && Storage::disk('local')->exists($ruta), 404);

        // no-cache: al reemplazar el modelo, nadie tiene que seguir viendo el anterior.
        return Storage::disk('local')->response(
            $ruta,
            'declaracion-jurada-'.Str::slug($temporada->nombre).'.pdf',
            ['Content-Type' => 'application/pdf', 'Cache-Control' => 'no-cache, private']
        );
    }

    /** Campos de la tabla: el archivo y el tilde de "quitar" se guardan aparte. */
    private function datos(Request $request): array
    {
        return $request->safe()->except(['modelo_declaracion', 'quitar_modelo']);
    }

    /**
     * Sube, reemplaza o quita el modelo de declaración jurada. Va al disco
     * privado `local` (storage/app/private/temporadas/{id}/): no depende de
     * carpetas públicas ni de acceso al servidor.
     */
    private function guardarModelo(Request $request, Temporada $temporada): void
    {
        if ($archivo = $request->file('modelo_declaracion')) {
            if ($temporada->declaracion_jurada_modelo) {
                Storage::disk('local')->delete($temporada->declaracion_jurada_modelo);
            }

            $ruta = $archivo->storeAs("temporadas/{$temporada->id}", 'declaracion_jurada.pdf', 'local');
            $temporada->update(['declaracion_jurada_modelo' => $ruta]);
        } elseif ($request->boolean('quitar_modelo') && $temporada->declaracion_jurada_modelo) {
            Storage::disk('local')->delete($temporada->declaracion_jurada_modelo);
            $temporada->update(['declaracion_jurada_modelo' => null]);
        }
    }
}
