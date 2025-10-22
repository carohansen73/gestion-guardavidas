<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materiales = [
            ['nombre' => 'Camioneta', 'detalle' => 'Amarok'],
            ['nombre' => 'Camioneta', 'detalle' => 'Toyota Hilux'],
            ['nombre' => 'Cuatriciclo'],
            ['nombre' => 'Moto de agua'],
            ['nombre' => 'Refugio'],
            ['nombre' => 'Handy'],
            ['nombre' => 'Collarin'],
        ];

        foreach ($materiales as $m) {
            Material::create($m);
        }
    }
}
