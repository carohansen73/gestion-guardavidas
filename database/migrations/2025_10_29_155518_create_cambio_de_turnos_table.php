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
        Schema::create('cambio_de_turnos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->enum('turno', ['M', 'T']);
            $table->unsignedBigInteger('guardavida_id')->nullable();
            $table->foreign('guardavida_id')->references('id')->on('guardavidas')->onDelete('cascade');
            $table->unsignedBigInteger('playa_id');
            $table->foreign('playa_id')->references('id')->on('playas')->onDelete('cascade');
            $table->unsignedBigInteger('puesto_id');
            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('cascade');
            $table->string('funcion');
            $table->longText('detalles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cambio_de_turnos');
    }
};
