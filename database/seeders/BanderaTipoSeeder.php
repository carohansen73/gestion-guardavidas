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
            ['codigo' => 'Mar bueno', 'color' => 'bandera-bueno' ],
            ['codigo' => 'Dudoso', 'color' => 'bandera-dudoso' ],
            ['codigo' => 'Peligroso', 'color' => 'bandera-peligroso' ],
            ['codigo' => 'Rayos', 'color' => 'bandera-rayos' ],
            ['codigo' => 'Prohibido bañarse', 'color' => 'bandera-prohibido' ],
            ['codigo' => 'Niño perdido', 'color' => 'bandera-perdido' ],
        ]);
    }
}
