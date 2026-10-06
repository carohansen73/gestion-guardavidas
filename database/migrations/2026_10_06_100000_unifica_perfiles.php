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
        });
    }

    public function down(): void
    {
        Schema::rename('perfiles', 'postulacion_perfiles');
    }
};
