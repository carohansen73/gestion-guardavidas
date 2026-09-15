<?php

namespace App\Http\Controllers;

use App\Exports\AsistenciaGeneralExport;
use App\Exports\AsistenciaPorGuardavidaExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PlayaMultiSheetExport;
use App\Exports\IntervencionesExport;
 use App\Exports\BanderasExport;
use App\Exports\CambiosDeTurnoExport;
use App\Exports\GuardavidasExport;
use App\Exports\LicenciasExport;
use App\Exports\NovedadesDeMaterialesExport;
use Carbon\Carbon;
use App\Models\Guardavida;

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

        switch ($tipo) {
            case 'intervenciones':
                $exportClass = IntervencionesExport::class;
                $nombreArchivo = 'intervenciones.xlsx';
                return Excel::download(
                    new PlayaMultiSheetExport($exportClass),
                    $nombreArchivo
                );

            case 'banderas':
                $exportClass = BanderasExport::class;
                $nombreArchivo = 'banderas.xlsx';
                return Excel::download(
                    new PlayaMultiSheetExport($exportClass),
                    $nombreArchivo
                );

            case 'novedad-material':
                $exportClass = NovedadesDeMaterialesExport::class;
                $nombreArchivo = 'novedades-de-materiales.xlsx';
                return Excel::download(
                    new PlayaMultiSheetExport($exportClass),
                    $nombreArchivo
                );

            case 'guardavidas':
                $nombreArchivo = 'guardavidas.xlsx';
                return Excel::download(
                    new GuardavidasExport(), // 👈 se exporta todo junto
                    $nombreArchivo
                );

            case 'cambio-de-turno':
                $nombreArchivo = 'cambios-de-turno.xlsx';
                return Excel::download(
                    new CambiosDeTurnoExport(), // 👈 se exporta todo junto
                    $nombreArchivo
                );

            case 'licencias':
                $exportClass = LicenciasExport::class;
                $nombreArchivo = 'licencias.xlsx';
                return Excel::download(
                    new PlayaMultiSheetExport($exportClass),
                    $nombreArchivo
                );

            case 'asistencia-general':
                //Obtiene filtros de fechas por request
                $inicio = $request->inicio ? Carbon::parse($request->inicio)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
                $fin = $request->fin ? Carbon::parse($request->fin)->endOfDay() : Carbon::now()->endOfDay();


                //Genera excel separado por playa, de todos los guardavidas
                $exportClass = AsistenciaGeneralExport::class;
                $nombreArchivo = 'Asistencia-general-' . now()->format('Y-m-d') . '.xlsx';
                return Excel::download(
                    new PlayaMultiSheetExport($exportClass, [$inicio, $fin]),
                    $nombreArchivo
                );

            case 'asistencia-guardavida':
                 //Obtiene filtros de fechas y guardavida por request
                $guardavidaId = $request->guardavida_id;
                $guardavida = Guardavida::findOrFail($guardavidaId);

                $inicio = $request->inicio ? Carbon::parse($request->inicio)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
                $fin = $request->fin ? Carbon::parse($request->fin)->endOfDay() : Carbon::now()->endOfDay();

                //Genera excel de un único guardavida.
                $nombreArchivo = 'Asistencia-'.$guardavida->apellido . ', ' . $guardavida->nombre . now()->format('Y-m-d_His') . '.xlsx';
                return Excel::download(
                    new AsistenciaPorGuardavidaExport($guardavidaId, $inicio, $fin),
                    $nombreArchivo
                );

            default:
                abort(404, 'Tipo de export no válido');
        }


    }

}
