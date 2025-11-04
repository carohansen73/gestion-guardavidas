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
        Schema::create('novedades', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha');
            $table->string('tipo')->nullable(); // ej: rescate, bandera, material, licencia
            $table->string('titulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->foreignId('playa_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('referencia_modelo')->nullable();
            $table->longText('icono')->nullable();
            $table->string('color')->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('novedades');
    }
};
