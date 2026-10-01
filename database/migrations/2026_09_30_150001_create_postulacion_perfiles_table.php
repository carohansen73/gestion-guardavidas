<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Datos "fijos" del postulante: una sola fila por persona, que se
     * precarga en cada inscripción nueva y que el postulante puede editar.
     * Nombre, apellido, DNI y email NO van acá: se leen de `users`.
     */
    public function up(): void
    {
        Schema::create('postulacion_perfiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('telefono', 30)->nullable();
            $table->string('direccion')->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('piso_dpto')->nullable();

            $table->date('fecha_nacimiento')->nullable();
            $table->string('genero', 30)->nullable();
            $table->enum('grupo_sanguineo', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->string('numero_libreta', 50)->nullable();

            $table->string('talle_remera', 10)->nullable();
            $table->string('talle_pantalon', 10)->nullable();
            $table->string('talle_campera', 10)->nullable();
            $table->string('talle_traje_bano', 10)->nullable();

            // Sin booleano: "tiene obra social" = obra_social_nombre no nulo.
            $table->string('obra_social_nombre')->nullable();
            $table->string('obra_social_numero_afiliado')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulacion_perfiles');
    }
};
