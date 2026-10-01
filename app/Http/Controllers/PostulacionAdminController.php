<?php

namespace App\Http\Controllers;

use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\PostulacionDocumento;
use App\Models\Temporada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $conteos = (clone $enviadas)->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado');

        $postulaciones = (clone $enviadas)
            ->with(['user', 'playas'])
            ->when($estado !== 'todas', fn ($q) => $q->where('estado', $estado))
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
            'postulaciones', 'temporadas', 'temporadaId', 'estado', 'playaId', 'buscar', 'conteos', 'playas'
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
            'observaciones' => 'nullable|string|max:2000',
        ]);

        // Una vez seleccionada (Fase 5) tiene un Guardavida asociado: no se
        // puede bajar de "aceptada" desde acá sin deshacer antes la selección.
        if ($postulacion->seleccionado && $datos['estado'] !== Postulacion::ESTADO_ACEPTADA) {
            return back()->with('error', 'Esta inscripción ya fue seleccionada: no se puede cambiar su estado.');
        }

        $postulacion->update($datos + [
            'revisado_por_user_id' => Auth::id(),
            'fecha_revision' => now(),
        ]);

        return redirect()->route('postulaciones.show', $postulacion)->with('success', 'Revisión guardada.');
    }


    public function documento(Postulacion $postulacion, string $tipo)
    {
        $this->authorize('view', $postulacion);

        return PostulacionController::servirDocumento($postulacion, $tipo);
    }
}
