<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Quién cargó cada registro. Banderas e intervenciones ya lo guardaban; licencias, novedades de material y cambios
 * de turno no. Nullable: los registros anteriores quedan sin autor y solo los toca quien puede gestionar los ajenos
 * (ver App\Models\Concerns\TieneAutor). Si se borra el usuario, el registro se conserva.
 */
return new class extends Migration
{
    private const TABLAS = ['licencias', 'novedad_materials', 'cambio_de_turnos'];

    public function up(): void
    {
        foreach (self::TABLAS as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLAS as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};
