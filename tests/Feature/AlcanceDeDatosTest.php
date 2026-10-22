<?php

namespace Tests\Feature;

use App\Models\Bandera;
use App\Models\BanderaTipo;
use App\Models\Guardavida;
use App\Models\Intervencion;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Nadie queda limitado a su playa por su rol: quien tiene el permiso ver_* ve los registros de todas las playas,
 * y editar o borrar depende solo de los permisos editar_* / eliminar_* (no del nombre del rol ni de quién lo cargó).
 */
class AlcanceDeDatosTest extends TestCase
{
    use RefreshDatabase;

    private Playa $norte;

    private Playa $sur;

    private User $guardavida;

    private BanderaTipo $tipo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable

        Temporada::create([
            'nombre' => 'Actual', 'fecha_inicio_postulacion' => '2026-08-01', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-10-01', 'fecha_fin' => '2027-03-31',
        ]);
        $this->norte = Playa::forceCreate(['nombre' => 'Playa Norte', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $this->sur = Playa::forceCreate(['nombre' => 'Playa Sur', 'color' => '#222222', 'lat' => -38.5, 'lon' => -58.5]);
        $puesto = Puesto::forceCreate(['nombre' => 'Puesto N', 'latitud' => -38.0, 'longitud' => -58.0, 'playa_id' => $this->norte->id, 'qr_encriptado' => 'x']);
        $this->tipo = BanderaTipo::forceCreate(['codigo' => 'V', 'color' => 'green']);

        $this->guardavida = $this->usuario('30000001', 'guardavida');
        Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $this->guardavida->id, 'playa_id' => $this->norte->id, 'puesto_id' => $puesto->id, 'turno' => 'M'])
            ->darDeAlta(Carbon::parse('2026-01-01'), null, 'Alta manual', true);
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

    private function bandera(Playa $playa, ?User $autor = null): Bandera
    {
        return Bandera::forceCreate(['fecha' => now(), 'turno' => 'M', 'bandera_id' => $this->tipo->id, 'playa_id' => $playa->id, 'user_id' => ($autor ?? $this->guardavida)->id]);
    }

    public function test_el_guardavida_ve_las_banderas_de_todas_las_playas(): void
    {
        $propia = $this->bandera($this->norte);
        $ajena = $this->bandera($this->sur, $this->usuario('30000002', 'guardavida'));

        $this->actingAs($this->guardavida)->get(route('bandera.index'))->assertOk()
            ->assertSee('Playa Norte')->assertSee('Playa Sur');
        $this->get(route('bandera.show', $propia))->assertOk();
        $this->get(route('bandera.show', $ajena))->assertOk();
    }

    public function test_los_formularios_ofrecen_todas_las_playas_con_la_propia_preseleccionada(): void
    {
        $html = $this->actingAs($this->guardavida)->get(route('bandera.create'))->assertOk()->getContent();

        $this->assertStringContainsString('Playa Norte', $html);
        $this->assertStringContainsString('Playa Sur', $html);
    }

    public function test_editar_y_borrar_dependen_del_permiso(): void
    {
        // Sin el permiso (se lo sacan al rol desde la pantalla de Permisos), tampoco con la propia.
        $propia = $this->bandera($this->norte);
        Role::findByName('guardavida')->revokePermissionTo('editar_bandera');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->assertFalse($this->guardavida->fresh()->can('update', $propia));
        $this->assertTrue($this->guardavida->fresh()->can('delete', $propia));
    }

    public function test_el_panel_no_limita_los_datos_a_la_playa_del_usuario(): void
    {
        $this->actingAs($this->guardavida);

        // Con o sin ?playa=, el panel responde con todas las playas disponibles para filtrar.
        $this->getJson('/api/dashboard')->assertOk();
        $this->getJson('/api/dashboard?playa='.$this->sur->id)->assertOk();
        $this->get(route('home'))->assertOk()->assertSee('todas las playas')->assertSee('Playa Sur');
    }

    public function test_las_intervenciones_listan_las_de_todas_las_playas(): void
    {
        $encargado = $this->usuario('30000003', 'encargado');
        $this->actingAs($encargado)->get(route('intervencion.index'))->assertOk();
        $this->assertSame(0, Intervencion::count());
    }
}
