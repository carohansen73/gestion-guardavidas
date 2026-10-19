<?php

namespace Tests\Feature;

use App\Models\Temporada;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** El login ofrece el link a registrarse solo mientras hay una inscripción abierta. */
class LoginInscripcionAbiertaTest extends TestCase
{
    use RefreshDatabase;

    private function temporada(int $inicioPostulacion, int $finPostulacion): Temporada
    {
        return Temporada::create([
            'nombre' => '2026/27',
            'fecha_inicio_postulacion' => now()->addDays($inicioPostulacion),
            'fecha_fin_postulacion' => now()->addDays($finPostulacion),
            'fecha_inicio' => now()->addMonths(3),
            'fecha_fin' => now()->addMonths(8),
        ]);
    }

    public function test_con_la_inscripcion_abierta_el_login_muestra_el_link_para_registrarse(): void
    {
        $this->temporada(-5, 20);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('temporada 2026/27')
            ->assertSee(now()->addDays(20)->format('d/m/Y'))
            ->assertSee('Registrate para postularte')
            ->assertSee(route('register'), false);
    }

    public function test_sin_inscripcion_abierta_el_login_no_muestra_el_link(): void
    {
        $this->get(route('login'))->assertOk()->assertDontSee('Registrate para postularte');

        // Todavía no abrió.
        $this->temporada(10, 40);
        $this->get(route('login'))->assertOk()->assertDontSee('Registrate para postularte')->assertDontSee(route('register'), false);

        // Ya cerró.
        Temporada::query()->delete();
        $this->temporada(-40, -5);
        $this->get(route('login'))->assertOk()->assertDontSee('Registrate para postularte');
    }
}
