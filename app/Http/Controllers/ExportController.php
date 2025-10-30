<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PlayaMultiSheetExport;
use App\Exports\IntervencionesExport;
 use App\Exports\BanderasExport;
use App\Exports\CambiosDeTurnoExport;
use App\Exports\GuardavidasExport;
use App\Exports\LicenciasExport;

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

            default:
                abort(404, 'Tipo de export no válido');
        }


    }
}
