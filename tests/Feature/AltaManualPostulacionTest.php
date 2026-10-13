<?php

namespace Tests\Feature;

use App\Models\Guardavida;
use App\Models\Perfil;
use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use App\Services\SeleccionPostulantes;
use Carbon\Carbon;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Alta manual / reincorporación: la persona queda dentro de la temporada que le corresponde
 * (seleccionada, como el resto del plantel) y el admin puede completar su inscripción si quiere.
 */
class AltaManualPostulacionTest extends TestCase
{
    use RefreshDatabase;

    private Temporada $t2627;

    private Temporada $t2728;

    private Playa $playa;

    private Puesto $puesto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable
        Storage::fake('local');

        // Inscripción 1/8 al 31/8, temporada 1/11 al 31/3 (la 2027/28, un año después).
        $this->t2627 = Temporada::create([
            'nombre' => '2026/27', 'fecha_inicio_postulacion' => '2026-08-01', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-11-01', 'fecha_fin' => '2027-03-31',
        ]);
        $this->t2728 = Temporada::create([
            'nombre' => '2027/28', 'fecha_inicio_postulacion' => '2027-08-01', 'fecha_fin_postulacion' => '2027-08-31',
            'fecha_inicio' => '2027-11-01', 'fecha_fin' => '2028-03-31',
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

    private function altaManual(User $admin, string $fechaAlta, string $dni = '31222333'): User
    {
        $this->actingAs($admin)->post(route('guardavida.store'), [
            'nombre' => 'Lucía', 'apellido' => 'Gómez', 'dni' => $dni, 'email' => "l{$dni}@test.com", 'rol' => 'guardavida',
            'telefono' => '2983000000', 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id,
            'funcion' => 'Guardavida', 'turno' => 'T', 'fecha_alta' => $fechaAlta,
        ])->assertRedirect(route('guardavida.index'));

        return User::where('dni', $dni)->firstOrFail();
    }

    // ------------------------------------------------------- a qué temporada pertenece una fecha

    public function test_una_fecha_pertenece_a_la_temporada_desde_que_abre_la_inscripcion(): void
    {
        $this->assertSame($this->t2627->id, Temporada::paraFecha('2026-08-15')->id, 'durante la inscripción');
        $this->assertSame($this->t2627->id, Temporada::paraFecha('2026-10-05')->id, 'entre la inscripción y el comienzo');
        $this->assertSame($this->t2627->id, Temporada::paraFecha('2026-12-20')->id, 'en plena temporada');
        $this->assertSame($this->t2627->id, Temporada::paraFecha('2027-03-31')->id, 'último día');
        $this->assertSame($this->t2728->id, Temporada::paraFecha('2027-08-01')->id, 'abre la inscripción siguiente');
    }

    public function test_fuera_de_toda_temporada_se_usa_la_mas_cercana(): void
    {
        // 15/4/2027: 15 días después de terminar la 26/27, y la 27/28 abre inscripción en casi 4 meses.
        $this->assertSame($this->t2627->id, Temporada::paraFecha('2027-04-15')->id);
        // 20/6/2027: más cerca de que abra la inscripción 27/28 (42 días) que de que terminara la anterior (81).
        $this->assertSame($this->t2728->id, Temporada::paraFecha('2027-06-20')->id);
        // Antes de la primera temporada cargada: esa misma.
        $this->assertSame($this->t2627->id, Temporada::paraFecha('2026-01-10')->id);
        // Después de la última: esa misma.
        $this->assertSame($this->t2728->id, Temporada::paraFecha('2029-01-10')->id);
    }

    public function test_sin_temporadas_cargadas_no_hay_temporada(): void
    {
        Temporada::query()->delete();

        $this->assertNull(Temporada::paraFecha('2026-12-01'));
    }

    // ------------------------------------------------------------------------------ alta manual

    public function test_el_alta_manual_crea_su_postulacion_seleccionada_y_el_cierre_no_la_da_de_baja(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $user = $this->altaManual($admin, '2026-12-01');

        $postulacion = Postulacion::where('user_id', $user->id)->sole();
        $this->assertSame($this->t2627->id, $postulacion->temporada_id);
        $this->assertSame('aceptada', $postulacion->estado);
        $this->assertTrue($postulacion->seleccionado);
        $this->assertNull($postulacion->enviada_at, 'nunca envió un formulario');
        $this->assertSame($this->playa->id, $postulacion->playa_asignada_id);
        $this->assertSame($this->puesto->id, $postulacion->puesto_asignado_id);
        $this->assertSame('T', $postulacion->turno_asignado);
        $this->assertSame('Guardavida', $postulacion->funcion_asignada);
        $this->assertSame($admin->id, $postulacion->revisado_por_user_id);
        $this->assertSame($this->t2627->id, $user->guardavida->periodos()->first()->temporada_id);

        $servicio = app(SeleccionPostulantes::class);
        $this->assertNotContains($user->guardavida->id, $servicio->candidatosCierre($this->t2627)->pluck('id')->all());
        $this->assertSame(0, $servicio->cerrar($this->t2627, [$user->guardavida->id], Carbon::parse('2027-03-31')));
        $this->assertTrue($user->fresh()->hasRole('guardavida'));
    }

    public function test_alta_manual_fuera_de_temporada_va_a_la_mas_cercana(): void
    {
        $admin = $this->usuario('20000001', 'admin');

        $user = $this->altaManual($admin, '2027-06-20');

        $this->assertSame($this->t2728->id, Postulacion::where('user_id', $user->id)->sole()->temporada_id);
        $this->assertSame($this->t2728->id, $user->guardavida->periodos()->first()->temporada_id);
    }

    public function test_alta_manual_de_un_administrador_no_crea_postulacion(): void
    {
        $admin = $this->usuario('20000001', 'admin');

        $this->actingAs($admin)->post(route('guardavida.store'), [
            'nombre' => 'Ana', 'apellido' => 'Admin', 'dni' => '25111222', 'email' => 'ana@test.com', 'rol' => 'admin',
        ])->assertRedirect(route('guardavida.index'));

        $this->assertSame(0, Postulacion::count());
    }

    public function test_si_ya_tenia_una_postulacion_en_esa_temporada_se_la_acepta_y_selecciona(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $user = $this->usuario('30000005', 'postulante');
        $guardavida = Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $user->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'turno' => 'M']);
        $previa = Postulacion::create(['user_id' => $user->id, 'temporada_id' => $this->t2627->id, 'estado' => 'rechazada', 'enviada_at' => now(), 'observaciones' => 'Faltaba la libreta']);

        app(SeleccionPostulantes::class)->incluirEnTemporada($guardavida, $this->t2627);

        $previa->refresh();
        $this->assertSame(1, Postulacion::where('user_id', $user->id)->count(), 'no se duplica');
        $this->assertSame('aceptada', $previa->estado);
        $this->assertTrue($previa->seleccionado);
        $this->assertSame('Faltaba la libreta', $previa->observaciones, 'no pisa lo que ya había escrito');
        $this->assertNotNull($previa->enviada_at);
    }

    // ------------------------------------------------------------------------------ reincorporar

    public function test_reincorporar_guarda_la_temporada_y_deja_a_la_persona_seleccionada(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $user = $this->usuario('30000006', 'guardavida');
        $g = Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $user->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'turno' => 'M']);
        $g->darDeAlta(Carbon::parse('2026-11-01'), $this->t2627->id, null, true);
        // El cierre de una temporada anterior la dio de baja, y ahora el admin la reincorpora en la nueva.
        app(SeleccionPostulantes::class)->darDeBaja($g, Carbon::parse('2027-03-31'));

        $this->actingAs($admin)->post(route('guardavida.reincorporar', $g), ['fecha' => '2027-12-01'])
            ->assertRedirect(route('guardavidas.bajas'));

        $abierto = $g->periodos()->whereNull('hasta')->first();
        $this->assertSame($this->t2728->id, $abierto->temporada_id);
        $postulacion = Postulacion::where('user_id', $user->id)->where('temporada_id', $this->t2728->id)->sole();
        $this->assertTrue($postulacion->seleccionado);
        $this->assertNotContains($g->id, app(SeleccionPostulantes::class)->candidatosCierre($this->t2728)->pluck('id')->all());
    }

    public function test_reincorporar_a_alguien_ya_seleccionado_en_la_temporada_no_cambia_su_postulacion(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $user = $this->usuario('30000007', 'guardavida');
        $g = Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $user->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'turno' => 'M']);
        $p = Postulacion::create(['user_id' => $user->id, 'temporada_id' => $this->t2627->id, 'estado' => 'aceptada', 'seleccionado' => true, 'enviada_at' => now(), 'observaciones' => 'Original', 'playa_asignada_id' => $this->playa->id]);
        $g->darDeAlta(Carbon::parse('2026-11-01'), $this->t2627->id, null, true);
        app(SeleccionPostulantes::class)->darDeBaja($g, Carbon::parse('2026-12-10'));

        $this->actingAs($admin)->post(route('guardavida.reincorporar', $g), ['fecha' => '2026-12-20'])->assertRedirect();

        $this->assertSame('Original', $p->fresh()->observaciones);
        $this->assertSame(1, Postulacion::where('user_id', $user->id)->count());
    }

    // ---------------------------------------------------------- el admin completa la inscripción

    public function test_el_detalle_y_la_edicion_cargan_aunque_no_haya_datos_ni_documentos(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $user = $this->altaManual($admin, '2026-12-01');
        $postulacion = Postulacion::where('user_id', $user->id)->sole();

        $this->actingAs($admin)->get(route('postulaciones.show', $postulacion))
            ->assertOk()->assertSee('Completar datos y documentos');
        $this->actingAs($admin)->get(route('postulaciones.edit', $postulacion))
            ->assertOk()->assertSee('Documentos');
        $this->actingAs($admin)->get(route('postulaciones.index', ['temporada' => $this->t2627->id, 'estado' => 'aceptada']))
            ->assertOk()->assertSee('Gómez');
    }

    public function test_las_solapas_del_listado_muestran_cuantas_hay_por_estado(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $this->altaManual($admin, '2026-12-01'); // aceptada
        $pendiente = $this->usuario('30000020', 'postulante');
        Postulacion::create(['user_id' => $pendiente->id, 'temporada_id' => $this->t2627->id, 'estado' => 'pendiente', 'enviada_at' => now()]);

        $html = $this->actingAs($admin)->get(route('postulaciones.index', ['temporada' => $this->t2627->id]))->assertOk()->getContent();

        // Regresión: el contador del sidebar (CounterServiceProvider) también se llama "conteos" y pisaba estos totales con 0.
        foreach (['Pendientes' => 1, 'Aceptadas' => 1, 'Rechazadas' => 0, 'Todas' => 2] as $solapa => $cantidad) {
            $this->assertMatchesRegularExpression("/{$solapa}\s*<span[^>]*>\({$cantidad}\)<\/span>/", $html, $solapa);
        }
    }

    public function test_el_admin_puede_guardar_sin_completar_nada(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $user = $this->altaManual($admin, '2026-12-01');
        $postulacion = Postulacion::where('user_id', $user->id)->sole();

        $this->actingAs($admin)->put(route('postulaciones.update', $postulacion), [])
            ->assertRedirect(route('postulaciones.show', $postulacion))->assertSessionHas('success');

        $this->assertSame('aceptada', $postulacion->fresh()->estado, 'no cambia el estado');
        $this->assertTrue($postulacion->fresh()->seleccionado);
        $this->assertSame('2983000000', Perfil::where('user_id', $user->id)->value('telefono'), 'lo que no se manda no se toca');
    }

    public function test_el_admin_puede_cargar_datos_playas_y_documentos(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $user = $this->altaManual($admin, '2026-12-01');
        $postulacion = Postulacion::where('user_id', $user->id)->sole();
        $playa2 = Playa::forceCreate(['nombre' => 'Mar', 'color' => '#222222', 'lat' => -38.1, 'lon' => -58.1]);

        $this->actingAs($admin)->put(route('postulaciones.update', $postulacion), [
            'telefono' => '2262000111', 'grupo_sanguineo' => 'O+', 'talle_remera' => 'L',
            'tiene_obra_social' => '1', 'obra_social_nombre' => 'OSDE', 'obra_social_numero_afiliado' => '123',
            'disponible_desde' => '2026-12-01', 'disponible_hasta' => '2027-03-01',
            'playa_1' => $this->playa->id, 'playa_2' => $playa2->id,
            'doc_libreta' => UploadedFile::fake()->createWithContent('libreta.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF"),
        ])->assertRedirect(route('postulaciones.show', $postulacion))->assertSessionHasNoErrors();

        $perfil = Perfil::where('user_id', $user->id)->sole();
        $this->assertSame('2262000111', $perfil->telefono);
        $this->assertSame('O+', $perfil->grupo_sanguineo);
        $this->assertSame('OSDE', $perfil->obra_social_nombre);
        $postulacion->refresh();
        $this->assertSame('2026-12-01', $postulacion->disponible_desde->toDateString());
        $this->assertSame([$this->playa->id, $playa2->id], $postulacion->playas->pluck('id')->all());
        $doc = $postulacion->documentos()->where('tipo', 'libreta')->sole();
        Storage::disk('local')->assertExists($doc->ruta);
        $this->actingAs($admin)->get(route('postulaciones.documento', [$postulacion, 'libreta']))->assertOk();
    }

    public function test_la_edicion_valida_formatos_y_exige_permiso(): void
    {
        $admin = $this->usuario('20000001', 'admin');
        $user = $this->altaManual($admin, '2026-12-01');
        $postulacion = Postulacion::where('user_id', $user->id)->sole();

        $this->actingAs($admin)->put(route('postulaciones.update', $postulacion), [
            'doc_libreta' => UploadedFile::fake()->create('libreta.exe', 10),
        ])->assertSessionHasErrors('doc_libreta');

        $guardavida = $this->usuario('30000009', 'guardavida');
        $this->actingAs($guardavida)->get(route('postulaciones.edit', $postulacion))->assertForbidden();
        $this->actingAs($guardavida)->put(route('postulaciones.update', $postulacion), [])->assertForbidden();
    }
}
