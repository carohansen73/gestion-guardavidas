<?php

namespace App\Exports;

use App\Models\Guardavida;
use App\Services\HistorialAsistenciaService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AsistenciaPorGuardavidaExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{

    protected $guardavida;
    protected $inicio;
    protected $fin;

    public function __construct($guardavidaId, $inicio, $fin)
    {
        $this->guardavida = Guardavida::findOrFail($guardavidaId);
        $this->inicio = $inicio;
        $this->fin = $fin;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $service = new HistorialAsistenciaService();
        $historial = $service->generar($this->guardavida->id, $this->inicio, $this->fin);

         return collect($historial)->map(function ($h) {
            return [
                Carbon::parse($h['fecha'])->format('d/m/Y'),
                $h['estado'],
                // $h['detalle'] ?? '',
                $h['ingreso'],
                $h['egreso'],
                $h['puesto'],
            ];
        });
    }

    public function headings(): array
    {
        return ['Fecha', 'Estado', 'Ingreso', 'Egreso', 'Puesto'];
    }

    public function title(): string
    {
        return $this->guardavida->nombre . ' ' . $this->guardavida->apellido;
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $headerRange = 'A1:' . $highestColumn .'1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);

        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFB3C6E5');
    }



}
