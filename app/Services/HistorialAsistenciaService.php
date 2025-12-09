<?php
namespace App\Services;

use App\Models\Asistencia;
use App\Models\Licencia;
use Carbon\Carbon;

class HistorialAsistenciaService{
    public function generar($guardavidaId, $inicio, $fin){

         // Asistencias del rango
        $asistencias = Asistencia::where('guardavidas_id', $guardavidaId)
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->orderBy('fecha_hora')
            ->get()
            ->groupBy(fn($a) => Carbon::parse($a->fecha_hora)->toDateString());

        // Licencias del rango
        $licencias = Licencia::where('guardavida_id', $guardavidaId)
            ->where(function($q) use ($inicio, $fin) {
                $q->whereBetween('fecha_inicio', [$inicio, $fin])
                ->orWhereBetween('fecha_fin', [$inicio, $fin])
                ->orWhere(function ($q2) use ($inicio, $fin) {
                    $q2->where('fecha_inicio', '<=', $inicio)
                        ->where('fecha_fin', '>=', $fin);
                });
            })
            ->get();

        // Construcción del historial día x día
        $historial = [];

        for ($fecha = $inicio->copy(); $fecha->lte($fin); $fecha->addDay()) {

            $dateString = $fecha->toDateString();

            // 1- Verifica si hay licencia ese día
            $licencia = $licencias->first(function ($l) use ($fecha) {
                return $fecha->between($l->fecha_inicio, $l->fecha_fin);
            });

            if ($licencia) {
                $historial[] = [
                    'fecha' => $dateString,
                    'estado' => 'LICENCIA',
                    'detalle' => $licencia->tipo_licencia,
                    'ingreso' => null,
                    'egreso' => null,
                    'puesto' => $licencia->puesto->nombre ?? '-'
                ];
                continue;
            }

            // 2- Verifica si hay asistencia ese día
            $asistencia = $asistencias->get($dateString);

            if ($asistencia) {
                $historial[] = [
                    'fecha' => $dateString,
                    'estado' => 'ASISTIÓ',
                    'ingreso' => $asistencia->first()->fecha_hora,
                    'egreso' => $asistencia->last()->fecha_hora,
                    'puesto' => $asistencia->first()->puesto->nombre ?? '-',
                ];
                continue;
            }

            // 3- Si no hay ni licencia ni asistencia → FALTÓ
            $historial[] = [
                'fecha' => $dateString,
                'estado' => 'FALTA',
                'ingreso' => null,
                'egreso' => null,
                'puesto' => '-',
            ];
        }

        return $historial;
    }
}
