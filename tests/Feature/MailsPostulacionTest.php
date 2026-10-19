<?php

namespace Tests\Feature;

use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\Temporada;
use App\Models\User;
use App\Notifications\PostulacionRevisadaMailNotification;
use App\Notifications\PostulacionSeleccionadaMailNotification;
use App\Services\SeleccionPostulantes;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/** Mails al postulante: revisión del admin (aceptada/rechazada/incompleta) y selección. */
class MailsPostulacionTest extends TestCase
{
    use RefreshDatabase;

    private Temporada $temporada;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable

        $this->temporada = Temporada::create([
            'nombre' => '2026/27', 'fecha_inicio_postulacion' => now()->subDays(10), 'fecha_fin_postulacion' => now()->addDays(10),
            'fecha_inicio' => now()->addMonths(3), 'fecha_fin' => now()->addMonths(8),
        ]);
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

    private function pendienteDe(User $user): Postulacion
    {
        return Postulacion::create(['user_id' => $user->id, 'temporada_id' => $this->temporada->id, 'estado' => 'pendiente', 'enviada_at' => now()]);
    }

    public function test_el_postulante_recibe_un_mail_cuando_el_admin_acepta_rechaza_o_pide_correcciones(): void
    {
        Notification::fake();
        $admin = $this->usuario('admin', '20000001');
        $postulante = $this->usuario('postulante', '30000001');
        $postulacion = $this->pendienteDe($postulante);
        $url = route('postulaciones.revisar', $postulacion);

        // Pedir correcciones, con comentario.
        $this->actingAs($admin)->patch($url, ['estado' => 'incompleta', 'observaciones' => 'Falta el apto médico'])->assertRedirect();
        Notification::assertSentTo($postulante, PostulacionRevisadaMailNotification::class,
            fn ($n) => $n->estado === 'incompleta' && $n->observaciones === 'Falta el apto médico' && $n->temporada === '2026/27');

        // Guardar de nuevo el mismo estado no vuelve a mandar nada.
        $this->actingAs($admin)->patch($url, ['estado' => 'incompleta', 'observaciones' => 'Falta el apto médico y el DNI'])->assertRedirect();
        Notification::assertSentToTimes($postulante, PostulacionRevisadaMailNotification::class, 1);

        // Dejarla pendiente no manda nada.
        $this->actingAs($admin)->patch($url, ['estado' => 'pendiente'])->assertRedirect();
        Notification::assertSentToTimes($postulante, PostulacionRevisadaMailNotification::class, 1);

        // Rechazar sin comentario: sale igual, sin comentario.
        $this->actingAs($admin)->patch($url, ['estado' => 'rechazada'])->assertRedirect();
        Notification::assertSentTo($postulante, PostulacionRevisadaMailNotification::class,
            fn ($n) => $n->estado === 'rechazada' && $n->observaciones === null);

        // Aceptar.
        $this->actingAs($admin)->patch($url, ['estado' => 'aceptada'])->assertRedirect();
        Notification::assertSentTo($postulante, PostulacionRevisadaMailNotification::class, fn ($n) => $n->estado === 'aceptada');
        Notification::assertSentToTimes($postulante, PostulacionRevisadaMailNotification::class, 3);
    }

    public function test_el_mail_de_revision_incluye_el_comentario_del_admin_solo_si_lo_escribio(): void
    {
        $postulante = $this->usuario('postulante', '30000001');

        $conComentario = (new PostulacionRevisadaMailNotification('2026/27', 'incompleta', 'Falta el apto médico', '19/01/2027'))->toMail($postulante);
        $html = (string) $conComentario->render();
        $this->assertSame('Necesitamos que corrijas tu inscripción a 2026/27', $conComentario->subject);
        $this->assertStringContainsString('Falta el apto médico', $html);
        $this->assertStringContainsString('Comentario del equipo', $html);
        $this->assertStringContainsString('19/01/2027', $html);
        $this->assertStringContainsString(route('postulacion.paso', 1), $html);

        $sinComentario = (new PostulacionRevisadaMailNotification('2026/27', 'rechazada'))->toMail($postulante);
        $this->assertSame('Tu inscripción a 2026/27 no fue aceptada', $sinComentario->subject);
        $this->assertStringNotContainsString('Comentario del equipo', (string) $sinComentario->render());

        $aceptada = (new PostulacionRevisadaMailNotification('2026/27', 'aceptada'))->toMail($postulante);
        $this->assertSame('Tu inscripción a 2026/27 fue aceptada', $aceptada->subject);
    }

    public function test_los_mails_de_postulacion_van_en_cola(): void
    {
        $this->assertContains(\Illuminate\Contracts\Queue\ShouldQueue::class, class_implements(PostulacionRevisadaMailNotification::class));
        $this->assertContains(\Illuminate\Contracts\Queue\ShouldQueue::class, class_implements(PostulacionSeleccionadaMailNotification::class));
    }

    public function test_confirmar_la_seleccion_manda_un_mail_con_playa_turno_y_funcion_y_no_al_deseleccionar(): void
    {
        Notification::fake();
        $playa = Playa::forceCreate(['nombre' => 'Playa Norte', 'color' => '#00aaff', 'lat' => -38.0, 'lon' => -58.0]);
        $postulante = $this->usuario('postulante', '30000001');
        $otro = $this->usuario('postulante', '30000002');
        $p1 = Postulacion::create(['user_id' => $postulante->id, 'temporada_id' => $this->temporada->id, 'estado' => 'aceptada', 'enviada_at' => now()]);
        $p2 = Postulacion::create(['user_id' => $otro->id, 'temporada_id' => $this->temporada->id, 'estado' => 'aceptada', 'enviada_at' => now()]);
        $servicio = app(SeleccionPostulantes::class);

        $servicio->confirmar([
            ['postulacion' => $p1->load('user'), 'playa_id' => $playa->id, 'puesto_id' => null, 'turno' => 'M', 'encargado' => false],
            ['postulacion' => $p2->load('user'), 'playa_id' => $playa->id, 'puesto_id' => null, 'turno' => 'T', 'encargado' => true],
        ], \Carbon\Carbon::parse('2026-12-01'));

        Notification::assertSentTo($postulante, PostulacionSeleccionadaMailNotification::class,
            fn ($n) => $n->playa === 'Playa Norte' && $n->turno === 'Mañana' && $n->funcion === 'Guardavida' && $n->desde === '01/12/2026' && $n->temporada === '2026/27');
        Notification::assertSentTo($otro, PostulacionSeleccionadaMailNotification::class,
            fn ($n) => $n->turno === 'Tarde' && $n->funcion === 'Encargado');

        // Corregir una selección no manda ningún mail.
        $servicio->deseleccionar($p1->fresh());
        Notification::assertSentToTimes($postulante, PostulacionSeleccionadaMailNotification::class, 1);
    }

    public function test_el_mail_de_seleccion_muestra_los_datos_asignados(): void
    {
        $postulante = $this->usuario('postulante', '30000001');

        $mail = (new PostulacionSeleccionadaMailNotification('2026/27', 'Playa Norte', 'Puesto 3', 'Mañana', 'Guardavida', '01/12/2026'))->toMail($postulante);
        $html = (string) $mail->render();

        $this->assertSame('Fuiste seleccionado/a para la temporada 2026/27', $mail->subject);
        foreach (['Playa Norte', 'Puesto 3', 'Mañana', 'Guardavida', '01/12/2026', route('home')] as $dato) {
            $this->assertStringContainsString($dato, $html);
        }

        // Sin puesto ni turno (se eligen al ingresar), esas líneas no aparecen.
        $sinPuesto = (string) (new PostulacionSeleccionadaMailNotification('2026/27', 'Playa Norte'))->toMail($postulante)->render();
        $this->assertStringNotContainsString('Puesto:', $sinPuesto);
        $this->assertStringNotContainsString('Turno:', $sinPuesto);
    }
}
