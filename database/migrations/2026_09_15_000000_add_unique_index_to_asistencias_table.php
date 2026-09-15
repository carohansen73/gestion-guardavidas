<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Evita fichajes duplicados: un mismo guardavida no puede tener dos
     * asistencias en el mismo puesto con la misma fecha_hora exacta. Esto
     * cubre el caso de reintentos de sincronización offline, que reenvían
     * la misma fecha_hora original en cada intento.
     */
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->unique(
                ['guardavidas_id', 'puesto_id', 'fecha_hora'],
                'asistencias_guardavida_puesto_fecha_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropUnique('asistencias_guardavida_puesto_fecha_unique');
        });
    }
};
