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
            ->assertRedirect(route('postulacion.index'))
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

        $this->actingAs($user)->post(route('postulacion.enviar'))->assertRedirect(route('postulacion.index'));

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
            ->assertRedirect(route('postulacion.index'))
            ->assertSessionHas('error');
    }

    public function test_un_postulante_no_entra_al_listado_de_admin(): void
    {
        $user = $this->usuario('postulante');

        $this->actingAs($user)->get(route('postulaciones.index'))->assertRedirect(route('postulacion.index'));
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
        $this->actingAs($otro)->get(route('postulaciones.documento', [$postulacion, 'dni_frente']))->assertRedirect(route('postulacion.index'));
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
