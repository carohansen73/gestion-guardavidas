<?php
namespace App\Exports;
use App\Models\Licencia;
use App\Models\Guardavida;
use App\Models\Asistencia;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;
class AsistenciasExport implements FromCollection, WithHeadings,WithMapping
{

    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        $guardavidas = Guardavida::with(['puesto.playa', 'asistencias'])->get();
        $data = collect();

        foreach ($guardavidas as $g) {

            // === ASISTENCIAS dentro del rango ===
            $asistencias = $g->asistencias()
                ->whereBetween('fecha_hora', [$this->fechaInicio, $this->fechaFin])
                ->get();

            // === LICENCIAS dentro del rango ===
            $licencias = Licencia::where('guardavida_id', $g->id)
                ->where(function ($query) {
                    $query->whereBetween('fecha_inicio', [$this->fechaInicio, $this->fechaFin])
                        ->orWhereBetween('fecha_fin', [$this->fechaInicio, $this->fechaFin])
                        ->orWhere(function ($q) {
                            $q->where('fecha_inicio', '<=', $this->fechaInicio)
                                ->where('fecha_fin', '>=', $this->fechaFin);
                        });
                })
                ->get();

            // === CASO SIN REGISTROS ===
            if ($asistencias->isEmpty() && $licencias->isEmpty()) {
                $data->push([
                    'guardavida' => $g->nombre . ' ' . $g->apellido,
                    'dni' => $g->dni,
                    'puesto' => $g->puesto->nombre_puesto ?? '-',
                    'playa' => $g->puesto->playa->nombre_playa ?? '-',
                    'fecha' =>  $g->fecha_hora ? $g->fecha_hora->format('Y-m-d') :'Sin registros',
                    'tipo' => 'No presente',
                    'hora_entrada' => '-',
                    'hora_salida' => '-',
                    'detalle_licencia' => '-'
                ]);
            }

            // === ASISTENCIAS ===
            foreach ($asistencias as $a) {
                $data->push([
                    'guardavida' => $g->nombre . ' ' . $g->apellido,
                    'dni' => $g->dni,
                    'puesto' => $g->puesto->nombre_puesto ?? '-',
                    'playa' => $g->puesto->playa->nombre_playa ?? '-',
                    'fecha' => $a->fecha_hora ? $a->fecha_hora->format('Y-m-d') : '-',
                    'tipo' => 'Asistencia',
                    'hora_entrada' => $a->hora_entrada ?? '-',
                    'hora_salida' => $a->hora_salida ?? '-',
                    'detalle_licencia' => '-'
                ]);
            }

            // === LICENCIAS ===
            foreach ($licencias as $l) {
                $data->push([
                    'guardavida' => $g->nombre . ' ' . $g->apellido,
                    'dni' => $g->dni,
                    'puesto' => $g->puesto->nombre_puesto ?? '-',
                    'playa' => $g->puesto->playa->nombre_playa ?? '-',
                    'fecha' => $l->fecha_inicio . ' al ' . $l->fecha_fin,
                    'tipo' => 'Licencia',
                    'hora_entrada' => '-',
                    'hora_salida' => '-',
                    'detalle_licencia' => $l->tipo_licencia . ' - ' . ($l->detalle ?? '-')
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Guardavida',
            'DNI',
            'Puesto',
            'Playa',
            'Fecha',
            'Tipo',
            'Hora Entrada',
            'Hora Salida',
            'Detalle Licencia'
        ];
    }

    public function map($row): array
    {
        // Como ya devolvemos arrays arriba, solo devolvemos el mismo
        return $row;
    }
}

