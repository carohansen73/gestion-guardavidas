<?php

namespace Tests\Feature;

use App\Models\Guardavida;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * - El listado de guardavidas exige ver_guardavida (el rol guardavida no lo tiene).
 * - Cada Excel exige el permiso de la vista de la que sale.
 * - Las asistencias de otras personas exigen ver_asistencia; las propias las ve cualquiera.
 */
class AccesoListadosYExportesTest extends TestCase
{
    use RefreshDatabase;

    private Playa $playa;

    private Puesto $puesto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable
        $this->playa = Playa::forceCreate(['nombre' => 'Reta', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $this->puesto = Puesto::forceCreate(['nombre' => 'Reta 1', 'latitud' => -38, 'longitud' => -58, 'playa_id' => $this->playa->id, 'qr_encriptado' => 'x']);
    }

    private function usuario(string $dni, string $rol, bool $conFicha = true): User
    {
        $user = User::create([
            'name' => "N{$dni}", 'lastname' => "A{$dni}", 'dni' => $dni, 'email' => "u{$dni}@test.com",
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $user->assignRole($rol);
        if ($conFicha) {
            Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $user->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'turno' => 'M']);
        }

        return $user->fresh();
    }

    public function test_el_listado_de_guardavidas_esta_cerrado_para_el_rol_guardavida(): void
    {
        $this->actingAs($this->usuario('30000001', 'guardavida'))->get(route('guardavida.index'))->assertForbidden();
    }

    public function test_el_perfil_de_otro_y_la_lista_json_de_guardavidas_estan_cerrados_para_el_rol_guardavida(): void
    {
        $uno = $this->usuario('30000001', 'guardavida');
        $otro = $this->usuario('30000003', 'guardavida');

        $this->actingAs($uno)->get(route('guardavida.show', $otro->guardavida->id))->assertForbidden();
        $this->actingAs($uno)->get('/get-all-guardavidas')->assertForbidden();
        $this->actingAs($this->usuario('30000002', 'encargado'))->get('/get-all-guardavidas')->assertOk();
    }

    public function test_el_listado_de_guardavidas_sigue_abierto_para_encargado_y_admin(): void
    {
        $this->actingAs($this->usuario('30000002', 'encargado'))->get(route('guardavida.index'))->assertOk();
        $this->actingAs($this->usuario('20000001', 'admin', false))->get(route('guardavida.index'))->assertOk();
    }

    public function test_el_excel_de_guardavidas_exige_ver_guardavida(): void
    {
        $guardavida = $this->usuario('30000001', 'guardavida');
        $this->actingAs($guardavida)->get(route('guardavidas.export'))->assertForbidden();
        $this->actingAs($guardavida)->get(route('export.playas', ['tipo' => 'guardavidas']))->assertForbidden();
        $this->actingAs($this->usuario('30000002', 'encargado'))->get(route('guardavidas.export'))->assertOk();
        $this->actingAs($this->usuario('20000001', 'admin', false))->get(route('guardavidas.export'))->assertOk();
    }

    public function test_cada_excel_exige_el_permiso_de_su_vista(): void
    {
        $guardavida = $this->usuario('30000001', 'guardavida'); // solo tiene ver_bandera
        $tiposCerrados = ['intervenciones', 'novedad-material', 'guardavidas', 'cambio-de-turno', 'licencias', 'asistencia-general', 'asistencia-guardavida'];

        foreach ($tiposCerrados as $tipo) {
            $this->assertSame(403, $this->actingAs($guardavida)->get(route('export.playas', ['tipo' => $tipo, 'guardavida_id' => $guardavida->guardavida->id]))->getStatusCode(), $tipo);
        }
        // Tiene acceso a la vista de banderas, así que también a su Excel.
        $this->assertSame(200, $this->actingAs($guardavida)->get(route('export.playas', ['tipo' => 'banderas']))->getStatusCode());

        $admin = $this->usuario('20000001', 'admin', false);
        foreach (['intervenciones', 'banderas', 'novedad-material', 'guardavidas', 'cambio-de-turno', 'licencias', 'asistencia-general'] as $tipo) {
            $this->assertSame(200, $this->actingAs($admin)->get(route('export.playas', ['tipo' => $tipo]))->getStatusCode(), $tipo);
        }
    }

    public function test_las_asistencias_de_terceros_estan_cerradas_pero_mis_asistencias_no(): void
    {
        $uno = $this->usuario('30000001', 'guardavida');
        $otro = $this->usuario('30000003', 'guardavida');

        $this->actingAs($uno)->get(route('asistencias.index'))->assertForbidden();
        $this->actingAs($uno)->get(route('asistencias.guardavida', $otro->guardavida->id))->assertForbidden();
        $this->actingAs($uno)->get(route('asistencias.guardavida', $uno->guardavida->id))->assertForbidden();
        $this->actingAs($uno)->get(route('guardavida.misAsistencias'))->assertOk();

        $admin = $this->usuario('20000001', 'admin', false);
        $this->actingAs($admin)->get(route('asistencias.index'))->assertOk();
        $this->actingAs($admin)->get(route('asistencias.guardavida', $otro->guardavida->id))->assertOk();
    }
}
