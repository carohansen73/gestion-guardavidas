<?php

namespace App\Http\Controllers;

use App\Exports\AsistenciaGeneralExport;
use App\Exports\AsistenciaPorGuardavidaExport;
use App\Exports\BanderasExport;
use App\Exports\CambiosDeTurnoExport;
use App\Exports\GuardavidasExport;
use App\Exports\IntervencionesExport;
use App\Exports\LicenciasExport;
use App\Exports\NovedadesDeMaterialesExport;
use App\Exports\PlayaMultiSheetExport;
use App\Models\Guardavida;
use App\Models\Temporada;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    // public function exportPorPlaya(Request $request)
    // {
    //     $tipo = $request->get('tipo');

    //     switch ($tipo) {
    //         case 'intervenciones':
    //             $exportClass = IntervencionesExport::class;
    //             $nombreArchivo = 'intervenciones.xlsx';
    //             break;

    //         case 'banderas':
    //             $exportClass = BanderasExport::class;
    //             $nombreArchivo = 'banderas.xlsx';
    //             break;

    //         case 'guardavidas':
    //             $exportClass = GuardavidasExport::class;
    //             $nombreArchivo = 'guardavidas.xlsx';
    //             break;

    //         default:
    //             abort(404, 'Tipo de export no válido');
    //     }

    //     return Excel::download(
    //         new PlayaMultiSheetExport($exportClass),
    //         $nombreArchivo
    //     );
    // }

    public function exportPorPlaya(Request $request)
    {

        $tipo = $request->get('tipo');

        // Cada Excel exige el permiso de la vista de la que sale: quien puede ver el listado
        // puede exportarlo, y quien no, no. Un tipo desconocido cae en el 404 de abajo.
        $permisoPorTipo = [
            'intervenciones' => 'ver_intervencion',
            'banderas' => 'ver_bandera',
            'novedad-material' => 'ver_novedad_material',
            'guardavidas' => 'ver_guardavida',
            'cambio-de-turno' => 'ver_cambio_turno',
            'licencias' => 'ver_licencia',
            'asistencia-general' => 'ver_asistencia',
            'asistencia-guardavida' => 'ver_asistencia',
        ];
        if (isset($permisoPorTipo[$tipo])) {
            abort_unless($request->user()->can($permisoPorTipo[$tipo]), 403, 'No tenés permiso para exportar esto.');
        }

        // Temporada del Excel: `?temporada=ID`, `todas` o, sin parámetro, la vigente (la misma que
        // muestran los listados por defecto). El nombre del archivo la incluye.
        [, $temporada] = Temporada::seleccionada($request->query('temporada'));
        $sufijoTemporada = '-'.($temporada ? Str::slug($temporada->nombre) : 'todas-las-temporadas');

        switch ($tipo) {
            case 'intervenciones':
                $exportClass = IntervencionesExport::class;
                $nombreArchivo = "intervenciones{$sufijoTemporada}.xlsx";

                return Excel::download(
                    new PlayaMultiSheetExport($exportClass, [$temporada]),
                    $nombreArchivo
                );

            case 'banderas':
                $exportClass = BanderasExport::class;
                $nombreArchivo = "banderas{$sufijoTemporada}.xlsx";

                return Excel::download(
                    new PlayaMultiSheetExport($exportClass, [$temporada]),
                    $nombreArchivo
                );

            case 'novedad-material':
                $exportClass = NovedadesDeMaterialesExport::class;
                $nombreArchivo = "novedades-de-materiales{$sufijoTemporada}.xlsx";

                return Excel::download(
                    new PlayaMultiSheetExport($exportClass, [$temporada]),
                    $nombreArchivo
                );

            case 'guardavidas':
                $nombreArchivo = 'guardavidas.xlsx';

                return Excel::download(
                    new GuardavidasExport, // 👈 se exporta todo junto
                    $nombreArchivo
                );

            case 'cambio-de-turno':
                $nombreArchivo = "cambios-de-turno{$sufijoTemporada}.xlsx";

                return Excel::download(
                    new CambiosDeTurnoExport($temporada), // 👈 se exporta todo junto
                    $nombreArchivo
                );

            case 'licencias':
                $exportClass = LicenciasExport::class;
                $nombreArchivo = "licencias{$sufijoTemporada}.xlsx";

                return Excel::download(
                    new PlayaMultiSheetExport($exportClass, [$temporada]),
                    $nombreArchivo
                );

            case 'asistencia-general':
                // Obtiene filtros de fechas por request
                $inicio = $request->inicio ? Carbon::parse($request->inicio)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
                $fin = $request->fin ? Carbon::parse($request->fin)->endOfDay() : Carbon::now()->endOfDay();

                // Genera excel separado por playa, de todos los guardavidas
                $exportClass = AsistenciaGeneralExport::class;
                $nombreArchivo = 'Asistencia-general-'.now()->format('Y-m-d').'.xlsx';

                return Excel::download(
                    new PlayaMultiSheetExport($exportClass, [$inicio, $fin]),
                    $nombreArchivo
                );

            case 'asistencia-guardavida':
                // Obtiene filtros de fechas y guardavida por request
                $guardavidaId = $request->guardavida_id;
                $guardavida = Guardavida::findOrFail($guardavidaId);

                $inicio = $request->inicio ? Carbon::parse($request->inicio)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
                $fin = $request->fin ? Carbon::parse($request->fin)->endOfDay() : Carbon::now()->endOfDay();

                // Genera excel de un único guardavida.
                $nombreArchivo = 'Asistencia-'.$guardavida->apellido.', '.$guardavida->nombre.now()->format('Y-m-d_His').'.xlsx';

                return Excel::download(
                    new AsistenciaPorGuardavidaExport($guardavidaId, $inicio, $fin),
                    $nombreArchivo
                );

            default:
                abort(404, 'Tipo de export no válido');
        }

    }
}
