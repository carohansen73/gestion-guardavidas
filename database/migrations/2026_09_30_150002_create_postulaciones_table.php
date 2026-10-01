<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Una fila por inscripción (persona + temporada). El historial año a año
     * son simplemente las filas de temporadas anteriores del mismo usuario.
     */
    public function up(): void
    {
        Schema::create('postulaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // restrict: no se puede borrar una temporada que tenga postulaciones.
            $table->unsignedBigInteger('temporada_id');
            $table->foreign('temporada_id')->references('id')->on('temporadas')->onDelete('restrict');

            // borrador / pendiente / aceptada / rechazada (más adelante
            // incompleta). String validado por la app, no enum de DB.
            $table->string('estado', 20)->default('borrador');
            $table->timestamp('enviada_at')->nullable();

            $table->date('disponible_desde')->nullable();
            $table->date('disponible_hasta')->nullable();

            // Revisión del admin (último cambio).
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('revisado_por_user_id')->nullable();
            $table->foreign('revisado_por_user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('fecha_revision')->nullable();

            // Selección (Fase 5): independiente de `estado`.
            $table->boolean('seleccionado')->default(false);
            $table->unsignedBigInteger('playa_asignada_id')->nullable();
            $table->foreign('playa_asignada_id')->references('id')->on('playas')->onDelete('set null');
            $table->unsignedBigInteger('puesto_asignado_id')->nullable();
            $table->foreign('puesto_asignado_id')->references('id')->on('puestos')->onDelete('set null');
            $table->enum('turno_asignado', ['M', 'T'])->nullable();
            $table->enum('funcion_asignada', ['Guardavida', 'Timonel', 'Encargado', 'Jefe_de_playa'])->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'temporada_id']);
            $table->index(['temporada_id', 'estado']);
            $table->index(['temporada_id', 'seleccionado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulaciones');
    }
};
