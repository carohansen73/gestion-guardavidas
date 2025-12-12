<?php

namespace App\Observers;

use App\Models\Bandera;
use App\Models\Novedad;

class BanderaObserver
{
    /**
     * Handle the Bandera "created" event.
     */
    public function created(Bandera $bandera): void
    {
        Novedad::create([
            'fecha' => $bandera->fecha,
            'tipo' => 'bandera',
            'titulo' => "Actualización de bandera: ". $bandera->bandera->codigo,
            'descripcion' => "Estado del clima:  {$bandera->viento_intensidad} {$bandera->viento_direccion} {$bandera->temperatura}º",
            'playa_id' => $bandera->playa_id,
            'referencia_modelo' => Bandera::class,
            'icono' => '<i class="fas fa-flag"></i>',
            'color' => $bandera->bandera->color,
            'referencia_id' => $bandera->id,
        ]);
    }

    /**
     * Handle the Bandera "updated" event.
     */
    public function updated(Bandera $bandera): void
    {
        //
    }

    /**
     * Handle the Bandera "deleted" event.
     */
    public function deleted(Bandera $bandera): void
    {
        //
    }

    /**
     * Handle the Bandera "restored" event.
     */
    public function restored(Bandera $bandera): void
    {
        //
    }

    /**
     * Handle the Bandera "force deleted" event.
     */
    public function forceDeleted(Bandera $bandera): void
    {
        //
    }
}
