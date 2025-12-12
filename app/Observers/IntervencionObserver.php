<?php

namespace App\Observers;

use App\Models\Intervencion;
use App\Models\Novedad;

class IntervencionObserver
{
    /**
     * Handle the Intervencion "created" event.
     */
    public function created(Intervencion $intervencion): void
    {
        Novedad::create([
            'fecha' => $intervencion->fecha,
            'tipo' => 'intervencion',
            'titulo' => "Nueva intervención: ". $intervencion->tipo_intervencion,
            'descripcion' => "{$intervencion->victimas} victimas, codigo {$intervencion->codigo}",
            'playa_id' => $intervencion->playa_id,
            'referencia_modelo' => Intervencion::class,
            'icono' => '<i class="fas fa-life-ring"></i>',
            'color' => 'orange',
            'referencia_id' => $intervencion->id,
        ]);
    }

    /**
     * Handle the Intervencion "updated" event.
     */
    public function updated(Intervencion $intervencion): void
    {
        //
    }

    /**
     * Handle the Intervencion "deleted" event.
     */
    public function deleted(Intervencion $intervencion): void
    {
        //
    }

    /**
     * Handle the Intervencion "restored" event.
     */
    public function restored(Intervencion $intervencion): void
    {
        //
    }

    /**
     * Handle the Intervencion "force deleted" event.
     */
    public function forceDeleted(Intervencion $intervencion): void
    {
        //
    }
}
