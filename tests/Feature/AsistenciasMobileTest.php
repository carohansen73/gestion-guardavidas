<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Guardavida;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Listado de asistencias en celular: tarjetas con resumen, orden y búsqueda en vivo (respuesta parcial). */
class AsistenciasMobileTest extends TestCase
{
    use RefreshDatabase;

    private const MOVIL = 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148';

    private const RANGO = ['inicio' => '2026-10-05', 'fin' => '2026-10-08']; // lunes a jueves: 4 días

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable

        Temporada::create([
            'nombre' => '2026/27', 'fecha_inicio_postulacion' => '2026-08-01', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-10-01', 'fecha_fin' => '2027-03-31',
        ]);
        $playa = Playa::forceCreate(['nombre' => 'Costa Azul', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $puesto = Puesto::forceCreate(['nombre' => 'Puesto 1', 'latitud' => -38.0, 'longitud' => -58.0, 'playa_id' => $playa->id, 'qr_encriptado' => 'x']);

        $this->admin = $this->usuario('20000001', 'Admin', 'Admin', 'admin');

        // Mora: 4 de 4 días (100 %). Aguirre: 2 de 4 (50 %, 2 faltas). Soto: ninguno (0 %, 4 faltas).
        foreach ([['Mora', 'Ana', 4], ['Aguirre', 'Beto', 2], ['Soto', 'Carla', 0]] as $i => [$apellido, $nombre, $dias]) {
            $user = $this->usuario('3000000'.$i, $nombre, $apellido, 'guardavida');
            $g = Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $user->id, 'playa_id' => $playa->id, 'puesto_id' => $puesto->id, 'turno' => 'M']);
            $g->darDeAlta(Carbon::parse('2026-01-01'), null, 'Alta manual', true);

            for ($d = 0; $d < $dias; $d++) {
                Asistencia::forceCreate([
                    'guardavidas_id' => $g->id, 'puesto_id' => $puesto->id, 'estado_validacion' => 'valido',
                    'longitud' => -58.0, 'latitud' => -38.0, 'precision' => 10,
                    'fecha_hora' => Carbon::parse(self::RANGO['inicio'])->addDays($d)->setTime(8, 0),
                ]);
            }
        }
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_USER_AGENT']);
        parent::tearDown();
    }

    private function usuario(string $dni, string $nombre, string $apellido, string $rol): User
    {
        $user = User::create([
            'name' => $nombre, 'lastname' => $apellido, 'dni' => $dni, 'email' => "u{$dni}@test.com",
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $user->assignRole($rol);

        return $user;
    }

    private function movil(array $query = [], bool $ajax = false)
    {
        $headers = ['User-Agent' => self::MOVIL] + ($ajax ? ['X-Requested-With' => 'XMLHttpRequest'] : []);
        // Jenssegers\Agent lee el superglobal, no el request: hay que setearlo para que detecte el celular.
        $_SERVER['HTTP_USER_AGENT'] = self::MOVIL;

        return $this->actingAs($this->admin)->withHeaders($headers)->get(route('asistencias.index', self::RANGO + $query));
    }

    public function test_la_tarjeta_muestra_porcentaje_y_faltas_solo_cuando_hay(): void
    {
        $html = $this->movil()->assertOk()->assertSee('Asistencias')->getContent();

        $this->assertStringContainsString('100%', $html);
        $this->assertStringContainsString('50%', $html);
        $this->assertStringContainsString('2 faltas', $html);
        $this->assertStringContainsString('4 faltas', $html);
        $this->assertStringContainsString(route('asistencias.guardavida', Guardavida::first()->id), $html);
    }

    public function test_por_defecto_va_alfabetico(): void
    {
        $this->movil()->assertSeeInOrder(['Aguirre', 'Mora', 'Soto']);
    }

    public function test_ordena_por_mas_faltas(): void
    {
        $this->movil(['orden' => 'faltas'])->assertSeeInOrder(['Soto', 'Aguirre', 'Mora']);
    }

    private function movilSinRango(array $query = [])
    {
        $_SERVER['HTTP_USER_AGENT'] = self::MOVIL;

        return $this->actingAs($this->admin)->withHeaders(['User-Agent' => self::MOVIL])->get(route('asistencias.index', $query));
    }

    public function test_por_defecto_muestra_el_mes_en_curso_sin_flecha_siguiente(): void
    {
        $ahora = Carbon::now();
        $html = $this->movilSinRango()->assertOk()->getContent();

        $this->assertStringContainsString(['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'][$ahora->month - 1].' '.$ahora->year, $html);
        $this->assertStringContainsString('mes='.$ahora->copy()->subMonthNoOverflow()->format('Y-m'), $html);
        $this->assertStringNotContainsString('aria-label="Mes siguiente"', $html);
    }

    public function test_la_flecha_pasa_de_un_mes_al_otro_y_toma_el_mes_completo(): void
    {
        $html = $this->movilSinRango(['mes' => '2026-09', 'orden' => 'faltas'])->assertOk()->getContent();

        $this->assertStringContainsString('Sep 2026', $html);
        $this->assertStringContainsString('value="2026-09-01"', $html);
        $this->assertStringContainsString('value="2026-09-30"', $html);
        $this->assertStringContainsString('mes=2026-08', $html);
        $this->assertStringContainsString('mes=2026-10', $html);
        $this->assertStringContainsString('orden=faltas', $html); // las flechas conservan el orden
    }

    public function test_un_mes_futuro_o_invalido_se_ignora(): void
    {
        $actual = Carbon::now()->format('Y-m-01');

        $this->movilSinRango(['mes' => '2099-01'])->assertOk()->assertSee('value="'.$actual.'"', false);
        $this->movilSinRango(['mes' => 'basura'])->assertOk()->assertSee('value="'.$actual.'"', false);
    }

    public function test_con_fechas_a_mano_no_hay_flechas(): void
    {
        $this->movil()->assertOk()->assertDontSee('aria-label="Mes anterior"', false);
    }

    public function test_un_orden_invalido_cae_en_alfabetico(): void
    {
        $this->movil(['orden' => 'cualquiera'])->assertSeeInOrder(['Aguirre', 'Mora', 'Soto']);
    }

    public function test_la_busqueda_en_vivo_devuelve_solo_la_lista_filtrada(): void
    {
        $res = $this->movil(['search' => 'Mora'], ajax: true)->assertOk();

        $res->assertSee('Mora')->assertDontSee('Aguirre')->assertDontSee('Soto');
        $res->assertDontSee('<html', false); // parcial, no la página entera
    }

    public function test_la_busqueda_en_vivo_respeta_el_orden(): void
    {
        $this->movil(['orden' => 'faltas'], ajax: true)->assertSeeInOrder(['Soto', 'Aguirre', 'Mora']);
    }

    public function test_el_orden_por_resumen_pagina_sobre_todo_el_filtro(): void
    {
        // 12 personas más sin asistencias (12 faltas... 4 faltas cada una): el orden y el paginado deben ser globales.
        $playa = Playa::first();
        $puesto = Puesto::first();
        for ($i = 0; $i < 12; $i++) {
            $user = $this->usuario('4000000'.$i, 'Pepe', 'Zzz'.str_pad((string) $i, 2, '0', STR_PAD_LEFT), 'guardavida');
            $g = Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $user->id, 'playa_id' => $playa->id, 'puesto_id' => $puesto->id, 'turno' => 'M']);
            $g->darDeAlta(Carbon::parse('2026-01-01'), null, 'Alta manual', true);
        }

        $pag1 = $this->movil(['orden' => 'faltas'])->assertOk()->getContent();
        $pag2 = $this->movil(['orden' => 'faltas', 'page' => 2])->assertOk()->getContent();

        $this->assertStringNotContainsString('Mora', $pag1); // la de 100 % queda al final (pág. 2)
        $this->assertStringContainsString('Mora', $pag2);
    }

    public function test_escritorio_sigue_con_su_vista(): void
    {
        $this->actingAs($this->admin)->get(route('asistencias.index', self::RANGO))
            ->assertOk()->assertSee('<table', false)->assertSee('% Asistencia');
    }
}
