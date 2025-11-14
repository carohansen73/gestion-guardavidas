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
        Schema::create('guardavidas', function (Blueprint $table) {
            $table->id();
            $table->enum('funcion', ['Guardavida','Timonel', 'Encargado', 'Jefe_de_playa']);
            $table->string('nombre');
            $table->string('apellido');
            $table->string('dni');
            $table->string('telefono');
            $table->string('direccion');
            $table->string('numero');
            $table->string('piso_dpto')->nullable();
            $table->unsignedBigInteger('playa_id');
            $table->foreign('playa_id')->references('id')->on('playas')->onDelete('cascade');
            $table->unsignedBigInteger('puesto_id');
            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('cascade');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardavidas');
    }
};
