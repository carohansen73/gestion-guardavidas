<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\PostulacionDocumento;
use App\Models\Temporada;
use App\Services\GuardadoPostulacion;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Revisión de inscripciones por el admin. Los borradores (inscripciones que
 * la persona todavía no envió) no aparecen nunca acá. La selección en lote
 * (Fase 5) es una pantalla aparte.
 */
class PostulacionAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Postulacion::class);

        $temporadas = Temporada::orderByDesc('fecha_inicio_postulacion')->get();
        $temporadaActual = Temporada::conPostulacionAbierta() ?? $temporadas->first();
        $temporadaId = (int) $request->input('temporada', $temporadaActual?->id);
        $estado = $request->input('estado', Postulacion::ESTADO_PENDIENTE);
        $playaId = $request->input('playa');
        $buscar = trim((string) $request->input('buscar'));

        $enviadas = Postulacion::where('temporada_id', $temporadaId)
            ->where('estado', '!=', Postulacion::ESTADO_BORRADOR);

        // Contadores por estado para las solapas (de toda la temporada, sin los otros filtros).
        $conteosPorEstado = (clone $enviadas)->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado');

        $postulaciones = (clone $enviadas)
            ->with(['user', 'playas'])
            // "Pendientes" incluye las devueltas para corregir (incompleta): siguen esperando una decisión.
            ->when($estado === Postulacion::ESTADO_PENDIENTE, fn ($q) => $q->whereIn('estado', [Postulacion::ESTADO_PENDIENTE, Postulacion::ESTADO_INCOMPLETA]))
            ->when($estado !== 'todas' && $estado !== Postulacion::ESTADO_PENDIENTE, fn ($q) => $q->where('estado', $estado))
            ->when($playaId, fn ($q) => $q->whereHas('playas', fn ($p) => $p->where('playas.id', $playaId)))
            ->when($buscar !== '', fn ($q) => $q->whereHas('user', function ($u) use ($buscar) {
                $u->where('name', 'like', "%{$buscar}%")
                    ->orWhere('lastname', 'like', "%{$buscar}%")
                    ->orWhere('dni', 'like', "%{$buscar}%");
            }))
            ->orderBy('enviada_at')
            ->paginate(25)
            ->withQueryString();

        $playas = Playa::orderBy('nombre')->get();

        return view('postulaciones.admin.index', compact(
            'postulaciones', 'temporadas', 'temporadaId', 'estado', 'playaId', 'buscar', 'conteosPorEstado', 'playas'
        ));
    }

    public function show(Postulacion $postulacion)
    {
        $this->authorize('view', $postulacion);
        abort_if($postulacion->estado === Postulacion::ESTADO_BORRADOR, 404);

        $postulacion->load(['user', 'temporada', 'perfil', 'playas', 'documentos', 'revisadoPor', 'playaAsignada', 'puestoAsignado']);
        $tipos = PostulacionDocumento::TIPOS;

        return view('postulaciones.admin.show', compact('postulacion', 'tipos'));
    }

    public function revisar(Request $request, Postulacion $postulacion)
    {
        $this->authorize('revisar', $postulacion);
        abort_if($postulacion->estado === Postulacion::ESTADO_BORRADOR, 404);

        $datos = $request->validate([
            'estado' => ['required', Rule::in(Postulacion::ESTADOS_REVISION)],
            // Al pedir correcciones el comentario es obligatorio: es lo que le dice al postulante qué arreglar.
            'observaciones' => 'required_if:estado,'.Postulacion::ESTADO_INCOMPLETA.'|nullable|string|max:2000',
        ], [
            'observaciones.required_if' => 'Escribí en las observaciones qué tiene que corregir o completar el postulante.',
        ]);

        // Una vez seleccionada (Fase 5) tiene un Guardavida asociado: no se
        // puede bajar de "aceptada" desde acá sin deshacer antes la selección.
        if ($postulacion->seleccionado && $datos['estado'] !== Postulacion::ESTADO_ACEPTADA) {
            return back()->with('error', 'Esta inscripción ya fue seleccionada: no se puede cambiar su estado.');
        }

        // Al guardar la revisión se da por vista cualquier modificación posterior al envío.
        $postulacion->update($datos + [
            'revisado_por_user_id' => Auth::id(),
            'fecha_revision' => now(),
            'modificada_at' => null,
        ]);

        return redirect()->route('postulaciones.show', $postulacion)->with('success', 'Revisión guardada.');
    }


    /**
     * El admin completa o corrige los datos de una inscripción en nombre de la persona (típico: alguien
     * dado de alta a mano que no pasó por el formulario). Todo es opcional: nada de lo que el postulante
     * no estaba obligado a cargar se vuelve obligatorio acá, y tampoco hace falta completar lo que sí lo era.
     * No cambia el estado de la inscripción.
     */
    public function edit(Postulacion $postulacion)
    {
        $this->authorize('revisar', $postulacion);
        abort_if($postulacion->estado === Postulacion::ESTADO_BORRADOR, 404);

        // Foto y DNI de otra temporada se copian solos (igual que en el formulario del postulante).
        $postulacion->reutilizarDocumentosAnteriores();
        $postulacion->load(['user', 'temporada', 'perfil', 'playas', 'documentos']);

        return view('postulaciones.admin.edit', [
            'postulacion' => $postulacion,
            'perfil' => $postulacion->perfil ?? new Perfil,
            'generos' => Perfil::GENEROS,
            'gruposSanguineos' => Perfil::GRUPOS_SANGUINEOS,
            'playas' => Playa::orderBy('nombre')->get(),
            'playaIds' => $postulacion->playas->pluck('id', 'pivot.prioridad'),
            'tipos' => PostulacionDocumento::TIPOS,
            'maxKb' => PostulacionDocumento::MAX_KB,
        ]);
    }

    public function update(Request $request, Postulacion $postulacion, GuardadoPostulacion $guardado)
    {
        $this->authorize('revisar', $postulacion);
        abort_if($postulacion->estado === Postulacion::ESTADO_BORRADOR, 404);

        $datos = $request->validate([
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'piso_dpto' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero' => ['nullable', Rule::in(Perfil::GENEROS)],
            'grupo_sanguineo' => ['nullable', Rule::in(Perfil::GRUPOS_SANGUINEOS)],
            'numero_libreta' => 'nullable|string|max:50',
            'talle_remera' => 'nullable|string|max:10',
            'talle_pantalon' => 'nullable|string|max:10',
            'talle_campera' => 'nullable|string|max:10',
            'talle_traje_bano' => 'nullable|string|max:10',
            'tiene_obra_social' => 'nullable|boolean',
            'obra_social_nombre' => 'required_if:tiene_obra_social,1|nullable|string|max:255',
            'obra_social_numero_afiliado' => 'nullable|string|max:255',
            'disponible_desde' => 'nullable|date',
            'disponible_hasta' => 'nullable|date|after_or_equal:disponible_desde',
            'playa_1' => 'nullable|required_with:playa_2|exists:playas,id',
            'playa_2' => 'nullable|exists:playas,id|different:playa_1',
        ] + GuardadoPostulacion::reglasDocumentos());

        DB::transaction(function () use ($request, $postulacion, $datos, $guardado) {
            if (! $request->boolean('tiene_obra_social')) {
                $datos['obra_social_nombre'] = null;
                $datos['obra_social_numero_afiliado'] = null;
            }

            Perfil::updateOrCreate(
                ['user_id' => $postulacion->user_id],
                Arr::only($datos, (new Perfil)->getFillable())
            );

            $postulacion->update([
                'disponible_desde' => $datos['disponible_desde'] ?? null,
                'disponible_hasta' => $datos['disponible_hasta'] ?? null,
            ]);
            $guardado->guardarPlayas($postulacion, $datos['playa_1'] ?? null, $datos['playa_2'] ?? null);
            $guardado->guardarDocumentos($request, $postulacion);
        });

        return redirect()->route('postulaciones.show', $postulacion)->with('success', 'Datos guardados.');
    }

    public function documento(Postulacion $postulacion, string $tipo)
    {
        $this->authorize('view', $postulacion);

        return PostulacionController::servirDocumento($postulacion, $tipo);
    }
}
