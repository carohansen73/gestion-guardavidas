<?php

namespace App\Http\Controllers;

use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\PostulacionDocumento;
use App\Models\PostulacionPerfil;
use App\Models\Temporada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Área del postulante: formulario de inscripción por pasos.
 *
 * El progreso se guarda paso a paso (la inscripción queda en `borrador`, con
 * columnas nullable) para que la persona pueda empezar hoy y subir los
 * archivos que le falten otro día. Recién con todo lo obligatorio completo
 * puede "enviar" (pasa a `pendiente`, y ahí la ve el admin).
 *
 * No usa Policy/permisos: un postulante no tiene ninguno, solo accede a SUS
 * inscripciones (ver RedirectPostulante).
 */
class PostulacionController extends Controller
{
    private const PASOS = [1, 2, 3, 4];

    public function index()
    {
        $this->autorizarPostulante();

        $user = Auth::user();
        $temporada = Temporada::conPostulacionAbierta();
        $postulacion = $temporada
            ? Postulacion::with(['temporada', 'playaAsignada', 'puestoAsignado'])
                ->where('user_id', $user->id)
                ->where('temporada_id', $temporada->id)
                ->first()
            : null;

        $anteriores = Postulacion::with(['temporada', 'playaAsignada', 'puestoAsignado'])
            ->where('user_id', $user->id)
            ->when($postulacion, fn ($q) => $q->where('id', '!=', $postulacion->id))
            ->where('estado', '!=', Postulacion::ESTADO_BORRADOR)
            ->latest('id')
            ->get();

        return view('postulaciones.postulante.index', compact('temporada', 'postulacion', 'anteriores'));
    }

    public function paso(int $paso)
    {
        abort_unless(in_array($paso, self::PASOS, true), 404);
        $this->autorizarPostulante();

        if ($redir = $this->validarAcceso($paso, $postulacion, $temporada)) {
            return $redir;
        }

        $user = Auth::user();
        $data = ['paso' => $paso, 'postulacion' => $postulacion, 'temporada' => $temporada];

        if ($paso === 1) {
            $data['perfil'] = $this->perfilPrecargado($user);
            $data['generos'] = PostulacionPerfil::GENEROS;
            $data['gruposSanguineos'] = PostulacionPerfil::GRUPOS_SANGUINEOS;
        } elseif ($paso === 2) {
            $data['playas'] = Playa::orderBy('nombre')->get();
            $data['playaIds'] = $postulacion->playas->pluck('id', 'pivot.prioridad');
        } elseif ($paso === 3) {
            $data['tipos'] = PostulacionDocumento::TIPOS;
            $data['maxKb'] = PostulacionDocumento::MAX_KB;
            // Modelo de Declaración Jurada de la temporada (lo sube un admin desde Temporadas). Si la
            // temporada todavía no tiene, no hay link ni se pide la declaración
            // firmada (ver Postulacion::documentoRequerido).
            $data['modeloDeclaracion'] = $postulacion->temporada->declaracion_jurada_modelo
                ? route('postulacion.modelo-declaracion', $postulacion->temporada)
                : null;
        } else {
            $data['faltantes'] = $postulacion->faltantes();
            $data['perfil'] = $postulacion->perfil;
            $data['tipos'] = PostulacionDocumento::TIPOS;
        }

        return view("postulaciones.postulante.paso{$paso}", $data);
    }

    public function guardar(Request $request, int $paso)
    {
        abort_unless(in_array($paso, [1, 2, 3], true), 404);
        $this->autorizarPostulante();

        if ($redir = $this->validarAcceso($paso, $postulacion, $temporada)) {
            return $redir;
        }

        match ($paso) {
            1 => $this->guardarDatosPersonales($request, $temporada),
            2 => $this->guardarInscripcion($request, $postulacion),
            3 => $this->guardarDocumentos($request, $postulacion),
        };

        return redirect()->route('postulacion.paso', $paso + 1)->with('success', 'Guardado. Podés seguir ahora o continuar más tarde.');
    }

    public function enviar()
    {
        $this->autorizarPostulante();

        if ($redir = $this->validarAcceso(4, $postulacion, $temporada)) {
            return $redir;
        }

        $faltantes = $postulacion->faltantes();
        if ($faltantes) {
            return redirect()->route('postulacion.paso', 4)
                ->with('error', 'Todavía faltan datos obligatorios: '.implode(', ', $faltantes).'.');
        }

        // Si ya estaba enviada (pendiente) solo confirma los cambios; el
        // estado y la fecha de envío se tocan recién al salir de borrador o
        // incompleta.
        if (in_array($postulacion->estado, [Postulacion::ESTADO_BORRADOR, Postulacion::ESTADO_INCOMPLETA], true)) {
            $postulacion->update([
                'estado' => Postulacion::ESTADO_PENDIENTE,
                'enviada_at' => now(),
            ]);
        }

        return redirect()->route('postulacion.index')->with('success', '¡Inscripción enviada! Te avisamos por acá cuando sea revisada.');
    }

    /** El postulante descarga el modelo de declaración jurada de la temporada. */
    public function modeloDeclaracion(Temporada $temporada)
    {
        $this->autorizarPostulante();

        return TemporadaController::servirModelo($temporada);
    }

    /** El postulante descarga/ve un documento propio. */
    public function documento(Postulacion $postulacion, string $tipo)
    {
        abort_unless($postulacion->user_id === Auth::id(), 403);

        return $this->servirDocumento($postulacion, $tipo);
    }

    // ---------------------------------------------------------------------------------------

    private function guardarDatosPersonales(Request $request, Temporada $temporada): void
    {
        $datos = $request->validate([
            'telefono' => 'required|string|max:30',
            'direccion' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'piso_dpto' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'required|date|before:today',
            'genero' => ['required', Rule::in(PostulacionPerfil::GENEROS)],
            'grupo_sanguineo' => ['required', Rule::in(PostulacionPerfil::GRUPOS_SANGUINEOS)],
            'numero_libreta' => 'required|string|max:50',
            'talle_remera' => 'required|string|max:10',
            'talle_pantalon' => 'required|string|max:10',
            'talle_campera' => 'required|string|max:10',
            'talle_traje_bano' => 'required|string|max:10',
            'tiene_obra_social' => 'nullable|boolean',
            'obra_social_nombre' => 'required_if:tiene_obra_social,1|nullable|string|max:255',
            'obra_social_numero_afiliado' => 'required_if:tiene_obra_social,1|nullable|string|max:255',
        ]);

        // "Tiene obra social" no se guarda: sin tildar, quedan sin nombre.
        if (! $request->boolean('tiene_obra_social')) {
            $datos['obra_social_nombre'] = null;
            $datos['obra_social_numero_afiliado'] = null;
        }
        unset($datos['tiene_obra_social']);

        DB::transaction(function () use ($datos, $temporada) {
            $userId = Auth::id();
            PostulacionPerfil::updateOrCreate(['user_id' => $userId], $datos);
            Postulacion::firstOrCreate(
                ['user_id' => $userId, 'temporada_id' => $temporada->id],
                ['estado' => Postulacion::ESTADO_BORRADOR]
            );
        });
    }

    private function guardarInscripcion(Request $request, Postulacion $postulacion): void
    {
        $datos = $request->validate([
            'disponible_desde' => 'required|date',
            'disponible_hasta' => 'required|date|after_or_equal:disponible_desde',
            'playa_1' => 'nullable|required_with:playa_2|exists:playas,id',
            'playa_2' => 'nullable|exists:playas,id|different:playa_1',
        ]);

        DB::transaction(function () use ($postulacion, $datos) {
            $postulacion->update([
                'disponible_desde' => $datos['disponible_desde'],
                'disponible_hasta' => $datos['disponible_hasta'],
            ]);

            // detach + attach (no sync): con la restricción única
            // (postulacion, prioridad), intercambiar el orden de dos playas
            // con sync chocaría a mitad de camino.
            $postulacion->playas()->detach();
            foreach ([1 => $datos['playa_1'] ?? null, 2 => $datos['playa_2'] ?? null] as $prioridad => $playaId) {
                if ($playaId) {
                    $postulacion->playas()->attach($playaId, ['prioridad' => $prioridad]);
                }
            }
        });
    }

    private function guardarDocumentos(Request $request, Postulacion $postulacion): void
    {
        $reglas = [];
        foreach (PostulacionDocumento::TIPOS as $tipo => $config) {
            $reglas["doc_{$tipo}"] = ['nullable', 'file', 'mimes:'.implode(',', $config['mimes']), 'max:'.PostulacionDocumento::MAX_KB];
        }
        $request->validate($reglas);

        foreach (PostulacionDocumento::TIPOS as $tipo => $config) {
            $archivo = $request->file("doc_{$tipo}");
            if (! $archivo) {
                continue; // subió solo algunos: el resto queda como estaba
            }

            $existente = $postulacion->documentos()->where('tipo', $tipo)->first();
            if ($existente) {
                Storage::disk('local')->delete($existente->ruta);
            }

            $ruta = $archivo->storeAs(
                "postulaciones/{$postulacion->temporada_id}/{$postulacion->id}",
                $tipo.'.'.strtolower($archivo->getClientOriginalExtension()),
                'local'
            );

            $postulacion->documentos()->updateOrCreate(['tipo' => $tipo], [
                'ruta' => $ruta,
                'nombre_original' => $archivo->getClientOriginalName(),
                'mime' => $archivo->getMimeType() ?? 'application/octet-stream',
                'tamano' => $archivo->getSize(),
            ]);
        }
    }

    /**
     * Valida que haya ventana abierta y, desde el paso 2, una inscripción
     * propia editable. Carga $postulacion y $temporada por referencia;
     * devuelve un redirect si no se puede seguir.
     */
    private function validarAcceso(int $paso, ?Postulacion &$postulacion, ?Temporada &$temporada)
    {
        $temporada = Temporada::conPostulacionAbierta();
        if (! $temporada) {
            return redirect()->route('postulacion.index')
                ->with('error', 'Por ahora no hay una inscripción abierta.');
        }

        $postulacion = Postulacion::with(['temporada', 'perfil', 'playas', 'documentos'])
            ->where('user_id', Auth::id())
            ->where('temporada_id', $temporada->id)
            ->first();

        if (! $postulacion) {
            // Sin inscripción todavía: solo se puede arrancar por el paso 1.
            return $paso === 1
                ? null
                : redirect()->route('postulacion.paso', 1)->with('error', 'Primero completá tus datos personales.');
        }

        if (! $postulacion->editablePorPostulante()) {
            return redirect()->route('postulacion.index')
                ->with('error', 'Tu inscripción ya fue revisada y no se puede modificar.');
        }

        return null;
    }

    /**
     * Datos del paso 1. Si la persona todavía no tiene perfil pero ya es
     * guardavida, se precargan teléfono y domicilio desde su ficha.
     */
    private function perfilPrecargado($user): PostulacionPerfil
    {
        $perfil = PostulacionPerfil::firstWhere('user_id', $user->id);
        if ($perfil) {
            return $perfil;
        }

        $perfil = new PostulacionPerfil;
        if ($guardavida = $user->guardavida) {
            $perfil->fill($guardavida->only(['telefono', 'direccion', 'numero', 'piso_dpto']));
        }

        return $perfil;
    }

    /** El postulante, o un guardavida/encargado que vuelve a inscribirse el año siguiente. */
    private function autorizarPostulante(): void
    {
        abort_unless(Auth::user()->hasAnyRole(['postulante', 'guardavida', 'encargado']), 403);
    }

    /** Usado también por el controlador de admin. */
    public static function servirDocumento(Postulacion $postulacion, string $tipo)
    {
        abort_unless(array_key_exists($tipo, PostulacionDocumento::TIPOS), 404);

        $documento = $postulacion->documentos()->where('tipo', $tipo)->firstOrFail();
        abort_unless(Storage::disk('local')->exists($documento->ruta), 404);

        return Storage::disk('local')->response($documento->ruta, $documento->nombre_original);
    }
}
