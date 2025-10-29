<?php

namespace App\Exports;

use App\Models\Bandera;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class BanderasExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithStyles
{

    protected $playa;

    public function __construct($playa)
    {
        $this->playa = $playa;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
         return Bandera::with(['user', 'playa', 'bandera'])
        ->where('playa_id', $this->playa->id)
        ->orderBy('fecha', 'desc')
        ->get();
    }


    public function map($i): array
    {
        return [
            optional($i->fecha)->format('d/m/Y'),
            $i->turno,
            optional($i->bandera)->codigo ?? '—',
            $i->viento_direccion,
            $i->viento_intensidad,
            $i->temperatura,
            $i->detalles,
            optional($i->user)->lastname . ' ' . optional($i->user)->name ?? '—'
        ];
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Turno',
            'Bandera',
            'Viento dir.',
            'Viento int.',
            'Tº',
            'Detalles',
            'Registrado por',
        ];
    }

    public function title(): string
    {
        return $this->playa->nombre;
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

        // Ejemplo: color de texto rojo para toda la columna B
        // $sheet->getStyle('B2:B'.$sheet->getHighestRow())
        //     ->getFont()->getColor()->setARGB('FFFF0000');
    }
}
