<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Guardavida;
use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use App\Services\SeleccionPostulantes;
use Carbon\Carbon;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Rol `personal`: empleados fijos (p. ej. aeródromo) que solo usan el sistema para fichar.
 * No se postulan, no dependen de la temporada y no acceden a los módulos de playa.
 */
class PersonalFijoTest extends TestCase
{
    use RefreshDatabase;

    private Temporada $temporada;

    private Playa $playa;

    private Puesto $puesto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable

        $this->temporada = Temporada::create([
            'nombre' => '2026/27', 'fecha_inicio_postulacion' => '2026-08-01', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-11-01', 'fecha_fin' => '2027-03-31',
        ]);
        $this->playa = Playa::forceCreate(['nombre' => 'Tres Arroyos', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $this->puesto = Puesto::forceCreate(['nombre' => 'Aeródromo', 'latitud' => -38.0, 'longitud' => -58.0, 'playa_id' => $this->playa->id, 'qr_encriptado' => 'x']);
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

    /** Un empleado fijo ya dado de alta (con su fila de guardavidas y su período). */
    private function personal(string $dni = '30111222'): User
    {
        $user = $this->usuario($dni, 'personal');
        $g = Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $user->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'turno' => 'M']);
        $g->darDeAlta(Carbon::parse('2026-01-01'), null, 'Alta manual', true);

        return $user->fresh();
    }

    private function fichar(User $user, string $fechaHora): \Illuminate\Testing\TestResponse
    {
        Sanctum::actingAs($user);

        return $this->postJson('/api/cargarAsistencia', [
            'user_id' => $user->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id,
            'lat' => -38.0, 'lng' => -58.0, 'precision' => 10, 'fecha_hora' => $fechaHora,
        ]);
    }

    // ------------------------------------------------------------------------------------ alta

    public function test_el_alta_manual_de_personal_no_crea_postulacion_ni_temporada(): void
    {
        $admin = $this->usuario('20000001', 'admin');

        $this->actingAs($admin)->post(route('guardavida.store'), [
            'nombre' => 'José', 'apellido' => 'Pérez', 'dni' => '28111222', 'email' => 'jose@test.com', 'rol' => 'personal',
            'telefono' => '2983000000', 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id,
            'funcion' => 'Guardavida', 'turno' => 'M', 'fecha_alta' => '2026-12-01', 'dias_franco' => [0, 6],
        ])->assertRedirect(route('guardavida.index'))->assertSessionHasNoErrors();

        $user = User::where('dni', '28111222')->firstOrFail();
        $this->assertTrue($user->hasRole('personal'));
        $this->assertNotNull($user->guardavida, 'tiene fila para poder fichar');
        $this->assertSame(0, Postulacion::count());
        $periodo = $user->guardavida->periodos()->sole();
        $this->assertNull($periodo->temporada_id);
        $this->assertNull($periodo->hasta);
        $this->assertSame([0, 6], $user->guardavida->diasFrancoActuales());
    }

    public function test_el_formulario_de_alta_ofrece_el_rol_personal(): void
    {
        $this->actingAs($this->usuario('20000001', 'admin'))->get(route('guardavida.create'))
            ->assertOk()->assertSee('value="personal"', false);
    }

    // ----------------------------------------------------------------------------------- fichaje

    public function test_el_personal_ficha_normalmente(): void
    {
        $user = $this->personal();

        $this->assertNotNull(Guardavida::obtenerGuardavidas($user->id), 'cuenta como plantel');

        $this->fichar($user, '2026-12-01 08:00:00')->assertOk()->assertJsonPath('success', true);
        $this->fichar($user, '2026-12-01 14:00:00')->assertOk();

        $this->assertSame(2, Asistencia::where('guardavidas_id', $user->guardavida->id)->count());
    }

    public function test_un_personal_deshabilitado_no_puede_fichar(): void
    {
        $user = $this->personal();
        $user->update(['enabled' => false]);

        $this->fichar($user, '2026-12-01 08:00:00')->assertForbidden();
    }

    /** Migración en producción: José Luis pasa de rol guardavida a personal sin perder nada. */
    public function test_pasar_de_guardavida_a_personal_no_interrumpe_el_fichaje_ni_el_historial(): void
    {
        $user = $this->usuario('30222333', 'guardavida');
        $g = Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $user->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'turno' => 'M']);
        $this->fichar($user, '2026-12-01 08:00:00')->assertOk();

        $user->syncRoles(['personal']);

        $this->fichar($user->fresh(), '2026-12-02 08:00:00')->assertOk();
        $this->assertSame(2, Asistencia::where('guardavidas_id', $g->id)->count(), 'conserva el historial');
        $this->assertSame($g->id, $user->fresh()->guardavida->id, 'misma fila de guardavidas');
    }

    // -------------------------------------------------------------------------- panel y accesos

    public function test_el_panel_muestra_mi_turno_pero_no_bandera_ni_postulacion(): void
    {
        $user = $this->personal();
        // Aunque haya una inscripción abierta, a personal no se le ofrece postularse.
        Temporada::create([
            'nombre' => '2027/28', 'fecha_inicio_postulacion' => now()->subDays(2), 'fecha_fin_postulacion' => now()->addDays(10),
            'fecha_inicio' => '2027-11-01', 'fecha_fin' => '2028-03-31',
        ]);
        $user->guardavida->establecerDiasFranco([0, 6], $user->id);

        $this->actingAs($user)->get(route('home'))->assertOk()
            ->assertSee('Mi turno')
            ->assertDontSee('INSCRIPCIÓN ABIERTA')
            ->assertDontSee('Postularme')
            ->assertDontSee('Cambios de Franco');
    }

    public function test_ve_sus_asistencias_y_su_perfil(): void
    {
        $user = $this->personal();
        $user->guardavida->establecerDiasFranco([0, 6], $user->id);

        $this->actingAs($user)->get(route('guardavida.misAsistencias'))->assertOk();
        $this->actingAs($user)->get(route('guardavida.myProfile'))->assertOk();
        $this->actingAs($user)->get(route('activeCamera'))->assertOk();
    }

    public function test_no_accede_a_los_modulos_de_playa_ni_a_postulaciones_ni_al_franco(): void
    {
        $user = $this->personal();

        foreach (['bandera.index', 'intervencion.index', 'novedad-de-material.index', 'guardavida.index', 'guardavidas.bajas', 'guardavidas.export', 'asistencias.index', 'licencia.index', 'cambio-de-turno.index', 'postulaciones.index'] as $ruta) {
            $this->assertSame(403, $this->actingAs($user)->get(route($ruta))->status(), $ruta);
        }
        $this->actingAs($user)->get(route('postulacion.index'))->assertForbidden();
        $this->actingAs($user)->get(route('franco-intercambio.index'))->assertForbidden();
    }

    // ------------------------------------------------------------------ temporada y cierre

    public function test_el_cierre_de_temporada_no_lo_toca(): void
    {
        $personal = $this->personal();
        $servicio = app(SeleccionPostulantes::class);

        $this->assertNotContains($personal->guardavida->id, $servicio->candidatosCierre($this->temporada)->pluck('id')->all());
        $this->assertSame(0, $servicio->cerrar($this->temporada, [$personal->guardavida->id], Carbon::parse('2027-03-31')));

        $personal->refresh();
        $this->assertTrue($personal->hasRole('personal'));
        $this->assertNull($personal->guardavida->periodos()->first()->hasta, 'su período sigue abierto');
        $this->assertNotNull(Guardavida::obtenerGuardavidas($personal->id));
    }

    public function test_no_aparece_en_dados_de_baja_ni_se_puede_seleccionar_como_postulante(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $personal = $this->personal();

        $this->actingAs($admin)->get(route('guardavidas.bajas'))->assertOk()->assertDontSee($personal->lastname);

        $p = Postulacion::create(['user_id' => $personal->id, 'temporada_id' => $this->temporada->id, 'estado' => 'aceptada', 'enviada_at' => now()]);
        $this->expectException(\DomainException::class);
        app(SeleccionPostulantes::class)->confirmar([['postulacion' => $p->load('user'), 'playa_id' => $this->playa->id, 'puesto_id' => null, 'turno' => null, 'encargado' => false]]);
    }

    public function test_aparece_en_el_listado_de_asistencias_del_admin(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $personal = $this->personal();
        $this->fichar($personal, now()->format('Y-m-d').' 08:00:00')->assertOk();

        $this->actingAs($admin)->get(route('asistencias.index'))->assertOk()->assertSee($personal->lastname);
        $this->actingAs($admin)->get(route('guardavida.index'))->assertOk()->assertSee($personal->lastname);
    }

    // ------------------------------------------------------------------------------------- baja

    public function test_la_baja_de_personal_lo_deshabilita_sin_pasarlo_a_postulante(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $user = $this->personal();
        $user->createToken('sw-token');

        $this->actingAs($admin)->post(route('guardavida.baja', $user->guardavida), ['fecha' => now()->toDateString(), 'motivo' => 'Renuncia'])
            ->assertRedirect(route('guardavida.index'))->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue($user->hasRole('personal'), 'conserva el rol');
        $this->assertFalse($user->hasRole('postulante'));
        $this->assertFalse((bool) $user->enabled);
        $this->assertSame(0, $user->tokens()->count());
        $this->assertNotNull($user->guardavida->periodos()->first()->hasta);
        $this->assertNull(Guardavida::obtenerGuardavidas($user->id), 'deja de ser plantel');
        $this->assertSame(0, Postulacion::count());

        // Ya no está en el listado principal y pasa a "Dados de baja".
        $this->actingAs($admin)->get(route('guardavida.index'))->assertOk(); // consume el aviso de éxito, que nombra a la persona
        $this->actingAs($admin)->get(route('guardavida.index'))->assertOk()->assertDontSee($user->lastname);
        $this->actingAs($admin)->get(route('guardavidas.bajas'))->assertOk()->assertSee($user->lastname);
    }

    public function test_el_personal_dado_de_baja_se_puede_volver_a_dar_de_alta(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $user = $this->personal();
        $this->actingAs($admin)->post(route('guardavida.baja', $user->guardavida), ['fecha' => now()->toDateString()]);

        $this->actingAs($admin)->post(route('guardavida.reincorporar', $user->guardavida), ['fecha' => now()->toDateString()])
            ->assertRedirect(route('guardavidas.bajas'))->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertTrue($user->hasRole('personal'));
        $this->assertTrue((bool) $user->enabled);
        $this->assertNotNull(Guardavida::obtenerGuardavidas($user->id));
        $this->assertSame(0, Postulacion::count(), 'no se le crea postulación');
        $this->assertNull($user->guardavida->periodos()->whereNull('hasta')->first()->temporada_id);
    }

    public function test_la_pestana_de_roles_ofrece_la_baja_del_personal(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $user = $this->personal();

        $this->actingAs($admin)->get(route('guardavida.edit', $user->guardavida))->assertOk()
            ->assertSee('Dar de baja')->assertSee('Su usuario queda deshabilitado');
    }
}
