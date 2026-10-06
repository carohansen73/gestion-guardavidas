<?php

namespace Tests\Feature;

use App\Models\Guardavida;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use App\Support\NormalizadorInscripcion as Norm;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Migración de las inscripciones del sistema viejo. Corre sobre SQLite en
 * memoria: la "base vieja" es una segunda conexión también en memoria.
 */
class ImportarPostulacionesTest extends TestCase
{
    use RefreshDatabase;

    private Temporada $temporada;

    private Playa $claromeco;

    private Playa $reta;

    private Playa $orense;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesYPermisosSeeder::class); // permitido: SQLite en memoria, base descartable

        $this->claromeco = $this->playa('Claromecó');
        $this->reta = $this->playa('Reta');
        $this->orense = $this->playa('Orense');
        $this->temporada = Temporada::create([
            'nombre' => '2027', 'fecha_inicio_postulacion' => '2026-07-15', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-11-01', 'fecha_fin' => '2027-04-30',
        ]);

        config(['database.connections.viejo' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => false]]);
        DB::purge('viejo');
        $this->crearEsquemaViejo();
    }

    // ------------------------------------------------------------------ helpers

    private function playa(string $nombre): Playa
    {
        return Playa::forceCreate(['nombre' => $nombre, 'color' => '#00aaff', 'lat' => -38.0, 'lon' => -58.0]);
    }

    private function crearEsquemaViejo(): void
    {
        Schema::connection('viejo')->create('users', function ($t) {
            $t->unsignedBigInteger('id')->primary();
            $t->string('email');
            $t->string('password');
            $t->timestamp('created_at')->nullable();
        });
        Schema::connection('viejo')->create('inscriptions', function ($t) {
            $t->unsignedInteger('id')->primary();
            $t->string('lastname');
            $t->string('name');
            $t->string('document_number')->nullable();
            $t->date('birthday');
            $t->string('phone');
            $t->string('beach')->nullable();
            $t->string('optional_beach')->nullable();
            $t->string('lifeguard_book_number')->nullable();
            $t->date('availability_from')->nullable();
            $t->date('availability_to')->nullable();
            $t->unsignedBigInteger('user_id');
            $t->string('status');
            $t->text('observations')->nullable();
            $t->string('shirt_size')->nullable();
            $t->string('pants_size')->nullable();
            $t->string('jacket_size')->nullable();
            $t->string('swimwear_size')->nullable();
            $t->string('blood_type')->nullable();
            $t->string('health_insurance')->nullable();
            $t->string('member_id')->nullable();
            $t->timestamp('created_at')->nullable();
            $t->timestamp('updated_at')->nullable();
        });
    }

    /** Crea un usuario + su inscripción en la base vieja. */
    private function inscripcionVieja(int $id, array $datos = []): void
    {
        DB::connection('viejo')->table('users')->insert([
            'id' => $id, 'email' => $datos['email'] ?? "persona{$id}@test.com",
            'password' => $datos['password'] ?? '$2y$10$hashviejohashviejohashviejohashviejohashviejohashvie',
            'created_at' => '2020-10-05 12:00:00',
        ]);
        DB::connection('viejo')->table('inscriptions')->insert(array_merge([
            'id' => $id, 'lastname' => $datos['lastname'] ?? "Apellido{$id}", 'name' => $datos['name'] ?? "Nombre{$id}",
            'document_number' => $datos['dni'] ?? (string) (30000000 + $id), 'birthday' => '1995-05-10', 'phone' => '2262123456',
            'beach' => 'Claromecó', 'optional_beach' => 'Reta', 'lifeguard_book_number' => 'G-123',
            'availability_from' => '2026-11-01', 'availability_to' => '2027-04-30', 'user_id' => $id,
            'status' => $datos['status'] ?? 'Aceptada', 'observations' => $datos['observations'] ?? null,
            'shirt_size' => 'l', 'pants_size' => '40', 'jacket_size' => 'M', 'swimwear_size' => 'extra large',
            'blood_type' => $datos['sangre'] ?? '0+', 'health_insurance' => $datos['obra'] ?? 'No', 'member_id' => $datos['afiliado'] ?? null,
            'created_at' => '2026-07-20 10:00:00', 'updated_at' => '2026-08-01 15:00:00',
        ], array_intersect_key($datos, array_flip(['beach', 'optional_beach']))));
    }

    private function usuarioNuevoSistema(string $dni, string $email, bool $guardavida = true): User
    {
        $u = User::create(['name' => 'Existente', 'lastname' => 'Guardavida', 'dni' => $dni, 'email' => $email, 'password' => 'clave-actual', 'enabled' => true, 'must_change_psw' => false]);
        $u->assignRole($guardavida ? 'guardavida' : 'admin');
        if ($guardavida) {
            $puesto = Puesto::forceCreate(['nombre' => 'P1', 'latitud' => -38, 'longitud' => -58, 'playa_id' => $this->claromeco->id, 'qr_encriptado' => 'x']);
            Guardavida::forceCreate([
                'funcion' => 'Guardavida', 'nombre' => 'Existente', 'apellido' => 'Guardavida', 'dni' => $dni, 'telefono' => '111',
                'direccion' => 'Calle Real', 'numero' => '742', 'piso_dpto' => '2B', 'playa_id' => $this->claromeco->id,
                'puesto_id' => $puesto->id, 'user_id' => $u->id, 'turno' => 'M',
            ]);
        }

        return $u;
    }

    private function importar(array $opciones = []): string
    {
        Artisan::call('postulaciones:importar', ['--conexion' => 'viejo', '--temporada' => $this->temporada->id] + $opciones);

        return Artisan::output();
    }

    // ------------------------------------------------------------- normalizador

    public function test_normaliza_el_grupo_sanguineo(): void
    {
        $esperado = [
            '0+' => 'O+', 'O positivo' => 'O+', 'cero positivo' => 'O+', '0 RH+' => 'O+', '"0" "Positivo"' => 'O+', 'o+' => 'O+',
            'A+' => 'A+', 'A -' => 'A-', 'A- (negativo)' => 'A-', 'Apositivo' => 'A+', 'Grupo A RH+' => 'A+', 'Arh+' => 'A+',
            'AB+' => 'AB+', 'B positivo' => 'B+', 'B-' => 'B-', '0rh-' => 'O-',
            // sin letra o sin signo: no se puede saber
            'A' => null, 'M' => null, 'Rh' => null, 'rh+' => null, 'RH positivo' => null, 'no sabe' => null, '-' => null, 'Positivo' => null, '' => null,
        ];
        foreach ($esperado as $entrada => $salida) {
            $this->assertSame($salida, Norm::grupoSanguineo($entrada), "grupo sanguíneo '{$entrada}'");
        }
    }

    public function test_normaliza_obra_social_talle_y_dni(): void
    {
        foreach (['No', 'no', 'Ninguna', 'Si', ' ', 'No tiene', 'no posee obra social'] as $sin) {
            $this->assertNull(Norm::obraSocial($sin), "obra social '{$sin}'");
        }
        $this->assertSame('IOMA', Norm::obraSocial('IOMA'));
        $this->assertSame('Osde 210', Norm::obraSocial(' Osde 210 '));

        $this->assertSame('L', Norm::talle('l'));
        $this->assertSame('XL', Norm::talle('extra large'));
        $this->assertSame('2XL', Norm::talle('2xl'));
        $this->assertSame('M/L', Norm::talle('m/l'));

        $this->assertSame('23974841', Norm::dni('23.974.841'));
        $this->assertNull(Norm::dni(' '));
    }

    // ------------------------------------------------------------------ comando

    public function test_el_simulacro_no_escribe_nada(): void
    {
        $this->inscripcionVieja(1);
        $usuarios = User::count();

        $salida = $this->importar();

        $this->assertStringContainsString('SIMULACRO', $salida);
        $this->assertSame($usuarios, User::count());
        $this->assertDatabaseCount('postulaciones', 0);
        $this->assertDatabaseCount('perfiles', 0);
    }

    public function test_usuario_nuevo_se_crea_como_postulante_con_su_contrasena_y_datos_normalizados(): void
    {
        $this->inscripcionVieja(7, ['dni' => '40111222', 'email' => 'nuevo@test.com', 'password' => '$2y$10$ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01', 'sangre' => 'cero positivo', 'obra' => 'IOMA', 'afiliado' => '555']);

        $this->importar(['--ejecutar' => true]);

        $user = User::where('dni', '40111222')->firstOrFail();
        $this->assertTrue($user->hasRole('postulante'));
        $this->assertSame('nuevo@test.com', $user->email);
        $this->assertSame('$2y$10$ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01', $user->getRawOriginal('password'));
        $this->assertTrue((bool) $user->enabled);
        $this->assertFalse((bool) $user->must_change_psw);

        $perfil = DB::table('perfiles')->where('user_id', $user->id)->first();
        $this->assertSame('O+', $perfil->grupo_sanguineo);
        $this->assertSame('L', $perfil->talle_remera);
        $this->assertSame('XL', $perfil->talle_traje_bano);
        $this->assertSame('IOMA', $perfil->obra_social_nombre);
        $this->assertSame('555', $perfil->obra_social_numero_afiliado);
        $this->assertNull($perfil->genero);
        $this->assertNull($perfil->direccion); // un usuario nuevo no tiene domicilio cargado

        $post = DB::table('postulaciones')->where('user_id', $user->id)->first();
        $this->assertSame($this->temporada->id, (int) $post->temporada_id);
        $this->assertSame('aceptada', $post->estado);
        $this->assertSame(0, (int) $post->seleccionado);
        $this->assertSame([$this->claromeco->id, $this->reta->id], DB::table('postulacion_playas')->where('postulacion_id', $post->id)->orderBy('prioridad')->pluck('playa_id')->map(fn ($i) => (int) $i)->all());
    }

    public function test_un_guardavida_existente_no_se_modifica_solo_se_le_agrega_la_postulacion(): void
    {
        $existente = $this->usuarioNuevoSistema('40222333', 'gv@test.com');
        $antes = DB::table('users')->where('id', $existente->id)->first();
        $this->inscripcionVieja(8, ['dni' => '40.222.333', 'email' => 'otro-mail@test.com', 'name' => 'Nombre distinto', 'lastname' => 'Apellido distinto', 'password' => 'hash-viejo-distinto']);

        $this->importar(['--ejecutar' => true]);

        // El usuario queda EXACTAMENTE igual (mail, contraseña, nombre...).
        $this->assertEquals($antes, DB::table('users')->where('id', $existente->id)->first());
        $this->assertTrue(Hash::check('clave-actual', $existente->fresh()->password));
        $this->assertSame(['guardavida'], $existente->fresh()->getRoleNames()->all());
        $this->assertDatabaseMissing('users', ['email' => 'otro-mail@test.com']);

        // Pero ahora tiene postulación y perfil, con el domicilio tomado de su ficha de guardavida.
        $this->assertDatabaseHas('postulaciones', ['user_id' => $existente->id, 'temporada_id' => $this->temporada->id, 'estado' => 'aceptada']);
        $perfil = DB::table('perfiles')->where('user_id', $existente->id)->first();
        $this->assertSame('Calle Real', $perfil->direccion);
        $this->assertSame('742', $perfil->numero);
        $this->assertSame('2B', $perfil->piso_dpto);
    }

    public function test_mismo_mail_con_dni_distinto_corrige_el_dni_del_usuario_existente(): void
    {
        $existente = $this->usuarioNuevoSistema('23947841', 'kiosco@test.com'); // dni mal tipeado en el sistema nuevo
        $this->inscripcionVieja(9, ['dni' => '23974841', 'email' => 'kiosco@test.com']);

        $this->importar(['--ejecutar' => true]);

        $this->assertSame('23974841', $existente->fresh()->dni);
        $this->assertSame('23974841', DB::table('guardavidas')->where('user_id', $existente->id)->value('dni'));
        $this->assertDatabaseCount('users', 1); // no se creó un usuario duplicado
        $this->assertDatabaseHas('postulaciones', ['user_id' => $existente->id]);
    }

    public function test_los_borradores_no_se_importan_y_los_estados_se_mapean(): void
    {
        $this->inscripcionVieja(10, ['status' => 'Pendiente', 'email' => 'borrador@test.com']);
        $this->inscripcionVieja(11, ['status' => 'Revisión Pendiente', 'observations' => 'Falta certificado de antecedentes']);
        $this->inscripcionVieja(12, ['status' => 'Rechazada']);
        $this->inscripcionVieja(13, ['status' => 'Aceptada']);

        $this->importar(['--ejecutar' => true]);

        $this->assertDatabaseMissing('users', ['email' => 'borrador@test.com']);
        $this->assertDatabaseCount('postulaciones', 3);

        $revision = DB::table('postulaciones')->where('estado', 'pendiente')->first();
        $this->assertSame('Falta certificado de antecedentes', $revision->observaciones);
        $this->assertNull($revision->fecha_revision); // todavía no fue revisada
        $this->assertDatabaseHas('postulaciones', ['estado' => 'rechazada']);
        $this->assertDatabaseHas('postulaciones', ['estado' => 'aceptada']);
        $this->assertNotNull(DB::table('postulaciones')->where('estado', 'aceptada')->value('fecha_revision'));
    }

    public function test_si_la_segunda_playa_es_igual_a_la_primera_no_se_repite(): void
    {
        $this->inscripcionVieja(14, ['beach' => 'Orense', 'optional_beach' => 'Orense']);

        $this->importar(['--ejecutar' => true]);

        $this->assertDatabaseCount('postulacion_playas', 1);
        $this->assertDatabaseHas('postulacion_playas', ['playa_id' => $this->orense->id, 'prioridad' => 1]);
    }

    public function test_correrlo_dos_veces_no_duplica_nada(): void
    {
        $this->usuarioNuevoSistema('40222333', 'gv@test.com');
        $this->inscripcionVieja(8, ['dni' => '40222333']);
        $this->inscripcionVieja(15, ['dni' => '40555666']);

        $this->importar(['--ejecutar' => true]);
        $usuarios = User::count();
        $this->importar(['--ejecutar' => true]);

        $this->assertSame($usuarios, User::count());
        $this->assertDatabaseCount('postulaciones', 2);
        $this->assertDatabaseCount('perfiles', 2);
        $this->assertDatabaseCount('postulacion_playas', 4);
    }

    public function test_genera_el_sql_para_produccion_y_el_sql_para_deshacer(): void
    {
        $this->usuarioNuevoSistema('23947841', 'kiosco@test.com');
        $this->inscripcionVieja(9, ['dni' => '23974841', 'email' => 'kiosco@test.com']);
        $this->inscripcionVieja(16, ['dni' => '40777888', 'email' => 'otra@test.com']);
        $sql = tempnam(sys_get_temp_dir(), 'imp').'.sql';
        $deshacer = tempnam(sys_get_temp_dir(), 'des').'.sql';
        $informe = tempnam(sys_get_temp_dir(), 'inf').'.csv';

        $this->importar(['--sql' => $sql, '--sql-deshacer' => $deshacer, '--informe' => $informe]); // simulacro

        $contenido = file_get_contents($sql);
        $this->assertStringContainsString('START TRANSACTION', $contenido);
        $this->assertStringContainsString("UPDATE users SET dni = '23974841'", $contenido);
        // Decide al ejecutarse: datos en tabla temporal + usuarios nuevos solo si no existe nadie con ese DNI o mail.
        $this->assertStringContainsString('CREATE TEMPORARY TABLE imp_inscripciones', $contenido);
        $this->assertStringContainsString("'otra@test.com'", $contenido);
        $this->assertMatchesRegularExpression('/INSERT INTO users .*?WHERE NOT EXISTS \(SELECT 1 FROM users u WHERE u\.dni = t\.dni/s', $contenido);
        $this->assertStringContainsString('INSERT IGNORE INTO perfiles', $contenido);
        $this->assertStringContainsString('INSERT IGNORE INTO postulaciones', $contenido);
        $this->assertStringContainsString('INSERT IGNORE INTO postulacion_playas', $contenido);
        $this->assertStringContainsString('COMMIT', $contenido);
        // El rol postulante solo se da a quien no tiene ningún rol (nunca a un guardavida que ya existe).
        $this->assertStringContainsString('NOT EXISTS (SELECT 1 FROM model_has_roles x', $contenido);

        $revertir = file_get_contents($deshacer);
        $this->assertStringContainsString('DELETE p FROM postulaciones', $revertir);
        $this->assertStringContainsString('p.seleccionado = 0', $revertir); // nunca borra lo ya seleccionado
        $this->assertStringContainsString("'otra@test.com'", $revertir);

        $this->assertStringContainsString('kiosco@test.com', file_get_contents($informe));
        $this->assertDatabaseCount('postulaciones', 0); // y el simulacro no escribió nada
    }
}
