<?php

namespace Tests\Feature;

use App\Models\Guardavida;
use App\Models\Licencia;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** La constancia médica de una licencia solo se abre con sesión y permiso (o siendo el dueño), nunca por una URL pública. */
class ConstanciaLicenciaTest extends TestCase
{
    use RefreshDatabase;

    private Guardavida $guardavida;

    private Licencia $licencia;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable
        Storage::fake('local');
        Storage::fake('public');

        $playa = Playa::forceCreate(['nombre' => 'P', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $puesto = Puesto::forceCreate(['nombre' => 'X', 'latitud' => -38.0, 'longitud' => -58.0, 'playa_id' => $playa->id, 'qr_encriptado' => 'x']);
        $this->guardavida = Guardavida::forceCreate([
            'funcion' => 'Guardavida', 'playa_id' => $playa->id, 'puesto_id' => $puesto->id, 'turno' => 'M',
            'user_id' => $this->usuario('30111222', 'guardavida')->id,
        ]);
        $this->licencia = Licencia::forceCreate([
            'fecha_inicio' => '2026-12-10', 'fecha_fin' => '2026-12-13', 'tipo_licencia' => 'Enfermedad', 'en_tiempo' => true, 'turno' => 'M',
            'playa_id' => $playa->id, 'puesto_id' => $puesto->id, 'guardavida_id' => $this->guardavida->id, 'archivo' => 'licencias/constancia.pdf',
        ]);
    }

    private function usuario(string $dni, string $rol): User
    {
        $user = User::create([
            'name' => "N{$dni}", 'lastname' => "A{$dni}", 'dni' => $dni, 'email' => "u{$dni}@test.com",
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $user->assignRole($rol);

        return $user;
    }

    public function test_sin_sesion_no_se_puede_abrir(): void
    {
        Storage::disk('local')->put('licencias/constancia.pdf', 'contenido');

        $this->get(route('licencia.constancia', $this->licencia))->assertRedirect(route('login'));
    }

    public function test_admin_superadmin_y_encargado_la_abren_desde_el_disco_privado(): void
    {
        Storage::disk('local')->put('licencias/constancia.pdf', 'contenido');

        foreach (['admin', 'superadmin', 'encargado'] as $i => $rol) {
            $this->actingAs($this->usuario("2000000{$i}", $rol))->get(route('licencia.constancia', $this->licencia))->assertOk();
        }
    }

    public function test_las_constancias_viejas_siguen_abriendose_desde_el_disco_publico(): void
    {
        Storage::disk('public')->put('licencias/constancia.pdf', 'contenido');

        $this->actingAs($this->usuario('20000001', 'admin'))->get(route('licencia.constancia', $this->licencia))->assertOk();
        $this->assertSame('public', $this->licencia->discoDeLaConstancia());

        // Si además está en el privado (ya se movió), se prefiere ese.
        Storage::disk('local')->put('licencias/constancia.pdf', 'contenido');
        $this->assertSame('local', $this->licencia->discoDeLaConstancia());
    }

    public function test_el_guardavida_ve_la_suya_pero_no_la_de_otro(): void
    {
        Storage::disk('local')->put('licencias/constancia.pdf', 'contenido');

        $this->actingAs($this->guardavida->user)->get(route('licencia.constancia', $this->licencia))->assertOk();

        $otro = $this->usuario('30999888', 'guardavida');
        $this->actingAs($otro)->get(route('licencia.constancia', $this->licencia))->assertForbidden();
    }

    public function test_si_el_archivo_no_esta_responde_404(): void
    {
        $this->actingAs($this->usuario('20000001', 'admin'))->get(route('licencia.constancia', $this->licencia))->assertNotFound();
    }

    public function test_los_links_apuntan_a_la_ruta_de_la_app_y_no_a_storage(): void
    {
        $this->assertSame(route('licencia.constancia', $this->licencia), $this->licencia->archivo_url);

        Storage::disk('local')->put('licencias/constancia.pdf', 'contenido');
        $this->actingAs($this->usuario('20000001', 'admin'))->get(route('licencia.show', $this->licencia))
            ->assertOk()->assertSee(route('licencia.constancia', $this->licencia), false)->assertDontSee('/storage/licencias', false);
    }
}
