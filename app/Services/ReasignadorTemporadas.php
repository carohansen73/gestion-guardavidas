<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Recalcula `temporada_id` de todos los registros operativos a partir de su fecha, con la misma regla
 * que Temporada::paraDato (la última temporada que ya había empezado ese día; si es anterior a todas, la
 * primera). Se usa cuando se crea una temporada o cambia su fecha de inicio, y desde el botón
 * "Recalcular" de la pantalla de Temporadas. Escribe con una sola consulta por tabla, a propósito: no
 * pasa por los eventos del modelo.
 */
class ReasignadorTemporadas
{
    /** tabla => columna de fecha que decide la temporada. */
    public const TABLAS = [
        'intervencions' => 'fecha',
        'banderas' => 'fecha',
        'novedad_materials' => 'fecha',
        'licencias' => 'fecha_inicio',
        'cambio_de_turnos' => 'fecha',
        'asistencias' => 'fecha_hora',
        'novedades' => 'fecha',
    ];

    /** @return array<string,int> tabla => cantidad de registros cuya temporada cambió */
    public function reasignar(): array
    {
        $cambios = [];

        DB::transaction(function () use (&$cambios) {
            $hayTemporadas = DB::table('temporadas')->exists();

            foreach (self::TABLAS as $tabla => $columna) {
                if (! $hayTemporadas) {
                    $cambios[$tabla] = DB::table($tabla)->whereNotNull('temporada_id')->update(['temporada_id' => null]);

                    continue;
                }

                $esperada = "COALESCE(
                    (SELECT t.id FROM temporadas t WHERE DATE(t.fecha_inicio) <= DATE({$tabla}.{$columna}) ORDER BY t.fecha_inicio DESC LIMIT 1),
                    (SELECT t2.id FROM temporadas t2 ORDER BY t2.fecha_inicio ASC LIMIT 1)
                )";

                // `{$columna} = {$columna}` a propósito: en una base con la columna TIMESTAMP "ON UPDATE CURRENT_TIMESTAMP"
                // (ver la migración quita_on_update_de_fecha) este UPDATE le pisaría la fecha con la hora actual.
                $cambios[$tabla] = DB::update(
                    "UPDATE {$tabla} SET temporada_id = {$esperada}, {$columna} = {$columna} WHERE temporada_id IS NULL OR temporada_id <> {$esperada}"
                );
            }
        });

        return $cambios;
    }
}
