<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Bandera;
use App\Models\Guardavida;
use App\Models\Intervencion;
use App\Models\Licencia;
use App\Models\Novedad;
use App\Models\NovedadMaterial;
use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\Temporada;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class HomeController extends Controller
{
    public function index()
    {
        // $intervenciones = Intervencion::with('guardavidas')->with('fuerzas')->get();
        $agent = new Agent;
        $isMobile = $agent->isMobile();
        // Tablet no cuenta como isMobile() para Jenssegers\Agent (esa manda a
        // home-mobile) — pero sí queda dentro de dashboard.index igual que
        // desktop, así que esto es lo que distingue "tablet" de "desktop"
        // adentro de esa misma vista (ver acceso directo a Fichar).
        $isTablet = $agent->isTablet();

        // bandera segun user->playa
        $user = Auth::user();
        $bandera = $this->buscarBanderaActual($user);

        $totales = [
            'intervenciones' => Intervencion::count(),
            'banderas' => Bandera::count(),
            'novedades' => NovedadMaterial::count(),
            'guardavidas' => Guardavida::activos()->whereHas('user', function ($u) {
                $u->where('enabled', 1);
            })->count(),
        ];

        // Exije que actualice puesto y turno al loguearse la 1era vez
        if ($user->guardavida && (is_null($user->guardavida->turno) || is_null($user->guardavida->puesto_id))) {
            session(['show_guardavida_setup' => true]);
        }

        // Exige (con un aviso, no bloqueante) que configure su día de franco
        // fijo si todavía no lo tiene. Se limpia en
        // GuardavidaController::actualizarDiaFranco() al configurarlo.
        if ($user->guardavida && $user->guardavida->diasFrancoActuales() === []) {
            session(['show_franco_setup' => true]);
        }

        // Card "Mi turno": guardavida y encargado, no admin — ambos fichan
        // (tienen Guardavida propio), y es además el único acceso a Fichar
        // ahora que se sacó del bloque de Atajos rápidos para no duplicarlo.
        //
        // No existe un campo "tipo" (ingreso/egreso) en asistencias — cada
        // fichaje es solo un evento de escaneo con su fecha_hora. Para
        // mostrar "pendiente de egreso" sin agregar una columna nueva, se
        // infiere por cantidad de fichajes de hoy: 0 = falta el ingreso,
        // 1 = ya fichó ingreso y falta el egreso, 2+ = turno completo (se
        // muestra el primero y el último).
        $esGuardavidaOEncargado = $user->hasRole('guardavida') || $user->hasRole('encargado');
        $asistenciasHoyPropias = collect();
        if ($esGuardavidaOEncargado) {
            $asistenciasHoyPropias = Asistencia::where('guardavidas_id', $user->guardavida->id)
                ->whereDate('fecha_hora', Carbon::today())
                ->with('puesto')
                ->orderBy('fecha_hora')
                ->get();
        }

        $novedades = Novedad::orderBy('fecha', 'desc')->take(10)->get();

        // El panel de estadísticas (antes era la vista /dashboard aparte,
        // ver ui/partials/panel-admin.blade.php) ahora lo ve cualquier
        // usuario autenticado, pero cada uno solo ve lo suyo: admin/
        // superadmin ven todas las playas (con filtro); encargado/guardavida
        // quedan limitados a la propia, y qué cards/secciones se muestran
        // depende de sus permisos (@can en la vista) — nada hardcodeado por
        // rol acá, así que basta con tocar los permisos para habilitar algo.
        $esAdmin = $user->hasRole('admin');
        $playaIdUsuario = $user->guardavida->playa_id ?? null;

        $playas = $esAdmin ? Playa::all() : collect();

        $guardavidasPorPlaya = Guardavida::activos()->select('playa_id')
            ->selectRaw('COUNT(*) as total')
            ->whereHas('user', function ($q) {
                $q->where('enabled', true);
            })
            ->when(! $esAdmin, fn ($q) => $q->where('playa_id', $playaIdUsuario))
            ->groupBy('playa_id')
            ->with('playa')
            ->get();

        $asistenciasHoy = Asistencia::whereDate('fecha_hora', Carbon::today())
            ->when(! $esAdmin, fn ($q) => $q->whereHas('puesto', fn ($q2) => $q2->where('playa_id', $playaIdUsuario)))
            ->count();

        $fueraDeRango30d = Asistencia::where('estado_validacion', 'fuera_de_rango')
            ->where('fecha_hora', '>=', Carbon::now()->subDays(30))
            ->when(! $esAdmin, fn ($q) => $q->whereHas('puesto', fn ($q2) => $q2->where('playa_id', $playaIdUsuario)))
            ->count();

        $licenciasActivasHoy = Licencia::whereDate('fecha_inicio', '<=', Carbon::today())
            ->whereDate('fecha_fin', '>=', Carbon::today())
            ->when(! $esAdmin, fn ($q) => $q->where('playa_id', $playaIdUsuario))
            ->count();

        // Intervenciones/novedades materiales/guardavidas activos del panel:
        // total global para admin, acotado a la propia playa para el resto.
        // Variables propias (no $totales, que ya se usa en otro lado con el
        // conteo 100% global — ver aside "Guardavidas registrados").
        $panelIntervenciones = Intervencion::when(! $esAdmin, fn ($q) => $q->where('playa_id', $playaIdUsuario))->count();
        $panelNovedadesMateriales = NovedadMaterial::when(! $esAdmin, fn ($q) => $q->where('playa_id', $playaIdUsuario))->count();
        $panelGuardavidasActivos = Guardavida::activos()->whereHas('user', fn ($q) => $q->where('enabled', true))
            ->when(! $esAdmin, fn ($q) => $q->where('playa_id', $playaIdUsuario))
            ->count();

        // Badge de "novedades de materiales de hoy" sobre el ícono de esa
        // card — aparte del total de temporada (número grande) de arriba.
        $novedadesMaterialesHoy = NovedadMaterial::whereDate('fecha', Carbon::today())
            ->when(! $esAdmin, fn ($q) => $q->where('playa_id', $playaIdUsuario))
            ->count();

        // Feed de "últimas novedades": solo de los tipos que el usuario
        // puede ver, y acotado a su playa si no es admin.
        $modelosPermitidos = collect([
            Bandera::class => 'ver_bandera',
            Intervencion::class => 'ver_intervencion',
            NovedadMaterial::class => 'ver_novedad_material',
        ])->filter(fn ($permiso) => $user->can($permiso))->keys();

        $novedades = Novedad::orderBy('fecha', 'desc')
            ->whereIn('referencia_modelo', $modelosPermitidos)
            ->when(! $esAdmin, fn ($q) => $q->where('playa_id', $playaIdUsuario))
            ->with(['referencia' => function ($morphTo) {
                // Solo Bandera/Intervencion tienen puesto — NovedadMaterial
                // no, así que no se le pide esa relación (moprhWith tirar
                // un error "relación indefinida" si se la pidiéramos igual).
                $morphTo->morphWith([
                    Bandera::class => ['puesto'],
                    Intervencion::class => ['puesto'],
                ]);
            }])
            ->take(10)
            ->get();

        // Guardavidas y encargados también se postulan cada temporada: si la ventana está abierta,
        // se les muestra el acceso (y el estado de su inscripción, si ya la empezaron).
        $postularme = null;
        if ($esGuardavidaOEncargado && $temporadaAbierta = Temporada::conPostulacionAbierta()) {
            $postularme = [
                'temporada' => $temporadaAbierta,
                'postulacion' => Postulacion::where('user_id', $user->id)->where('temporada_id', $temporadaAbierta->id)->first(),
            ];
        }

        $data = compact(
            'postularme',
            'isMobile', 'isTablet', 'bandera', 'totales', 'novedades', 'esAdmin',
            'playas', 'guardavidasPorPlaya', 'asistenciasHoy', 'fueraDeRango30d', 'licenciasActivasHoy',
            'panelIntervenciones', 'panelNovedadesMateriales', 'panelGuardavidasActivos', 'novedadesMaterialesHoy',
            'esGuardavidaOEncargado', 'asistenciasHoyPropias'
        );

        return $agent->isMobile()
            ? view('dashboard.index', $data)
            : view('dashboard.index', $data);
    }

    private function buscarBanderaActual($user)
    {
        $hoy = Carbon::today();

        // Determinar turno actual (ejemplo simple: mañana/tarde)
        $hora = Carbon::now()->hour;
        $turno = $hora < 13 ? 'mañana' : 'tarde';

        if ($user->hasRole('admin')) {
            // Última bandera del día por playa (las que ya se cargaron)
            $banderasPorPlaya = Bandera::with(['bandera'])
                ->whereDate('fecha', $hoy)
                // ->where('turno', $turno)
                ->latest('created_at')
                ->get()
                ->groupBy('playa_id')
                ->map(function ($banderas) {
                    return $banderas->first(); // último registro por playa
                });

            // Una entrada por CADA playa (tenga bandera cargada hoy o no),
            // para que el carrusel del admin muestre también las que están
            // pendientes en vez de simplemente omitirlas.
            $bandera = Playa::all()->map(function ($playa) use ($banderasPorPlaya) {
                return [
                    'playa' => $playa,
                    'bandera' => $banderasPorPlaya->get($playa->id),
                ];
            });
        } else {
            // Solo su playa
            $bandera = Bandera::with(['playa', 'bandera'])
                ->where('playa_id', $user->guardavida->playa_id)
                ->whereDate('fecha', $hoy)
                // ->where('turno', $turno)
                ->latest('created_at')
                ->first();
        }

        return $bandera;
    }

    /**
     * /dashboard ya no es una pantalla aparte — el panel de estadísticas de
     * admin se fusionó dentro de /home (ver panel-admin.blade.php). Esto
     * queda solo para no romper bookmarks/links viejos.
     */
    public function dashboard()
    {
        return redirect()->route('home');
    }

    public function getData(Request $request)
    {
        // Cualquier usuario autenticado puede pedir estos datos, pero cada
        // uno recibe solo lo suyo: admin/superadmin pueden filtrar por
        // cualquier playa (o pedir todas); el resto queda forzado a la
        // propia, sin importar qué mande el query param ?playa=. Y cada
        // bloque de datos solo se calcula/devuelve si el usuario tiene el
        // permiso correspondiente — así el JSON no expone de más aunque la
        // vista lo tenga oculto.
        $user = Auth::user();
        $esAdmin = $user->hasRole('admin');
        $playaIdUsuario = $user->guardavida->playa_id ?? null;
        $playaId = $esAdmin ? $request->get('playa') : $playaIdUsuario;

        $response = [];

        if ($user->can('ver_intervencion')) {
            $totalIntervencionesGlobal = Intervencion::when(! $esAdmin, fn ($q) => $q->where('playa_id', $playaIdUsuario))->count();

            $intervencionesPorPlaya = Intervencion::select('playa_id')
                ->selectRaw('COUNT(*) as total')
                ->when($playaId, fn ($q) => $q->where('playa_id', $playaId))
                ->groupBy('playa_id')
                ->with('playa')
                ->get()
                ->each(function ($item) use ($totalIntervencionesGlobal) {
                    $item->porcentaje = $totalIntervencionesGlobal > 0 ? round(($item->total / $totalIntervencionesGlobal) * 100) : 0;
                    $item->sigla = Str::substr($item->playa->nombre, 0, 3);
                });

            $response['totalIntervenciones'] = Intervencion::when($playaId, fn ($q) => $q->where('playa_id', $playaId))->count();
            $response['intervencionesPorPlaya'] = $intervencionesPorPlaya;
        }

        if ($user->can('ver_novedad_material')) {
            $totalNovedadesGlobal = NovedadMaterial::when(! $esAdmin, fn ($q) => $q->where('playa_id', $playaIdUsuario))->count();

            $novedadesMaterialesPorPlaya = NovedadMaterial::select('playa_id')
                ->selectRaw('COUNT(*) as total')
                ->when($playaId, fn ($q) => $q->where('playa_id', $playaId))
                ->groupBy('playa_id')
                ->with('playa')
                ->get()
                ->each(function ($item) use ($totalNovedadesGlobal) {
                    $item->porcentaje = $totalNovedadesGlobal > 0 ? round(($item->total / $totalNovedadesGlobal) * 100) : 0;
                    $item->sigla = Str::substr($item->playa->nombre, 0, 3);
                });

            $response['totalNovedadesMateriales'] = NovedadMaterial::when($playaId, fn ($q) => $q->where('playa_id', $playaId))->count();
            $response['novedadesMaterialesPorPlaya'] = $novedadesMaterialesPorPlaya;
        }

        if ($user->can('ver_guardavida')) {
            $response['totalGuardavidasActivos'] = Guardavida::activos()->whereHas('user', fn ($q) => $q->where('enabled', true))
                ->when($playaId, fn ($q) => $q->where('playa_id', $playaId))
                ->count();

            $response['guardavidasPorPlaya'] = Guardavida::activos()->select('playa_id')
                ->selectRaw('COUNT(*) as total')
                ->whereHas('user', fn ($q) => $q->where('enabled', true))
                ->when($playaId, fn ($q) => $q->where('playa_id', $playaId))
                ->groupBy('playa_id')
                ->with('playa')
                ->get();
        }

        if ($user->can('ver_asistencia')) {
            $response['asistenciasHoy'] = Asistencia::whereDate('fecha_hora', Carbon::today())
                ->when($playaId, fn ($q) => $q->whereHas('puesto', fn ($q2) => $q2->where('playa_id', $playaId)))
                ->count();

            $response['fueraDeRango30d'] = Asistencia::where('estado_validacion', 'fuera_de_rango')
                ->where('fecha_hora', '>=', Carbon::now()->subDays(30))
                ->when($playaId, fn ($q) => $q->whereHas('puesto', fn ($q2) => $q2->where('playa_id', $playaId)))
                ->count();
        }

        if ($user->can('ver_licencia')) {
            $response['licenciasActivasHoy'] = Licencia::whereDate('fecha_inicio', '<=', Carbon::today())
                ->whereDate('fecha_fin', '>=', Carbon::today())
                ->when($playaId, fn ($q) => $q->where('playa_id', $playaId))
                ->count();
        }

        if ($user->can('ver_bandera')) {
            $response['banderas'] = Bandera::when($playaId, fn ($q) => $q->where('playa_id', $playaId))
                ->join('bandera_tipos', 'bandera_tipos.id', '=', 'banderas.bandera_id')
                ->select(
                    'bandera_tipos.codigo as codigo',
                    'bandera_tipos.color as color',
                    DB::raw('count(*) as total')
                )
                ->groupBy('bandera_tipos.codigo', 'bandera_tipos.color')
                ->get();
        }

        return response()->json($response);
    }
}
