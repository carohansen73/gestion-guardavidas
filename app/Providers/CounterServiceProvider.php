<?php

namespace App\Providers;

use App\Models\Bandera;
use App\Models\CambioDeTurno;
use App\Models\Guardavida;
use App\Models\Intervencion;
use App\Models\Licencia;
use App\Models\NovedadMaterial;
use App\Models\Playa;
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
                'banderas' => Bandera::whereDate('fecha', $hoy)->distinct('playa_id')->count('playa_id'),
                'novedades' => NovedadMaterial::whereDate('fecha', $hoy)->count(),
                'licencias' => Licencia::whereDate('fecha_inicio', $hoy)->count(),
                'cambios-turno' => CambioDeTurno::whereDate('fecha', $hoy)->count(),
              //Guardavidas puedo ver cuantos hay fichados en el dia  o para asistencias

                // Por ahora pongo una constante = 4, porque tengo tsAs como playa (y n quiero que la cuente),
                // Cuando saque TsAs descomentar esto para usarlo!!
                // 'totalPlayas' => Playa::count(),

                // ver asistencias
            ];


            $view->with('conteos', $conteos);
        });
    }
}
