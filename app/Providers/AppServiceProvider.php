<?php

namespace App\Providers;

use App\Enums\RolUsuario;
use App\Models\Bandera;
use App\Models\Intervencion;
use App\Models\NovedadMaterial;
use App\Models\User;
use App\Observers\BanderaObserver;
use App\Observers\IntervencionObserver;
use App\Observers\NovedadMaterialObserver;
use App\Services\CatalogoRoles;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Qué significa cada rol (tabla `roles`): se lee una vez por request y se recarga solo en el siguiente.
        $this->app->scoped(CatalogoRoles::class);
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
            return $user->hasRole(RolUsuario::Superadmin->value) ? true : null;
        });
    }
}
