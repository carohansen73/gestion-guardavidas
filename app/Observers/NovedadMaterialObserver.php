<?php

namespace App\Observers;

use App\Models\Novedad;
use App\Models\NovedadMaterial;

class NovedadMaterialObserver
{
    /**
     * Handle the NovedadMaterial "created" event.
     */
    public function created(NovedadMaterial $novedadMaterial): void
    {
        Novedad::create([
            'fecha' => $novedadMaterial->created_at,
            'tipo' => 'Novedad de materiales',
            'titulo' => "Nueva Novedad: ". $novedadMaterial->tipo_novedad->value . " " .  $novedadMaterial->material->nombre,
            'descripcion' => "{$novedadMaterial->detalles}",
            'playa_id' => $novedadMaterial->playa_id,
            'referencia_modelo' => NovedadMaterial::class,
            'icono' => '<i class="fas fa-toolbox"></i>',
            'color' => 'teal',
            'referencia_id' => $novedadMaterial->id,
        ]);
    }

    /**
     * Handle the NovedadMaterial "updated" event.
     */
    public function updated(NovedadMaterial $novedadMaterial): void
    {
        //
    }

    /**
     * Handle the NovedadMaterial "deleted" event.
     */
    public function deleted(NovedadMaterial $novedadMaterial): void
    {
        //
    }

    /**
     * Handle the NovedadMaterial "restored" event.
     */
    public function restored(NovedadMaterial $novedadMaterial): void
    {
        //
    }

    /**
     * Handle the NovedadMaterial "force deleted" event.
     */
    public function forceDeleted(NovedadMaterial $novedadMaterial): void
    {
        //
    }
}
