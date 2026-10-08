<?php

namespace Tests\Feature;

use App\Models\Guardavida;
use App\Models\Perfil;
use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use App\Services\ResumenAsistenciaService;
use App\Services\SeleccionPostulantes;
use Carbon\Carbon;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AltaBajaGuardavidaTest extends TestCase
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
            'nombre' => '2026/27',
            'fecha_inicio_postulacion' => now()->subDays(30),
            'fecha_fin_postulacion' => now()->subDays(20),
            'fecha_inicio' => '2026-11-01',
            'fecha_fin' => '2027-03-31',
        ]);
        $this->playa = Playa::forceCreate(['nombre' => 'Reta', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $this->puesto = Puesto::forceCreate(['nombre' => 'Reta 1', 'latitud' => -38, 'longitud' => -58, 'playa_id' => $this->playa->id, 'qr_encriptado' => 'x']);
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

    private function guardavida(string $dni, string $rol = 'guardavida'): Guardavida
    {
        $user = $this->usuario($dni, $rol);

        return Guardavida::forceCreate([
            'funcion' => 'Guardavida', 'nombre' => "N{$dni}", 'apellido' => "A{$dni}", 'dni' => $dni,
            'user_id' => $user->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'turno' => 'M',
        ]);
    }

    private function resumen(Guardavida $g, string $desde, string $hasta): array
    {
        return (new ResumenAsistenciaService)->generar(collect([$g]), Carbon::parse($desde), Carbon::parse($hasta)->endOfDay())[$g->id];
    }

    // ------------------------------------------------------------ presentismo

    public function test_las_faltas_se_cuentan_solo_desde_la_fecha_de_alta(): void
    {
        $g = $this->guardavida('30000001');
        $g->darDeAlta(Carbon::parse('2026-12-01'), $this->temporada->id, null, true);

        // Reporte del 25/11 al 05/12: antes del alta no cuenta nada, y del 1 al 5 son 5 faltas (sin franco configurado).
        $r = $this->resumen($g->fresh(), '2026-11-25', '2026-12-05');

        $this->assertSame(5, $r['dias_totales']);
        $this->assertSame(5, $r['faltas']);
    }

    public function test_despues_de_la_baja_no_se_cuentan_faltas(): void
    {
        $g = $this->guardavida('30000001');
        $g->darDeAlta(Carbon::parse('2026-12-01'), $this->temporada->id, null, true);
        $g->darDeBajaDelPeriodo(Carbon::parse('2026-12-10'), 'renuncia');

        $r = $this->resumen($g->fresh(), '2026-12-01', '2026-12-31');

        $this->assertSame(10, $r['dias_totales'], 'del 1 al 10 inclusive');
        $this->assertSame(10, $r['faltas']);
    }

    public function test_un_dia_con_asistencia_fuera_del_periodo_igual_cuenta(): void
    {
        $g = $this->guardavida('30000001');
        $g->darDeAlta(Carbon::parse('2026-12-10'), $this->temporada->id, null, true);
        \App\Models\Asistencia::forceCreate([
            'longitud' => -58, 'latitud' => -38, 'precision' => 5, 'puesto_id' => $this->puesto->id,
            'guardavidas_id' => $g->id, 'fecha_hora' => '2026-12-08 10:00:00', 'estado_validacion' => 'valido',
        ]);

        $r = $this->resumen($g->fresh(), '2026-12-08', '2026-12-10');

        $this->assertSame(1, $r['asistencias']);
        $this->assertSame(2, $r['dias_totales'], 'el 8 (fichó) y el 10; el 9 es anterior al alta');
    }

    public function test_sin_periodos_se_mantiene_el_comportamiento_de_siempre(): void
    {
        $g = $this->guardavida('30000001'); // anterior a este registro: sin períodos, rol vigente

        $r = $this->resumen($g, '2026-11-25', '2026-12-05');

        $this->assertSame(11, $r['dias_totales']);
        $this->assertSame(11, $r['faltas']);
    }

    public function test_el_primer_alta_de_un_guardavida_anterior_deja_asentado_su_periodo_historico(): void
    {
        $g = Guardavida::find($this->guardavida('30000001')->id);

        $g->darDeAlta(Carbon::parse('2026-12-01'), $this->temporada->id);

        $periodos = $g->periodos()->get();
        $this->assertCount(2, $periodos);
        $this->assertNull($periodos[0]->desde);
        $this->assertSame('2026-11-30', $periodos[0]->hasta->toDateString());
        $this->assertSame('2026-12-01', $periodos[1]->desde->toDateString());
        $this->assertNull($periodos[1]->hasta);

        // Un reporte de fechas anteriores sigue contando como antes.
        $r = $this->resumen($g->fresh(), '2026-11-20', '2026-11-22');
        $this->assertSame(3, $r['dias_totales']);
    }

    public function test_dar_de_alta_dos_veces_no_duplica_el_periodo(): void
    {
        $g = $this->guardavida('30000001');
        $g->darDeAlta(Carbon::parse('2026-12-01'));
        $g->darDeAlta(Carbon::parse('2026-12-05'));

        $this->assertSame(1, $g->periodos()->whereNull('hasta')->count());
    }

    public function test_el_reporte_de_un_mes_incluye_a_quien_se_dio_de_baja_despues(): void
    {
        $g = $this->guardavida('30000001');
        $g->darDeAlta(Carbon::parse('2026-11-01'), $this->temporada->id);
        $g->darDeBajaDelPeriodo(Carbon::parse('2026-11-20'));
        $g->user->syncRoles(['postulante']);

        $nov = Guardavida::activosOConAsistenciaEn('2026-11-01 00:00:00', '2026-11-30 23:59:59')->pluck('id')->all();
        $feb = Guardavida::activosOConAsistenciaEn('2027-02-01 00:00:00', '2027-02-28 23:59:59')->pluck('id')->all();

        $this->assertSame([$g->id], $nov);
        $this->assertSame([], $feb);
    }

    // ------------------------------------------------------------------- baja

    public function test_la_baja_lo_devuelve_a_postulante_sin_perder_nada(): void
    {
        $g = $this->guardavida('30000001');
        $postulacion = Postulacion::create(['user_id' => $g->user_id, 'temporada_id' => $this->temporada->id, 'estado' => 'aceptada', 'seleccionado' => true, 'enviada_at' => now()]);
        $g->darDeAlta(Carbon::parse('2026-11-01'), $this->temporada->id);
        $g->user->createToken('sw-token');
        $admin = $this->usuario('20000001', 'admin');

        $this->actingAs($admin)->post(route('guardavida.baja', $g), ['fecha' => now()->toDateString(), 'motivo' => 'renuncia'])
            ->assertRedirect(route('guardavida.index'))->assertSessionHas('success');

        $user = $g->user->fresh();
        $this->assertTrue($user->hasRole('postulante'));
        $this->assertFalse($user->hasRole('guardavida'));
        $this->assertTrue($user->enabled, 'sigue habilitado: tiene que poder entrar a postularse');
        $this->assertSame(0, $user->tokens()->count());
        $this->assertTrue($postulacion->fresh()->seleccionado, 'conserva la marca de seleccionado');
        $this->assertNotNull(Guardavida::find($g->id));
        $ultimo = $g->periodos()->reorder('id', 'desc')->first();
        $this->assertSame('renuncia', $ultimo->motivo);
        $this->assertNotNull($ultimo->hasta);

        // Ya no aparece en el listado de guardavidas.
        $this->actingAs($admin)->get(route('guardavida.index'))->assertOk()->assertDontSee('u30000001@test.com'); // el aviso de éxito nombra a la persona, el mail solo sale en el listado
    }

    public function test_no_se_puede_dar_de_baja_con_fecha_futura_ni_a_un_postulante(): void
    {
        $g = $this->guardavida('30000001');
        $admin = $this->usuario('20000001', 'admin');

        $this->actingAs($admin)->post(route('guardavida.baja', $g), ['fecha' => now()->addDays(3)->toDateString()])
            ->assertSessionHasErrors('fecha');
        $this->assertTrue($g->user->fresh()->hasRole('guardavida'));

        $g->user->syncRoles(['postulante']);
        $this->actingAs($admin)->post(route('guardavida.baja', $g), ['fecha' => now()->toDateString()])
            ->assertSessionHasErrors();
    }

    public function test_sin_permiso_no_se_puede_dar_de_baja(): void
    {
        $g = $this->guardavida('30000001');
        $otro = $this->usuario('30000002', 'guardavida');

        $this->actingAs($otro)->post(route('guardavida.baja', $g), ['fecha' => now()->toDateString()])->assertForbidden();
        $this->assertTrue($g->user->fresh()->hasRole('guardavida'));
    }

    // -------------------------------------------------------------- selección

    public function test_la_seleccion_da_el_alta_en_la_fecha_indicada_y_el_cierre_la_baja(): void
    {
        $nuevo = $this->usuario('30000010', 'postulante');
        Perfil::create(['user_id' => $nuevo->id, 'telefono' => '1']);
        $p = Postulacion::create(['user_id' => $nuevo->id, 'temporada_id' => $this->temporada->id, 'estado' => 'aceptada', 'enviada_at' => now()]);
        $sale = $this->guardavida('30000011');
        $servicio = app(SeleccionPostulantes::class);

        $servicio->confirmar([['postulacion' => $p->load('user'), 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'turno' => null, 'encargado' => false]], Carbon::parse('2026-12-01'));

        $periodo = $nuevo->fresh()->guardavida->periodos()->first();
        $this->assertSame('2026-12-01', $periodo->desde->toDateString());
        $this->assertSame($this->temporada->id, $periodo->temporada_id);

        $servicio->cerrar($this->temporada, [$sale->id], Carbon::parse('2026-11-30'));

        $this->assertTrue($sale->user->fresh()->hasRole('postulante'));
        $this->assertSame('2026-11-30', $sale->periodos()->reorder('id', 'desc')->first()->hasta->toDateString());
    }

    public function test_corregir_una_seleccion_borra_el_alta_de_esa_temporada(): void
    {
        $nuevo = $this->usuario('30000010', 'postulante');
        $p = Postulacion::create(['user_id' => $nuevo->id, 'temporada_id' => $this->temporada->id, 'estado' => 'aceptada', 'enviada_at' => now()]);
        $servicio = app(SeleccionPostulantes::class);
        $servicio->confirmar([['postulacion' => $p->load('user'), 'playa_id' => $this->playa->id, 'puesto_id' => null, 'turno' => null, 'encargado' => false]]);

        $servicio->deseleccionar($p->fresh());

        $this->assertSame(0, $nuevo->fresh()->guardavida->periodos()->count());
        $this->assertTrue($nuevo->fresh()->hasRole('postulante'));
    }

    public function test_se_puede_volver_a_dar_de_alta_a_alguien_dado_de_baja(): void
    {
        $g = $this->guardavida('30000001');
        $g->update(['funcion' => 'Encargado']);
        $g->darDeAlta(Carbon::parse('2026-11-01'), $this->temporada->id, null, true);
        $admin = $this->usuario('20000001', 'admin');

        $this->actingAs($admin)->post(route('guardavida.baja', $g), ['fecha' => now()->toDateString()]);

        // Aparece en "Dados de baja" y NO en el listado de activos.
        $this->actingAs($admin)->get(route('guardavidas.bajas'))->assertOk()->assertSee('A30000001')->assertSee('Volver a dar de alta');

        $this->actingAs($admin)->post(route('guardavida.reincorporar', $g), ['fecha' => '2026-12-01'])
            ->assertRedirect(route('guardavidas.bajas'))->assertSessionHas('success');

        $user = $g->user->fresh();
        $this->assertTrue($user->hasRole('encargado'), 'recupera el rol según su función');
        $this->assertFalse($user->hasRole('postulante'));
        $this->assertTrue($user->enabled);
        $abierto = $g->periodos()->whereNull('hasta')->first();
        $this->assertSame('2026-12-01', $abierto->desde->toDateString());
        $this->assertSame(2, $g->periodos()->count(), 'el período anterior (cerrado) queda como historial');
        // El aviso de éxito nombra a la persona: se mira el DNI, que solo sale en la tabla.
        $this->actingAs($admin)->get(route('guardavidas.bajas'))->assertOk()->assertDontSee('<td class="px-4 py-2">30000001</td>', false);
    }

    public function test_no_se_reincorpora_a_quien_ya_esta_en_el_plantel_ni_sin_permiso(): void
    {
        $g = $this->guardavida('30000001');
        $admin = $this->usuario('20000001', 'admin');
        $otro = $this->usuario('30000002', 'guardavida');

        $this->actingAs($admin)->post(route('guardavida.reincorporar', $g), ['fecha' => '2026-12-01'])->assertSessionHasErrors();
        $this->assertSame(0, $g->periodos()->count());

        $g->user->syncRoles(['postulante']);
        $this->actingAs($otro)->post(route('guardavida.reincorporar', $g), ['fecha' => '2026-12-01'])->assertForbidden();
        $this->assertTrue($g->user->fresh()->hasRole('postulante'));
    }

    // ------------------------------------------------------ alta manual / postularme

    private function datosAltaManual(array $extra = []): array
    {
        return $extra + [
            'nombre' => 'Lucía', 'apellido' => 'Gómez', 'dni' => '31222333', 'email' => 'lucia@test.com', 'rol' => 'guardavida',
            'telefono' => '2983000000', 'direccion' => 'Calle 1', 'numero' => '10',
            'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'funcion' => 'Guardavida', 'turno' => 'M',
            'fecha_alta' => '2026-12-01',
        ];
    }

    public function test_el_alta_manual_queda_en_el_plantel_con_su_periodo_y_temporada_y_sin_postulacion(): void
    {
        $admin = $this->usuario('20000001', 'admin');

        $this->actingAs($admin)->post(route('guardavida.store'), $this->datosAltaManual())->assertRedirect(route('guardavida.index'));

        $user = User::where('dni', '31222333')->first();
        $this->assertTrue($user->hasRole('guardavida'));
        $this->assertSame(0, Postulacion::where('user_id', $user->id)->count(), 'el alta manual no inventa una postulación');
        $periodo = $user->guardavida->periodos()->first();
        $this->assertSame('2026-12-01', $periodo->desde->toDateString());
        $this->assertSame($this->temporada->id, $periodo->temporada_id);
        $this->assertSame(1, $user->guardavida->periodos()->count(), 'no hay período histórico: es una persona nueva');
        $this->assertNotNull(Perfil::where('user_id', $user->id)->first(), 'tiene perfil para precargar su próxima postulación');
    }

    public function test_el_alta_manual_de_alguien_que_ya_existe_explica_que_hacer(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $this->usuario('31222333', 'postulante');

        $this->actingAs($admin)->post(route('guardavida.store'), $this->datosAltaManual(['email' => 'otro@test.com']))
            ->assertSessionHasErrors(['dni' => 'Ya existe una persona con ese DNI. No la des de alta de nuevo: si se postuló, seleccionala desde Postulaciones; si ya fue guardavida, volvela a dar de alta desde "Dados de baja".']);
    }

    public function test_quien_entro_por_alta_manual_se_postula_y_se_selecciona_sin_chocar(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $this->actingAs($admin)->post(route('guardavida.store'), $this->datosAltaManual());
        $user = User::where('dni', '31222333')->first();
        $guardavida = $user->guardavida;

        $user->update(['must_change_psw' => false]); // ya cambió la clave inicial

        // Con la inscripción abierta, ve el acceso en su panel y puede postularse (sigue siendo guardavida).
        $abierta = Temporada::create([
            'nombre' => '2027/28', 'fecha_inicio_postulacion' => now()->subDays(2), 'fecha_fin_postulacion' => now()->addDays(10),
            'fecha_inicio' => '2027-11-01', 'fecha_fin' => '2028-03-31',
        ]);
        $this->actingAs($user)->get(route('home'))->assertOk()->assertSee('INSCRIPCIÓN ABIERTA')->assertSee('2027/28')->assertSee('Postularme');
        $this->actingAs($user)->get(route('postulacion.index'))->assertOk();

        // Selección: ya tiene un período abierto, no se duplica; se actualiza su fila (no se crea otra).
        $p = Postulacion::create(['user_id' => $user->id, 'temporada_id' => $abierta->id, 'estado' => 'aceptada', 'enviada_at' => now()]);
        $r = app(SeleccionPostulantes::class)->confirmar([['postulacion' => $p->load('user'), 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'turno' => null, 'encargado' => false]], Carbon::parse('2027-11-01'));

        $this->assertSame(['creados' => 0, 'actualizados' => 1], $r);
        $this->assertSame(1, Guardavida::where('user_id', $user->id)->count());
        $this->assertSame(1, $guardavida->periodos()->whereNull('hasta')->count());
    }

    public function test_si_el_ano_siguiente_no_es_seleccionado_el_cierre_lo_pasa_a_postulante(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $this->actingAs($admin)->post(route('guardavida.store'), $this->datosAltaManual());
        $user = User::where('dni', '31222333')->first();
        $otra = $this->usuario('30000090', 'postulante');
        $siguiente = Temporada::create([
            'nombre' => '2027/28', 'fecha_inicio_postulacion' => now()->subDays(30), 'fecha_fin_postulacion' => now()->subDays(20),
            'fecha_inicio' => '2027-11-01', 'fecha_fin' => '2028-03-31',
        ]);
        $p = Postulacion::create(['user_id' => $otra->id, 'temporada_id' => $siguiente->id, 'estado' => 'aceptada', 'enviada_at' => now()]);
        $servicio = app(SeleccionPostulantes::class);
        $servicio->confirmar([['postulacion' => $p->load('user'), 'playa_id' => $this->playa->id, 'puesto_id' => null, 'turno' => null, 'encargado' => false]], Carbon::parse('2027-11-01'));

        $this->assertContains($user->guardavida->id, $servicio->candidatosCierre($siguiente)->pluck('id')->all());
        $servicio->cerrar($siguiente, [$user->guardavida->id], Carbon::parse('2027-10-31'));

        $this->assertTrue($user->fresh()->hasRole('postulante'));
    }
}
