<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('temporadas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            // Ventana en la que un postulante puede completar/editar el
            // formulario de postulación (ej. jun-ago, meses antes de que la
            // temporada esté operativa).
            $table->date('fecha_inicio_postulacion');
            $table->date('fecha_fin_postulacion');
            // Ventana operativa real de la temporada (ej. nov-abr) — la que
            // importa para "temporada activa" a los fines de intervenciones/
            // banderas/etc. y para la restricción de escritura fuera de
            // temporada.
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporadas');
    }
};
