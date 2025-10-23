<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' =>'ver usuarios']);
        Permission::create(['name' =>'crear usuarios']);
        Permission::create(['name' =>'editar usuarios']);
        Permission::create(['name' =>'eliminar usuarios']);

        Permission::create(['name' =>'ver puestos']);
        Permission::create(['name' =>'crear puestos']);
        Permission::create(['name' =>'editar puestos']);
        Permission::create(['name' =>'eliminar puestos']);

        //creo usuario admin
        $adminUser = User::query()->create([
            'name' => 'admin',
            'lastname' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now()
        ]);

        //creo rol admin
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        //asigno rol admin a usuario admin
        $adminUser->assignRole($roleAdmin);
        //traigo tosdos los permisos
        $permissionAdmin = Permission::query()->pluck('name');
        //asigno todos los permisos al rol admin
        $roleAdmin->syncPermissions($permissionAdmin);

        //creo usuario encargado
        $encargadoUser = User::query()->create([
            'name' => 'María',
            'lastname' => 'Gómez',
            'email' => 'guardavida@gmail.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now()
        ]);

        //creo usuario guardavidas
        $guardavidaUser2 = User::query()->create([
            'name' => 'Carla Fernández',
            'lastname' => 'Carla Fernández',
            'email' => 'guardavida2@gmail.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now()
        ]);

        //creo usuario guardavidas
        $guardavidaUser3 = User::query()->create([
            'name' => 'Juan Pérez',
            'lastname' => 'Juan Pérez',
            'email' => 'guardavida3@gmail.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now()
        ]);

        //creo rol guardavida
        $roleGuardavida = Role::firstOrCreate(['name' => 'guardavida']);
        //asigno rol guardavida a usuario guardavida

        $guardavidaUser2->assignRole($roleGuardavida);
        $guardavidaUser3->assignRole($roleGuardavida);

        $roleGuardavida->syncPermissions('ver puestos');

        //creo rol
        $roleEncargado = Role::firstOrCreate(['name' => 'encargado']);
        //asigno rol a usuario
        $encargadoUser->assignRole($roleEncargado);
        //traigo todos los permisos
        $permissionAll = Permission::query()->pluck('name');
        //asigno todos los permisos al rol encargado
        $roleEncargado->syncPermissions($permissionAll);

        //TODO Crear policy para cada modelo y @can('ver puestos'),etc. en buttons, menu, etc.
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
