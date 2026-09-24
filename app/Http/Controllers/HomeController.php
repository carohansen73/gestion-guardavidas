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

        // bandera segun user->playa
        $user = Auth::user();
        $bandera = $this->buscarBanderaActual($user);

        $totales = [
            'intervenciones' => Intervencion::count(),
            'banderas' => Bandera::count(),
            'novedades' => NovedadMaterial::count(),
            'guardavidas' => Guardavida::whereHas('user', function ($u) {
                $u->where('enabled', 1);
            })->count(),
        ];

        // Exije que actualice puesto y turno al loguearse la 1era vez
        if ($user->guardavida && is_null($user->guardavida->turno)) {
            session(['show_guardavida_setup' => true]);
        }

        // Exige (con un aviso, no bloqueante) que configure su día de franco
        // fijo si todavía no lo tiene. Se limpia en
        // GuardavidaController::actualizarDiaFranco() al configurarlo.
        if ($user->guardavida && $user->guardavida->diasFrancoActuales() === []) {
            session(['show_franco_setup' => true]);
        }

        $novedades = Novedad::orderBy('fecha', 'desc')->take(10)->get();

        // El panel de estadísticas (antes era la vista /dashboard aparte,
        // ver ui/partials/panel-admin.blade.php) solo lo ve un admin, así
        // que estas consultas extra solo corren para ese caso.
        $esAdmin = $user->hasRole('admin');

        $playas = collect();
        $guardavidasPorPlaya = collect();
        $asistenciasHoy = 0;
        $fueraDeRango30d = 0;
        $licenciasActivasHoy = 0;

        if ($esAdmin) {
            $playas = Playa::all();

            $guardavidasPorPlaya = Guardavida::select('playa_id')
                ->selectRaw('COUNT(*) as total')
                ->whereHas('user', function ($q) {
                    $q->where('enabled', true);
                })
                ->groupBy('playa_id')
                ->with('playa')
                ->get();

            $asistenciasHoy = Asistencia::whereDate('fecha_hora', Carbon::today())->count();

            $fueraDeRango30d = Asistencia::where('estado_validacion', 'fuera_de_rango')
                ->where('fecha_hora', '>=', Carbon::now()->subDays(30))
                ->count();

            $licenciasActivasHoy = Licencia::whereDate('fecha_inicio', '<=', Carbon::today())
                ->whereDate('fecha_fin', '>=', Carbon::today())
                ->count();
        }

        $data = compact(
            'isMobile', 'bandera', 'totales', 'novedades', 'esAdmin',
            'playas', 'guardavidasPorPlaya', 'asistenciasHoy', 'fueraDeRango30d', 'licenciasActivasHoy'
        );

        return $agent->isMobile()
            ? view('ui.home-mobile', $data)
            : view('ui.home-desktop', $data);
    }

    private function buscarBanderaActual($user)
    {
        $hoy = Carbon::today();

        // Determinar turno actual (ejemplo simple: mañana/tarde)
        $hora = Carbon::now()->hour;
        $turno = $hora < 13 ? 'mañana' : 'tarde';

        if ($user->hasRole('admin')) {
            // Todas las playas, última bandera del día y turno
            $bandera = Bandera::with(['playa', 'bandera'])
                ->whereDate('fecha', $hoy)
                // ->where('turno', $turno)
                ->latest('created_at')
                ->get()
                ->groupBy('playa_id')
                ->map(function ($banderas) {
                    return $banderas->first(); // último registro por playa
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
        // Mismo criterio que dashboard(): esto solo lo consume esa página,
        // que ya es admin-only.
        if (! Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $playaId = $request->get('playa');

        $intervencionesQuery = Intervencion::query();
        $novedadesQuery = NovedadMaterial::query();
        $banderasQuery = Bandera::query();

        // Totales globales (sin filtro)
        $totalIntervencionesGlobal = Intervencion::count();
        $totalNovedadesGlobal = NovedadMaterial::count();

        if ($playaId) {
            $intervencionesQuery->where('playa_id', $playaId);
            $novedadesQuery->where('playa_id', $playaId);
            $banderasQuery->where('playa_id', $playaId);
        }

        /* INTERVENCIONES */
        $totalIntervenciones = $intervencionesQuery->count();

        $intervencionesPorPlaya = Intervencion::select('playa_id') // count por playa
            ->selectRaw('COUNT(*) as total')
            ->when($playaId, fn ($q) => $q->where('playa_id', $playaId))
            ->groupBy('playa_id')
            ->with('playa')
            ->get();

        // Agrego porcentaje
        $intervencionesPorPlaya->transform(function ($item) use ($totalIntervencionesGlobal) {
            $item->porcentaje = round(($item->total / $totalIntervencionesGlobal) * 100);
            $item->sigla = Str::substr($item->playa->nombre, 0, 3);

            return $item;
        });

        /* NOVEDADES MAT */
        $totalNovedadesMateriales = $novedadesQuery->count();

        $novedadesMaterialesPorPlaya = NovedadMaterial::select('playa_id') // count por playa
            ->selectRaw('COUNT(*) as total')
            ->when($playaId, fn ($q) => $q->where('playa_id', $playaId))
            ->groupBy('playa_id')
            ->with('playa')
            ->get();

        // Agrego porcentaje
        $novedadesMaterialesPorPlaya->transform(function ($item) use ($totalNovedadesGlobal) {
            $item->porcentaje = round(($item->total / $totalNovedadesGlobal) * 100);
            $item->sigla = Str::substr($item->playa->nombre, 0, 3);

            return $item;
        });

        /* GUARDAVIDAS ACTIVOS (plantel), por playa */
        $totalGuardavidasActivos = Guardavida::whereHas('user', function ($q) {
            $q->where('enabled', true);
        })
            ->when($playaId, fn ($q) => $q->where('playa_id', $playaId))
            ->count();

        $guardavidasPorPlaya = Guardavida::select('playa_id')
            ->selectRaw('COUNT(*) as total')
            ->whereHas('user', function ($q) {
                $q->where('enabled', true);
            })
            ->when($playaId, fn ($q) => $q->where('playa_id', $playaId))
            ->groupBy('playa_id')
            ->with('playa')
            ->get();

        /* ASISTENCIAS DE HOY */
        $asistenciasHoy = Asistencia::whereDate('fecha_hora', Carbon::today())
            ->when($playaId, fn ($q) => $q->whereHas('puesto', fn ($q2) => $q2->where('playa_id', $playaId)))
            ->count();

        /* FUERA DE RANGO, ÚLTIMOS 30 DÍAS */
        $fueraDeRango30d = Asistencia::where('estado_validacion', 'fuera_de_rango')
            ->where('fecha_hora', '>=', Carbon::now()->subDays(30))
            ->when($playaId, fn ($q) => $q->whereHas('puesto', fn ($q2) => $q2->where('playa_id', $playaId)))
            ->count();

        /* LICENCIAS ACTIVAS HOY */
        $licenciasActivasHoy = Licencia::whereDate('fecha_inicio', '<=', Carbon::today())
            ->whereDate('fecha_fin', '>=', Carbon::today())
            ->when($playaId, fn ($q) => $q->where('playa_id', $playaId))
            ->count();

        return response()->json([
            'totalIntervenciones' => $totalIntervenciones,
            'intervencionesPorPlaya' => $intervencionesPorPlaya,
            'totalNovedadesMateriales' => $totalNovedadesMateriales,
            'novedadesMaterialesPorPlaya' => $novedadesMaterialesPorPlaya,
            'totalGuardavidasActivos' => $totalGuardavidasActivos,
            'guardavidasPorPlaya' => $guardavidasPorPlaya,
            'asistenciasHoy' => $asistenciasHoy,
            'fueraDeRango30d' => $fueraDeRango30d,
            'licenciasActivasHoy' => $licenciasActivasHoy,
            'banderas' => $banderasQuery
                ->join('bandera_tipos', 'bandera_tipos.id', '=', 'banderas.bandera_id')
                ->select(
                    'bandera_tipos.codigo as codigo',
                    'bandera_tipos.color as color',
                    DB::raw('count(*) as total')
                )
                ->groupBy('bandera_tipos.codigo', 'bandera_tipos.color')
                ->get(),
        ]);
    }
}
