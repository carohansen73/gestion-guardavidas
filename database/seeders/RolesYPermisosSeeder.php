<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolesYPermisosSeeder extends Seeder
{
    /**
     * Cómo es cada rol para el sistema (ver la migración add_metadatos_a_roles para el significado de cada columna).
     * Un rol nuevo se crea desde la app, no hace falta agregarlo acá: esto es el punto de partida de una base nueva.
     */
    private const ROLES = [
        'superadmin' => ['etiqueta' => 'Superadmin', 'descripcion' => null, 'color' => 'fuchsia', 'icono' => 'sparkles', 'es_plantel' => false, 'usa_temporada' => true, 'asignable' => false, 'intocable' => true, 'rango' => 100],
        'admin' => ['etiqueta' => 'Administrador', 'descripcion' => null, 'color' => 'sky', 'icono' => 'shield-check', 'es_plantel' => false, 'usa_temporada' => true, 'asignable' => true, 'intocable' => true, 'rango' => 80],
        'rrhh' => ['etiqueta' => 'RRHH', 'descripcion' => 'solo consulta', 'color' => 'teal', 'icono' => 'user', 'es_plantel' => false, 'usa_temporada' => true, 'asignable' => true, 'intocable' => true, 'rango' => 60],
        'encargado' => ['etiqueta' => 'Encargado', 'descripcion' => null, 'color' => 'amber', 'icono' => 'star', 'es_plantel' => true, 'usa_temporada' => true, 'asignable' => true, 'intocable' => false, 'rango' => 40],
        'guardavida' => ['etiqueta' => 'Guardavida', 'descripcion' => null, 'color' => 'emerald', 'icono' => 'lifebuoy', 'es_plantel' => true, 'usa_temporada' => true, 'asignable' => true, 'intocable' => false, 'rango' => 20],
        'personal' => ['etiqueta' => 'Personal', 'descripcion' => 'solo ficha, sin temporada', 'color' => 'violet', 'icono' => 'user', 'es_plantel' => true, 'usa_temporada' => false, 'asignable' => true, 'intocable' => true, 'rango' => 50],
        'postulante' => ['etiqueta' => 'Postulante', 'descripcion' => null, 'color' => 'slate', 'icono' => 'user', 'es_plantel' => false, 'usa_temporada' => true, 'asignable' => false, 'intocable' => false, 'rango' => 10],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Roles ---
        $guardavida = $this->rol('guardavida');
        $encargado = $this->rol('encargado');
        // Empleados fijos que solo usan el sistema para fichar (p. ej. aeródromo): sin postulación ni temporada.
        $personal = $this->rol('personal');
        // RRHH: solo consulta (asistencias, listado y ficha de guardavidas, con sus Excel). No crea ni modifica nada:
        // todo lo demás le queda cerrado porque cada ruta pide su permiso.
        $rrhh = $this->rol('rrhh');
        $admin = $this->rol('admin');
        // superadmin: rol adicional (no reemplaza a admin). Un usuario superadmin
        // tiene AMBOS roles asignados (admin + superadmin), así que todo chequeo
        // hasRole('admin')/hasAnyRole([...,'admin']) que ya existe en el código
        // sigue funcionando sin tocarlo. Lo único exclusivo de superadmin es el
        // permiso abm_roles_y_permisos (ver más abajo), que gatea la pantalla de
        // administración de permisos.
        $superadmin = $this->rol('superadmin');
        // postulante: sin ningún permiso asignado a propósito — queda
        // bloqueado de todo el sistema existente por el propio sistema de
        // permisos, sin necesidad de chequeos ad-hoc en cada pantalla. Solo
        // puede acceder a lo que esté explícitamente fuera del sistema de
        // permisos (su propia postulación), vía RedirectPostulante.
        // Su único permiso es tener Home (sus fechas de inscripción y su postulación): el resto sigue cerrado.
        $postulante = $this->rol('postulante');

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

        // ver_panel_inicio: entrar al panel de inicio (home/dashboard); sin él, el home redirige a su primera sección.
        // solicitar_cambio_franco: pedir/aceptar intercambios de franco con compañeros (autoservicio).
        Permission::create(['name' => 'ver_panel_inicio']);
        // gestionar_temporada_cerrada: editar o borrar registros de temporadas pasadas y cargarlos fuera de la temporada en curso.
        Permission::create(['name' => 'gestionar_temporada_cerrada']);
        Permission::create(['name' => 'solicitar_cambio_franco']);

        // gestionar_ajenos_*: editar o borrar los registros que cargó OTRO usuario. Sin él, cada uno toca solo lo que cargó
        // (ver App\Models\Concerns\TieneAutor). Por defecto solo admin/superadmin; se reparte desde la pantalla de Permisos.
        foreach (['bandera', 'intervencion', 'novedad_material', 'licencia', 'cambio_turno'] as $recurso) {
            Permission::create(['name' => "gestionar_ajenos_{$recurso}"]);
        }

        Permission::create(['name' => 'agregar_temporada']);
        Permission::create(['name' => 'editar_temporada']);
        Permission::create(['name' => 'eliminar_temporada']);
        Permission::create(['name' => 'ver_temporada']);

        // ver_postulacion: ver el listado/detalle de inscripciones y sus
        // documentos. revisar_postulacion: aceptar/rechazar + observaciones.
        Permission::create(['name' => 'ver_postulacion']);
        Permission::create(['name' => 'revisar_postulacion']);
        // seleccionar_postulacion: elegir postulantes en lote (los pasa a guardavida) y cerrar la selección.
        Permission::create(['name' => 'seleccionar_postulacion']);

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
            'ver_asistencia_propia',
            'ver_panel_inicio',
            'solicitar_cambio_franco',
        ]);

        $personal->givePermissionTo(['ver_asistencia_propia', 'ver_panel_inicio']);

        $rrhh->givePermissionTo(['ver_guardavida', 'ver_asistencia']);

        $postulante->givePermissionTo(['ver_panel_inicio']);

        $encargado->givePermissionTo([
            'agregar_bandera',
            'editar_bandera',
            'eliminar_bandera',
            'ver_bandera',

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
            'ver_panel_inicio',
            'solicitar_cambio_franco',
        ]);

        // admin tiene todos los permisos MENOS abm_roles_y_permisos: la gestión
        // de permisos queda exclusiva de superadmin (ver comentario arriba).
        $admin->givePermissionTo(
            Permission::all()->reject(fn (Permission $permiso) => $permiso->name === 'abm_roles_y_permisos')
        );

        $superadmin->givePermissionTo(Permission::all());
    }

    private function rol(string $nombre): Rol
    {
        return Rol::updateOrCreate(['name' => $nombre, 'guard_name' => 'web'], self::ROLES[$nombre]);
    }
}
