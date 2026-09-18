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
        Schema::create('franco_excepciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guardavida_id');
            $table->foreign('guardavida_id')->references('id')->on('guardavidas')->onDelete('cascade');
            $table->date('fecha');
            // 'cancelado': ese día puntual deja de ser franco (el fijo se corrió a otra fecha esa semana).
            // 'agregado': ese día puntual pasa a ser franco aunque no coincida con el día fijo.
            $table->enum('tipo', ['cancelado', 'agregado']);
            $table->string('motivo')->nullable();
            $table->unsignedBigInteger('cargado_por_user_id')->nullable();
            $table->foreign('cargado_por_user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['guardavida_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franco_excepciones');
    }
};
