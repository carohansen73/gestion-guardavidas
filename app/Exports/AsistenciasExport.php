<?php
namespace App\Exports;
use App\Models\Licencia;
use App\Models\Guardavida;
use App\Models\Asistencia;
//use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;
class LicenciasExport implements FromCollection, WithHeadings
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
        $guardavidas = Guardavida::with(['puesto.playa'])->get();
        $data = collect();

        foreach ($guardavidas as $g) {
            // Asistencias dentro del rango
            $asistencias = $g->asistencias()
                ->whereBetween('fecha_hora', [$this->fechaInicio, $this->fechaFin])
                ->get();

            // Licencias dentro del rango
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

            // Si no tiene nada en el rango
            if ($asistencias->isEmpty() && $licencias->isEmpty()) {
                $data->push([
                    'guardavida' => $g->nombre . ' ' . $g->apellido,
                    'dni' => $g->dni,
                    'puesto' => $g->puesto->nombre_puesto ?? '-',
                    'playa' => $g->puesto->playa->nombre_playa ?? '-',
                    'fecha' => 'Sin registros',
                    'tipo' => 'No presente',
                    'hora_entrada' => '-',
                    'hora_salida' => '-',
                    'detalle_licencia' => '-'
                ]);
            }

            // Asistencias
            foreach ($asistencias as $a) {
                $data->push([
                    'guardavida' => $g->nombre . ' ' . $g->apellido,
                    'dni' => $g->dni,
                    'puesto' => $g->puesto->nombre_puesto ?? '-',
                    'playa' => $g->puesto->playa->nombre_playa ?? '-',
                    'fecha' => $a->fecha_hora->format('Y-m-d'),
                    'tipo' => 'Asistencia',
                    'hora_entrada' => $a->hora_entrada ?? '-',
                    'hora_salida' => $a->hora_salida ?? '-',
                    'detalle_licencia' => '-'
                ]);
            }

            // Licencias
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
                    'detalle_licencia' => $l->tipo_licencia . ' - ' . $l->detalle
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
}

