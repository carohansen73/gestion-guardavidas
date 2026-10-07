<?php

namespace Tests\Feature;

use App\Models\Guardavida;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardavidaActivosTest extends TestCase
{
    use RefreshDatabase;

    private ?Playa $playa = null;

    private ?Puesto $puesto = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable
    }

    private function guardavida(string $dni, string $rol): Guardavida
    {
        $this->playa ??= Playa::forceCreate(['nombre' => 'Claromecó', 'color' => '#00aaff', 'lat' => -38.0, 'lon' => -58.0]);
        $this->puesto ??= Puesto::forceCreate(['nombre' => 'P1', 'latitud' => -38, 'longitud' => -58, 'playa_id' => $this->playa->id, 'qr_encriptado' => 'x']);

        $user = User::create([
            'name' => "N{$dni}", 'lastname' => "A{$dni}", 'dni' => $dni, 'email' => "u{$dni}@test.com",
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $user->assignRole($rol);

        return Guardavida::forceCreate([
            'funcion' => 'Guardavida', 'nombre' => "N{$dni}", 'apellido' => "A{$dni}", 'dni' => $dni,
            'user_id' => $user->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'turno' => 'M',
        ]);
    }

    public function test_activos_son_los_de_rol_guardavida_o_encargado(): void
    {
        $g = $this->guardavida('30000001', 'guardavida');
        $e = $this->guardavida('30000002', 'encargado');
        $ex = $this->guardavida('30000003', 'postulante');

        $ids = Guardavida::activos()->pluck('id')->all();

        $this->assertEqualsCanonicalizing([$g->id, $e->id], $ids);
        $this->assertSame(3, Guardavida::count(), 'el ex guardavida conserva su fila (historial)');
    }

    public function test_se_puede_incluir_a_un_ex_guardavida_de_un_registro_viejo(): void
    {
        $g = $this->guardavida('30000001', 'guardavida');
        $ex = $this->guardavida('30000003', 'postulante');

        $ids = Guardavida::activos([$ex->id])->pluck('id')->all();

        $this->assertEqualsCanonicalizing([$g->id, $ex->id], $ids);
    }

    public function test_el_listado_de_guardavidas_no_muestra_a_quien_volvio_a_postulante(): void
    {
        $this->guardavida('30000001', 'guardavida');
        $this->guardavida('30000003', 'postulante');
        $admin = User::create([
            'name' => 'Adm', 'lastname' => 'In', 'dni' => '20000001', 'email' => 'adm@test.com',
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $admin->assignRole('admin');

        $this->actingAs($admin)->get(route('guardavida.index'))
            ->assertOk()
            ->assertSee('A30000001')
            ->assertDontSee('A30000003');
    }

    public function test_el_presentismo_incluye_a_quien_trabajo_en_el_periodo_aunque_ya_no_sea_del_plantel(): void
    {
        $activo = $this->guardavida('30000001', 'guardavida');
        $trabajo = $this->guardavida('30000003', 'postulante');
        $nuncaTrabajo = $this->guardavida('30000004', 'postulante');

        \App\Models\Asistencia::forceCreate([
            'longitud' => -58, 'latitud' => -38, 'precision' => 5, 'puesto_id' => $this->puesto->id,
            'guardavidas_id' => $trabajo->id, 'fecha_hora' => '2026-01-15 10:00:00', 'estado_validacion' => 'valido',
        ]);

        $enero = Guardavida::activosOConAsistenciaEn('2026-01-01 00:00:00', '2026-01-31 23:59:59')->pluck('id')->all();
        $marzo = Guardavida::activosOConAsistenciaEn('2026-03-01 00:00:00', '2026-03-31 23:59:59')->pluck('id')->all();

        $this->assertEqualsCanonicalizing([$activo->id, $trabajo->id], $enero, 'enero: el que trabajó aparece');
        $this->assertEqualsCanonicalizing([$activo->id], $marzo, 'marzo: no trabajó, no aparece como ausente');
        $this->assertNotContains($nuncaTrabajo->id, $enero);
    }
}
