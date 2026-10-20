<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Guardavida;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Services\HistorialAsistenciaService;
use App\Services\ResumenAsistenciaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use Laravel\Sanctum\PersonalAccessToken;

class AsistenciaController extends Controller
{
    public function cargarAsistencia(Request $request)
    {
        $user = Auth::check() ? Auth::user() : null;

        if (! $user && $request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());
            if ($accessToken) {
                $user = $accessToken->tokenable; // Usuario asociado al token
            }
        }

        if (! $user) {
            return response()->json([
                'success' => false,
                'data' => 'Debe loguearse para guardar la asistencia',
            ], 401);
        }

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'playa_id' => 'required|integer|exists:playas,id',
            'puesto_id' => 'required|integer|exists:puestos,id',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between: -180,180',
            'precision' => 'required|numeric|min:0',
            'fecha_hora' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $idUser = $validated['user_id'];

        $guardavidas = Guardavida::obtenerGuardavidas($idUser);
        if (is_null($guardavidas)) {
            return response()->json([
                'success' => false,
                'data' => 'No se pudo registrar la asistencia',
            ], 400);
        }
        $fecha_hora = $validated['fecha_hora'];
        $lat = $validated['lat'];
        $lng = $validated['lng'];
        $precision = $validated['precision'];
        $idPuesto = $validated['puesto_id'];
        $guardavidas_id = $guardavidas->id;

        $puesto = Puesto::findOrFail($idPuesto);
        $estadoValidacion = $this->validarDistanciaAlPuesto($puesto, $lat, $lng, $precision);

        $asistencia = Asistencia::nuevaAsistencia($lng, $lat, $precision, $idPuesto, $guardavidas_id, $fecha_hora, $estadoValidacion);

        return response()->json([
            'success' => true,
            'data' => $asistencia,
        ], 200);
    }

    /**
     * Determina si un fichaje quedó dentro del radio esperado del puesto
     * (200m), sin bloquear el registro: solo lo marca para revisión.
     *
     * Le da el beneficio de la duda al margen de error propio del GPS
     * (`$precisionMetros`, lo que el dispositivo reporta como incertidumbre
     * de su propia ubicación) — un GPS con poca señal o el toggle de
     * "Ubicación exacta" desactivado en iOS puede devolver una posición con
     * cientos/miles de metros de margen de error; en esos casos no tiene
     * sentido usar la distancia calculada como si fuera exacta.
     *
     * Los puestos "móviles" (fuera de zona de baño) no tienen radio fijo,
     * así que siempre quedan como válidos.
     */
    private function validarDistanciaAlPuesto(Puesto $puesto, float $lat, float $lng, float $precisionMetros): string
    {
        $esMovil = Puesto::getMovil()->contains('id', $puesto->id);
        if ($esMovil) {
            return 'valido';
        }

        $distancia = Asistencia::distanciaMetros($lat, $lng, (float) $puesto->latitud, (float) $puesto->longitud);

        $distanciaConTolerancia = $distancia - $precisionMetros;

        return $distanciaConTolerancia > 200 ? 'fuera_de_rango' : 'valido';
    }

    /**
     * ?mes=YYYY-MM (flechas de mes): el mes pedido, o null si falta, está mal escrito o es futuro.
     */
    private function mesPedido(Request $request): ?Carbon
    {
        if (! $request->filled('mes') || ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $request->input('mes'))) {
            return null;
        }

        $mes = Carbon::createFromFormat('!Y-m', $request->input('mes'));

        return $mes->gt(Carbon::now()->startOfMonth()) ? null : $mes;
    }

    /**
     * Datos de las flechas de mes ("Oct 2026" y los meses vecinos), solo cuando el período mostrado es un
     * mes entero (o el actual hasta hoy); con fechas a mano o una temporada devuelve null.
     *
     * @return array{nombre:string,anterior:string,siguiente:?string}|null
     */
    private function navegacionMes(Carbon $inicio, Carbon $fin): ?array
    {
        if (! $inicio->isSameDay($inicio->copy()->startOfMonth()) || ! $fin->isSameMonth($inicio)
            || ! ($fin->isSameDay($inicio->copy()->endOfMonth()) || $fin->isSameDay(Carbon::now()))) {
            return null;
        }

        return [
            // Mes abreviado a 3 letras ("Oct 2026"), a mano: las abreviaturas de Carbon en español traen punto y varían.
            'nombre' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'][$inicio->month - 1].' '.$inicio->year,
            'anterior' => $inicio->copy()->subMonthNoOverflow()->format('Y-m'),
            'siguiente' => $inicio->lt(Carbon::now()->startOfMonth()) ? $inicio->copy()->addMonthNoOverflow()->format('Y-m') : null,
        ];
    }

    /**
     * Listado general de asistencias (vista admin)
     * Muestra todas las asistencias de todos los guardavidas.
     */
    public function index(Request $request)
    {
        // Las asistencias de otras personas las ve solo quien tiene el permiso ver_asistencia
        // (admin/superadmin y, por defecto, encargado).
        abort_unless(auth()->user()->can('ver_asistencia'), 403);

        // Panel de presentismo: por defecto, mes en curso. Con ?temporada=ID (y sin fechas a mano) el rango
        // pasa a ser el de esa temporada, hasta hoy como máximo si todavía no terminó.
        $temporadas = Temporada::orderByDesc('fecha_inicio')->get();
        $temporadaElegida = $request->filled('temporada') ? $temporadas->firstWhere('id', (int) $request->input('temporada')) : null;

        // ?mes=YYYY-MM (flechas del celular): ese mes completo, o hasta hoy si es el actual. Un mes futuro o mal
        // escrito se ignora. Las fechas a mano y la temporada tienen prioridad sobre el mes.
        $mes = $this->mesPedido($request);

        $inicio = $request->filled('inicio')
            ? Carbon::parse($request->input('inicio'))->startOfDay()
            : ($temporadaElegida ? $temporadaElegida->fecha_inicio->copy()->startOfDay() : ($mes ?? Carbon::now())->copy()->startOfMonth());

        $fin = $request->filled('fin')
            ? Carbon::parse($request->input('fin'))->endOfDay()
            : ($temporadaElegida
                ? $temporadaElegida->fecha_fin->copy()->endOfDay()->min(Carbon::now()->endOfDay())
                : ($mes ?? Carbon::now())->copy()->endOfMonth()->min(Carbon::now()->endOfDay()));

        // Navegación por mes (flechas): solo cuando el período mostrado es un mes entero (o el actual hasta hoy).
        $mesNav = $temporadaElegida ? null : $this->navegacionMes($inicio, $fin);

        // Filtros de playa y búsqueda. nombre/apellido ya no viven en
        // guardavidas -> join a users para buscar/ordenar.
        // (select('guardavidas.*') evita ambigüedad de columnas
        // con el join.
        $guardavidasQuery = Guardavida::activosOConAsistenciaEn($inicio, $fin)
            ->select('guardavidas.*')
            ->join('users', 'users.id', '=', 'guardavidas.user_id')
            ->with(['puesto.playa', 'user']);

        if ($request->filled('playa_id') && $request->input('playa_id') !== 'all') {
            $guardavidasQuery->where('guardavidas.playa_id', $request->input('playa_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $guardavidasQuery->where(function ($q) use ($search) {
                $q->where('users.name', 'LIKE', "%{$search}%")
                    ->orWhere('users.lastname', 'LIKE', "%{$search}%");
            });
        }

        $guardavidasQuery->orderBy('users.lastname')->orderBy('users.name');

        // Orden: alfabético por defecto. "faltas" depende del resumen (que se calcula en PHP, no en SQL), así que
        // ahí se calcula para todas las personas del filtro y se pagina a mano.
        $orden = $request->input('orden') === 'faltas' ? 'faltas' : 'alfabetico';
        $service = new ResumenAsistenciaService;

        if ($orden === 'alfabetico') {
            $guardavidas = $guardavidasQuery->paginate(10)->withQueryString();
            $resumen = $service->generar($guardavidas->getCollection(), $inicio, $fin);
        } else {
            $todos = $guardavidasQuery->get();
            $resumen = $service->generar($todos, $inicio, $fin);

            // sortBy es estable: dentro de un empate queda el orden alfabético de arriba.
            $ordenados = $todos->sortByDesc(fn ($g) => $resumen[$g->id]['faltas'] ?? 0);

            $pagina = LengthAwarePaginator::resolveCurrentPage();
            $guardavidas = new LengthAwarePaginator(
                $ordenados->forPage($pagina, 10)->values(),
                $ordenados->count(),
                10,
                $pagina,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        $playas = Playa::all();
        $agent = new Agent;

        // Búsqueda en vivo del celular: solo se pide la lista (tarjetas + paginación), no la página entera.
        if ($request->ajax()) {
            return view('asistencias.partials.lista-mobile', compact('guardavidas', 'resumen'));
        }

        if ($agent->isMobile()) {
            return view('asistencias.index', compact('guardavidas', 'playas', 'resumen', 'inicio', 'fin', 'temporadas', 'temporadaElegida', 'mesNav'));
        } else {
            return view('asistencias.index-desktop', compact('guardavidas', 'playas', 'resumen', 'inicio', 'fin', 'temporadas', 'temporadaElegida'));
        }

    }

    /**
     * Muestra todas las asistencias de un guardavida en especifico*cuando el admin lo selecciona o cuando el propio usuario
     * ingresa a la seccion "mis asistencias o asistencia"
     * (usado en admin/asistenciaPorPerfil.blade.php)
     */
    public function asistenciasPorGuardavida(Request $request, $id)
    {
        $guardavida = Guardavida::with(['puesto.playa', 'user', 'francoHistorial', 'periodos'])->findOrFail($id);

        // Esta pantalla es de gestión (necesita ver_asistencia). Cada uno ve las propias en "Mis asistencias".
        abort_unless(auth()->user()->can('ver_asistencia'), 403);

        $esAdmin = auth()->user()->hasAnyRole(['admin', 'encargado']);

        // Período: el mes en curso por defecto (igual que la lista), o el mes de las flechas (?mes=YYYY-MM), o
        // fechas a mano (?inicio&fin). Hasta hoy como máximo: los días que vienen no son ni asistencia ni falta.
        $mes = $this->mesPedido($request);
        $inicio = $request->filled('inicio')
            ? Carbon::parse($request->input('inicio'))->startOfDay()
            : ($mes ?? Carbon::now())->copy()->startOfMonth();
        $fin = $request->filled('fin')
            ? Carbon::parse($request->input('fin'))->endOfDay()
            : ($mes ?? Carbon::now())->copy()->endOfMonth()->min(Carbon::now()->endOfDay());
        $mesNav = $this->navegacionMes($inicio, $fin);

        // Mismo cálculo que la lista y los Excel, así los números nunca discrepan entre pantallas.
        $historial = array_reverse((new HistorialAsistenciaService)->generar($guardavida->id, $inicio, $fin)); // más nuevo primero
        $resumen = (new ResumenAsistenciaService)->generar(collect([$guardavida]), $inicio, $fin)[$guardavida->id] ?? null;

        // Aviso si no estaba en el plantel durante todo el período: esos días no se listan ni cuentan como falta
        // (ver Guardavida::enPlantelEl), y sin el aviso parece que faltan datos.
        $diasFuera = 0;
        $diasTotales = 0;
        for ($dia = $inicio->copy()->startOfDay(); $dia->lte($fin); $dia->addDay()) {
            $diasTotales++;
            $diasFuera += $guardavida->enPlantelEl($dia) ? 0 : 1;
        }

        $avisoPlantel = null;
        if ($diasFuera > 0) {
            $hastaUltimo = $guardavida->periodos->whereNotNull('hasta')->sortByDesc('hasta')->first()?->hasta;
            $avisoPlantel = $diasFuera === $diasTotales
                ? 'Fuera del plantel: en este período no figura dado de alta, así que solo se listan los días en que fichó y no se cuentan faltas.'
                  .($hastaUltimo ? ' Estuvo en el plantel hasta el '.$hastaUltimo->format('d/m/Y').'.' : '')
                : 'Estuvo en el plantel solo una parte de este período: los días en que no figuraba dado de alta no se listan ni cuentan como falta.';
        }

        $francoExcepciones = $esAdmin
            ? $guardavida->francoExcepciones()->orderByDesc('fecha')->limit(15)->get()
            : null;
        $francoIntercambios = null;
        if ($esAdmin) {
            $comoSolicitante = $guardavida->intercambiosFrancoSolicitados()->with('destinatario')->get();
            $comoDestinatario = $guardavida->intercambiosFrancoRecibidos()->with('solicitante')->get();
            $francoIntercambios = $comoSolicitante->concat($comoDestinatario)->sortByDesc('created_at')->take(15)->values();
        }

        return view('asistencias.show', compact('guardavida', 'esAdmin', 'inicio', 'fin', 'mesNav', 'historial', 'resumen', 'avisoPlantel', 'francoExcepciones', 'francoIntercambios'));
    }

    /**
     * metodo para que muestre en la seccion "mis asistencias" las asistencias del usuario logueado (solo las ve no puede descargar ni nada
     * como administradores)
     */
    public function misAsistencias()
    {
        $guardavida = auth()->user()->guardavida;
        if (! $guardavida) {
            abort(403, 'No tiene un perfil de guardavida asignado.');
        }

        // Cargar relaciones
        $guardavida->load('asistencias.puesto.playa');

        // Pasamos $esAdmin = false para que el Blade detecte que no es vista administrativa
        $esAdmin = false;

        // No necesitamos filtros ni balnearios/puestos para este caso
        return view('asistencias.por-perfil', compact('guardavida', 'esAdmin'));
    }

    /**
     * Muestra todas las asistencias de un puesto específico
     */
    public function asistenciasPorPuesto($idPuesto)
    {
        $asistencias = Asistencia::with(['guardavida', 'puesto.playa'])
            ->where('puesto_id', $idPuesto)
            ->orderByDesc('fecha_hora')
            ->get();

        // Extraigo los guardavidas únicos
        $guardavidas = $asistencias->pluck('guardavida')->unique('id')->values();

        return view('asistencias.index', compact('guardavidas'));
    }
}
