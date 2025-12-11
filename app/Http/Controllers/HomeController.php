<?php

namespace App\Http\Controllers;

use App\Models\Bandera;
use App\Models\Guardavida;
use App\Models\Intervencion;
use App\Models\Novedad;
use App\Models\NovedadMaterial;
use App\Models\Playa;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index(){
        // $intervenciones = Intervencion::with('guardavidas')->with('fuerzas')->get();
        $agent = new Agent();
        $isMobile = $agent->isMobile();

        // bandera segun user->playa
        $user = Auth::user();
        $bandera = $this->buscarBanderaActual($user);

        // Exije que actualice puesto y turn al loguearse la 1era vez
        if ($user->guardavida && is_null($user->guardavida->turno)) {
            session(['show_guardavida_setup' => true]);
        }

        if( $agent->isMobile()) {
            return view('ui.home-mobile', compact( 'isMobile', 'bandera'));
        } else{
                $novedades = Novedad::orderBy('fecha', 'desc')->take(10)->get();
               return view('ui.home-desktop', compact( 'isMobile', 'bandera', 'novedades'));
        }
    }

    private function buscarBanderaActual($user){
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

    public function dashboard(){
        $novedades = Novedad::orderBy('fecha', 'desc')->take(10)->get();
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;
        $playas = Playa::all();


        //Intervenciones
        $totalIntervenciones = Intervencion::count(); // total de intervenciones

        $intervencionesPorPlaya = Intervencion::select('playa_id') //count por playa
            ->selectRaw('COUNT(*) as total')
            ->groupBy('playa_id')
            ->with('playa')
            ->get();

        // Agrego porcentaje
        $intervencionesPorPlaya->transform(function ($item) use ($totalIntervenciones) {
            $item->porcentaje = round(($item->total / $totalIntervenciones) * 100);
             $item->sigla = Str::substr($item->playa->nombre, 0, 3);
            return $item;
        });

        //Novedades de materiales
        $totalNovedadesMateriales = NovedadMaterial::count(); // total de intervenciones

        $novedadesMaterialesPorPlaya = NovedadMaterial::select('playa_id') //count por playa
            ->selectRaw('COUNT(*) as total')
            ->groupBy('playa_id')
            ->with('playa')
            ->get();

        // Agrego porcentaje
        $novedadesMaterialesPorPlaya->transform(function ($item) use ($totalNovedadesMateriales) {
            $item->porcentaje = round(($item->total / $totalNovedadesMateriales) * 100);
            $item->sigla = Str::substr($item->playa->nombre, 0, 3);
            return $item;
        });

        //
        $intervenciones = Intervencion::whereMonth('fecha', $mesActual)
            ->whereYear('fecha', $anioActual)
            ->get();
        $banderas = Bandera::whereMonth('fecha', $mesActual)
            ->whereYear('fecha', $anioActual)
            ->get();
        $novedadesMateriales = NovedadMaterial::whereMonth('fecha', $mesActual)
            ->whereYear('fecha', $anioActual)
            ->get();
       $guardavidas = Guardavida::whereHas('user', function ($query) {
            $query->where('enabled', true);
        })->get();

        return view('ui.dashboard',
        compact( 'playas', 'novedades', 'intervenciones', 'banderas','novedadesMateriales', 'guardavidas',
        'totalIntervenciones', 'intervencionesPorPlaya', 'totalNovedadesMateriales', 'novedadesMaterialesPorPlaya'));
    }


    public function getData(Request $request)
    {
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

        /*INTERVENCIONES*/
        $totalIntervenciones = $intervencionesQuery->count();

        $intervencionesPorPlaya = Intervencion::select('playa_id') //count por playa
            ->selectRaw('COUNT(*) as total')
            ->when($playaId, fn($q) => $q->where('playa_id', $playaId))
            ->groupBy('playa_id')
            ->with('playa')
            ->get();

        // Agrego porcentaje
        $intervencionesPorPlaya->transform(function ($item) use ($totalIntervencionesGlobal) {
            $item->porcentaje = round(($item->total / $totalIntervencionesGlobal) * 100);
             $item->sigla = Str::substr($item->playa->nombre, 0, 3);
            return $item;
        });

        /*NOVEDADES MAT*/
        $totalNovedadesMateriales = $novedadesQuery->count();

        $novedadesMaterialesPorPlaya = NovedadMaterial::select('playa_id') //count por playa
            ->selectRaw('COUNT(*) as total')
            ->when($playaId, fn($q) => $q->where('playa_id', $playaId))
            ->groupBy('playa_id')
            ->with('playa')
            ->get();

        // Agrego porcentaje
        $novedadesMaterialesPorPlaya->transform(function ($item) use ($totalNovedadesGlobal) {
            $item->porcentaje = round(($item->total / $totalNovedadesGlobal) * 100);
            $item->sigla = Str::substr($item->playa->nombre, 0, 3);
            return $item;
        });



        return response()->json([
            'totalIntervenciones' => $totalIntervenciones,
            'intervencionesPorPlaya' => $intervencionesPorPlaya,
            'totalNovedadesMateriales' => $totalNovedadesMateriales,
            'novedadesMaterialesPorPlaya' => $novedadesMaterialesPorPlaya,
            'banderas' => $banderasQuery
               ->join('bandera_tipos', 'bandera_tipos.id', '=', 'banderas.bandera_id')
                ->select(
                    'bandera_tipos.codigo as codigo',
                    'bandera_tipos.color as color',
                    DB::raw('count(*) as total')
                 )
            ->groupBy('bandera_tipos.codigo', 'bandera_tipos.color')
            ->get()
        ]);
    }
}
