<?php

namespace Tests\Feature;

use App\Models\Postulacion;
use App\Models\PostulacionDocumento;
use App\Models\Temporada;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostulacionDocumentosReutilizadosTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Temporada $anterior;

    private Temporada $abierta;

    private Postulacion $vieja;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable
        Storage::fake('local');

        $this->user = User::create([
            'name' => 'Ana', 'lastname' => 'Pérez', 'dni' => '30111222', 'email' => 'ana@test.com',
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $this->user->assignRole('guardavida');

        $this->anterior = Temporada::create([
            'nombre' => '2025/26', 'fecha_inicio_postulacion' => now()->subYear()->subDays(30), 'fecha_fin_postulacion' => now()->subYear()->subDays(10),
            'fecha_inicio' => now()->subYear()->addMonths(2), 'fecha_fin' => now()->subYear()->addMonths(6),
        ]);
        $this->abierta = Temporada::create([
            'nombre' => '2026/27', 'fecha_inicio_postulacion' => now()->subDays(5), 'fecha_fin_postulacion' => now()->addDays(20),
            'fecha_inicio' => now()->addMonths(2), 'fecha_fin' => now()->addMonths(6),
        ]);

        $this->vieja = Postulacion::create(['user_id' => $this->user->id, 'temporada_id' => $this->anterior->id, 'estado' => 'aceptada', 'enviada_at' => now()->subYear()]);
        foreach (array_keys(PostulacionDocumento::TIPOS) as $tipo) {
            $this->subirViejo($tipo);
        }
    }

    private function subirViejo(string $tipo): PostulacionDocumento
    {
        $ruta = "postulaciones/{$this->anterior->id}/{$this->vieja->id}/{$tipo}.pdf";
        Storage::disk('local')->put($ruta, "contenido original de {$tipo}");

        return $this->vieja->documentos()->create([
            'tipo' => $tipo, 'ruta' => $ruta, 'nombre_original' => "{$tipo}-2025.pdf", 'mime' => 'application/pdf', 'tamano' => 20,
        ]);
    }

    private function postulacionNueva(): Postulacion
    {
        return Postulacion::create(['user_id' => $this->user->id, 'temporada_id' => $this->abierta->id, 'estado' => 'borrador']);
    }

    public function test_se_copian_solo_los_documentos_que_no_cambian(): void
    {
        $nueva = $this->postulacionNueva();

        $copiados = $nueva->reutilizarDocumentosAnteriores();

        $this->assertSame(3, $copiados);
        $this->assertEqualsCanonicalizing(['foto_personal', 'dni_frente', 'dni_dorso'], $nueva->documentos()->pluck('tipo')->all());
        foreach (['antecedentes_penales', 'declaracion_jurada', 'libreta', 'curriculum', 'licencia_motonautica'] as $tipo) {
            $this->assertNull($nueva->documentos()->where('tipo', $tipo)->first(), "{$tipo} se tiene que subir cada temporada");
        }
    }

    public function test_no_se_duplica_el_archivo_el_registro_nuevo_apunta_al_mismo(): void
    {
        $nueva = $this->postulacionNueva();
        $nueva->reutilizarDocumentosAnteriores();

        $doc = $nueva->documentos()->where('tipo', 'foto_personal')->first();
        $this->assertSame($this->vieja->documento('foto_personal')->ruta, $doc->ruta);
        $this->assertSame('contenido original de foto_personal', Storage::disk('local')->get($doc->ruta));
    }

    public function test_es_idempotente_y_no_pisa_lo_que_la_persona_ya_subio(): void
    {
        $nueva = $this->postulacionNueva();
        $nueva->documentos()->create([
            'tipo' => 'dni_frente', 'ruta' => 'postulaciones/x/nuevo.pdf', 'nombre_original' => 'nuevo.pdf', 'mime' => 'application/pdf', 'tamano' => 5,
        ]);

        $this->assertSame(2, $nueva->reutilizarDocumentosAnteriores());
        $this->assertSame(0, $nueva->reutilizarDocumentosAnteriores());
        $this->assertSame(3, $nueva->documentos()->count());
        $this->assertSame('nuevo.pdf', $nueva->documentos()->where('tipo', 'dni_frente')->value('nombre_original'));
    }

    public function test_si_el_archivo_viejo_ya_no_existe_no_se_copia(): void
    {
        Storage::disk('local')->delete($this->vieja->documento('dni_dorso')->ruta);
        $nueva = $this->postulacionNueva();

        $this->assertSame(2, $nueva->reutilizarDocumentosAnteriores());
        $this->assertNull($nueva->documentos()->where('tipo', 'dni_dorso')->first());
    }

    public function test_se_toma_el_mas_reciente_y_solo_de_la_misma_persona(): void
    {
        // Otra persona con documentos propios: no se mezclan.
        $otro = User::create([
            'name' => 'Otro', 'lastname' => 'X', 'dni' => '30999888', 'email' => 'otro@test.com',
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $pOtro = Postulacion::create(['user_id' => $otro->id, 'temporada_id' => $this->anterior->id, 'estado' => 'aceptada']);
        Storage::disk('local')->put('postulaciones/otro/foto.pdf', 'de otro');
        $pOtro->documentos()->create(['tipo' => 'foto_personal', 'ruta' => 'postulaciones/otro/foto.pdf', 'nombre_original' => 'otro.pdf', 'mime' => 'application/pdf', 'tamano' => 7]);

        // Una temporada intermedia con una foto más nueva.
        $media = Temporada::create([
            'nombre' => '2026 verano', 'fecha_inicio_postulacion' => now()->subMonths(8), 'fecha_fin_postulacion' => now()->subMonths(7),
            'fecha_inicio' => now()->subMonths(6), 'fecha_fin' => now()->subMonths(3),
        ]);
        $intermedia = Postulacion::create(['user_id' => $this->user->id, 'temporada_id' => $media->id, 'estado' => 'aceptada']);
        $ruta = "postulaciones/{$media->id}/{$intermedia->id}/foto_personal.pdf";
        Storage::disk('local')->put($ruta, 'foto mas nueva');
        $intermedia->documentos()->create(['tipo' => 'foto_personal', 'ruta' => $ruta, 'nombre_original' => 'nueva.pdf', 'mime' => 'application/pdf', 'tamano' => 14]);

        $nueva = $this->postulacionNueva();
        $nueva->reutilizarDocumentosAnteriores();

        $foto = $nueva->documentos()->where('tipo', 'foto_personal')->first();
        $this->assertSame('foto mas nueva', Storage::disk('local')->get($foto->ruta));
        $this->assertSame($ruta, $foto->ruta);
    }

    public function test_al_abrir_el_paso_de_documentos_ya_estan_cargados(): void
    {
        $this->postulacionNueva();

        $this->actingAs($this->user)->get(route('postulacion.paso', 3))
            ->assertOk();

        $this->assertSame(3, Postulacion::where('temporada_id', $this->abierta->id)->first()->documentos()->count());
    }

    public function test_reemplazar_un_documento_reutilizado_no_toca_el_de_la_temporada_anterior(): void
    {
        $nueva = $this->postulacionNueva();
        $nueva->reutilizarDocumentosAnteriores();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        $this->actingAs($this->user)->post(route('postulacion.guardar', 3), [
            'doc_foto_personal' => UploadedFile::fake()->createWithContent('foto-nueva.png', $png),
        ])->assertRedirect();

        $doc = $nueva->documentos()->where('tipo', 'foto_personal')->first();
        $this->assertSame('foto-nueva.png', $doc->nombre_original);
        $viejo = $this->vieja->documento('foto_personal');
        $this->assertTrue(Storage::disk('local')->exists($viejo->ruta));
        $this->assertSame('contenido original de foto_personal', Storage::disk('local')->get($viejo->ruta));
    }
}
