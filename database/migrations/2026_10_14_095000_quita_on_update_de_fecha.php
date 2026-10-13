<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * En MySQL/MariaDB con `explicit_defaults_for_timestamp` apagado, la PRIMERA columna TIMESTAMP de una tabla
 * (acá `fecha`) queda con `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`: editar cualquier otro campo
 * de una intervención, bandera o novedad pisaba su `fecha` con la hora de la edición. Como ahora la temporada
 * de cada registro sale de su fecha, eso la movería de temporada sin que nadie lo note.
 * Esto deja `fecha` como un TIMESTAMP común (con valor por defecto, sin autoactualización). Donde ya está bien
 * (MySQL 8, SQLite) no hace nada.
 */
return new class extends Migration
{
    private const TABLAS = ['intervencions', 'banderas', 'novedad_materials', 'novedades'];

    public function up(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        foreach (self::TABLAS as $tabla) {
            $extra = DB::table('information_schema.columns')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', $tabla)
                ->where('column_name', 'fecha')
                ->value('EXTRA');

            if ($extra !== null && stripos($extra, 'on update') !== false) {
                DB::statement("ALTER TABLE `{$tabla}` MODIFY `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
            }
        }
    }

    public function down(): void
    {
        // No se revierte: volver a la autoactualización reintroduciría el problema.
    }
};
