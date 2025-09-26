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
        Schema::create('intervencions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha');
            $table->string('tipo_intervencion');
            $table->integer('victimas');
            $table->integer('codigo');
            $table->unsignedBigInteger('bandera_id');
            $table->foreign('bandera_id')->references('id')->on('bandera_tipos')->onDelete('cascade');
            $table->boolean('traslado');
            $table->unsignedBigInteger('playa_id');
            $table->foreign('playa_id')->references('id')->on('playas')->onDelete('cascade');
            $table->unsignedBigInteger('puesto_id');
            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('cascade');
            $table->longText('detalles');
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
        Schema::dropIfExists('intervencions');
    }
};
