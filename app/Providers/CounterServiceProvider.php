<?php

namespace App\Providers;

use App\Models\Bandera;
use App\Models\CambioDeTurno;
use App\Models\Intervencion;
use App\Models\Licencia;
use App\Models\NovedadMaterial;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class CounterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $hoy = Carbon::today();

            $conteos = [
                'intervenciones' => Intervencion::whereDate('fecha', $hoy)->count(),
                'banderas' => Bandera::whereDate('fecha', $hoy)->count(),
                'novedades' => NovedadMaterial::whereDate('fecha', $hoy)->count(),
                'licencias' => Licencia::whereDate('fecha_inicio', $hoy)->count(),
                'cambios-turno' => CambioDeTurno::whereDate('fecha', $hoy)->count(),
                //ver asistencias
            ];


            $view->with('conteos', $conteos);
        });
    }
}
