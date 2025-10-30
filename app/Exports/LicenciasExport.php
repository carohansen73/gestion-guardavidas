<?php

namespace App\Exports;

use App\Models\Licencia;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LicenciasExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithStyles
{
    protected $playa;

    public function __construct($playa)
    {
        $this->playa = $playa;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {

       return Licencia::select(['licencias.*'])
        ->join('guardavidas', 'licencias.guardavida_id', '=', 'guardavidas.id')
        ->orderBy('licencias.playa_id')
        ->orderBy('guardavidas.apellido')
        ->orderBy('guardavidas.nombre')
        ->with(['guardavida', 'playa', 'puesto'])
        ->get();
    }

    public function map($i): array {
        return [
            optional($i->fecha_inicio)->format('d/m/Y'),
            optional($i->fecha_fin)->format('d/m/Y'),
            $i->tipo_licencia,
            optional($i->guardavida)->apellido . ' ' . optional($i->guardavida)->nombre ?? '—',
            $i->en_tiempo ? 'Sí' : 'No',
            $i->turno,
            optional($i->playa)->nombre ?? '—',
            optional($i->puesto)->nombre ?? '—',
            $i->detalle,
        ];
    }

    public function headings(): array {
        return [
            'Fecha inicio',
            'Fecha fin',
            'Licencia',
            'Guardavidad',
            'En timepo',
            'Turno',
            'Playa',
            'Puesto',
            'Detalle',
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










