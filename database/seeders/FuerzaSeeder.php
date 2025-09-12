<?php

namespace Database\Seeders;

use App\Models\Fuerza;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FuerzaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Fuerza::insert([
            ['nombre' => 'Ambulancia'],
            ['nombre' => 'Bomberos'],
            ['nombre' => 'Policía'],
            ['nombre' => 'Inspección municipal'],
            ['nombre' => 'Prefectura']
        ]);
    }
}
