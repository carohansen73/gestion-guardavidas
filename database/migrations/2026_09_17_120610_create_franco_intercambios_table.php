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
        Schema::create('franco_intercambios', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('guardavida_solicitante_id');
            $table->foreign('guardavida_solicitante_id')->references('id')->on('guardavidas')->onDelete('cascade');

            $table->unsignedBigInteger('guardavida_destinatario_id');
            $table->foreign('guardavida_destinatario_id')->references('id')->on('guardavidas')->onDelete('cascade');

            // Día que el solicitante tiene/tomaría como franco y quiere ceder,
            // y día que quiere tomar en su lugar (típicamente el franco del
            // destinatario) — swap puntual de esa semana, no cambia el
            // dia_franco fijo de ninguno de los dos.
            $table->date('fecha_propia');
            $table->date('fecha_deseada');

            $table->enum('estado', ['pendiente', 'aceptado', 'rechazado', 'cancelado'])->default('pendiente');
            $table->string('mensaje')->nullable();
            $table->timestamp('respondido_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franco_intercambios');
    }
};
