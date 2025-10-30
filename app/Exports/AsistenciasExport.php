<?php
namespace App\Exports;
use App\Models\Licencia;
use App\Models\Guardavida;
use App\Models\Asistencias;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class LicenciasExport implements FromCollection, WithHeadings, WithMapping
{

protected $asistencias;


public function __construct ($asistencias){
 $this->asistencias = $asistencias;
}


}


