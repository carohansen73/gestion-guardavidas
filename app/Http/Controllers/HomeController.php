<?php

namespace App\Http\Controllers;

use App\Models\BanderaTipo;
use Jenssegers\Agent\Agent;

class HomeController extends Controller
{
    public function index(){
        // $intervenciones = Intervencion::with('guardavidas')->with('fuerzas')->get();
        $agent = new Agent();
        $isMobile = $agent->isMobile();


        $bandera = BanderaTipo::first();
         $bandera = (object) [
            'id' => 1,
            'codigo' => 'Dudoso',
            'color' => 'yellow',
            'descripcion' => 'Mar peligroso, bañarse con precaución',
        ];

        return view('ui.home', compact( 'isMobile', 'bandera'));
    }
}
