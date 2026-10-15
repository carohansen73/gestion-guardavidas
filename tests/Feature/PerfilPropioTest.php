<?php

namespace Tests\Feature;

use App\Models\Guardavida;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Perfil propio (/profile): qué ve y qué puede guardar cada rol. La ficha de solo lectura es guardavida.show. */
class PerfilPropioTest extends TestCase
{
    use RefreshDatabase;

    private Playa $playa;

    private Puesto $puesto1;

    private Puesto $puesto2;

    private Puesto $puestoDeOtraPlaya;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable

        $this->playa = Playa::forceCreate(['nombre' => 'Claromecó', 'color' => '#00aaff', 'lat' => -38.0, 'lon' => -58.0]);
        $otra = Playa::forceCreate(['nombre' => 'Reta', 'color' => '#00aaff', 'lat' => -38.0, 'lon' => -58.0]);
        $this->puesto1 = Puesto::forceCreate(['nombre' => 'P1', 'latitud' => -38, 'longitud' => -58, 'playa_id' => $this->playa->id, 'qr_encriptado' => 'x']);
        $this->puesto2 = Puesto::forceCreate(['nombre' => 'P2', 'latitud' => -38, 'longitud' => -58, 'playa_id' => $this->playa->id, 'qr_encriptado' => 'x']);
        $this->puestoDeOtraPlaya = Puesto::forceCreate(['nombre' => 'P3', 'latitud' => -38, 'longitud' => -58, 'playa_id' => $otra->id, 'qr_encriptado' => 'x']);
    }

    private function usuario(string $dni, string $rol, bool $conFicha = true): User
    {
        $user = User::create([
            'name' => "N{$dni}", 'lastname' => "A{$dni}", 'dni' => $dni, 'email' => "u{$dni}@test.com",
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $user->assignRole($rol);
        if ($conFicha) {
            Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $user->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto1->id, 'turno' => 'M']);
        }

        return $user->fresh();
    }

    private function datos(array $extra = []): array
    {
        return array_merge(['nombre' => 'Ana', 'apellido' => 'Pérez', 'dni' => '30111222', 'telefono' => '2983111111'], $extra);
    }

    public function test_cada_rol_ve_su_pantalla_de_perfil(): void
    {
        $guardavida = $this->usuario('30000001', 'guardavida');
        $encargado = $this->usuario('30000002', 'encargado');
        $admin = $this->usuario('20000001', 'admin', false);

        $this->actingAs($guardavida)->get(route('guardavida.myProfile'))
            ->assertOk()->assertSee('Estadísticas')->assertSee('name="nombre"', false)
            ->assertDontSee('id="selectPuesto"', false)->assertDontSee('name="turno"', false)->assertDontSee('name="playa_id"', false);

        $this->actingAs($encargado)->get(route('guardavida.myProfile'))
            ->assertOk()->assertSee('id="selectPuesto"', false)->assertSee('name="turno"', false)->assertDontSee('name="playa_id"', false);

        $this->actingAs($admin)->get(route('guardavida.myProfile'))
            ->assertOk()->assertSee('Datos de Usuario')->assertDontSee('name="nombre"', false);
    }

    public function test_el_encargado_puede_cambiarse_el_puesto_y_el_turno(): void
    {
        $encargado = $this->usuario('30000002', 'encargado');

        $this->actingAs($encargado)
            ->put(route('guardavida.updateProfile', $encargado->guardavida), $this->datos(['puesto_id' => $this->puesto2->id, 'turno' => 'T']))
            ->assertRedirect();

        $g = $encargado->guardavida->fresh();
        $this->assertSame($this->puesto2->id, $g->puesto_id);
        $this->assertSame('T', $g->turno);
    }

    public function test_el_encargado_no_puede_cambiar_la_playa_ni_irse_a_un_puesto_de_otra_playa(): void
    {
        $encargado = $this->usuario('30000002', 'encargado');
        $otraPlaya = Playa::where('id', '!=', $this->playa->id)->first();

        $this->actingAs($encargado)
            ->putJson(route('guardavida.updateProfile', $encargado->guardavida), $this->datos(['puesto_id' => $this->puestoDeOtraPlaya->id, 'turno' => 'M']))
            ->assertStatus(422)->assertJsonValidationErrors('puesto_id');

        // playa_id en el request se descarta
        $this->actingAs($encargado)
            ->put(route('guardavida.updateProfile', $encargado->guardavida), $this->datos(['playa_id' => $otraPlaya->id, 'puesto_id' => $this->puesto2->id, 'turno' => 'M']))
            ->assertRedirect();
        $this->assertSame($this->playa->id, $encargado->guardavida->fresh()->playa_id);
    }

    public function test_el_guardavida_comun_no_puede_cambiarse_puesto_ni_turno(): void
    {
        $guardavida = $this->usuario('30000001', 'guardavida');

        $this->actingAs($guardavida)
            ->put(route('guardavida.updateProfile', $guardavida->guardavida), $this->datos(['puesto_id' => $this->puesto2->id, 'turno' => 'T', 'funcion' => 'Timonel']))
            ->assertRedirect();

        $g = $guardavida->guardavida->fresh();
        $this->assertSame($this->puesto1->id, $g->puesto_id);
        $this->assertSame('M', $g->turno);
        $this->assertSame('Guardavida', $g->funcion);
        $this->assertSame('Ana', $g->user->name, 'los datos personales sí se guardan');
    }

    public function test_no_se_puede_editar_el_perfil_de_otro(): void
    {
        $uno = $this->usuario('30000001', 'guardavida');
        $otro = $this->usuario('30000003', 'guardavida');

        $this->actingAs($uno)->put(route('guardavida.updateProfile', $otro->guardavida), $this->datos())->assertForbidden();
    }
}
