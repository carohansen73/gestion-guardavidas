<?php

namespace Database\Seeders;

use App\Models\BanderaTipo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BanderaTipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         BanderaTipo::insert([
            ['codigo' => 'Mar bueno', 'color' => 'celeste'],
            ['codigo' => 'Dudoso', 'color' => 'amarillo-negro'],
            ['codigo' => 'Peligroso', 'color' => 'rojo-negro'],
            ['codigo' => 'Rayos', 'color' => 'negro'],
            ['codigo' => 'Prohibido bañarse', 'color' => 'rojo'],
        ]);
    }
}
