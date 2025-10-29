<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Playa;

class PlayaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Playa::insert([
            ['nombre' => 'Claromecó', 'lat' => -38.2500, "lon" => -60.1499, 'color' => 'cyan' ],
            ['nombre' => 'Dunamar' , 'lat' => -38.2000, "lon" => -60.1833, 'color' => 'fuchsia'],
            ['nombre' => 'Orense', 'lat' => -38.0444, "lon" => -60.2244, 'color' => 'purple'],
            ['nombre' => 'Reta', 'lat' => -38.1100, "lon" => -60.2889, 'color' => 'teal'],
        ]);
    }
}
