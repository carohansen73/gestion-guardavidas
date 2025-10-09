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
        Schema::create('banderas', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha');
            $table->enum('turno', ['mañana','tarde']);
            $table->unsignedBigInteger('bandera_id');
            $table->foreign('bandera_id')->references('id')->on('bandera_tipos')->onDelete('cascade');
            // $table->enum('viento_direccion', ['N','S', 'E', 'O', 'NE', 'NO', 'SE', 'SO']);
            // $table->enum('viento_intensidad', ['Calmo','Leve', 'Moderado', 'Fuerte']);
            $table->string('viento_direccion')->nullable(); //Prueba con API, sino lo dejo manual con seleccionable
            $table->string('viento_intensidad')->nullable();
            $table->string('temperatura')->nullable();
            $table->unsignedBigInteger('playa_id');
            $table->foreign('playa_id')->references('id')->on('playas')->onDelete('cascade');
            $table->longText('detalles')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banderas');
    }
};
