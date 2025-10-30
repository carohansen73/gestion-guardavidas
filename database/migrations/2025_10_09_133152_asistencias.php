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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->decimal('longitud', 10, 7);
            $table->decimal('latitud', 10, 7);
            $table->float('precision');
            //Despues borrar de que puedan ser null
            $table->string('estado_validacion')->nullable();
            $table->string('modo_sync')->nullable();
            $table->unsignedBigInteger('puesto_id');
            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('cascade');
            $table->unsignedBigInteger('guardavidas_id');
            $table->foreign('guardavidas_id')->references('id')->on('guardavidas')->onDelete('cascade');
            $table->dateTime('fecha_hora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
