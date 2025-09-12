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
            ['nombre' => 'Claromecó'],
            ['nombre' => 'Dunamar'],
            ['nombre' => 'Orense'],
            ['nombre' => 'Reta'],
        ]);
    }
}
