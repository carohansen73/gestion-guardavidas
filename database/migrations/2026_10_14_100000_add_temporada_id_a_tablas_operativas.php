<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase 2: cada registro operativo pertenece a una temporada. La columna la completa el trait
 * App\Models\Concerns\TieneTemporada a partir de la fecha del propio registro.
 * Nullable: solo queda NULL si no hay ninguna temporada cargada. FK restrict: no se borra una
 * temporada con registros asociados.
 */
return new class extends Migration
{
    private const TABLAS = [
        'intervencions',
        'banderas',
        'novedad_materials',
        'licencias',
        'cambio_de_turnos',
        'asistencias',
        'novedades',
    ];

    public function up(): void
    {
        foreach (self::TABLAS as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->foreignId('temporada_id')->nullable()->constrained('temporadas')->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLAS as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropConstrainedForeignId('temporada_id');
            });
        }
    }
};
