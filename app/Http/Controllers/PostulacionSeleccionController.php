<?php

namespace App\Http\Controllers;

use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Services\SeleccionPostulantes;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Fase 5: selección en lote de postulantes. Se puede repetir varias veces
 * (por ejemplo una tanda por playa): cada confirmación es independiente.
 *
 *  1. index():     lista filtrable y paginada, con casillas (la selección se
 *                  guarda en el navegador y sobrevive al cambio de página/filtro).
 *  2. revisar():   pantalla previa a confirmar: puesto (obligatorio), turno y
 *                  "encargado" por persona. No escribe nada.
 *  3. confirmar(): crea/actualiza el guardavida y le asigna el rol.
 *  4. cierre():    los guardavidas de la temporada anterior que NO fueron
 *                  seleccionados vuelven a postulante.
 */
class PostulacionSeleccionController extends Controller
{
    public function __construct(private SeleccionPostulantes $seleccion) {}

    public function index(Request $request)
    {
        $this->authorize('seleccionar', Postulacion::class);

        [$temporadas, $temporadaId] = $this->temporadaElegida($request);
        $estado = $request->input('estado', Postulacion::ESTADO_ACEPTADA);
        $playaId = $request->input('playa');
        $fueGuardavida = $request->input('ex', 'todos'); // todos | si | no
        $ya = $request->input('ya', 'ocultar');          // ocultar | mostrar | solo
        $buscar = trim((string) $request->input('buscar'));

        $base = Postulacion::where('temporada_id', $temporadaId)
            ->where('estado', '!=', Postulacion::ESTADO_BORRADOR)
            ->when($estado !== 'todas', fn ($q) => $q->where('estado', $estado))
            ->when($playaId, fn ($q) => $q->whereHas('playas', fn ($p) => $p->where('playas.id', $playaId)))
            ->when($fueGuardavida === 'si', fn ($q) => $q->whereHas('user.guardavida'))
            ->when($fueGuardavida === 'no', fn ($q) => $q->whereDoesntHave('user.guardavida'))
            ->when($ya === 'ocultar', fn ($q) => $q->where('seleccionado', false))
            ->when($ya === 'solo', fn ($q) => $q->where('seleccionado', true))
            ->when($buscar !== '', fn ($q) => $q->whereHas('user', function ($u) use ($buscar) {
                $u->where('name', 'like', "%{$buscar}%")
                    ->orWhere('lastname', 'like', "%{$buscar}%")
                    ->orWhere('dni', 'like', "%{$buscar}%");
            }));

        // Todos los que se pueden tildar con estos filtros (en todas las páginas), para "seleccionar todos".
        $idsSeleccionables = (clone $base)
            ->where('estado', Postulacion::ESTADO_ACEPTADA)
            ->where('seleccionado', false)
            ->pluck('id')
            ->all();

        $postulaciones = (clone $base)
            ->join('users', 'users.id', '=', 'postulaciones.user_id')
            ->select('postulaciones.*')
            ->with(['user.guardavida.playa', 'user.guardavida.puesto', 'playas'])
            ->orderBy('users.lastname')
            ->orderBy('users.name')
            ->paginate(50)
            ->withQueryString();

        $porPlaya = Postulacion::where('temporada_id', $temporadaId)
            ->where('seleccionado', true)
            ->selectRaw('playa_asignada_id, count(*) as total')
            ->groupBy('playa_asignada_id')
            ->pluck('total', 'playa_asignada_id');

        $resumen = [
            'aceptadas' => Postulacion::where('temporada_id', $temporadaId)->where('estado', Postulacion::ESTADO_ACEPTADA)->count(),
            'seleccionadas' => (int) $porPlaya->sum(),
            'porPlaya' => $porPlaya,
        ];

        $playas = Playa::orderBy('nombre')->get();

        return view('postulaciones.admin.seleccion', compact(
            'postulaciones', 'temporadas', 'temporadaId', 'estado', 'playaId', 'fueGuardavida', 'ya', 'buscar',
            'playas', 'idsSeleccionables', 'resumen'
        ));
    }

    public function revisar(Request $request)
    {
        $this->authorize('seleccionar', Postulacion::class);

        $temporadaId = (int) $request->input('temporada');
        $ids = collect(explode(',', (string) $request->input('ids')))
            ->map(fn ($id) => (int) $id)->filter()->unique()->values();

        $postulaciones = Postulacion::where('temporada_id', $temporadaId)
            ->whereIn('id', $ids)
            ->where('estado', Postulacion::ESTADO_ACEPTADA)
            ->where('seleccionado', false)
            ->with(['user.guardavida.playa', 'user.guardavida.puesto', 'playas'])
            ->get()
            ->sortBy(fn ($p) => mb_strtolower($p->user->lastname.' '.$p->user->name))
            ->values();

        if ($postulaciones->isEmpty()) {
            return redirect()->route('postulaciones.seleccion', ['temporada' => $temporadaId])
                ->withErrors('No hay postulaciones válidas para seleccionar (tienen que estar aceptadas y sin seleccionar).');
        }

        $descartadas = $ids->count() - $postulaciones->count();
        $playasConPuestos = Playa::with(['puestos' => fn ($q) => $q->orderBy('nombre')])->orderBy('nombre')->get()
            ->filter(fn ($playa) => $playa->puestos->isNotEmpty())->values();
        $temporada = Temporada::findOrFail($temporadaId);

        return view('postulaciones.admin.seleccion-revision', compact('postulaciones', 'playasConPuestos', 'temporada', 'descartadas'));
    }

    public function confirmar(Request $request)
    {
        $this->authorize('seleccionar', Postulacion::class);

        $request->validate([
            'temporada' => 'required|exists:temporadas,id',
            'desde' => 'required|date',
            'filas' => 'required|array|min:1',
            'filas.*.playa_id' => 'required|integer|exists:playas,id',
            'filas.*.puesto_id' => 'nullable|integer|exists:puestos,id',
            'filas.*.turno' => 'nullable|in:M,T',
            'filas.*.encargado' => 'nullable|boolean',
        ], [
            'filas.*.playa_id.required' => 'Falta elegir la playa de una o más personas.',
            'filas.*.puesto_id.exists' => 'Hay un puesto que no existe.',
        ]);

        $temporadaId = (int) $request->input('temporada');
        $postulaciones = Postulacion::where('temporada_id', $temporadaId)
            ->whereIn('id', array_keys($request->input('filas')))
            ->with('user')
            ->get()
            ->keyBy('id');

        $filas = [];
        foreach ($request->input('filas') as $postulacionId => $datos) {
            $postulacion = $postulaciones->get((int) $postulacionId);

            if (! $postulacion || $postulacion->estado !== Postulacion::ESTADO_ACEPTADA || $postulacion->seleccionado) {
                return back()->withInput()->withErrors('Una de las postulaciones ya no se puede seleccionar (cambió de estado o ya estaba seleccionada). Volvé a armar la lista.');
            }

            if (filled($datos['puesto_id'] ?? null) && Puesto::whereKey($datos['puesto_id'])->value('playa_id') !== (int) $datos['playa_id']) {
                return back()->withInput()->withErrors("El puesto elegido para {$postulacion->user->lastname}, {$postulacion->user->name} no pertenece a la playa elegida.");
            }

            $filas[] = [
                'postulacion' => $postulacion,
                'playa_id' => (int) $datos['playa_id'],
                'puesto_id' => filled($datos['puesto_id'] ?? null) ? (int) $datos['puesto_id'] : null,
                'turno' => $datos['turno'] ?? null,
                'encargado' => (bool) ($datos['encargado'] ?? false),
            ];
        }

        try {
            $resultado = $this->seleccion->confirmar($filas, Carbon::parse($request->input('desde')));
        } catch (\DomainException $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()->route('postulaciones.seleccion', ['temporada' => $temporadaId])
            ->with('success', "Selección confirmada: {$resultado['creados']} guardavida(s) nuevo(s) y {$resultado['actualizados']} actualizado(s).")
            ->with('limpiar_seleccion', $temporadaId);
    }

    public function deseleccionar(Postulacion $postulacion)
    {
        $this->authorize('seleccionar', Postulacion::class);
        abort_unless($postulacion->seleccionado, 404);

        $this->seleccion->deseleccionar($postulacion);

        return back()->with('success', "Se sacó de la selección a {$postulacion->user->lastname}, {$postulacion->user->name}: vuelve a ser postulante.");
    }

    public function cierre(Request $request)
    {
        $this->authorize('seleccionar', Postulacion::class);

        [$temporadas, $temporadaId] = $this->temporadaElegida($request);
        $temporada = $temporadas->firstWhere('id', $temporadaId);
        $seleccionadas = Postulacion::where('temporada_id', $temporadaId)->where('seleccionado', true)->count();
        $candidatos = $temporada ? $this->seleccion->candidatosCierre($temporada) : collect();

        return view('postulaciones.admin.seleccion-cierre', compact('temporadas', 'temporadaId', 'temporada', 'seleccionadas', 'candidatos'));
    }

    public function cerrar(Request $request)
    {
        $this->authorize('seleccionar', Postulacion::class);

        $datos = $request->validate([
            'temporada' => 'required|exists:temporadas,id',
            'guardavidas' => 'nullable|array',
            'guardavidas.*' => 'integer',
            'hasta' => 'required|date',
            'entiendo' => 'accepted',
        ], ['entiendo.accepted' => 'Tildá la casilla de confirmación para continuar.']);

        $temporada = Temporada::findOrFail($datos['temporada']);

        // Cortafuegos: sin ninguna selección hecha, el cierre dejaría a TODO el plantel como postulante.
        if (! Postulacion::where('temporada_id', $temporada->id)->where('seleccionado', true)->exists()) {
            return back()->withErrors('Todavía no seleccionaste a nadie en esta temporada: el cierre dejaría a todos como postulantes.');
        }

        $total = $this->seleccion->cerrar($temporada, $datos['guardavidas'] ?? [], Carbon::parse($datos['hasta']));

        return redirect()->route('postulaciones.seleccion', ['temporada' => $temporada->id])
            ->with('success', "{$total} guardavida(s) de la temporada anterior pasaron a postulante.");
    }

    /** @return array{0:\Illuminate\Support\Collection,1:int} */
    private function temporadaElegida(Request $request): array
    {
        $temporadas = Temporada::orderByDesc('fecha_inicio_postulacion')->get();
        $actual = Temporada::conPostulacionAbierta() ?? $temporadas->first();

        return [$temporadas, (int) $request->input('temporada', $actual?->id)];
    }
}
