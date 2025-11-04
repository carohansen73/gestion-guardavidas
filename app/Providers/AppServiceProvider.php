<?php

namespace App\Providers;

use App\Models\Bandera;
use App\Models\Intervencion;
use App\Models\NovedadMaterial;
use App\Observers\BanderaObserver;
use App\Observers\IntervencionObserver;
use App\Observers\NovedadMaterialObserver;
use Illuminate\Support\ServiceProvider;
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
    }
}
