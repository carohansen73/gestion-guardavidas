<?php

namespace App\Http\Controllers;

use App\Models\Bandera;
use App\Models\Guardavida;
use App\Models\Intervencion;
use App\Models\Novedad;
use App\Models\NovedadMaterial;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index(){
        // $intervenciones = Intervencion::with('guardavidas')->with('fuerzas')->get();
        $agent = new Agent();
        $isMobile = $agent->isMobile();

        //bandera segun user->playa
        $user = Auth::user();

        $bandera = $this->buscarBanderaActual($user);

        // var_dump($bandera);die;

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
        compact( 'novedades', 'intervenciones', 'banderas','novedadesMateriales', 'guardavidas',
        'totalIntervenciones', 'intervencionesPorPlaya', 'totalNovedadesMateriales', 'novedadesMaterialesPorPlaya'));
    }
}
