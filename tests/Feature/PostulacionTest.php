<?php

namespace Tests\Feature;

use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\PostulacionDocumento;
use App\Models\Temporada;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostulacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seeder permitido acá: SQLite en memoria, base totalmente descartable.
        $this->seed(RolesYPermisosSeeder::class);
        Storage::fake('local');
    }

    private function temporadaAbierta(): Temporada
    {
        return Temporada::create([
            'nombre' => 'Temporada de prueba',
            'fecha_inicio_postulacion' => now()->subDays(10),
            'fecha_fin_postulacion' => now()->addDays(10),
            'fecha_inicio' => now()->addMonths(3),
            'fecha_fin' => now()->addMonths(8),
        ]);
    }

    private function playa(string $nombre, string $color): Playa
    {
        // lat/lon son NOT NULL y no están en $fillable.
        return Playa::forceCreate(['nombre' => $nombre, 'color' => $color, 'lat' => -38.0, 'lon' => -58.0]);
    }

    private function usuario(string $rol, string $dni = '30111222'): User
    {
        $user = User::create([
            'name' => 'Ana',
            'lastname' => 'Pérez',
            'dni' => $dni,
            'email' => "u{$dni}@test.com",
            'password' => 'password',
            'enabled' => true,
            'must_change_psw' => false,
        ]);
        $user->assignRole($rol);

        return $user;
    }

    private function datosPaso1(array $extra = []): array
    {
        return $extra + [
            'telefono' => '2262123456',
            'direccion' => 'Calle Falsa',
            'numero' => '123',
            'fecha_nacimiento' => '1995-05-10',
            'genero' => 'Femenino',
            'grupo_sanguineo' => 'O+',
            'numero_libreta' => 'L-998',
            'talle_remera' => 'M',
            'talle_pantalon' => '40',
            'talle_campera' => 'M',
            'talle_traje_bano' => 'M',
            'tiene_obra_social' => '0',
        ];
    }

    private function pdf(string $nombre = 'doc.pdf'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($nombre, "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF");
    }

    private function png(string $nombre = 'foto.png'): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($nombre, $png);
    }

    /** Sube todos los documentos obligatorios. */
    private function documentosCompletos(): array
    {
        return [
            'doc_foto_personal' => $this->png('foto.png'),
            'doc_dni_frente' => $this->png('dni1.png'),
            'doc_dni_dorso' => $this->png('dni2.png'),
            'doc_curriculum' => $this->pdf('cv.pdf'),
            'doc_libreta' => $this->pdf('libreta.pdf'),
            'doc_antecedentes_penales' => $this->pdf('ant.pdf'),
            'doc_declaracion_jurada' => $this->pdf('dj.pdf'),
        ];
    }

    private function completarInscripcion(User $user, Playa $playa): Postulacion
    {
        $this->actingAs($user)->post(route('postulacion.guardar', 1), $this->datosPaso1())->assertRedirect(route('postulacion.paso', 2));
        $this->actingAs($user)->post(route('postulacion.guardar', 2), [
            'disponible_desde' => now()->addMonths(3)->toDateString(),
            'disponible_hasta' => now()->addMonths(8)->toDateString(),
            'playa_1' => $playa->id,
        ])->assertRedirect(route('postulacion.paso', 3));
        $this->actingAs($user)->post(route('postulacion.guardar', 3), $this->documentosCompletos())->assertRedirect(route('postulacion.paso', 4));

        return Postulacion::where('user_id', $user->id)->firstOrFail();
    }

    public function test_los_cuatro_pasos_del_formulario_se_muestran(): void
    {
        $this->temporadaAbierta();
        $user = $this->usuario('postulante');

        // El paso 1 se ve aun sin inscripción creada; los demás piden haber guardado el 1.
        $this->actingAs($user)->get(route('postulacion.paso', 1))->assertOk()->assertSee('Datos personales');
        $this->actingAs($user)->post(route('postulacion.guardar', 1), $this->datosPaso1());

        // Cada paso muestra su encabezado ("Paso N de 4") y sus secciones con subtítulo.
        $secciones = [1 => 'Domicilio', 2 => 'Playas de preferencia', 3 => 'Identidad', 4 => 'Resumen'];
        foreach ($secciones as $paso => $seccion) {
            $this->actingAs($user)->get(route('postulacion.paso', $paso))
                ->assertOk()
                ->assertSee("Paso {$paso} de 4")
                ->assertSee($seccion);
        }
    }

    public function test_sin_inscripcion_abierta_no_puede_iniciar(): void
    {
        $user = $this->usuario('postulante');

        $this->actingAs($user)->get(route('postulacion.paso', 1))
            ->assertRedirect(route('home'))
            ->assertSessionHas('error');
    }

    public function test_flujo_completo_hasta_enviar(): void
    {
        $this->temporadaAbierta();
        $playa = $this->playa('Playa Norte', '#00aaff');
        $user = $this->usuario('postulante');

        $postulacion = $this->completarInscripcion($user, $playa);

        // Hasta enviar queda en borrador: el admin no la ve.
        $this->assertSame('borrador', $postulacion->estado);
        $this->assertSame([], $postulacion->faltantes());
        $this->assertCount(7, $postulacion->documentos);
        Storage::disk('local')->assertExists($postulacion->documento('dni_frente')->ruta);

        $this->actingAs($user)->post(route('postulacion.enviar'))->assertRedirect(route('home'));

        $postulacion->refresh();
        $this->assertSame('pendiente', $postulacion->estado);
        $this->assertNotNull($postulacion->enviada_at);
    }

    public function test_no_puede_enviar_con_datos_faltantes(): void
    {
        $this->temporadaAbierta();
        $user = $this->usuario('postulante');

        // Solo el paso 1: faltan disponibilidad y documentos.
        $this->actingAs($user)->post(route('postulacion.guardar', 1), $this->datosPaso1());

        $this->actingAs($user)->post(route('postulacion.enviar'))->assertSessionHas('error');

        $this->assertSame('borrador', Postulacion::first()->estado);
    }

    public function test_paso1_exige_datos_de_obra_social_si_la_tiene(): void
    {
        $this->temporadaAbierta();
        $user = $this->usuario('postulante');

        $this->actingAs($user)
            ->post(route('postulacion.guardar', 1), $this->datosPaso1(['tiene_obra_social' => '1']))
            ->assertSessionHasErrors(['obra_social_nombre', 'obra_social_numero_afiliado']);
    }

    public function test_documento_con_formato_invalido_es_rechazado(): void
    {
        $this->temporadaAbierta();
        $user = $this->usuario('postulante');
        $this->actingAs($user)->post(route('postulacion.guardar', 1), $this->datosPaso1());

        // La libreta tiene que ser PDF.
        $this->actingAs($user)
            ->post(route('postulacion.guardar', 3), ['doc_libreta' => $this->png('libreta.png')])
            ->assertSessionHasErrors('doc_libreta');
    }

    public function test_se_puede_invertir_el_orden_de_las_playas(): void
    {
        $this->temporadaAbierta();
        $a = $this->playa('A', '#111111');
        $b = $this->playa('B', '#222222');
        $user = $this->usuario('postulante');
        $this->actingAs($user)->post(route('postulacion.guardar', 1), $this->datosPaso1());

        $fechas = ['disponible_desde' => now()->toDateString(), 'disponible_hasta' => now()->addDays(30)->toDateString()];
        $this->actingAs($user)->post(route('postulacion.guardar', 2), $fechas + ['playa_1' => $a->id, 'playa_2' => $b->id])->assertSessionHasNoErrors();
        $this->actingAs($user)->post(route('postulacion.guardar', 2), $fechas + ['playa_1' => $b->id, 'playa_2' => $a->id])->assertSessionHasNoErrors();

        $playas = Postulacion::first()->playas;
        $this->assertSame(['B', 'A'], $playas->pluck('nombre')->all());
    }

    public function test_la_segunda_playa_no_puede_repetir_la_primera(): void
    {
        $this->temporadaAbierta();
        $a = $this->playa('A', '#111111');
        $user = $this->usuario('postulante');
        $this->actingAs($user)->post(route('postulacion.guardar', 1), $this->datosPaso1());

        $this->actingAs($user)->post(route('postulacion.guardar', 2), [
            'disponible_desde' => now()->toDateString(),
            'disponible_hasta' => now()->addDays(30)->toDateString(),
            'playa_1' => $a->id,
            'playa_2' => $a->id,
        ])->assertSessionHasErrors('playa_2');
    }

    public function test_una_inscripcion_aceptada_ya_no_se_puede_editar(): void
    {
        $this->temporadaAbierta();
        $playa = $this->playa('Playa Norte', '#00aaff');
        $user = $this->usuario('postulante');
        $postulacion = $this->completarInscripcion($user, $playa);
        $postulacion->update(['estado' => 'aceptada']);

        $this->actingAs($user)->post(route('postulacion.guardar', 1), $this->datosPaso1(['telefono' => '999']))
            ->assertRedirect(route('home'))
            ->assertSessionHas('error');
    }

    public function test_el_paso_4_se_adapta_a_si_la_inscripcion_ya_fue_enviada(): void
    {
        $this->temporadaAbierta();
        $playa = $this->playa('Playa Norte', '#00aaff');
        $user = $this->usuario('postulante');
        $postulacion = $this->completarInscripcion($user, $playa);

        // Borrador completo: invita a enviarla.
        $this->actingAs($user)->get(route('postulacion.paso', 4))
            ->assertOk()->assertSee('Ya podés enviar tu inscripción')->assertSee('Enviar inscripción')->assertDontSee('Volver al inicio');

        // Ya enviada: lo dice, no ofrece enviarla de nuevo.
        $this->actingAs($user)->post(route('postulacion.enviar'));
        $this->actingAs($user)->get(route('postulacion.paso', 4))
            ->assertOk()->assertSee('fue enviada')->assertSee('la está revisando')->assertSee('Volver al inicio')
            ->assertDontSee('Ya podés enviar tu inscripción')->assertDontSee('Enviar inscripción')->assertDontSee('Confirmar cambios');

        // Devuelta por el equipo: se la vuelve a enviar.
        $postulacion->update(['estado' => 'incompleta']);
        $this->actingAs($user)->get(route('postulacion.paso', 4))
            ->assertOk()->assertSee('te pidió correcciones')->assertSee('Revisá lo que te pidió el equipo')->assertSee('Enviar inscripción')
            ->assertDontSee('Ya podés enviar tu inscripción');
    }

    public function test_cerrada_la_inscripcion_no_se_suman_inscripciones_nuevas(): void
    {
        $temporada = $this->temporadaAbierta();
        $playa = $this->playa('Playa Norte', '#00aaff');
        $conBorrador = $this->usuario('postulante');
        $this->completarInscripcion($conBorrador, $playa); // queda en borrador, sin enviar
        $nuevo = $this->usuario('postulante', '30999888');

        $temporada->update(['fecha_inicio_postulacion' => now()->subDays(30), 'fecha_fin_postulacion' => now()->subDay()]);

        // Un borrador no se puede enviar una vez cerrada la inscripción.
        $this->actingAs($conBorrador)->post(route('postulacion.enviar'))->assertRedirect(route('home'))->assertSessionHas('error');
        $this->assertSame('borrador', Postulacion::where('user_id', $conBorrador->id)->value('estado'));

        // Y quien no se había inscripto no puede empezar.
        $this->actingAs($nuevo)->get(route('postulacion.paso', 1))->assertRedirect(route('home'))->assertSessionHas('error');
        $this->actingAs($nuevo)->post(route('postulacion.guardar', 1), $this->datosPaso1())->assertRedirect(route('home'));
        $this->assertSame(0, Postulacion::where('user_id', $nuevo->id)->count());
    }

    public function test_cerrada_la_inscripcion_se_puede_corregir_lo_enviado_hasta_que_empiece_la_temporada(): void
    {
        $temporada = $this->temporadaAbierta();
        $playa = $this->playa('Playa Norte', '#00aaff');
        $user = $this->usuario('postulante');
        $postulacion = $this->completarInscripcion($user, $playa);
        $this->actingAs($user)->post(route('postulacion.enviar'));
        $this->assertSame('pendiente', $postulacion->fresh()->estado);

        $temporada->update(['fecha_inicio_postulacion' => now()->subDays(30), 'fecha_fin_postulacion' => now()->subDay()]);

        // Enviada (pendiente) con la inscripción cerrada: puede corregir.
        $this->actingAs($user)->post(route('postulacion.guardar', 1), $this->datosPaso1(['telefono' => '2262000000']))
            ->assertRedirect(route('postulacion.paso', 2));
        $this->assertSame('2262000000', $user->perfil->fresh()->telefono);

        // Devuelta por el equipo (incompleta): corrige y reenvía.
        $postulacion->update(['estado' => 'incompleta', 'observaciones' => 'Falta algo']);
        $this->actingAs($user)->get(route('postulacion.paso', 3))->assertOk();
        $this->actingAs($user)->post(route('postulacion.enviar'))->assertRedirect(route('home'))->assertSessionHas('success');
        $this->assertSame('pendiente', $postulacion->fresh()->estado);

        // Empezó la temporada: ya no.
        $temporada->update(['fecha_inicio' => now()->subDay()]);
        $this->actingAs($user)->post(route('postulacion.guardar', 1), $this->datosPaso1(['telefono' => '1']))
            ->assertRedirect(route('home'))->assertSessionHas('error');
    }

    public function test_modificar_una_inscripcion_ya_enviada_le_avisa_al_admin_hasta_que_la_revisa(): void
    {
        $this->temporadaAbierta();
        $playa = $this->playa('Playa Norte', '#00aaff');
        $user = $this->usuario('postulante');
        $admin = $this->usuario('admin', '20000001');
        $postulacion = $this->completarInscripcion($user, $playa);

        // Mientras es borrador no se marca nada; al enviarla tampoco.
        $this->assertNull($postulacion->fresh()->modificada_at);
        $this->actingAs($user)->post(route('postulacion.enviar'));
        $this->assertNull($postulacion->fresh()->modificada_at);

        // Guardar sin cambiar nada no cuenta como modificación.
        $this->actingAs($user)->post(route('postulacion.guardar', 1), $this->datosPaso1());
        $this->assertNull($postulacion->fresh()->modificada_at);

        // Cambiar un dato sí.
        $this->actingAs($user)->post(route('postulacion.guardar', 1), $this->datosPaso1(['telefono' => '2262999999']));
        $this->assertNotNull($postulacion->fresh()->modificada_at);

        $this->actingAs($admin)->get(route('postulaciones.index'))->assertOk()->assertSee('Modificada');
        $this->actingAs($admin)->get(route('postulaciones.show', $postulacion))->assertOk()->assertSee('Inscripción modificada');

        // Cuando el admin guarda la revisión, el aviso se limpia.
        $this->actingAs($admin)->patch(route('postulaciones.revisar', $postulacion), ['estado' => 'pendiente', 'observaciones' => 'Vista'])
            ->assertRedirect(route('postulaciones.show', $postulacion));
        $this->assertNull($postulacion->fresh()->modificada_at);
        $this->actingAs($admin)->get(route('postulaciones.show', $postulacion))->assertDontSee('Inscripción modificada');
    }

    public function test_un_postulante_no_entra_al_listado_de_admin(): void
    {
        $user = $this->usuario('postulante');

        $this->actingAs($user)->get(route('postulaciones.index'))->assertRedirect(route('home'));
    }

    public function test_admin_ve_solo_pendientes_y_puede_revisar(): void
    {
        $temporada = $this->temporadaAbierta();
        $playa = $this->playa('Playa Norte', '#00aaff');
        $admin = $this->usuario('admin', '20000001');

        $enviada = $this->usuario('postulante', '30000001');
        $postulacion = $this->completarInscripcion($enviada, $playa);
        $postulacion->update(['estado' => 'pendiente', 'enviada_at' => now()]);

        $borrador = $this->usuario('postulante', '30000002');
        Postulacion::create(['user_id' => $borrador->id, 'temporada_id' => $temporada->id]);

        $this->actingAs($admin)->get(route('postulaciones.index'))
            ->assertOk()
            ->assertSee('30000001')
            ->assertDontSee('30000002');

        $this->actingAs($admin)
            ->patch(route('postulaciones.revisar', $postulacion), ['estado' => 'aceptada', 'observaciones' => 'Documentación ok'])
            ->assertRedirect(route('postulaciones.show', $postulacion));

        $postulacion->refresh();
        $this->assertSame('aceptada', $postulacion->estado);
        $this->assertSame('Documentación ok', $postulacion->observaciones);
        $this->assertSame($admin->id, $postulacion->revisado_por_user_id);
        $this->assertNotNull($postulacion->fecha_revision);
    }

    public function test_el_admin_puede_pedir_correcciones_y_el_postulante_las_reenvia(): void
    {
        $this->temporadaAbierta();
        $playa = $this->playa('Playa Norte', '#00aaff');
        $user = $this->usuario('postulante');
        $admin = $this->usuario('admin', '20000001');
        $postulacion = $this->completarInscripcion($user, $playa);
        $this->actingAs($user)->post(route('postulacion.enviar'));

        // El comentario es opcional: el admin decide si lo escribe. Queda "incompleta" y sigue figurando entre las pendientes (con su insignia).
        $this->actingAs($admin)->patch(route('postulaciones.revisar', $postulacion), ['estado' => 'incompleta', 'observaciones' => 'Subí el DNI más nítido'])
            ->assertRedirect(route('postulaciones.show', $postulacion));
        $this->assertSame('incompleta', $postulacion->fresh()->estado);
        $this->actingAs($admin)->get(route('postulaciones.index'))
            ->assertOk()->assertSee('Pendientes')->assertSee((string) $user->dni)->assertSee('Incompleta')->assertSee('Pendientes <span class="ms-1 text-xs font-normal opacity-70">(1)</span>', false);
        $this->actingAs($admin)->get(route('postulaciones.show', $postulacion))->assertOk()->assertSee('Pedir correcciones');

        // El postulante ve el pedido (en Home y también en el propio formulario), corrige y vuelve a enviar.
        $this->actingAs($user)->get(route('home'))->assertOk()->assertSee('FALTA COMPLETAR')->assertSee('Subí el DNI más nítido');
        $this->actingAs($user)->get(route('postulacion.paso', 1))
            ->assertOk()->assertSee('Incompleta')->assertSee('Comentario del equipo')->assertSee('Subí el DNI más nítido')->assertDontSee('Borrador');
        $this->actingAs($user)->post(route('postulacion.enviar'))->assertRedirect(route('home'));
        $this->assertSame('pendiente', $postulacion->fresh()->estado);
    }

    public function test_el_admin_no_puede_revisar_un_borrador(): void
    {
        $temporada = $this->temporadaAbierta();
        $admin = $this->usuario('admin', '20000001');
        $borrador = Postulacion::create(['user_id' => $this->usuario('postulante')->id, 'temporada_id' => $temporada->id]);

        $this->actingAs($admin)->patch(route('postulaciones.revisar', $borrador), ['estado' => 'aceptada'])->assertNotFound();
    }

    public function test_solo_el_dueno_o_un_admin_descargan_un_documento(): void
    {
        $this->temporadaAbierta();
        $playa = $this->playa('Playa Norte', '#00aaff');
        $dueno = $this->usuario('postulante', '30000001');
        $otro = $this->usuario('postulante', '30000002');
        $admin = $this->usuario('admin', '20000001');
        $postulacion = $this->completarInscripcion($dueno, $playa);

        $this->actingAs($dueno)->get(route('postulacion.documento', [$postulacion, 'dni_frente']))->assertOk();
        $this->actingAs($otro)->get(route('postulacion.documento', [$postulacion, 'dni_frente']))->assertForbidden();
        $this->actingAs($admin)->get(route('postulaciones.documento', [$postulacion, 'dni_frente']))->assertOk();
        $this->actingAs($otro)->get(route('postulaciones.documento', [$postulacion, 'dni_frente']))->assertRedirect(route('home'));
    }

    public function test_no_se_puede_borrar_una_temporada_con_postulaciones(): void
    {
        $temporada = $this->temporadaAbierta();
        Postulacion::create(['user_id' => $this->usuario('postulante')->id, 'temporada_id' => $temporada->id]);
        $admin = $this->usuario('admin', '20000001');

        $this->actingAs($admin)->delete(route('temporada.destroy', $temporada))->assertSessionHas('error');

        $this->assertDatabaseHas('temporadas', ['id' => $temporada->id]);
    }
}
