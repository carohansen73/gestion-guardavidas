<?php

namespace App\Exports;

use App\Models\CambioDeTurno;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CambiosDeTurnoExport implements FromCollection, WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $CambioDeTurno = CambioDeTurno::select(['cambio_de_turnos.*'])
        ->join('guardavidas', 'cambio_de_turnos.guardavida_id', '=', 'guardavidas.id')
        ->orderBy('cambio_de_turnos.playa_id')
        ->orderBy('guardavidas.apellido')
        ->orderBy('guardavidas.nombre')
        ->with(['guardavida', 'playa', 'puesto'])
        ->get();

        return $CambioDeTurno->map(function ($ct) {
            return [
                'fecha' =>  optional($ct->fecha)->format('d/m/Y'),
                'nombre' => $ct->guardavida->nombre,
                'apellido' => $ct->guardavida->apellido,
                'funcion' => $ct->funcion,
                'turno' => $ct->turno_nuevo,
                'playa' => $ct->playa->nombre ?? 'Sin asignar',
                'puesto' => $ct->puesto->nombre ?? 'Sin asignar',
            ];
        });
    }

     /**
     * Agrega cabeceras al excel de cambio de turno
     *
     * @return array
     */
     public function headings(): array
    {
        return [
            'Fecha',
            'Nombre',
            'Apellido',
            'Funcion',
            'Turno nuevo',
            'Playa',
            'Puesto',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Negrita para la fila 1 (encabezados)
        $highestColumn = $sheet->getHighestColumn();
        $headerRange = 'A1:' . $highestColumn .'1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);

        // Color de fondo azul claro en encabezados
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFB3C6E5');

    }

}














