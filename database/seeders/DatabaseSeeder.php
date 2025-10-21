<?php

namespace Database\Seeders;

use App\Models\LicenciaTipo;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            PlayaSeeder::class,
        ]);

        $this->call([
            BanderaTipoSeeder::class,
        ]);

        $this->call([
            FuerzaSeeder::class,
        ]);

        $this->call([
            LicenciaTipoSeeder::class,
        ]);


        $this->call([
            UserSeeder::class,
        ]);

         $this->call([
            PuestoSeeder::class,
        ]);

         $this->call([
            GuardavidaSeeder::class,
        ]);
    }
}
