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
            ['codigo' => 'Mar bueno', 'color' => 'sky', 'borde' => 'sky'],
            ['codigo' => 'Dudoso', 'color' => 'yellow', 'borde' => 'black'],
            ['codigo' => 'Peligroso', 'color' => 'black', 'borde' => 'red'],
            ['codigo' => 'Rayos', 'color' => 'black', 'borde' => 'black'],
            ['codigo' => 'Prohibido bañarse', 'color' => 'red', 'borde' => 'red'],
            ['codigo' => 'Niño perdido', 'color' => 'white', 'borde' => 'white'],
        ]);
    }
}
