<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesYPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

         // --- Roles ---
        $guardavida = Role::firstOrCreate(['name' => 'guardavida']);
        $encargado = Role::firstOrCreate(['name' => 'encargado']);
        $admin = Role::firstOrCreate(['name' => 'admin']);


        // --- Permisos ---
        Permission::create(['name' => 'agregar_bandera']);
        Permission::create(['name' => 'editar_bandera']);
        Permission::create(['name' => 'eliminar_bandera']);
        Permission::create(['name' => 'ver_bandera']);

        Permission::create(['name' => 'agregar_guardavida']);
        Permission::create(['name' => 'editar_guardavida']);
        Permission::create(['name' => 'eliminar_guardavida']);
        Permission::create(['name' => 'ver_guardavida']);

        Permission::create(['name' => 'agregar_intervencion']);
        Permission::create(['name' => 'editar_intervencion']);
        Permission::create(['name' => 'eliminar_intervencion']);
        Permission::create(['name' => 'ver_intervencion']);

        Permission::create(['name' => 'agregar_novedad_material']);
        Permission::create(['name' => 'editar_novedad_material']);
        Permission::create(['name' => 'eliminar_novedad_material']);
        Permission::create(['name' => 'ver_novedad_material']);

        Permission::create(['name' => 'agregar_cambio_turno']);
        Permission::create(['name' => 'editar_cambio_turno']);
        Permission::create(['name' => 'eliminar_cambio_turno']);
        Permission::create(['name' => 'ver_cambio_turno']);

        Permission::create(['name' => 'agregar_licencia']);
        Permission::create(['name' => 'editar_licencia']);
        Permission::create(['name' => 'eliminar_licencia']);
        Permission::create(['name' => 'ver_licencia']);

        Permission::create(['name' => 'agregar_asistencia']);
        Permission::create(['name' => 'editar_asistencia']);
        Permission::create(['name' => 'eliminar_asistencia']);
        Permission::create(['name' => 'ver_asistencia']);

        Permission::create(['name' => 'ver_asistencia_propia']);

        Permission::create(['name' => 'abm_roles_y_permisos']);

        // Permission::create(['name' => 'fichar_asistencia']);
        // Permission::create(['name' => 'ver_historial_asistencia']);
        // Permission::create(['name' => 'editar_perfil']);
        // Permission::create(['name' => 'abm_guardavidas']);
        // Permission::create(['name' => 'abm_registros']);
        // Permission::create(['name' => 'administrar_todo']);

        // Asignación de permisos
        $guardavida->givePermissionTo([
        'agregar_bandera',
        'editar_bandera',
        'eliminar_bandera',
        'ver_bandera',
        'ver_asistencia_propia'
    ]);

        $encargado->givePermissionTo([
            'agregar_bandera',
            'editar_bandera',
            'eliminar_bandera',
            'ver_bandera',

            'agregar_guardavida',
            'editar_guardavida',
            'eliminar_guardavida',
            'ver_guardavida',

            'agregar_intervencion',
            'editar_intervencion',
            'eliminar_intervencion',
            'ver_intervencion',

            'agregar_novedad_material',
            'editar_novedad_material',
            'eliminar_novedad_material',
            'ver_novedad_material',

            'agregar_cambio_turno',
            'editar_cambio_turno',
            'eliminar_cambio_turno',
            'ver_cambio_turno',

            'agregar_licencia',
            'editar_licencia',
            'eliminar_licencia',
            'ver_licencia',

            'ver_asistencia',
            'ver_asistencia_propia',
        ]);

        $admin->givePermissionTo(Permission::all());
    }
}
