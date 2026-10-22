<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Usuarios de prueba. Los roles y sus permisos los crea RolesYPermisosSeeder (que tiene que correr antes,
 * ver DatabaseSeeder): acá solo se asignan por nombre.
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::query()->create([
            'name' => 'admin',
            'lastname' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('admin');

        $encargadoUser = User::query()->create([
            'name' => 'María',
            'lastname' => 'Gómez',
            'email' => 'guardavida@gmail.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
        ]);
        $encargadoUser->assignRole('encargado');

        $guardavidas = [
            ['name' => 'Carla Fernández', 'lastname' => 'Carla Fernández', 'email' => 'guardavida2@gmail.com'],
            ['name' => 'Juan Pérez', 'lastname' => 'Juan Pérez', 'email' => 'guardavida3@gmail.com'],
            ['name' => 'Valentina', 'lastname' => 'Pérez', 'email' => 'guardavidaTresArroyos@gmail.com'],
        ];

        foreach ($guardavidas as $datos) {
            User::query()->create($datos + [
                'password' => Hash::make('123456789'),
                'email_verified_at' => now(),
            ])->assignRole('guardavida');
        }
    }
}
