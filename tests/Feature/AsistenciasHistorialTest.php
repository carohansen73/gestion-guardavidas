<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Guardavida;
use App\Models\Licencia;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use App\Services\HistorialAsistenciaService;
use Carbon\Carbon;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Historial de asistencias de una persona: mes, resumen, calendario, lista y detalle de fichajes. */
class AsistenciasHistorialTest extends TestCase
{
    use RefreshDatabase;

    private const RANGO = ['inicio' => '2026-10-05', 'fin' => '2026-10-09']; // lunes a viernes

    private User $admin;

    private Guardavida $g;

    private Puesto $puesto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable

        Temporada::create([
            'nombre' => '2026/27', 'fecha_inicio_postulacion' => '2026-08-01', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-10-01', 'fecha_fin' => '2027-03-31',
        ]);
        $playa = Playa::forceCreate(['nombre' => 'Costa Azul', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $this->puesto = Puesto::forceCreate(['nombre' => 'Puesto 1', 'latitud' => -38.0, 'longitud' => -58.0, 'playa_id' => $playa->id, 'qr_encriptado' => 'x']);

        $this->admin = $this->usuario('20000001', 'Admin', 'Admin', 'admin');
        $user = $this->usuario('30000001', 'Beto', 'Aguirre', 'guardavida');
        $this->g = Guardavida::forceCreate(['funcion' => 'Guardavida', 'user_id' => $user->id, 'playa_id' => $playa->id, 'puesto_id' => $this->puesto->id, 'turno' => 'M']);
        $this->g->darDeAlta(Carbon::parse('2026-01-01'), null, 'Alta manual', true);

        // Lunes: un fichaje en el puesto. Martes: entra en el puesto y sale 111 m más lejos (≈0,001° de latitud).
        $this->fichaje('2026-10-05 08:00:00', -38.0, 5);
        $this->fichaje('2026-10-06 08:00:00', -38.0, 5);
        $this->fichaje('2026-10-06 17:00:00', -38.001, 12);
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

    private function fichaje(string $fechaHora, float $lat, int $precision): void
    {
        Asistencia::forceCreate([
            'guardavidas_id' => $this->g->id, 'puesto_id' => $this->puesto->id, 'estado_validacion' => 'valido',
            'longitud' => -58.0, 'latitud' => $lat, 'precision' => $precision, 'fecha_hora' => Carbon::parse($fechaHora),
        ]);
    }

    private function ver(array $query = [])
    {
        return $this->actingAs($this->admin)->get(route('asistencias.guardavida', [$this->g->id] + $query));
    }

    public function test_el_servicio_trae_cada_fichaje_con_distancia_y_precision(): void
    {
        $historial = (new HistorialAsistenciaService)->generar($this->g->id, Carbon::parse('2026-10-05')->startOfDay(), Carbon::parse('2026-10-06')->endOfDay());

        $martes = collect($historial)->firstWhere('fecha', '2026-10-06');

        $this->assertCount(2, $martes['fichajes']);
        $this->assertSame(0, $martes['fichajes'][0]['distancia']);
        $this->assertSame(111, $martes['fichajes'][1]['distancia']);
        $this->assertSame(12, $martes['fichajes'][1]['precision']);
        $this->assertSame('17:00', $martes['fichajes'][1]['hora']);
    }

    public function test_los_francos_y_licencias_traen_su_detalle(): void
    {
        Licencia::forceCreate([
            'guardavida_id' => $this->g->id, 'tipo_licencia' => 'Enfermedad', 'fecha_inicio' => '2026-10-07', 'fecha_fin' => '2026-10-07', 'detalle' => 'x', 'en_tiempo' => true, 'turno' => 'M', 'playa_id' => $this->g->playa_id,
        ]);

        $historial = collect((new HistorialAsistenciaService)->generar($this->g->id, Carbon::parse('2026-10-05')->startOfDay(), Carbon::parse('2026-10-09')->endOfDay()));

        $this->assertSame('Enfermedad', $historial->firstWhere('fecha', '2026-10-07')['detalle']);
    }

    public function test_muestra_resumen_calendario_y_lista(): void
    {
        $res = $this->ver(self::RANGO)->assertOk();

        $res->assertSee('Aguirre Beto')->assertSee('Costa Azul')->assertSee('Revisar ubicación');
        $html = $res->getContent();
        // 5 días (lun-vie), 2 con fichaje y 3 sin nada → 3 faltas, 40 %.
        $this->assertStringContainsString('40%', $html);
        $this->assertStringContainsString('aria-label="Lunes 05/10/2026: Asistió"', $html);
        $this->assertStringContainsString('aria-label="Miércoles 07/10/2026: Falta"', $html);
        $this->assertStringContainsString('tipo=asistencia-guardavida', $html);
    }

    public function test_la_lista_va_del_dia_mas_nuevo_al_mas_viejo(): void
    {
        $historial = $this->ver(self::RANGO)->assertOk()->viewData('historial');

        $this->assertSame('2026-10-09', $historial[0]['fecha']);
        $this->assertSame('2026-10-05', end($historial)['fecha']);
    }

    public function test_el_mes_de_las_flechas_trae_el_mes_completo(): void
    {
        $res = $this->ver(['mes' => '2026-09'])->assertOk();

        $res->assertSee('Sep 2026');
        $this->assertSame('2026-09-01', $res->viewData('inicio')->toDateString());
        $this->assertSame('2026-09-30', $res->viewData('fin')->toDateString());
        $this->assertStringContainsString('mes=2026-08', $res->getContent());
        $this->assertStringContainsString('mes=2026-10', $res->getContent());
    }

    public function test_por_defecto_es_el_mes_en_curso(): void
    {
        $res = $this->ver()->assertOk();

        $this->assertSame(Carbon::now()->startOfMonth()->toDateString(), $res->viewData('inicio')->toDateString());
        $this->assertSame(Carbon::now()->toDateString(), $res->viewData('fin')->toDateString());
    }

    public function test_el_detalle_incluye_distancia_precision_y_puesto_de_cada_fichaje(): void
    {
        $detalles = $this->ver(self::RANGO)->assertOk()->viewData('historial');
        $martes = collect($detalles)->firstWhere('fecha', '2026-10-06');

        $this->assertSame(111, $martes['fichajes'][1]['distancia']);
        // El JSON del detalle viaja en la página para el modal.
        $this->ver(self::RANGO)->assertSee('111', false)->assertSee('Puesto 1');
    }

    public function test_el_franco_es_solo_para_admin_o_encargado(): void
    {
        $this->ver(self::RANGO)->assertSee('Cargar cambio de franco');

        $otro = $this->usuario('30000002', 'Otro', 'Guardia', 'guardavida');
        $otro->givePermissionTo('ver_asistencia');
        $this->actingAs($otro)->get(route('asistencias.guardavida', [$this->g->id] + self::RANGO))
            ->assertOk()->assertDontSee('Cargar cambio de franco');
    }

    public function test_no_avisa_si_estuvo_en_el_plantel_todo_el_periodo(): void
    {
        $this->ver(self::RANGO)->assertOk()->assertDontSee('Fuera del plantel')->assertDontSee('solo una parte');
    }

    public function test_avisa_cuando_estuvo_solo_una_parte_del_periodo(): void
    {
        $this->g->periodos()->update(['hasta' => '2026-10-06']);

        $this->ver(self::RANGO)->assertOk()->assertSee('solo una parte de este período');
    }

    public function test_avisa_cuando_esta_fuera_del_plantel_todo_el_periodo_y_desde_cuando(): void
    {
        $this->g->periodos()->update(['hasta' => '2026-10-06']);

        $this->ver(['inicio' => '2026-10-07', 'fin' => '2026-10-09'])->assertOk()
            ->assertSee('Fuera del plantel')->assertSee('hasta el 06/10/2026');
    }

    public function test_sin_permiso_no_entra(): void
    {
        $otro = $this->usuario('30000003', 'Sin', 'Permiso', 'guardavida');

        $this->actingAs($otro)->get(route('asistencias.guardavida', [$this->g->id]))->assertForbidden();
    }
}
