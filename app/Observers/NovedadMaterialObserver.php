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
            'icono' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />',
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
