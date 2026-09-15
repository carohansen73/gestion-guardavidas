<?php

namespace App\Providers;

use App\Models\Bandera;
use App\Models\Intervencion;
use App\Models\NovedadMaterial;
use App\Models\User;
use App\Observers\BanderaObserver;
use App\Observers\IntervencionObserver;
use App\Observers\NovedadMaterialObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Intervencion::observe(IntervencionObserver::class);
        NovedadMaterial::observe(NovedadMaterialObserver::class);
        Bandera::observe(BanderaObserver::class);

        // superadmin pasa CUALQUIER chequeo de autorización (permisos y Policies),
        // incluidos los que se agreguen en el futuro — sin esto, un permiso nuevo
        // no queda disponible para superadmin hasta asignárselo a mano por SQL.
        // Devolver null (no false) para todos los demás roles es clave: así se
        // deja que Spatie y las Policies sigan resolviendo el resto normalmente.
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('superadmin') ? true : null;
        });
    }
}
