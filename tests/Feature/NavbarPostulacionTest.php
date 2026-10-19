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

    public function test_la_postulacion_usa_el_mismo_layout_con_sidebar_para_todos_y_los_permisos_deciden_que_se_ve(): void
    {
        // Postulante: sidebar solo con Home (sin los grupos del sistema) y sin la barra inferior del celular.
        $this->actingAs($this->usuario('postulante'))->get(route('postulacion.paso', 1))
            ->assertOk()
            ->assertSee('id="separator-sidebar"', false)
            ->assertDontSee('Mi inscripción')
            ->assertSee('<span class="ms-3">Home</span>', false)
            ->assertDontSee('Operación')
            ->assertDontSee('buttom-navigation', false);
    }

    public function test_el_inicio_del_postulante_muestra_la_card_si_hay_inscripcion_abierta_y_las_fechas_si_no(): void
    {
        $postulante = $this->usuario('postulante');

        // Inscripción abierta: la card de postulación (la misma de guardavidas) y el acceso a "Mi inscripción".
        $this->actingAs($postulante)->get(route('home'))
            ->assertOk()->assertSee('INSCRIPCIÓN ABIERTA')->assertSee('2026/27')->assertSee('Postularme')->assertSee('TU PROGRESO')
            ->assertDontSee('Operación');

        // Sin inscripción abierta: avisa cuándo abre la próxima.
        Temporada::query()->delete();
        Temporada::create([
            'nombre' => '2027/28', 'fecha_inicio_postulacion' => now()->addDays(40), 'fecha_fin_postulacion' => now()->addDays(70),
            'fecha_inicio' => now()->addMonths(5), 'fecha_fin' => now()->addMonths(9),
        ]);
        $this->actingAs($postulante)->get(route('home'))
            ->assertOk()->assertSee('Próxima inscripción')->assertSee('2027/28')->assertSee(now()->addDays(40)->format('d/m/Y'))
            ->assertDontSee('INSCRIPCIÓN ABIERTA');

        // Las demás pantallas del sistema lo mandan a su inicio.
        $this->actingAs($postulante)->get(route('intervencion.index'))->assertRedirect(route('home'));
    }

    public function test_la_card_del_inicio_muestra_el_resultado_y_el_comentario_aunque_la_inscripcion_haya_cerrado(): void
    {
        $postulante = $this->usuario('postulante');
        $temporada = Temporada::first();
        $post = \App\Models\Postulacion::create(['user_id' => $postulante->id, 'temporada_id' => $temporada->id, 'estado' => 'rechazada', 'observaciones' => 'Falta el apto médico']);

        // Inscripción abierta: ya no es la card naranja de "abierta", muestra el resultado y el comentario.
        $this->actingAs($postulante)->get(route('home'))
            ->assertOk()->assertSee('INSCRIPCIÓN NO ACEPTADA')->assertSee('Falta el apto médico')->assertDontSee('INSCRIPCIÓN ABIERTA');

        // Cerrada la inscripción (la temporada todavía no terminó): sigue visible.
        $temporada->update(['fecha_inicio_postulacion' => now()->subDays(30), 'fecha_fin_postulacion' => now()->subDays(2)]);
        $this->actingAs($postulante)->get(route('home'))
            ->assertOk()->assertSee('INSCRIPCIÓN NO ACEPTADA')->assertSee('Inscripción cerrada')->assertSee('Falta el apto médico');

        // Ya revisada no se puede corregir: la card es informativa, sin botón.
        $this->actingAs($postulante)->get(route('home'))->assertDontSee('Revisar / corregir mi inscripción');

        // Terminada la temporada deja de mostrarse (no hay historial).
        $temporada->update(['fecha_inicio' => now()->subMonths(8), 'fecha_fin' => now()->subDays(1)]);
        $this->actingAs($postulante)->get(route('home'))
            ->assertOk()->assertDontSee('INSCRIPCIÓN NO ACEPTADA')->assertDontSee('inscripciones anteriores');

        $post->update(['estado' => 'aceptada']);
    }

    public function test_con_la_inscripcion_cerrada_se_avisa_a_quien_no_se_inscribio_o_no_llego_a_enviar(): void
    {
        $temporada = Temporada::first();
        $temporada->update(['fecha_inicio_postulacion' => now()->subDays(30), 'fecha_fin_postulacion' => now()->subDays(2)]);
        Temporada::create([
            'nombre' => '2027/28', 'fecha_inicio_postulacion' => now()->addDays(200), 'fecha_fin_postulacion' => now()->addDays(230),
            'fecha_inicio' => now()->addMonths(10), 'fecha_fin' => now()->addMonths(14),
        ]);
        $postulante = $this->usuario('postulante');
        $cerro = now()->subDays(2)->format('d/m/Y');

        // Ni empezó: sabe que cerró, y también cuándo abre la próxima.
        $this->actingAs($postulante)->get(route('home'))
            ->assertOk()->assertSee('INSCRIPCIÓN CERRADA')->assertSee("cerró el {$cerro}")->assertSee('Próxima inscripción')
            ->assertDontSee('Postularme')->assertDontSee('INSCRIPCIÓN ABIERTA');

        // Dejó un borrador sin enviar: lo mismo, con el motivo y sin forma de continuar.
        \App\Models\Postulacion::create(['user_id' => $postulante->id, 'temporada_id' => $temporada->id, 'estado' => 'borrador']);
        $this->actingAs($postulante)->get(route('home'))
            ->assertOk()->assertSee('INSCRIPCIÓN CERRADA')->assertSee('no llegaste a enviarla')
            ->assertDontSee('Continuar postulación')->assertDontSee('TU PROGRESO');

        // Empezó la temporada: ya no hace falta el aviso.
        $temporada->update(['fecha_inicio' => now()->subDay()]);
        $this->actingAs($postulante)->get(route('home'))->assertOk()->assertDontSee('INSCRIPCIÓN CERRADA');
    }

    public function test_quien_fue_seleccionado_ve_la_card_hasta_que_empieza_la_temporada(): void
    {
        $postulante = $this->usuario('postulante');
        $temporada = Temporada::first();
        $playa = \App\Models\Playa::forceCreate(['nombre' => 'Playa Norte', 'lat' => -38, 'lon' => -57, 'color' => '#00aaff']);
        \App\Models\Postulacion::create([
            'user_id' => $postulante->id, 'temporada_id' => $temporada->id, 'estado' => 'aceptada',
            'seleccionado' => true, 'playa_asignada_id' => $playa->id,
        ]);

        // Inscripción abierta y también cerrada, con la temporada por empezar: ve que fue seleccionado/a y dónde.
        $this->actingAs($postulante)->get(route('home'))
            ->assertOk()->assertSee('INSCRIPCIÓN ACEPTADA')->assertSee('seleccionado/a')->assertSee('Playa Norte');
        $temporada->update(['fecha_inicio_postulacion' => now()->subDays(30), 'fecha_fin_postulacion' => now()->subDays(2)]);
        $this->actingAs($postulante)->get(route('home'))
            ->assertOk()->assertSee('INSCRIPCIÓN ACEPTADA')->assertSee('Playa Norte');

        // Empezó la temporada: la card deja de mostrarse.
        $temporada->update(['fecha_inicio' => now()->subDay()]);
        $this->actingAs($postulante)->get(route('home'))
            ->assertOk()->assertDontSee('INSCRIPCIÓN ACEPTADA')->assertDontSee('Playa Norte');
    }

    public function test_el_guardavida_ve_en_la_postulacion_el_mismo_sidebar_con_home(): void
    {
        $this->actingAs($this->usuario('guardavida'))->get(route('postulacion.paso', 1))
            ->assertOk()
            ->assertSee('id="separator-sidebar"', false)
            ->assertSee('<span class="ms-3">Home</span>', false);
    }
}
