<?php

namespace Tests\Feature;

use App\Models\Guardavida;
use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\Temporada;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasosCompletosPostulacionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Temporada $abierta;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable

        $this->user = User::create([
            'name' => 'Ana', 'lastname' => 'Pérez', 'dni' => '30111222', 'email' => 'ana@test.com',
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $this->user->assignRole('guardavida');
        Guardavida::create([
            'funcion' => 'Guardavida', 'nombre' => 'Ana', 'apellido' => 'Pérez', 'dni' => '30111222', 'user_id' => $this->user->id,
            'playa_id' => Playa::forceCreate(['nombre' => 'Playa Test', 'color' => '#0ea5e9', 'lat' => -38.0, 'lon' => -57.5])->id,
        ]);

        $this->abierta = Temporada::create([
            'nombre' => '2026/27', 'fecha_inicio_postulacion' => now()->subDays(5), 'fecha_fin_postulacion' => now()->addDays(20),
            'fecha_inicio' => now()->addMonths(2), 'fecha_fin' => now()->addMonths(6),
        ]);
    }

    public function test_una_inscripcion_recien_empezada_solo_tiene_pendientes(): void
    {
        $p = Postulacion::create(['user_id' => $this->user->id, 'temporada_id' => $this->abierta->id, 'estado' => 'borrador']);

        $this->assertSame([1 => false, 2 => false, 3 => false, 4 => false], $p->pasosCompletos());
    }

    public function test_los_pasos_se_completan_de_forma_independiente(): void
    {
        $p = Postulacion::create([
            'user_id' => $this->user->id, 'temporada_id' => $this->abierta->id, 'estado' => 'borrador',
            'disponible_desde' => now()->addMonths(2), 'disponible_hasta' => now()->addMonths(5),
        ]);

        $this->assertSame([1 => false, 2 => true, 3 => false, 4 => false], $p->pasosCompletos());
    }

    public function test_enviada_marca_el_paso_4(): void
    {
        $p = Postulacion::create(['user_id' => $this->user->id, 'temporada_id' => $this->abierta->id, 'estado' => 'pendiente']);

        $this->assertTrue($p->pasosCompletos()[4]);
    }

    public function test_el_dashboard_se_ve_sin_inscripcion_empezada_y_con_ella(): void
    {
        $this->actingAs($this->user)->get(route('home'))
            ->assertOk()->assertSee('Sin empezar')->assertSee('Para empezar');

        Postulacion::create(['user_id' => $this->user->id, 'temporada_id' => $this->abierta->id, 'estado' => 'borrador']);

        $this->actingAs($this->user)->get(route('home'))
            ->assertOk()->assertSee('Borrador')->assertSee('En curso');
    }
}
