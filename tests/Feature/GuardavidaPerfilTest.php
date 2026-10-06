<?php

namespace Tests\Feature;

use App\Models\Guardavida;
use App\Models\Perfil;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardavidaPerfilTest extends TestCase
{
    use RefreshDatabase;

    private function guardavida(): Guardavida
    {
        $user = User::create([
            'name' => 'Ana', 'lastname' => 'Pérez', 'dni' => '30111222', 'email' => 'ana@test.com',
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $playa = Playa::forceCreate(['nombre' => 'Claromecó', 'color' => '#00aaff', 'lat' => -38.0, 'lon' => -58.0]);
        $puesto = Puesto::forceCreate(['nombre' => 'P1', 'latitud' => -38, 'longitud' => -58, 'playa_id' => $playa->id, 'qr_encriptado' => 'x']);

        return Guardavida::forceCreate([
            'funcion' => 'Guardavida', 'nombre' => 'Ana', 'apellido' => 'Pérez', 'dni' => '30111222', 'user_id' => $user->id, 'playa_id' => $playa->id, 'puesto_id' => $puesto->id, 'turno' => 'M',
        ]);
    }

    public function test_sin_perfil_los_datos_personales_son_null(): void
    {
        $g = $this->guardavida();

        $this->assertNull($g->telefono);
        $this->assertNull($g->direccion);
    }

    public function test_guardar_datos_personales_crea_el_perfil_y_se_leen_desde_ahi(): void
    {
        $g = $this->guardavida();

        $g->guardarDatosPersonales(['telefono' => '2983111111', 'direccion' => 'Calle 11', 'numero' => '123', 'piso_dpto' => '2-A', 'funcion' => 'Timonel']);

        $g = Guardavida::find($g->id);
        $this->assertSame('2983111111', $g->telefono);
        $this->assertSame('Calle 11', $g->direccion);
        $this->assertSame('123', $g->numero);
        $this->assertSame('2-A', $g->piso_dpto);
        $this->assertSame(1, Perfil::count());
        $this->assertSame('Guardavida', $g->funcion, 'solo se guardan los datos personales, nada más');
    }

    public function test_actualizar_reutiliza_el_mismo_perfil_y_no_pisa_otros_campos(): void
    {
        $g = $this->guardavida();
        Perfil::create(['user_id' => $g->user_id, 'telefono' => '1', 'grupo_sanguineo' => 'O+', 'talle_remera' => 'M']);

        $g->guardarDatosPersonales(['telefono' => '2983999999']);

        $this->assertSame(1, Perfil::count());
        $this->assertSame('2983999999', $g->telefono);
        $perfil = Perfil::first();
        $this->assertSame('O+', $perfil->grupo_sanguineo);
        $this->assertSame('M', $perfil->talle_remera);
    }

    public function test_direccion_y_numero_pueden_quedar_vacios_y_la_ficha_carga(): void
    {
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable
        $g = $this->guardavida();
        $g->guardarDatosPersonales(['telefono' => '2983111111', 'direccion' => null, 'numero' => null, 'piso_dpto' => null]);

        $admin = User::create([
            'name' => 'Adm', 'lastname' => 'In', 'dni' => '20000001', 'email' => 'adm@test.com',
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $admin->assignRole('admin');

        $this->assertNull(Guardavida::find($g->id)->direccion);
        $this->actingAs($admin)->get(route('guardavida.show', $g))
            ->assertOk()
            ->assertSee('No especificado');
    }
}
