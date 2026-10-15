<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Bandera;
use App\Models\CambioDeTurno;
use App\Models\Guardavida;
use App\Models\Intervencion;
use App\Models\Licencia;
use App\Models\Novedad;
use App\Models\NovedadMaterial;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use App\Services\ReasignadorTemporadas;
use Carbon\Carbon;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

/**
 * Fase 2: cada registro operativo pertenece a una temporada, calculada siempre a partir de SU fecha.
 */
class TemporadaEnRegistrosTest extends TestCase
{
    use RefreshDatabase;

    private Temporada $pasada;

    private Temporada $actual;

    private Playa $playa;

    private Puesto $puesto;

    private User $admin;

    private \App\Models\BanderaTipo $banderaTipo;

    private \App\Models\Material $material;

    private Guardavida $guardavida;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable

        // 2025-2026: temporada 1/11/2025 al 30/4/2026. 2026-2027 (en curso): 1/11/2026 al 30/4/2027.
        $this->pasada = Temporada::create([
            'nombre' => '2025-2026', 'fecha_inicio_postulacion' => '2025-07-15', 'fecha_fin_postulacion' => '2025-08-31',
            'fecha_inicio' => '2025-11-01', 'fecha_fin' => '2026-04-30',
        ]);
        $this->actual = Temporada::create([
            'nombre' => '2026-2027', 'fecha_inicio_postulacion' => '2026-07-15', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-11-01', 'fecha_fin' => '2027-04-30',
        ]);
        $this->playa = Playa::forceCreate(['nombre' => 'Reta', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $this->puesto = Puesto::forceCreate(['nombre' => 'Reta 1', 'latitud' => -38, 'longitud' => -58, 'playa_id' => $this->playa->id, 'qr_encriptado' => 'x']);
        $this->admin = $this->usuario('20000001', 'admin');
        $this->banderaTipo = \App\Models\BanderaTipo::forceCreate(['codigo' => 'V', 'color' => 'green']);
        $this->material = \App\Models\Material::forceCreate(['nombre' => 'Tabla', 'playa_id' => $this->playa->id]);
        $this->guardavida = $this->usuario('30000099', 'guardavida', true)->guardavida;
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function usuario(string $dni, string $rol, bool $conFicha = false): User
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

    private function intervencion(string $fecha): Intervencion
    {
        return Intervencion::forceCreate(['fecha' => $fecha, 'tipo_intervencion' => 'Rescate', 'victimas' => 1, 'codigo' => 1, 'traslado' => false,
            'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'user_id' => $this->admin->id]);
    }

    private function bandera(string $fecha, ?int $userId = null): Bandera
    {
        return Bandera::forceCreate(['fecha' => $fecha, 'turno' => 'M', 'bandera_id' => $this->banderaTipo->id, 'playa_id' => $this->playa->id, 'user_id' => $userId ?? $this->admin->id]);
    }

    private function novedadMaterial(string $fecha): NovedadMaterial
    {
        return NovedadMaterial::forceCreate(['tipo_novedad' => 'Daño', 'material_id' => $this->material->id, 'fecha' => $fecha, 'playa_id' => $this->playa->id]);
    }

    private function licencia(string $inicio): Licencia
    {
        return Licencia::forceCreate(['fecha_inicio' => $inicio, 'fecha_fin' => Carbon::parse($inicio)->addDays(3)->toDateString(), 'tipo_licencia' => 'Enfermedad',
            'en_tiempo' => true, 'turno' => 'M', 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'guardavida_id' => $this->guardavida->id]);
    }

    private function cambioDeTurno(string $fecha): CambioDeTurno
    {
        return CambioDeTurno::forceCreate(['fecha' => $fecha, 'turno_nuevo' => 'T', 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'funcion' => 'Guardavida', 'guardavida_id' => $this->guardavida->id]);
    }

    // ------------------------------------------------------------------- a qué temporada pertenece un dato

    public function test_la_temporada_de_un_dato_es_la_ultima_que_ya_habia_empezado(): void
    {
        $this->assertSame($this->pasada->id, Temporada::paraDato('2025-11-01')->id, 'primer día');
        $this->assertSame($this->pasada->id, Temporada::paraDato('2026-04-30')->id, 'último día');
        $this->assertSame($this->pasada->id, Temporada::paraDato('2026-06-15')->id, 'entre temporadas: la que terminó');
        $this->assertSame($this->pasada->id, Temporada::paraDato('2026-10-31')->id, 'víspera de la nueva');
        $this->assertSame($this->actual->id, Temporada::paraDato('2026-11-01')->id, 'arranca la nueva');
        $this->assertSame($this->actual->id, Temporada::paraDato('2028-01-01')->id, 'después de la última');
        $this->assertSame($this->pasada->id, Temporada::paraDato('2020-01-01')->id, 'antes de la primera: la primera');
        $this->assertSame($this->actual->id, Temporada::paraDato(Carbon::parse('2027-01-10 15:30:00'))->id, 'con hora');
    }

    public function test_sin_temporadas_no_hay_temporada_de_un_dato(): void
    {
        Temporada::query()->delete();

        $this->assertNull(Temporada::paraDato('2026-12-01'));
        $this->assertNull(Temporada::paraListados());
    }

    public function test_la_temporada_por_defecto_de_los_listados(): void
    {
        Carbon::setTestNow('2026-12-15');
        $this->assertSame($this->actual->id, Temporada::paraListados()->id, 'la activa');

        Carbon::setTestNow('2026-09-10'); // todavía no empezó la nueva: se muestra la anterior
        $this->assertSame($this->pasada->id, Temporada::paraListados()->id);

        Carbon::setTestNow('2027-06-01'); // ya terminó la actual y no hay otra: se sigue mostrando la última
        $this->assertSame($this->actual->id, Temporada::paraListados()->id);

        Carbon::setTestNow('2024-01-01'); // ninguna empezó: la primera
        $this->assertSame($this->pasada->id, Temporada::paraListados()->id);
    }

    public function test_el_parametro_de_temporada_de_los_listados(): void
    {
        Carbon::setTestNow('2026-12-15');

        [$todas, $elegida] = Temporada::seleccionada(null);
        $this->assertSame([$this->actual->id, $this->pasada->id], $todas->pluck('id')->all(), 'la más nueva primero');
        $this->assertSame($this->actual->id, $elegida->id);
        $this->assertSame($this->pasada->id, Temporada::seleccionada((string) $this->pasada->id)[1]->id);
        $this->assertNull(Temporada::seleccionada('todas')[1]);
        $this->assertSame($this->actual->id, Temporada::seleccionada('9999')[1]->id, 'un id inexistente cae en la vigente');
    }

    // ---------------------------------------------------------------- asignación automática al guardar

    public function test_cada_registro_se_asigna_segun_su_fecha_y_no_segun_hoy(): void
    {
        Carbon::setTestNow('2026-12-15'); // hoy es de la temporada actual…

        // …pero los registros se cargan con fechas de la anterior.
        $this->assertSame($this->pasada->id, $this->intervencion('2026-01-10 12:00:00')->temporada_id);
        $this->assertSame($this->pasada->id, $this->bandera('2026-01-10 09:00:00')->temporada_id);
        $this->assertSame($this->pasada->id, $this->novedadMaterial('2026-01-10 09:00:00')->temporada_id);
        $this->assertSame($this->pasada->id, $this->licencia('2026-01-10')->temporada_id);
        $this->assertSame($this->pasada->id, $this->cambioDeTurno('2026-01-10')->temporada_id);
        $this->assertSame($this->pasada->id, Novedad::forceCreate(['fecha' => '2026-01-10 09:00:00', 'tipo' => 'x'])->temporada_id);

        $this->assertSame($this->actual->id, $this->intervencion('2026-12-01 12:00:00')->temporada_id);
        $this->assertSame($this->actual->id, $this->licencia('2026-12-01')->temporada_id);
    }

    public function test_los_fichajes_offline_quedan_en_la_temporada_de_su_fecha(): void
    {
        Carbon::setTestNow('2026-12-15');
        $guardavida = Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $this->usuario('30000001', 'guardavida')->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'turno' => 'M']);

        // Un celular sincroniza hoy un fichaje hecho en enero.
        $viejo = Asistencia::nuevaAsistencia(-58, -38, 10, $this->puesto->id, $guardavida->id, '2026-01-20 08:00:00');
        $nuevo = Asistencia::nuevaAsistencia(-58, -38, 10, $this->puesto->id, $guardavida->id, '2026-12-15 08:00:00');
        // Un reintento de la misma sincronización no duplica ni cambia nada.
        $reintento = Asistencia::nuevaAsistencia(-58, -38, 10, $this->puesto->id, $guardavida->id, '2026-01-20 08:00:00');

        $this->assertSame($this->pasada->id, $viejo->fresh()->temporada_id);
        $this->assertSame($this->actual->id, $nuevo->fresh()->temporada_id);
        $this->assertSame($viejo->id, $reintento->id);
        $this->assertSame(2, Asistencia::count());
    }

    public function test_si_se_cambia_la_fecha_el_registro_cambia_de_temporada(): void
    {
        $intervencion = $this->intervencion('2026-01-10 12:00:00');
        $this->assertSame($this->pasada->id, $intervencion->temporada_id);

        $intervencion->update(['fecha' => '2026-12-10 12:00:00']);
        $this->assertSame($this->actual->id, $intervencion->fresh()->temporada_id);

        $intervencion->update(['detalles' => 'Sin cambio de fecha']);
        $this->assertSame($this->actual->id, $intervencion->fresh()->temporada_id, 'editar otro campo no la toca');

        $licencia = $this->licencia('2026-12-10');
        $licencia->update(['fecha_inicio' => '2026-02-01']);
        $this->assertSame($this->pasada->id, $licencia->fresh()->temporada_id, 'la licencia se rige por su fecha de inicio');
    }

    public function test_sin_temporadas_el_registro_queda_sin_temporada_y_se_corrige_al_crear_una(): void
    {
        Temporada::query()->delete();
        $intervencion = $this->intervencion('2026-12-10 12:00:00');
        $this->assertNull($intervencion->temporada_id);

        $this->actingAs($this->admin)->post(route('temporada.store'), [
            'nombre' => '2026-2027', 'fecha_inicio_postulacion' => '2026-07-15', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-11-01', 'fecha_fin' => '2027-04-30',
        ])->assertRedirect(route('temporada.index'))->assertSessionHasNoErrors();

        $this->assertSame(Temporada::sole()->id, $intervencion->fresh()->temporada_id);
    }

    // --------------------------------------------------------------------- reasignación al tocar temporadas

    public function test_crear_una_temporada_reasigna_los_registros_posteriores_a_su_inicio(): void
    {
        $registro = $this->intervencion('2027-08-10 12:00:00'); // hoy cae en la actual (no hay otra)
        $this->assertSame($this->actual->id, $registro->temporada_id);

        $this->actingAs($this->admin)->post(route('temporada.store'), [
            'nombre' => '2027-2028', 'fecha_inicio_postulacion' => '2027-07-15', 'fecha_fin_postulacion' => '2027-08-31',
            'fecha_inicio' => '2027-11-01', 'fecha_fin' => '2028-04-30',
        ])->assertSessionHasNoErrors();
        $this->assertSame($this->actual->id, $registro->fresh()->temporada_id, 'agosto sigue en la anterior');

        $posterior = $this->intervencion('2027-12-01 12:00:00');
        $this->assertSame(Temporada::where('nombre', '2027-2028')->value('id'), $posterior->temporada_id);
    }

    public function test_cambiar_el_inicio_de_una_temporada_mueve_los_registros_de_lado(): void
    {
        $registro = $this->intervencion('2026-10-20 12:00:00');
        $this->assertSame($this->pasada->id, $registro->temporada_id);

        // La temporada actual pasa a empezar el 15/10: ese registro ahora es suyo.
        $this->actingAs($this->admin)->put(route('temporada.update', $this->actual), [
            'nombre' => '2026-2027', 'fecha_inicio_postulacion' => '2026-07-15', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-10-15', 'fecha_fin' => '2027-04-30',
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertSame($this->actual->id, $registro->fresh()->temporada_id);
    }

    public function test_el_boton_recalcular_corrige_lo_que_quedo_mal_asignado(): void
    {
        $registro = $this->intervencion('2026-01-10 12:00:00');
        DB::table('intervencions')->where('id', $registro->id)->update(['temporada_id' => $this->actual->id]); // mal, por fuera de la app
        DB::table('intervencions')->insert(['fecha' => '2026-12-01 10:00:00', 'tipo_intervencion' => 'X', 'victimas' => 0, 'codigo' => 1, 'traslado' => 0,
            'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'user_id' => $this->admin->id, 'temporada_id' => null]);

        $this->actingAs($this->admin)->post(route('temporada.reasignar'))->assertRedirect(route('temporada.index'))->assertSessionHas('success');

        $this->assertSame($this->pasada->id, $registro->fresh()->temporada_id);
        $this->assertSame(0, Intervencion::whereNull('temporada_id')->count());
        $this->assertSame([], array_filter(app(ReasignadorTemporadas::class)->reasignar()), 'una segunda pasada no cambia nada');

        $this->actingAs($this->usuario('30000001', 'guardavida', true))->post(route('temporada.reasignar'))->assertForbidden();
    }

    public function test_una_temporada_con_registros_no_se_puede_borrar(): void
    {
        $this->bandera('2026-01-10 09:00:00');

        $this->actingAs($this->admin)->delete(route('temporada.destroy', $this->pasada))
            ->assertRedirect(route('temporada.index'))->assertSessionHas('error');
        $this->assertNotNull(Temporada::find($this->pasada->id));

        $this->actingAs($this->admin)->delete(route('temporada.destroy', $this->actual))->assertSessionHas('success'); // sin registros: sí
        $this->assertNull(Temporada::find($this->actual->id));
    }

    public function test_la_pantalla_de_temporadas_muestra_los_registros_de_cada_una(): void
    {
        $this->intervencion('2026-01-10 12:00:00');
        $this->bandera('2026-01-11 09:00:00');

        $this->actingAs($this->admin)->get(route('temporada.index'))->assertOk()->assertSee('Recalcular registros')->assertSee('Registros');
        $this->assertSame(['intervencions' => 1, 'banderas' => 1, 'novedades' => 2], array_filter($this->pasada->registrosAsociados(), fn ($n) => $n > 0), 'la intervención y la bandera generan además su novedad en el feed');
    }

    // -------------------------------------------------------------------------------------- listados

    public function test_los_listados_del_celular_muestran_cada_registro_como_tarjeta_con_su_menu(): void
    {
        Carbon::setTestNow('2026-12-15');
        $this->intervencion('2026-12-10 12:00:00')->update(['traslado' => true]);
        $this->bandera('2026-12-10 09:00:00');
        $this->novedadMaterial('2026-12-10 12:00:00');
        $this->licencia('2026-12-10');
        $this->cambioDeTurno('2026-12-10');

        foreach (['intervencion.index', 'bandera.index', 'novedad-de-material.index', 'licencia.index', 'cambio-de-turno.index'] as $ruta) {
            $this->actingAs($this->admin)->get(route($ruta))->assertOk()
                ->assertSee('Más acciones de', false)->assertSee('x-ref="formMenu"', false) // los tres puntos y el menú inferior compartido
                ->assertDontSee('drawer-bottom-example', false);
        }

        $this->actingAs($this->admin)->get(route('intervencion.index'))->assertSee('Hubo traslado');
    }

    public function test_los_listados_filtran_por_temporada_con_la_actual_por_defecto(): void
    {
        Carbon::setTestNow('2026-12-15');
        $this->intervencion('2026-01-10 12:00:00')->update(['detalles' => 'VIEJA-INT']);
        $this->intervencion('2026-12-10 12:00:00')->update(['detalles' => 'NUEVA-INT']);
        $this->novedadMaterial('2026-01-10 12:00:00')->update(['detalles' => 'VIEJA-NOV']);
        $this->novedadMaterial('2026-12-10 12:00:00')->update(['detalles' => 'NUEVA-NOV']);
        $this->licencia('2026-01-10')->update(['detalle' => 'VIEJA-LIC']);
        $this->licencia('2026-12-10')->update(['detalle' => 'NUEVA-LIC']);
        $this->cambioDeTurno('2026-01-10')->update(['detalles' => 'VIEJO-CT']);
        $this->cambioDeTurno('2026-12-10')->update(['detalles' => 'NUEVO-CT']);

        $casos = [
            ['intervencion.index', 'viejo' => 'VIEJA-INT', 'nuevo' => 'NUEVA-INT'],
            ['novedad-de-material.index', 'viejo' => 'VIEJA-NOV', 'nuevo' => 'NUEVA-NOV'],
            ['licencia.index', 'viejo' => 'VIEJA-LIC', 'nuevo' => 'NUEVA-LIC'],
        ];
        foreach ($casos as $caso) {
            $ruta = $caso[0];
            // Por defecto: la temporada en curso.
            $this->actingAs($this->admin)->get(route($ruta))->assertOk()->assertSee($caso['nuevo'])->assertDontSee($caso['viejo']);
            // La histórica.
            $this->actingAs($this->admin)->get(route($ruta, ['temporada' => $this->pasada->id]))->assertOk()->assertSee($caso['viejo'])->assertDontSee($caso['nuevo']);
            // Todas.
            $this->actingAs($this->admin)->get(route($ruta, ['temporada' => 'todas']))->assertOk()->assertSee($caso['viejo'])->assertSee($caso['nuevo']);
        }

        // El selector aparece con las dos temporadas y "Todas".
        $this->actingAs($this->admin)->get(route('intervencion.index'))->assertSee('selector-temporada', false)->assertSee('2025-2026')->assertSee('Todas');

        // Cambios de turno: el listado paginado.
        $this->actingAs($this->admin)->get(route('cambio-de-turno.index'))->assertOk()->assertSee('NUEVO-CT')->assertDontSee('VIEJO-CT');
        $this->actingAs($this->admin)->get(route('cambio-de-turno.index', ['temporada' => 'todas']))->assertSee('NUEVO-CT')->assertSee('VIEJO-CT');
    }

    public function test_el_listado_de_banderas_filtra_por_temporada(): void
    {
        Carbon::setTestNow('2026-12-15');
        $this->bandera('2026-01-10 09:00:00')->update(['detalles' => 'VIEJA-BAN']);
        $this->bandera('2026-12-10 09:00:00')->update(['detalles' => 'NUEVA-BAN']);

        $this->actingAs($this->admin)->get(route('bandera.index'))->assertOk()->assertSee('2026-2027')->assertDontSee('VIEJA-BAN');
        $this->assertSame(1, Bandera::deTemporada($this->pasada)->count());
        $this->assertSame(2, Bandera::deTemporada(null)->count());
    }

    public function test_entre_temporadas_el_listado_muestra_la_ultima_y_lo_cargado_por_el_admin_se_ve(): void
    {
        Carbon::setTestNow('2026-06-15'); // entre las dos temporadas
        $this->intervencion('2026-06-10 12:00:00')->update(['detalles' => 'CARGADA-EN-JUNIO']);

        $this->actingAs($this->admin)->get(route('intervencion.index'))->assertOk()->assertSee('CARGADA-EN-JUNIO');
    }

    // ------------------------------------------------------------------------------------- panel

    public function test_el_panel_cuenta_solo_la_temporada_vigente(): void
    {
        Carbon::setTestNow('2026-12-15');
        $this->intervencion('2026-01-10 12:00:00');
        $this->intervencion('2026-01-11 12:00:00');
        $this->intervencion('2026-12-10 12:00:00');
        $this->novedadMaterial('2026-01-10 12:00:00');

        $respuesta = $this->actingAs($this->admin)->getJson('/api/dashboard')->assertOk();

        $respuesta->assertJsonPath('totalIntervenciones', 1)->assertJsonPath('totalNovedadesMateriales', 0);
        $this->assertSame(1, $respuesta->json('intervencionesPorPlaya.0.total'));
        $this->assertSame(100, $respuesta->json('intervencionesPorPlaya.0.porcentaje'), 'total y porcentaje usan el mismo filtro');

        $this->actingAs($this->admin)->get(route('home'))->assertOk()->assertSee('temporada 2026-2027');
    }

    public function test_entre_temporadas_el_panel_muestra_la_ultima(): void
    {
        Carbon::setTestNow('2026-09-10'); // todavía no empezó la 2026-2027
        $this->intervencion('2026-01-10 12:00:00');
        $this->intervencion('2026-12-10 12:00:00'); // futura (otra temporada)

        $this->actingAs($this->admin)->getJson('/api/dashboard')->assertJsonPath('totalIntervenciones', 1);
        $this->actingAs($this->admin)->get(route('home'))->assertOk()->assertSee('temporada 2025-2026');
    }

    // ----------------------------------------------------------------------------------- Excel

    public function test_el_excel_respeta_la_temporada_y_la_incluye_en_el_nombre(): void
    {
        Carbon::setTestNow('2026-12-15');
        Excel::fake();

        $this->actingAs($this->admin)->get(route('export.playas', ['tipo' => 'intervenciones']))->assertOk();
        Excel::assertDownloaded('intervenciones-2026-2027.xlsx');

        $this->actingAs($this->admin)->get(route('export.playas', ['tipo' => 'banderas', 'temporada' => $this->pasada->id]))->assertOk();
        Excel::assertDownloaded('banderas-2025-2026.xlsx');

        $this->actingAs($this->admin)->get(route('export.playas', ['tipo' => 'licencias', 'temporada' => 'todas']))->assertOk();
        Excel::assertDownloaded('licencias-todas-las-temporadas.xlsx');
    }

    public function test_el_contenido_del_excel_solo_trae_la_temporada_pedida(): void
    {
        $this->intervencion('2026-01-10 12:00:00');
        $this->intervencion('2026-12-10 12:00:00');
        $this->licencia('2026-01-10');
        $this->licencia('2026-12-10');
        $otraPlaya = Playa::forceCreate(['nombre' => 'Mar', 'color' => '#222222', 'lat' => -38.1, 'lon' => -58.1]);
        $this->licencia('2026-12-12')->update(['playa_id' => $otraPlaya->id]);

        $this->assertCount(1, (new \App\Exports\IntervencionesExport($this->playa, $this->pasada))->collection());
        $this->assertCount(2, (new \App\Exports\IntervencionesExport($this->playa, null))->collection());
        $this->assertCount(1, (new \App\Exports\LicenciasExport($this->playa, $this->actual))->collection(), 'cada hoja trae solo su playa');
        $this->assertCount(1, (new \App\Exports\LicenciasExport($otraPlaya, $this->actual))->collection());
    }

    // ------------------------------------------------------------------ temporadas cerradas: solo admin

    public function test_un_encargado_no_modifica_registros_de_una_temporada_cerrada_pero_si_de_la_actual(): void
    {
        Carbon::setTestNow('2026-12-15');
        $encargado = $this->usuario('30000002', 'encargado', true);
        $vieja = $this->bandera('2026-01-10 09:00:00', $encargado->id);
        $nueva = $this->bandera('2026-12-10 09:00:00', $encargado->id);

        $this->assertFalse($vieja->modificablePor($encargado));
        $this->assertTrue($nueva->modificablePor($encargado));
        $this->assertTrue($vieja->modificablePor($this->admin), 'el admin sí');

        $this->actingAs($encargado)->get(route('bandera.edit', $vieja))->assertForbidden();
        $this->actingAs($encargado)->delete(route('bandera.destroy', $vieja))->assertForbidden();
        $this->actingAs($encargado)->get(route('bandera.edit', $nueva))->assertOk();
        $this->actingAs($this->admin)->get(route('bandera.edit', $vieja))->assertOk();
    }

    public function test_los_botones_de_editar_y_eliminar_no_aparecen_en_temporadas_cerradas(): void
    {
        Carbon::setTestNow('2026-12-15');
        $encargado = $this->usuario('30000002', 'encargado', true);
        $vieja = $this->licencia('2026-01-10');
        $vieja->update(['detalle' => 'LIC-CERRADA']);

        $html = $this->actingAs($encargado)->get(route('licencia.index', ['temporada' => $this->pasada->id]))->assertOk()->assertSee('LIC-CERRADA')->getContent();
        $this->assertStringNotContainsString(route('licencia.edit', $vieja), $html);

        $html = $this->actingAs($this->admin)->get(route('licencia.index', ['temporada' => $this->pasada->id]))->getContent();
        $this->assertStringContainsString(route('licencia.edit', $vieja), $html);
    }

    public function test_la_fecha_de_lo_que_carga_un_encargado_tiene_que_caer_en_la_temporada_en_curso(): void
    {
        Carbon::setTestNow('2026-12-15');
        $encargado = $this->usuario('30000002', 'encargado', true);

        // Fuera de la temporada en curso (cae en la anterior): se rechaza.
        $this->actingAs($encargado)->post(route('licencia.store'), ['fecha_inicio' => '2026-01-10', 'fecha_fin' => '2026-01-12'])
            ->assertSessionHasErrors('fecha_inicio');
        $this->actingAs($encargado)->post(route('bandera.store'), ['fecha' => '2026-01-10 09:00:00'])->assertSessionHasErrors('fecha');

        // Dentro: pasa el control de temporada (puede fallar por otros campos obligatorios, pero no por la fecha).
        $respuesta = $this->actingAs($encargado)->post(route('licencia.store'), ['fecha_inicio' => '2026-12-14', 'fecha_fin' => '2026-12-16']);
        $this->assertArrayNotHasKey('fecha_inicio', session('errors')?->getBag('default')->toArray() ?? []);

        // El admin carga lo que quiera (p. ej. completar datos del año pasado).
        $this->actingAs($this->admin)->post(route('licencia.store'), ['fecha_inicio' => '2026-01-10', 'fecha_fin' => '2026-01-12']);
        $this->assertArrayNotHasKey('fecha_inicio', session('errors')?->getBag('default')->toArray() ?? []);
        $this->assertNotNull($respuesta);
    }

    public function test_fuera_de_temporada_el_encargado_sigue_sin_poder_cargar(): void
    {
        Carbon::setTestNow('2026-06-15'); // entre temporadas
        $encargado = $this->usuario('30000002', 'encargado', true);

        $this->actingAs($encargado)->post(route('licencia.store'), ['fecha_inicio' => '2026-06-15', 'fecha_fin' => '2026-06-16'])->assertForbidden();
        $this->actingAs($this->admin)->post(route('licencia.store'), ['fecha_inicio' => '2026-06-15', 'fecha_fin' => '2026-06-16'])->assertStatus(302);
    }

    // ------------------------------------------------------------------ presentismo por temporada

    public function test_el_presentismo_puede_tomar_el_rango_de_una_temporada(): void
    {
        Carbon::setTestNow('2026-12-15');

        $this->actingAs($this->admin)->get(route('asistencias.index', ['temporada' => $this->pasada->id]))->assertOk()
            ->assertSee('value="2025-11-01"', false)->assertSee('value="2026-04-30"', false);

        // La temporada en curso se corta hoy.
        $this->actingAs($this->admin)->get(route('asistencias.index', ['temporada' => $this->actual->id]))->assertOk()
            ->assertSee('value="2026-11-01"', false)->assertSee('value="2026-12-15"', false);

        // Si hay fechas a mano, mandan.
        $this->actingAs($this->admin)->get(route('asistencias.index', ['temporada' => $this->pasada->id, 'inicio' => '2026-01-01', 'fin' => '2026-01-31']))
            ->assertSee('value="2026-01-01"', false)->assertSee('value="2026-01-31"', false);

        // Sin temporada: el mes en curso, como siempre.
        $this->actingAs($this->admin)->get(route('asistencias.index'))->assertSee('value="2026-12-01"', false);
    }
}
