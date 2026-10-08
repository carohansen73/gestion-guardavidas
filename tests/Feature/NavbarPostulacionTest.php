<?php

namespace Tests\Feature;

use App\Models\Temporada;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarPostulacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable

        Temporada::create([
            'nombre' => '2026/27', 'fecha_inicio_postulacion' => now()->subDays(5), 'fecha_fin_postulacion' => now()->addDays(20),
            'fecha_inicio' => now()->addMonths(2), 'fecha_fin' => now()->addMonths(6),
        ]);
    }

    private function usuario(string $rol): User
    {
        $user = User::create([
            'name' => 'Ana', 'lastname' => 'Pérez', 'dni' => '30111222', 'email' => 'ana@test.com',
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $user->assignRole($rol);

        return $user;
    }

    public function test_el_postulante_ve_la_barra_del_sistema_con_sus_datos_y_cerrar_sesion_pero_sin_enlaces_del_sistema(): void
    {
        $this->actingAs($this->usuario('postulante'))->get(route('postulacion.paso', 1))
            ->assertOk()
            ->assertSee('ana@test.com')
            ->assertSee('Mis datos')
            ->assertSee(__('Log Out'))
            ->assertSee('Postulante')
            ->assertDontSee(route('guardavida.myProfile'), false)
            ->assertDontSee(route('guardavida.misAsistencias'), false);
    }

    public function test_un_guardavida_conserva_su_menu_completo_y_puede_volver_al_inicio(): void
    {
        $this->actingAs($this->usuario('guardavida'))->get(route('postulacion.paso', 1))
            ->assertOk()
            ->assertSee(route('home'), false)
            ->assertSee(route('guardavida.myProfile'), false)
            ->assertSee(__('Log Out'))
            ->assertDontSee('Mis datos');
    }

    public function test_el_menu_agrupa_los_enlaces_y_solo_muestra_los_grupos_permitidos(): void
    {
        $admin = $this->usuario('admin');
        $this->actingAs($admin);
        $html = view('layouts.sidebar')->render();
        foreach (['Operación', 'Personal', 'Temporada'] as $grupo) {
            $this->assertStringContainsString($grupo, $html);
        }
        // 'Administración' (permisos) es solo de superadmin
        $this->assertStringNotContainsString('Administración', $html);

        $super = User::create(['name' => 'Sup', 'lastname' => 'Er', 'dni' => '99', 'email' => 'sup@test.com', 'password' => 'password', 'enabled' => true, 'must_change_psw' => false]);
        $super->assignRole(['admin', 'superadmin']);
        $this->actingAs($super);
        $this->assertStringContainsString('Administración', view('layouts.sidebar')->render());
    }
}
