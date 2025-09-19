<?php

namespace Database\Seeders;

use App\Models\Puesto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PuestoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Puesto::insert([
            ['nombre' => 'Samoa', 'playa_id' => '1'],
            ['nombre' => 'Anexo', 'playa_id' => '1'],
            ['nombre' => 'Nahuel Epu', 'playa_id' => '1'],
            ['nombre' => '37', 'playa_id' => '1'],
            ['nombre' => 'Cazadores', 'playa_id' => '1'],
            ['nombre' => 'Plumas Verdes', 'playa_id' => '1'],
            ['nombre' => 'Bagre (Timoneles)', 'playa_id' => '1'],
            ['nombre' => 'Base Claromeco', 'playa_id' => '1'],
            ['nombre' => 'Otro', 'playa_id' => '1'],
            ['nombre' => 'Fuera zona de baño', 'playa_id' => '1'],

            ['nombre' => 'Base Dunamar', 'playa_id' => '2'],
            ['nombre' => 'Kuyen', 'playa_id' => '2'],
            ['nombre' => 'Boreli', 'playa_id' => '2'],
            ['nombre' => 'Puesto Foca', 'playa_id' => '2'],
            ['nombre' => 'Otro', 'playa_id' => '2'],
            ['nombre' => 'Fuera zona de baño', 'playa_id' => '2'],

            ['nombre' => 'Piedras Blancas', 'playa_id' => '3'],
            ['nombre' => 'Califa', 'playa_id' => '3'],
            ['nombre' => 'Refugio', 'playa_id' => '3'],
            ['nombre' => 'Otro', 'playa_id' => '3'],
            ['nombre' => 'Fuera zona de baño', 'playa_id' => '3'],

            ['nombre' => 'Victor 1', 'playa_id' => '4'],
            ['nombre' => 'Victor 2', 'playa_id' => '4'],
            ['nombre' => 'Victor 4', 'playa_id' => '4'],
            ['nombre' => 'Quincho', 'playa_id' => '4'],
            ['nombre' => 'Walters', 'playa_id' => '4'],
            ['nombre' => 'Otro', 'playa_id' => '4'],
            ['nombre' => 'Fuera zona de baño', 'playa_id' => '4'],
        ]);
    }
}
