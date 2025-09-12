<?php

namespace Database\Seeders;

use App\Models\LicenciaTipo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LicenciaTipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          LicenciaTipo::insert([
            ['nombre' => 'Enfermedad'],
            ['nombre' => 'Lesión'],
            ['nombre' => 'Licencia médica'],
            ['nombre' => 'Examen'],
            ['nombre' => 'Evento deportivo']
        ]);
    }
}
