<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Playas preferidas de una postulación, en orden de prioridad
     * (1 = primera opción, 2 = segunda opción por si no hay cupo). Ambas
     * son opcionales: una postulación puede no tener ninguna fila.
     */
    public function up(): void
    {
        Schema::create('postulacion_playas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('postulacion_id');
            $table->foreign('postulacion_id')->references('id')->on('postulaciones')->onDelete('cascade');
            $table->unsignedBigInteger('playa_id');
            $table->foreign('playa_id')->references('id')->on('playas')->onDelete('cascade');
            $table->unsignedTinyInteger('prioridad');

            $table->unique(['postulacion_id', 'playa_id']);
            $table->unique(['postulacion_id', 'prioridad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulacion_playas');
    }
};
