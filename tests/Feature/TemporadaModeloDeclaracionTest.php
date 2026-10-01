<?php

namespace Tests\Feature;

use App\Models\Postulacion;
use App\Models\PostulacionDocumento;
use App\Models\PostulacionPerfil;
use App\Models\Temporada;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TemporadaModeloDeclaracionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seeder permitido acá: SQLite en memoria, base totalmente descartable.
        $this->seed(RolesYPermisosSeeder::class);
        Storage::fake('local');
    }

    private function usuario(string $rol, string $dni): User
    {
        $user = User::create([
            'name' => 'Ana', 'lastname' => 'Pérez', 'dni' => $dni, 'email' => "u{$dni}@test.com",
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $user->assignRole($rol);

        return $user;
    }

    private ?User $admin = null;

    /** Un solo admin por test (el DNI es único). */
    private function admin(): User
    {
        if (! $this->admin) {
            $this->admin = $this->usuario('admin', '20000001');
            $this->admin->assignRole('superadmin');
        }

        return $this->admin;
    }

    private function pdf(string $contenido = 'modelo 2026'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('modelo.pdf', "%PDF-1.4\n{$contenido}\n%%EOF");
    }

    private function datosTemporada(array $extra = []): array
    {
        return $extra + [
            'nombre' => 'Temporada 2026-27',
            'fecha_inicio_postulacion' => now()->subDays(10)->toDateString(),
            'fecha_fin_postulacion' => now()->addDays(10)->toDateString(),
            'fecha_inicio' => now()->addMonths(3)->toDateString(),
            'fecha_fin' => now()->addMonths(8)->toDateString(),
        ];
    }

    private function crearTemporada(array $extra = []): Temporada
    {
        $this->actingAs($this->admin())->post(route('temporada.store'), $this->datosTemporada($extra))->assertSessionHasNoErrors();

        return Temporada::firstOrFail();
    }

    public function test_el_admin_sube_el_modelo_al_crear_la_temporada(): void
    {
        $temporada = $this->crearTemporada(['modelo_declaracion' => $this->pdf()]);

        $this->assertSame("temporadas/{$temporada->id}/declaracion_jurada.pdf", $temporada->declaracion_jurada_modelo);
        Storage::disk('local')->assertExists($temporada->declaracion_jurada_modelo);
    }

    public function test_crear_la_temporada_sin_modelo_sigue_funcionando(): void
    {
        $temporada = $this->crearTemporada();

        $this->assertNull($temporada->declaracion_jurada_modelo);
    }

    public function test_subir_otro_modelo_reemplaza_al_anterior(): void
    {
        $temporada = $this->crearTemporada(['modelo_declaracion' => $this->pdf('version vieja')]);

        $this->actingAs($this->admin())
            ->put(route('temporada.update', $temporada), $this->datosTemporada(['modelo_declaracion' => $this->pdf('version nueva')]))
            ->assertSessionHasNoErrors();

        $temporada->refresh();
        $this->assertStringContainsString('version nueva', Storage::disk('local')->get($temporada->declaracion_jurada_modelo));
        $this->assertCount(1, Storage::disk('local')->files("temporadas/{$temporada->id}"));
    }

    public function test_editar_sin_subir_archivo_conserva_el_modelo(): void
    {
        $temporada = $this->crearTemporada(['modelo_declaracion' => $this->pdf()]);

        $this->actingAs($this->admin())
            ->put(route('temporada.update', $temporada), $this->datosTemporada(['nombre' => 'Otro nombre']))
            ->assertSessionHasNoErrors();

        $temporada->refresh();
        $this->assertSame('Otro nombre', $temporada->nombre);
        Storage::disk('local')->assertExists($temporada->declaracion_jurada_modelo);
    }

    public function test_se_puede_quitar_el_modelo(): void
    {
        $temporada = $this->crearTemporada(['modelo_declaracion' => $this->pdf()]);
        $ruta = $temporada->declaracion_jurada_modelo;

        $this->actingAs($this->admin())
            ->put(route('temporada.update', $temporada), $this->datosTemporada(['quitar_modelo' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertNull($temporada->refresh()->declaracion_jurada_modelo);
        Storage::disk('local')->assertMissing($ruta);
    }

    public function test_solo_se_aceptan_pdf(): void
    {
        $this->actingAs($this->admin())
            ->post(route('temporada.store'), $this->datosTemporada(['modelo_declaracion' => UploadedFile::fake()->create('modelo.txt', 10, 'text/plain')]))
            ->assertSessionHasErrors('modelo_declaracion');

        $this->assertDatabaseCount('temporadas', 0);
    }

    public function test_eliminar_la_temporada_borra_su_modelo(): void
    {
        $temporada = $this->crearTemporada(['modelo_declaracion' => $this->pdf()]);
        $ruta = $temporada->declaracion_jurada_modelo;

        $this->actingAs($this->admin())->delete(route('temporada.destroy', $temporada))->assertRedirect(route('temporada.index'));

        $this->assertDatabaseCount('temporadas', 0);
        Storage::disk('local')->assertMissing($ruta);
    }

    public function test_el_admin_puede_ver_el_modelo_y_un_guardavida_no(): void
    {
        $temporada = $this->crearTemporada(['modelo_declaracion' => $this->pdf()]);

        $this->actingAs($this->admin())->get(route('temporada.modelo-declaracion', $temporada))
            ->assertOk()->assertHeader('content-type', 'application/pdf');

        $this->actingAs($this->usuario('guardavida', '30000009'))
            ->get(route('temporada.modelo-declaracion', $temporada))->assertForbidden();
    }

    public function test_el_postulante_descarga_el_modelo_y_lo_ve_en_el_paso_3(): void
    {
        $temporada = $this->crearTemporada(['modelo_declaracion' => $this->pdf('modelo del postulante')]);
        $postulante = $this->usuario('postulante', '30000001');
        Postulacion::create(['user_id' => $postulante->id, 'temporada_id' => $temporada->id]);

        $this->actingAs($postulante)->get(route('postulacion.modelo-declaracion', $temporada))
            ->assertOk()->assertHeader('content-type', 'application/pdf');

        $this->actingAs($postulante)->get(route('postulacion.paso', 3))
            ->assertOk()->assertSee(route('postulacion.modelo-declaracion', $temporada), false);
    }

    public function test_sin_modelo_no_se_muestra_ni_el_link_ni_el_campo_de_la_declaracion(): void
    {
        $temporada = $this->crearTemporada();
        $postulante = $this->usuario('postulante', '30000001');
        Postulacion::create(['user_id' => $postulante->id, 'temporada_id' => $temporada->id]);

        $this->actingAs($postulante)->get(route('postulacion.modelo-declaracion', $temporada))->assertNotFound();

        $this->actingAs($postulante)->get(route('postulacion.paso', 3))
            ->assertOk()
            ->assertDontSee('Descargar modelo', false)
            ->assertDontSee('doc_declaracion_jurada', false)
            ->assertSee('doc_dni_frente', false); // el resto de los documentos sigue igual
    }

    public function test_sin_modelo_la_declaracion_no_se_exige_para_enviar(): void
    {
        $temporada = $this->crearTemporada();
        $postulacion = $this->postulacionCompleta($temporada, conDeclaracion: false);

        $this->assertSame([], $postulacion->faltantes());
    }

    public function test_con_modelo_la_declaracion_si_se_exige(): void
    {
        $temporada = $this->crearTemporada(['modelo_declaracion' => $this->pdf()]);
        $postulacion = $this->postulacionCompleta($temporada, conDeclaracion: false);

        $this->assertSame(['Declaración jurada firmada'], $postulacion->faltantes());

        // y el paso 3 muestra el link y el campo
        $this->actingAs($postulacion->user)->get(route('postulacion.paso', 3))
            ->assertOk()
            ->assertSee('Descargar modelo', false)
            ->assertSee('doc_declaracion_jurada', false);
    }

    public function test_si_ya_subio_la_declaracion_el_campo_sigue_visible_aunque_se_quite_el_modelo(): void
    {
        $temporada = $this->crearTemporada();
        $postulacion = $this->postulacionCompleta($temporada, conDeclaracion: true);

        $this->actingAs($postulacion->user)->get(route('postulacion.paso', 3))
            ->assertOk()
            ->assertSee('doc_declaracion_jurada', false)
            ->assertDontSee('Descargar modelo', false);
    }

    /** Inscripción con todos los datos y documentos cargados, con o sin la declaración jurada. */
    private function postulacionCompleta(Temporada $temporada, bool $conDeclaracion): Postulacion
    {
        $user = $this->usuario('postulante', '30000001');
        PostulacionPerfil::create([
            'user_id' => $user->id, 'telefono' => '2262', 'direccion' => 'Calle', 'numero' => '1',
            'fecha_nacimiento' => '1995-05-10', 'genero' => 'Femenino', 'grupo_sanguineo' => 'O+',
            'numero_libreta' => 'L-1', 'talle_remera' => 'M', 'talle_pantalon' => '40',
            'talle_campera' => 'M', 'talle_traje_bano' => 'M',
        ]);
        $postulacion = Postulacion::create([
            'user_id' => $user->id, 'temporada_id' => $temporada->id,
            'disponible_desde' => now()->addMonths(3), 'disponible_hasta' => now()->addMonths(8),
        ]);

        foreach (PostulacionDocumento::TIPOS as $tipo => $config) {
            if ($tipo === 'licencia_motonautica' || ($tipo === 'declaracion_jurada' && ! $conDeclaracion)) {
                continue;
            }
            $postulacion->documentos()->create([
                'tipo' => $tipo, 'ruta' => "postulaciones/x/{$tipo}.pdf",
                'nombre_original' => "{$tipo}.pdf", 'mime' => 'application/pdf', 'tamano' => 10,
            ]);
        }

        return $postulacion->fresh(['perfil', 'documentos', 'temporada', 'user']);
    }
}
