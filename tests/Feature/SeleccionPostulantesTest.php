<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Guardavida;
use App\Models\Perfil;
use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use App\Services\SeleccionPostulantes;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeleccionPostulantesTest extends TestCase
{
    use RefreshDatabase;

    private Temporada $temporada;

    private Playa $reta;

    private Playa $claromeco;

    private Puesto $puestoReta;

    private Puesto $puestoClaro;

    protected function setUp(): void
    {
        parent::setUp();

        // Seeder permitido acá: SQLite en memoria, base totalmente descartable.
        $this->seed(RolesYPermisosSeeder::class);

        $this->temporada = Temporada::create([
            'nombre' => '2026/27',
            'fecha_inicio_postulacion' => now()->subDays(10),
            'fecha_fin_postulacion' => now()->subDays(1),
            'fecha_inicio' => now()->addMonths(2),
            'fecha_fin' => now()->addMonths(6),
        ]);
        $this->reta = Playa::forceCreate(['nombre' => 'Reta', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $this->claromeco = Playa::forceCreate(['nombre' => 'Claromecó', 'color' => '#222222', 'lat' => -38.1, 'lon' => -58.1]);
        $this->puestoReta = Puesto::forceCreate(['nombre' => 'Reta 1', 'latitud' => -38, 'longitud' => -58, 'playa_id' => $this->reta->id, 'qr_encriptado' => 'x']);
        $this->puestoClaro = Puesto::forceCreate(['nombre' => 'Claro 1', 'latitud' => -38, 'longitud' => -58, 'playa_id' => $this->claromeco->id, 'qr_encriptado' => 'x']);
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

    private function postulante(string $dni, string $estado = 'aceptada'): Postulacion
    {
        $user = $this->usuario($dni, 'postulante');
        Perfil::create(['user_id' => $user->id, 'telefono' => '2983000000']);

        return Postulacion::create(['user_id' => $user->id, 'temporada_id' => $this->temporada->id, 'estado' => $estado, 'enviada_at' => now()]);
    }

    private function guardavidaExistente(string $dni, string $rol = 'guardavida', string $turno = 'T'): Guardavida
    {
        $user = $this->usuario($dni, $rol);

        return Guardavida::forceCreate([
            'funcion' => $rol === 'encargado' ? 'Encargado' : 'Guardavida', 'nombre' => "N{$dni}", 'apellido' => "A{$dni}", 'dni' => $dni,
            'user_id' => $user->id, 'playa_id' => $this->claromeco->id, 'puesto_id' => $this->puestoClaro->id, 'turno' => $turno,
        ]);
    }

    private function fila(Postulacion $p, ?Puesto $puesto = null, ?string $turno = null, bool $encargado = false): array
    {
        $puesto ??= $this->puestoReta;

        return ['postulacion' => $p->load('user'), 'playa_id' => $puesto->playa_id, 'puesto_id' => $puesto->id, 'turno' => $turno, 'encargado' => $encargado];
    }

    private function admin(): User
    {
        return $this->usuario('20000001', 'admin');
    }

    // ------------------------------------------------------------------ servicio

    public function test_un_postulante_nuevo_pasa_a_guardavida(): void
    {
        $p = $this->postulante('30000001');

        $r = app(SeleccionPostulantes::class)->confirmar([$this->fila($p)]);

        $this->assertSame(['creados' => 1, 'actualizados' => 0], $r);
        $user = $p->user->fresh();
        $this->assertTrue($user->hasRole('guardavida'));
        $this->assertFalse($user->hasRole('postulante'));
        $guardavida = $user->guardavida;
        $this->assertSame($this->reta->id, $guardavida->playa_id);
        $this->assertSame($this->puestoReta->id, $guardavida->puesto_id);
        $this->assertSame('Guardavida', $guardavida->funcion);
        $this->assertNull($guardavida->turno, 'el turno lo carga el propio guardavida');
        $this->assertSame('2983000000', $guardavida->telefono, 'el perfil de la postulación es el mismo');

        $p->refresh();
        $this->assertTrue($p->seleccionado);
        $this->assertSame($this->puestoReta->id, $p->puesto_asignado_id);
        $this->assertSame($this->reta->id, $p->playa_asignada_id);
    }

    public function test_marcar_encargado_da_rol_y_funcion_encargado(): void
    {
        $p = $this->postulante('30000001');

        app(SeleccionPostulantes::class)->confirmar([$this->fila($p, null, 'M', true)]);

        $user = $p->user->fresh();
        $this->assertTrue($user->hasRole('encargado'));
        $this->assertSame('Encargado', $user->guardavida->funcion);
        $this->assertSame('M', $user->guardavida->turno);
    }

    public function test_un_guardavida_existente_se_actualiza_y_conserva_su_turno_y_funcion(): void
    {
        $existente = $this->guardavidaExistente('30000002', 'guardavida', 'T');
        $existente->update(['funcion' => 'Timonel']);
        $p = Postulacion::create(['user_id' => $existente->user_id, 'temporada_id' => $this->temporada->id, 'estado' => 'aceptada', 'enviada_at' => now()]);

        $r = app(SeleccionPostulantes::class)->confirmar([$this->fila($p, $this->puestoReta)]);

        $this->assertSame(['creados' => 0, 'actualizados' => 1], $r);
        $this->assertSame(1, Guardavida::where('user_id', $existente->user_id)->count());
        $existente->refresh();
        $this->assertSame($this->puestoReta->id, $existente->puesto_id);
        $this->assertSame($this->reta->id, $existente->playa_id);
        $this->assertSame('T', $existente->turno);
        $this->assertSame('Timonel', $existente->funcion);
        $this->assertTrue($existente->user->hasRole('guardavida'));
    }

    public function test_nunca_se_toca_a_un_admin(): void
    {
        $p = $this->postulante('30000001');
        $p->user->assignRole('admin');

        $this->expectException(\DomainException::class);

        try {
            app(SeleccionPostulantes::class)->confirmar([$this->fila($p)]);
        } finally {
            $this->assertFalse($p->fresh()->seleccionado);
            $this->assertNull($p->user->fresh()->guardavida);
        }
    }

    public function test_deseleccionar_vuelve_a_postulante_y_corta_las_sesiones_offline(): void
    {
        $p = $this->postulante('30000001');
        app(SeleccionPostulantes::class)->confirmar([$this->fila($p)]);
        $p->user->createToken('sw-token');

        app(SeleccionPostulantes::class)->deseleccionar($p->fresh());

        $user = $p->user->fresh();
        $this->assertTrue($user->hasRole('postulante'));
        $this->assertFalse($user->hasRole('guardavida'));
        $this->assertSame(0, $user->tokens()->count());
        $this->assertFalse($p->fresh()->seleccionado);
        $this->assertNull($p->fresh()->puesto_asignado_id);
        $this->assertNotNull($user->guardavida, 'conserva la fila (historial)');
    }

    public function test_el_cierre_solo_ofrece_a_los_del_plantel_no_seleccionados_y_no_borra_historial(): void
    {
        $sigue = $this->guardavidaExistente('30000010');
        $seVa = $this->guardavidaExistente('30000011');
        $pSigue = Postulacion::create(['user_id' => $sigue->user_id, 'temporada_id' => $this->temporada->id, 'estado' => 'aceptada', 'enviada_at' => now()]);
        app(SeleccionPostulantes::class)->confirmar([$this->fila($pSigue)]);
        $nuevo = $this->postulante('30000012');
        Asistencia::forceCreate([
            'longitud' => -58, 'latitud' => -38, 'precision' => 5, 'puesto_id' => $this->puestoClaro->id,
            'guardavidas_id' => $seVa->id, 'fecha_hora' => '2026-01-15 10:00:00', 'estado_validacion' => 'valido',
        ]);

        $servicio = app(SeleccionPostulantes::class);
        $candidatos = $servicio->candidatosCierre($this->temporada);

        $this->assertSame([$seVa->id], $candidatos->pluck('id')->all(), 'no incluye ni al seleccionado ni a postulantes que nunca fueron guardavidas');

        // Aunque intenten colar a otro id, solo se procesan los candidatos reales.
        $total = $servicio->cerrar($this->temporada, [$seVa->id, $sigue->id, $nuevo->user_id]);

        $this->assertSame(1, $total);
        $this->assertTrue($seVa->user->fresh()->hasRole('postulante'));
        $this->assertTrue($sigue->user->fresh()->hasRole('guardavida'));
        $this->assertSame(1, Asistencia::where('guardavidas_id', $seVa->id)->count(), 'el historial queda');
        $this->assertNotNull(Guardavida::find($seVa->id));
    }

    // ----------------------------------------------------------------- pantallas

    public function test_flujo_completo_por_pantallas(): void
    {
        $p1 = $this->postulante('30000001');
        $p2 = $this->postulante('30000002');
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('postulaciones.seleccion', ['temporada' => $this->temporada->id]))
            ->assertOk()->assertSee('A30000001')->assertSee('A30000002');

        $this->actingAs($admin)->get(route('postulaciones.seleccion.revisar', ['temporada' => $this->temporada->id, 'ids' => "{$p1->id},{$p2->id}"]))
            ->assertOk()->assertSee('Se crea');

        $this->actingAs($admin)->post(route('postulaciones.seleccion.confirmar'), [
            'temporada' => $this->temporada->id,
            'filas' => [
                $p1->id => ['playa_id' => $this->reta->id, 'puesto_id' => $this->puestoReta->id, 'turno' => '', 'encargado' => '0'],
                $p2->id => ['playa_id' => $this->claromeco->id, 'puesto_id' => $this->puestoClaro->id, 'turno' => 'M', 'encargado' => '1'],
            ],
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertTrue($p1->user->fresh()->hasRole('guardavida'));
        $this->assertTrue($p2->user->fresh()->hasRole('encargado'));
        $this->assertSame(2, Postulacion::where('seleccionado', true)->count());

        // Ya seleccionados: se ocultan por defecto de la lista.
        $this->actingAs($admin)->get(route('postulaciones.seleccion', ['temporada' => $this->temporada->id]))
            ->assertOk()->assertDontSee('A30000001');
    }

    public function test_confirmar_sin_playa_no_guarda_nada(): void
    {
        $p = $this->postulante('30000001');

        $this->actingAs($this->admin())->post(route('postulaciones.seleccion.confirmar'), [
            'temporada' => $this->temporada->id,
            'filas' => [$p->id => ['playa_id' => '', 'puesto_id' => '', 'turno' => '', 'encargado' => '0']],
        ])->assertSessionHasErrors('filas.'.$p->id.'.playa_id');

        $this->assertFalse($p->fresh()->seleccionado);
        $this->assertNull($p->user->fresh()->guardavida);
    }

    public function test_se_puede_seleccionar_solo_con_playa_y_sin_puesto(): void
    {
        $p = $this->postulante('30000001');

        $admin = $this->admin();

        $this->actingAs($admin)->post(route('postulaciones.seleccion.confirmar'), [
            'temporada' => $this->temporada->id,
            'filas' => [$p->id => ['playa_id' => $this->reta->id, 'puesto_id' => '', 'turno' => '', 'encargado' => '0']],
        ])->assertSessionHas('success');

        $guardavida = $p->user->fresh()->guardavida;
        $this->assertSame($this->reta->id, $guardavida->playa_id);
        $this->assertNull($guardavida->puesto_id);
        $this->assertNull($p->fresh()->puesto_asignado_id);
        $this->assertSame($this->reta->id, $p->fresh()->playa_asignada_id);

        // Sin puesto el sistema no se rompe: el listado lo muestra y el primer ingreso le pide elegirlo.
        $this->actingAs($admin)->get(route('guardavida.index'))->assertOk()->assertSee('A30000001');
        $this->actingAs($guardavida->user)->get(route('home'))->assertOk();
        $this->assertTrue(session('show_guardavida_setup'));
    }

    public function test_el_puesto_tiene_que_ser_de_la_playa_elegida(): void
    {
        $p = $this->postulante('30000001');

        $this->actingAs($this->admin())->post(route('postulaciones.seleccion.confirmar'), [
            'temporada' => $this->temporada->id,
            'filas' => [$p->id => ['playa_id' => $this->reta->id, 'puesto_id' => $this->puestoClaro->id, 'turno' => '', 'encargado' => '0']],
        ])->assertSessionHasErrors();

        $this->assertFalse($p->fresh()->seleccionado);
    }

    public function test_solo_se_seleccionan_postulaciones_aceptadas(): void
    {
        $pendiente = $this->postulante('30000001', 'pendiente');

        $this->actingAs($this->admin())->post(route('postulaciones.seleccion.confirmar'), [
            'temporada' => $this->temporada->id,
            'filas' => [$pendiente->id => ['puesto_id' => $this->puestoReta->id, 'turno' => '', 'encargado' => '0']],
        ])->assertSessionHasErrors();

        $this->assertFalse($pendiente->fresh()->seleccionado);
    }

    public function test_el_cierre_se_niega_si_todavia_no_se_selecciono_a_nadie(): void
    {
        $g = $this->guardavidaExistente('30000010');

        $this->actingAs($this->admin())->post(route('postulaciones.seleccion.cerrar'), [
            'temporada' => $this->temporada->id, 'guardavidas' => [$g->id], 'entiendo' => '1',
        ])->assertSessionHasErrors();

        $this->assertTrue($g->user->fresh()->hasRole('guardavida'));
    }

    public function test_sin_el_permiso_no_se_accede(): void
    {
        $encargado = $this->usuario('30000099', 'encargado');

        $this->actingAs($encargado)->get(route('postulaciones.seleccion'))->assertForbidden();
        $this->actingAs($encargado)->post(route('postulaciones.seleccion.confirmar'), [])->assertForbidden();
    }
}
