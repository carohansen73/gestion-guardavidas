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
        Schema::create('novedad_materials', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_novedad', ['Daño', 'Faltante', 'Falla','Pérdida', 'Rotura']);
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('id')->on('materiales')->onDelete('cascade');
            $table->timestamp('fecha');
            $table->unsignedBigInteger('playa_id');
            $table->foreign('playa_id')->references('id')->on('playas')->onDelete('cascade');
            $table->longText('detalles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('novedad_materials');
    }
};
