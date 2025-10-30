<?php

namespace App\Exports;

use App\Models\NovedadMaterial;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NovedadesDeMaterialesExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithStyles
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
        return NovedadMaterial::with(['material', 'playa'])
        ->where('playa_id', $this->playa->id)
        ->orderBy('fecha', 'desc')
        ->get();
    }

    public function map($i): array {
        return [
            optional($i->fecha)->format('d/m/Y'),
             optional($i->tipo_novedad)->value ?? '—',

            optional($i->material)->nombre ?? '—',
            optional($i->playa)->nombre ?? '—',
            $i->detalles,
        ];
    }

    public function headings(): array {
        return [
            'Fecha',
            'Novedad',
            'Material',
            'Playa',
            'Detalles',
        ];
    }

    public function title(): string {
        return $this->playa->nombre;
    }

    public function styles(Worksheet $sheet) {
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
