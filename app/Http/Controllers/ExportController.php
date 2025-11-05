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
use App\Exports\AsistenciasExport;
use App\Exports\NovedadesDeMaterialesExport;
use Carbon\Carbon;
use App\Models\Asistencia;
use App\Models\Guardavida;
use App\Models\Licencia;

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

            default:
                abort(404, 'Tipo de export no válido');
        }


    }

    /**
     * Metodo Excel para asistencias con licencias


    El Excel incluirá todas las asistencias y licencias por guardavida dentro del rango.

    Los guardavidas sin registros mostrarán "No presente".

    Tendrás columnas completas: Guardavida, DNI, Puesto, Playa, Fecha, Tipo, Horas y Detalle.

    Se genera con nombre asistencias_y_licencias_YYYY-MM-DD_HHMMSS.xlsx.
     */

    public function exportAsistenciasPorDia(Request $request)
    {
        // Guardar sesión antes de comenzar export (evita bloqueo)
        $request->session()->save();
        // Validación
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        // Fechas desde el request
        $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->input('fecha_fin'))->endOfDay();

       // Exportar directamente (la clase hace toda la lógica)
    $nombreArchivo = 'asistencias_y_licencias_' . now()->format('Y-m-d_His') . '.xlsx';

    return Excel::download(new AsistenciasExport($fechaInicio, $fechaFin), $nombreArchivo);
    }









/**metodo viejo antes de la modificacion */
/*
     public function exportAsistenciasPorDia(Request $request)
     {
         $request->validate([
             'fecha_inicio' => 'required|date',
             'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $query= Asistencia::query();




         if($request->filled('fecha')){
            $inicio=$request->input('fecha_inicio');
            $query->where('fecha', 'LIKE', "%{$inicio}%");

         }

         if($request->filled('fecha')){
            $fin = $request->input('fecha_fin');
            $query->where('fecha', 'LIKE', "%{$fin}%");
         }

         $fecha= timezone_open();
         $asistencias = $query->get();
         if($asistencias->isEmpty()){
            $mensaje = [
                'titulo' => '¡Error!',
                'detalle' => 'No se encontraron registros para las fechas seleccionadas.'
            ];
            return back()->with('error', $mensaje);
         }else{

                  return Excel::download(new AsistenciasExport(($asistencias)) ,"asistencias_$fecha.xlsx");

         }
    }
         */
}
