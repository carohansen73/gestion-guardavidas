<?php

namespace App\Exports;

use App\Models\Intervencion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class IntervencionesExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithStyles
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
        return Intervencion::with(['puesto', 'bandera', 'user', 'guardavidas', 'fuerzas'])
        ->where('playa_id', $this->playa->id)
        ->orderBy('fecha', 'desc')
        ->get();
    }

    public function map($i): array
    {
        return [
            optional($i->fecha)->format('d/m/Y'),
            $i->tipo_intervencion,
            $i->victimas,
            $i->codigo,
            optional($i->bandera)->nombre ?? '—',
            $i->traslado ? 'Sí' : 'No',
            optional($i->puesto)->nombre ?? '—',
            $i->detalles,
            $i->guardavidas
                ->map(fn($g) => "{$g->nombre} {$g->apellido}")
                ->implode(', '),
            $i->fuerzas->pluck('nombre')->implode(', '),
            optional($i->user)->name ?? '—',
        ];
    }


      public function headings(): array
    {
        return [
            'Fecha',
            'Tipo de intervención',
            'Víctimas',
            'Código',
            'Bandera',
            'Traslado',
            'Puesto',
            'Detalles',
            'Guardavidas',
            'Fuerzas',
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
