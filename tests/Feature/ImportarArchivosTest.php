<?php

namespace Tests\Feature;

use App\Models\Temporada;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Migración de archivos del sistema viejo a postulacion_documentos. SQLite en memoria; los
 * archivos "viejos" son de mentira, en una carpeta temporal.
 */
class ImportarArchivosTest extends TestCase
{
    use RefreshDatabase;

    private Temporada $temporada;

    private string $origen;

    private const PDF = "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF";

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->temporada = Temporada::create([
            'nombre' => '2027', 'fecha_inicio_postulacion' => '2026-07-15', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-11-01', 'fecha_fin' => '2027-04-30',
        ]);

        config(['database.connections.viejo' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => false]]);
        DB::purge('viejo');
        Schema::connection('viejo')->create('users', function ($t) {
            $t->unsignedBigInteger('id')->primary();
            $t->string('email');
        });
        Schema::connection('viejo')->create('inscriptions', function ($t) {
            $t->unsignedInteger('id')->primary();
            $t->string('lastname');
            $t->string('name');
            $t->string('document_number')->nullable();
            $t->unsignedBigInteger('user_id');
            $t->string('status');
            foreach (['photography', 'front_document', 'back_document', 'cv', 'lifeguard_notebook', 'criminal_record', 'declaration', 'motorcycle_licence_photo'] as $c) {
                $t->string($c)->nullable();
            }
        });

        $this->origen = sys_get_temp_dir().DIRECTORY_SEPARATOR.'origen_'.uniqid();
        File::makeDirectory($this->origen);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->origen);
        parent::tearDown();
    }

    // ------------------------------------------------------------------ helpers

    private function archivo(string $carpeta, string $nombre, ?string $contenido = null): void
    {
        File::ensureDirectoryExists($this->origen.DIRECTORY_SEPARATOR.$carpeta);
        File::put($this->origen.DIRECTORY_SEPARATOR.$carpeta.DIRECTORY_SEPARATOR.$nombre, $contenido ?? self::PDF);
    }

    private function jpg(): string
    {
        return "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00\xFF\xD9";
    }

    private function png(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
    }

    /** Persona con postulación en el sistema NUEVO + su inscripción en el viejo. Devuelve el id de la postulación. */
    private function persona(int $n, array $archivos = [], string $status = 'Aceptada', ?string $dniViejo = null, bool $conPostulacion = true): int
    {
        $dni = (string) (30000000 + $n);
        $userId = DB::table('users')->insertGetId([
            'name' => "Nombre{$n}", 'lastname' => "Apellido{$n}", 'dni' => $dni, 'email' => "p{$n}@test.com",
            'password' => 'x', 'enabled' => 1, 'must_change_psw' => 0, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $postulacionId = 0;
        if ($conPostulacion) {
            $postulacionId = DB::table('postulaciones')->insertGetId([
                'user_id' => $userId, 'temporada_id' => $this->temporada->id, 'estado' => 'aceptada', 'seleccionado' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        DB::connection('viejo')->table('users')->insert(['id' => $n, 'email' => "p{$n}@test.com"]);
        DB::connection('viejo')->table('inscriptions')->insert([
            'id' => $n, 'lastname' => "Apellido{$n}", 'name' => "Nombre{$n}", 'document_number' => $dniViejo ?? $dni,
            'user_id' => $n, 'status' => $status,
        ] + $archivos);

        return $postulacionId;
    }

    private function importar(array $opciones = []): string
    {
        Artisan::call('postulaciones:importar-archivos', ['--conexion' => 'viejo', '--temporada' => $this->temporada->id, '--origen' => $this->origen] + $opciones);

        return Artisan::output();
    }

    // -------------------------------------------------------------------- casos

    public function test_copia_los_archivos_a_la_estructura_nueva_y_crea_las_filas(): void
    {
        $this->archivo('photography', '1700-foto.jpg', $this->jpg());
        $this->archivo('front_document', '1701-dni.png', $this->png());
        $this->archivo('cv', '1702.pdf');
        $this->archivo('lifeguard_notebook', '1703.PDF'); // extensión en mayúsculas
        $id = $this->persona(1, ['photography' => '1700-foto.jpg', 'front_document' => '1701-dni.png', 'cv' => '1702.pdf', 'lifeguard_notebook' => '1703.PDF']);

        $this->importar(['--ejecutar' => true]);

        $t = $this->temporada->id;
        foreach (['foto_personal.jpg', 'dni_frente.png', 'curriculum.pdf', 'libreta.pdf'] as $f) {
            Storage::disk('local')->assertExists("postulaciones/{$t}/{$id}/{$f}");
        }
        $this->assertDatabaseCount('postulacion_documentos', 4);
        $this->assertDatabaseHas('postulacion_documentos', [
            'postulacion_id' => $id, 'tipo' => 'foto_personal', 'ruta' => "postulaciones/{$t}/{$id}/foto_personal.jpg",
            'nombre_original' => '1700-foto.jpg', 'mime' => 'image/jpeg',
        ]);
        $this->assertDatabaseHas('postulacion_documentos', ['postulacion_id' => $id, 'tipo' => 'libreta', 'mime' => 'application/pdf']);
        // El original NO se mueve ni se borra.
        $this->assertFileExists($this->origen.DIRECTORY_SEPARATOR.'photography'.DIRECTORY_SEPARATOR.'1700-foto.jpg');
    }

    public function test_el_simulacro_no_copia_ni_escribe_nada(): void
    {
        $this->archivo('cv', '1.pdf');
        $this->persona(1, ['cv' => '1.pdf']);

        $salida = $this->importar();

        $this->assertStringContainsString('SIMULACRO', $salida);
        $this->assertDatabaseCount('postulacion_documentos', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_informa_lo_que_falta_lo_roto_y_lo_vacio_sin_romper_el_resto(): void
    {
        $this->archivo('cv', 'bueno.pdf');
        $this->archivo('criminal_record', 'vacio.pdf', '');
        $id = $this->persona(1, [
            'cv' => 'bueno.pdf',
            'photography' => '1785537758-.', // nombre roto: sin extensión
            'front_document' => 'no-esta.jpg', // referenciado pero no está en la carpeta
            'criminal_record' => 'vacio.pdf',
        ]);
        $informe = tempnam(sys_get_temp_dir(), 'inf').'.csv';

        $salida = $this->importar(['--ejecutar' => true, '--informe' => $informe]);

        $this->assertDatabaseCount('postulacion_documentos', 1);
        $this->assertDatabaseHas('postulacion_documentos', ['postulacion_id' => $id, 'tipo' => 'curriculum']);
        $this->assertStringContainsString('roto', $salida);
        $this->assertStringContainsString('falta', $salida);
        $this->assertStringContainsString('vacio', $salida);
        $csv = file_get_contents($informe);
        $this->assertStringContainsString('no-esta.jpg', $csv);
        $this->assertStringContainsString('1785537758-.', $csv);
    }

    public function test_un_archivo_sin_extension_o_con_extension_rara_se_identifica_por_su_contenido(): void
    {
        $this->archivo('cv', '1788918783', self::PDF);       // sin extensión
        $this->archivo('lifeguard_notebook', '1789.pdf3', self::PDF); // "pdf3"
        $id = $this->persona(1, ['cv' => '1788918783', 'lifeguard_notebook' => '1789.pdf3']);

        $this->importar(['--ejecutar' => true]);

        $t = $this->temporada->id;
        Storage::disk('local')->assertExists("postulaciones/{$t}/{$id}/curriculum.pdf");
        Storage::disk('local')->assertExists("postulaciones/{$t}/{$id}/libreta.pdf");
    }

    public function test_un_archivo_cuyo_nombre_viejo_termina_en_punto_se_encuentra_sin_el_punto(): void
    {
        // En el sistema viejo se llamaban "1785537758-." y "1787623129."; al bajarlos a Windows perdieron el punto final.
        $this->archivo('photography', '1785537758-', $this->jpg());
        $this->archivo('lifeguard_notebook', '1787623129', self::PDF);
        $id = $this->persona(1, ['photography' => '1785537758-.', 'lifeguard_notebook' => '1787623129.']);

        $this->importar(['--ejecutar' => true]);

        $t = $this->temporada->id;
        Storage::disk('local')->assertExists("postulaciones/{$t}/{$id}/foto_personal.jpg"); // el formato sale del contenido
        Storage::disk('local')->assertExists("postulaciones/{$t}/{$id}/libreta.pdf");
        $this->assertDatabaseHas('postulacion_documentos', ['postulacion_id' => $id, 'tipo' => 'foto_personal', 'nombre_original' => '1785537758-.']);
    }

    public function test_los_borradores_y_las_personas_sin_postulacion_no_se_importan(): void
    {
        $this->archivo('cv', 'a.pdf');
        $this->archivo('cv', 'b.pdf');
        $this->persona(1, ['cv' => 'a.pdf'], status: 'Pendiente');                 // borrador
        $this->persona(2, ['cv' => 'b.pdf'], conPostulacion: false);               // inscripta pero sin postulación importada

        $salida = $this->importar(['--ejecutar' => true]);

        $this->assertDatabaseCount('postulacion_documentos', 0);
        $this->assertStringContainsString('SIN POSTULACIÓN', $salida);
    }

    public function test_si_el_dni_no_coincide_se_vincula_por_mail(): void
    {
        $this->archivo('cv', 'a.pdf');
        $id = $this->persona(1, ['cv' => 'a.pdf'], dniViejo: '99999999'); // DNI distinto, mismo mail

        $this->importar(['--ejecutar' => true]);

        $this->assertDatabaseHas('postulacion_documentos', ['postulacion_id' => $id, 'tipo' => 'curriculum']);
    }

    public function test_correrlo_dos_veces_no_duplica_ni_pisa_nada(): void
    {
        $this->archivo('cv', 'a.pdf');
        $this->persona(1, ['cv' => 'a.pdf']);

        $this->importar(['--ejecutar' => true]);
        $original = DB::table('postulacion_documentos')->first();
        $salida = $this->importar(['--ejecutar' => true]);

        $this->assertDatabaseCount('postulacion_documentos', 1);
        $this->assertEquals($original, DB::table('postulacion_documentos')->first());
        $this->assertStringContainsString('ya existían', $salida);
    }

    public function test_genera_el_sql_para_produccion_y_el_de_deshacer(): void
    {
        $this->archivo('cv', 'a.pdf');
        $this->archivo('declaration', 'd.jpg', $this->jpg());
        $this->persona(1, ['cv' => 'a.pdf', 'declaration' => 'd.jpg']);
        $sql = tempnam(sys_get_temp_dir(), 'doc').'.sql';
        $deshacer = tempnam(sys_get_temp_dir(), 'des').'.sql';

        $this->importar(['--sql' => $sql, '--sql-deshacer' => $deshacer]); // simulacro

        $texto = file_get_contents($sql);
        $this->assertStringContainsString('CREATE TEMPORARY TABLE imp_documentos', $texto);
        $this->assertStringContainsString("'declaracion_jurada'", $texto);
        $this->assertStringContainsString('INSERT IGNORE INTO postulacion_documentos', $texto);
        $this->assertStringContainsString("CONCAT('postulaciones/{$this->temporada->id}/', p.id", $texto);
        $this->assertStringContainsString('START TRANSACTION', $texto);
        $this->assertStringContainsString('COMMIT', $texto);

        $revertir = file_get_contents($deshacer);
        $this->assertStringContainsString('DELETE d FROM postulacion_documentos', $revertir);
        $this->assertStringContainsString('x.nombre_original = d.nombre_original', $revertir);
        $this->assertDatabaseCount('postulacion_documentos', 0);
    }
}
