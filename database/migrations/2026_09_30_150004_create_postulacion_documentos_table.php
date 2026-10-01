<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un archivo por fila. `tipo` (foto_personal, dni_frente, dni_dorso,
     * curriculum, libreta, antecedentes_penales, declaracion_jurada,
     * licencia_motonautica) lo valida la app, así un documento nuevo no
     * requiere migración. Los archivos van al disco `local` (privado).
     */
    public function up(): void
    {
        Schema::create('postulacion_documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('postulacion_id');
            $table->foreign('postulacion_id')->references('id')->on('postulaciones')->onDelete('cascade');
            $table->string('tipo', 40);
            $table->string('ruta');
            $table->string('nombre_original');
            $table->string('mime', 100);
            $table->unsignedInteger('tamano');
            $table->timestamps();

            $table->unique(['postulacion_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulacion_documentos');
    }
};
