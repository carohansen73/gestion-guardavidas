<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Bandera;
use App\Models\Intervencion;
use App\Policies\BanderaPolicy;
use App\Policies\IntervencionPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Bandera::class => BanderaPolicy::class,
        Intervencion::class => IntervencionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
