<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marca un fichaje como 'valido' o 'fuera_de_rango' (GPS a más de 200m
     * del puesto, descontando el margen de error del propio GPS) en vez de
     * bloquearlo. 'fuera_de_rango' queda para que un encargado/admin lo
     * revise, no impide que el guardavida registre su presencia.
     */
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->string('estado_validacion')->default('valido')->after('fecha_hora');
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropColumn('estado_validacion');
        });
    }
};
