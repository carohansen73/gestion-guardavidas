<?php

namespace Database\Seeders;
use App\Models\Guardavida;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuardavidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

          Guardavida::create([
            'funcion' => 'Encargado',
            'nombre' => 'María',
            'apellido' => 'Gómez',
            'dni' => '30222333',
            'telefono' => '2983456789',
            'direccion' => 'Calle 12',
            'numero' => '456',
            'piso_dpto' => '2B',
            'playa_id' => 1,
            'puesto_id' => 1,//SAMOA
            'user_id' => 2,
        ]);



        Guardavida::create([
            'funcion' => 'Guardavida',
            'nombre' => 'Carla',
            'apellido' => 'Fernández',
            'dni' => '30444555',
            'telefono' => '2983678901',
            'direccion' => 'Av. Libertad',
            'numero' => '321',
            'piso_dpto' => '1A',
            'playa_id' => 2,
            'puesto_id' => 4,
            'user_id' => 3,
        ]);



         Guardavida::create([
            'funcion' => 'Timonel',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '30111222',
            'telefono' => '2983123456',
            'direccion' => 'Av. Costanera',
            'numero' => '123',
            'piso_dpto' => null,
            'playa_id' => 1,
            'puesto_id' => 1,
            'user_id' => 4,
        ]);

        Guardavida::create([
            'nombre' => 'Valentina',
            'apellido' => 'Cast',
            'dni' => '44993777',
            'telefono' => '2983123453',
            'direccion' => 'Av. Costanera',
            'numero' => '123',
            'piso_dpto' => null,
            'playa_id' => 5,
            'puesto_id' => 29,
            'user_id' => 6,
        ]);


    }
}
