<?php

namespace Tests\Feature;

use App\Models\Guardavida;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Rol `rrhh`: solo consulta. Ve asistencias (listado e historial por persona), el listado y la ficha de
 * guardavidas, y descarga esos Excel. No crea ni modifica nada ni entra a ninguna otra pantalla.
 */
class RolesPorPermisosTest extends TestCase
{
    use RefreshDatabase;

    private User $rrhh;

    private Guardavida $g;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable

        Temporada::create([
            'nombre' => '2026/27', 'fecha_inicio_postulacion' => '2026-08-01', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-10-01', 'fecha_fin' => '2027-03-31',
        ]);
        $playa = Playa::forceCreate(['nombre' => 'Costa Azul', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $puesto = Puesto::forceCreate(['nombre' => 'Puesto 1', 'latitud' => -38.0, 'longitud' => -58.0, 'playa_id' => $playa->id, 'qr_encriptado' => 'x']);

        $this->rrhh = $this->usuario('20000009', 'Rita', 'Recursos', 'rrhh');
        $user = $this->usuario('30000001', 'Beto', 'Aguirre', 'guardavida');
        $this->g = Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $user->id, 'playa_id' => $playa->id, 'puesto_id' => $puesto->id, 'turno' => 'M']);
        $this->g->darDeAlta(Carbon::parse('2026-01-01'), null, 'Alta manual', true);
    }

    private function usuario(string $dni, string $nombre, string $apellido, string $rol): User
    {
        $user = User::create([
            'name' => $nombre, 'lastname' => $apellido, 'dni' => $dni, 'email' => "u{$dni}@test.com",
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $user->assignRole($rol);

        return $user;
    }

    public function test_ve_las_asistencias_y_la_ficha_pero_sin_acciones_de_edicion(): void
    {
        $this->actingAs($this->rrhh);

        $this->get(route('asistencias.index'))->assertOk();
        $this->get(route('asistencias.guardavida', $this->g->id))->assertOk()
            ->assertSee('Aguirre Beto')->assertDontSee('Cargar cambio de franco');
        $this->get(route('guardavida.index'))->assertOk()
            ->assertDontSee(route('guardavida.create'), false)->assertDontSee(route('guardavida.edit', $this->g), false);
        $this->get(route('guardavida.show', $this->g))->assertOk()
            ->assertDontSee(route('guardavida.edit', $this->g), false);
    }

    public function test_descarga_los_excel_de_sus_pantallas(): void
    {
        $this->actingAs($this->rrhh);

        $this->get(route('guardavidas.export'))->assertOk();
        $this->get(route('export.playas', ['tipo' => 'guardavidas']))->assertOk();
        $this->get(route('export.playas', ['tipo' => 'asistencia-guardavida', 'guardavida_id' => $this->g->id, 'inicio' => '2026-10-01', 'fin' => '2026-10-15']))->assertOk();
        // Los de otros módulos, no.
        $this->get(route('export.playas', ['tipo' => 'intervenciones']))->assertForbidden();
        $this->get(route('export.playas', ['tipo' => 'licencias']))->assertForbidden();
    }

    public function test_no_puede_crear_ni_modificar_guardavidas(): void
    {
        $this->actingAs($this->rrhh);

        $this->get(route('guardavida.create'))->assertForbidden();
        $this->get(route('guardavida.edit', $this->g))->assertForbidden();
        $this->post(route('guardavida.store'), [])->assertForbidden();
        $this->put(route('guardavida.update', $this->g), [])->assertForbidden();
        $this->post(route('guardavida.baja', $this->g))->assertForbidden();
        $this->put(route('rol.update', $this->g->user), ['role' => 'admin'])->assertForbidden();
        $this->post(route('franco-excepcion.store', $this->g), [])->assertForbidden();
    }

    public function test_no_entra_al_resto_del_sistema(): void
    {
        $this->actingAs($this->rrhh);

        foreach (['bandera.index', 'intervencion.index', 'novedad-de-material.index', 'licencia.index', 'temporada.index',
            'postulaciones.index', 'franco-intercambio.index', 'guardavida.misAsistencias', 'activeCamera', 'dashboard'] as $ruta) {
            $this->get(route($ruta))->assertForbidden();
        }
    }

    public function test_ve_los_dados_de_baja_pero_sin_poder_reincorporar(): void
    {
        $this->actingAs($this->rrhh);

        $this->get(route('guardavidas.bajas'))->assertOk()->assertDontSee('Volver a dar de alta');
        $this->post(route('guardavida.reincorporar', $this->g))->assertForbidden();
    }

    public function test_su_inicio_es_el_listado_de_asistencias(): void
    {
        $this->actingAs($this->rrhh)->get(route('home'))->assertRedirect(route('asistencias.index'));
    }

    public function test_el_resto_de_los_roles_no_se_ven_afectados(): void
    {
        $this->actingAs($this->usuario('20000001', 'Admin', 'Admin', 'admin'));

        $this->get(route('guardavida.create'))->assertOk();
        $this->get(route('bandera.index'))->assertOk();
    }

    public function test_el_formulario_de_alta_ofrece_el_rol_rrhh(): void
    {
        $this->actingAs($this->usuario('20000001', 'Admin', 'Admin', 'admin'))->get(route('guardavida.create'))
            ->assertOk()->assertSee('value="rrhh"', false);
    }

    public function test_el_alta_de_rrhh_crea_el_usuario_sin_ficha_de_guardavidas(): void
    {
        $this->actingAs($this->usuario('20000001', 'Admin', 'Admin', 'admin'))->post(route('guardavida.store'), [
            'nombre' => 'Ana', 'apellido' => 'Gómez', 'dni' => '28111222', 'email' => 'ana@test.com', 'rol' => 'rrhh',
        ])->assertRedirect(route('guardavida.index'))->assertSessionHasNoErrors();

        $user = User::where('dni', '28111222')->firstOrFail();
        $this->assertTrue($user->hasRole('rrhh'));
        $this->assertNull($user->guardavida);
    }

    // ------------------------------------------------------- los roles son solo conjuntos de permisos

    public function test_un_rol_nuevo_funciona_solo_con_crear_la_fila_y_tildar_permisos(): void
    {
        // Sin tocar código: un rol inventado en el momento, con un único permiso.
        Role::create(['name' => 'auditoria'])->givePermissionTo('ver_asistencia');
        $auditor = $this->usuario('20000010', 'Aldo', 'Auditor', 'auditoria');

        $this->actingAs($auditor)->get(route('asistencias.index'))->assertOk();
        $this->get(route('asistencias.guardavida', $this->g->id))->assertOk();
        $this->get(route('home'))->assertRedirect(route('asistencias.index'));
        $this->get(route('guardavida.index'))->assertForbidden();
        $this->get(route('guardavida.show', $this->g))->assertForbidden();
        $this->get(route('guardavida.create'))->assertForbidden();
        $this->get(route('bandera.index'))->assertForbidden();
    }

    public function test_quien_no_puede_editar_guardavidas_no_cambia_datos_ni_roles_de_otros(): void
    {
        $victima = $this->usuario('30000002', 'Enzo', 'Encargado', 'encargado');

        // Un guardavida común (y RRHH) no puede pisar el email/contraseña ni el rol de otra cuenta.
        foreach ([$this->g->user, $this->rrhh] as $atacante) {
            $this->actingAs($atacante)->put(route('user.update', $victima), [
                'nombre' => 'X', 'apellido' => 'Y', 'email' => 'otro@test.com', 'password' => 'nuevaclave1', 'password_confirmation' => 'nuevaclave1',
            ])->assertForbidden();
            $this->put(route('rol.update', $victima), ['role' => 'admin'])->assertForbidden();
        }

        $this->assertSame('u30000002@test.com', $victima->fresh()->email);
        $this->assertTrue($victima->fresh()->hasRole('encargado'));
    }
}
