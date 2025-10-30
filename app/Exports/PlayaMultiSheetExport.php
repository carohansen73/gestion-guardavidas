<?php

namespace App\Exports;

use App\Models\Playa;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PlayaMultiSheetExport implements WithMultipleSheets {

    protected $exportClass;
    protected $extraParams;

    /**
     * @param string $exportClass Clase exportadora
     * @param array $extraParams Parámetros extra
     */
    public function __construct(string $exportClass, array $extraParams = [])
    {
        $this->exportClass = $exportClass;
        $this->extraParams = $extraParams;
    }

    public function sheets(): array
    {
        $sheets = [];
        $playas = Playa::all();

        foreach ($playas as $playa) {
            $sheets[] = new $this->exportClass($playa, ...$this->extraParams);
        }

        return $sheets;
    }
}
