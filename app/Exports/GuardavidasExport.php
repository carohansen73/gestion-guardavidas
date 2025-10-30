<?php

namespace App\Exports;

use App\Models\Guardavida;
use Maatwebsite\Excel\Concerns\FromCollection;
//Para encabezados personalizados
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class GuardavidasExport implements FromCollection, WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return Guardavida::all();
        //o seleccionando columnas
        //return Guardavida::select('nombre', 'dni', 'email')->get();

        $guardavidas = Guardavida::with(['user', 'playa', 'puesto'])->orderBy('playa_id')->orderBy('apellido')->orderBy('nombre')->get();

        return $guardavidas->map(function ($g) {
            return [
                'nombre' => $g->user->name,
                'apellido' => $g->user->lastname,
                'email' => $g->user->email,
                'dni' => $g->dni,
                'telefono' => $g->telefono,
                'direccion' => $g->direccion,
                'numero' => $g->numero,
                'piso_dpto' => $g->piso_dpto,
                'funcion' => $g->funcion,
                'turno' => $g->turno,
                'playa' => $g->playa->nombre ?? 'Sin asignar',
                'puesto' => $g->puesto->nombre ?? 'Sin asignar',
            ];
        });

    }

    /**
     * Agrega cabeceras al excel de guardavidas
     *
     * @return array
     */
     public function headings(): array
    {
        return [
             'Nombre',
            'Apellido',
            'Email',
            'DNI',
            'Telefono',
            'Direccion',
            'Nro.',
            'Piso - dpto',
            'Funcion',
            'Turno',
            'Playa',
            'Puesto',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Negrita para la fila 1 (encabezados)
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        // Color de fondo azul claro en encabezados
        $sheet->getStyle('A1:L1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFB3C6E5');

        // Ejemplo: color de texto rojo para toda la columna B
        // $sheet->getStyle('B2:B'.$sheet->getHighestRow())
        //     ->getFont()->getColor()->setARGB('FFFF0000');
    }
}
