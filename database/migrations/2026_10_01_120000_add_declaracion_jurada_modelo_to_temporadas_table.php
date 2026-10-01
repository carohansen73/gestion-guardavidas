<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ruta (en el disco privado `local`) del PDF modelo de declaración jurada
     * de la temporada. Lo sube un admin desde la pantalla de Temporadas, así
     * cada temporada conserva su propio modelo (la declaración cambia de un
     * año al otro) y no hace falta acceso al servidor para actualizarlo.
     */
    public function up(): void
    {
        Schema::table('temporadas', function (Blueprint $table) {
            $table->string('declaracion_jurada_modelo')->nullable()->after('fecha_fin');
        });
    }

    public function down(): void
    {
        Schema::table('temporadas', function (Blueprint $table) {
            $table->dropColumn('declaracion_jurada_modelo');
        });
    }
};
