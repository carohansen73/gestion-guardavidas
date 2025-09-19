<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categoria::insert([
            ['nombre' => 'Intervenciones'],
            ['nombre' => 'Banderas'],
            ['nombre' => 'Novedades de materiales'],
            ['nombre' => 'Licencia'],
            ['nombre' => 'Cambio de turno']
        ]);
    }
}
