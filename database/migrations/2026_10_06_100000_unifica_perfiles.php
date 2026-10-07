<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Un único perfil personal por usuario: `postulacion_perfiles` pasa a
 * llamarse `perfiles` (lo usan postulantes y guardavidas). Las columnas
 * telefono/direccion/numero de `guardavidas` dejan de ser obligatorias porque
 * ya no se escriben ahí (se borrarán en un paso posterior).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('postulacion_perfiles', 'perfiles');

        Schema::table('guardavidas', function (Blueprint $table) {
            $table->string('telefono')->nullable()->change();
            $table->string('direccion')->nullable()->change();
            $table->string('numero')->nullable()->change();
            // nombre/apellido/dni ya no se escriben acá desde la Fase 3b (viven en `users`);
            // en MySQL ya son opcionales, esto alinea el esquema de las migraciones (tests).
            $table->string('nombre')->nullable()->change();
            $table->string('apellido')->nullable()->change();
            $table->string('dni')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::rename('perfiles', 'postulacion_perfiles');
    }
};
