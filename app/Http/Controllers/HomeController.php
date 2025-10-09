<?php

namespace App\Http\Controllers;

use App\Models\Bandera;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(){
        // $intervenciones = Intervencion::with('guardavidas')->with('fuerzas')->get();
        $agent = new Agent();
        $isMobile = $agent->isMobile();

        //bandera segun user->playa
        $user = Auth::user();

        $bandera = $this->buscarBanderaActual($user);

        if( $agent->isMobile()) {
            return view('ui.home', compact( 'isMobile', 'bandera'));
        } else{
               return view('ui.dashboard', compact( 'isMobile', 'bandera'));
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
}
