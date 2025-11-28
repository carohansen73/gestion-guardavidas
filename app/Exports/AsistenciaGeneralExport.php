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

class AsistenciaGeneralExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $playa;
    protected $inicio;
    protected $fin;

    public function __construct($playaId, $inicio, $fin)
    {
        $this->playa = $playaId;
        $this->inicio = $inicio;
        $this->fin = $fin;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //Instancia al service
        $service = new HistorialAsistenciaService();

        //busca los guardavidas habilitados que pertenecen a esa playa
        $guardavidas = Guardavida::where('playa_id', $this->playa->id)
            ->whereHas('user', fn($q) => $q->where('enabled', true))
            ->orderBy('apellido')
            ->get();

        $rows = [];

        //Genera el historial de asistencias por cada guardavida
        foreach ($guardavidas as $g) {
            $historial = $service->generar($g->id, $this->inicio, $this->fin);

            foreach ($historial as $h) {
                $rows[] = [
                    $g->nombre . ' ' . $g->apellido,
                    Carbon::parse($h['fecha'])->format('d/m/Y'),
                    $h['estado'],
                    // $h['detalle'] ?? '',
                    $h['ingreso'],
                    $h['egreso'],
                    $h['puesto'],
                ];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return ['Guardavida', 'Fecha', 'Estado', 'Ingreso', 'Egreso', 'Puesto'];
    }

    public function title(): string
    {
        return $this->playa->nombre;
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
