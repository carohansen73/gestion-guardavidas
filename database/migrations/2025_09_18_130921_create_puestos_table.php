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
        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('latitud', 10, 6);
            $table->decimal('longitud', 10, 6);
            // Se deja null ya que no se puede crear el QR en la migración entonces, cuando se crea por primera vez el modelo,
            // se le da el valor y se encripta
            $table->text('qr_encriptado')->nullable();
            $table->unsignedBigInteger('playa_id');
            $table->foreign('playa_id')->references('id')->on('playas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puestos');
    }

};
