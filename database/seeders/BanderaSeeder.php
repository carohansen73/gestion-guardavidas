<?php

namespace Database\Seeders;

use App\Models\Bandera;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BanderaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Bandera::insert([
            ['codigo' => 'Mar bueno', 'color' => 'celeste'],
            ['codigo' => 'Dudoso', 'color' => 'amarillo-negro'],
            ['codigo' => 'Peligroso', 'color' => 'rojo-negro'],
            ['codigo' => 'Rayos', 'color' => 'negro'],
            ['codigo' => 'Prohibido bañarse', 'color' => 'rojo'],
        ]);
    }
}
