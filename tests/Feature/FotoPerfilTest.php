<?php

namespace Tests\Feature;

use App\Models\Guardavida;
use App\Models\Playa;
use App\Models\Postulacion;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** La foto personal (documento privado de la postulación) se muestra en el perfil del guardavida. */
class FotoPerfilTest extends TestCase
{
    use RefreshDatabase;

    private Guardavida $guardavida;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable
        Storage::fake('local');

        $playa = Playa::forceCreate(['nombre' => 'P', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $puesto = Puesto::forceCreate(['nombre' => 'X', 'latitud' => -38.0, 'longitud' => -58.0, 'playa_id' => $playa->id, 'qr_encriptado' => 'x']);
        $this->guardavida = Guardavida::forceCreate([
            'funcion' => 'Guardavida', 'playa_id' => $playa->id, 'puesto_id' => $puesto->id, 'turno' => 'M',
            'user_id' => $this->usuario('30111222', 'guardavida')->id,
        ]);
    }

    private function usuario(string $dni, string $rol): User
    {
        $user = User::create([
            'name' => "N{$dni}", 'lastname' => "A{$dni}", 'dni' => $dni, 'email' => "u{$dni}@test.com",
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $user->assignRole($rol);

        return $user;
    }

    private function conFoto(): void
    {
        $temporada = Temporada::create([
            'nombre' => '2026/27', 'fecha_inicio_postulacion' => '2026-08-01', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-11-01', 'fecha_fin' => '2027-03-31',
        ]);
        $postulacion = Postulacion::forceCreate(['user_id' => $this->guardavida->user_id, 'temporada_id' => $temporada->id, 'estado' => 'borrador']);
        Storage::disk('local')->put('postulaciones/foto.webp', 'contenido');
        $postulacion->documentos()->create(['tipo' => 'foto_personal', 'ruta' => 'postulaciones/foto.webp', 'nombre_original' => 'foto.webp', 'mime' => 'image/webp', 'tamano' => 9]);
    }

    public function test_sin_foto_el_perfil_muestra_el_icono(): void
    {
        $this->actingAs($this->guardavida->user)->get(route('guardavida.myProfile'))
            ->assertOk()->assertSee('fa-user', false)->assertDontSee('/foto', false);
    }

    public function test_con_foto_el_perfil_la_muestra_y_se_puede_descargar(): void
    {
        $this->conFoto();

        $this->actingAs($this->guardavida->user)->get(route('guardavida.myProfile'))
            ->assertOk()->assertSee(route('guardavida.foto', $this->guardavida), false);
        $this->get(route('guardavida.foto', $this->guardavida))->assertOk();
    }

    public function test_solo_el_dueno_el_encargado_o_el_admin_ven_la_foto(): void
    {
        $this->conFoto();

        $otro = $this->usuario('30999888', 'guardavida');
        $this->actingAs($otro)->get(route('guardavida.foto', $this->guardavida))->assertForbidden();

        $admin = $this->usuario('30777666', 'admin');
        $this->actingAs($admin)->get(route('guardavida.foto', $this->guardavida))->assertOk();
    }

    public function test_el_listado_muestra_la_foto_de_cada_uno_o_el_icono(): void
    {
        $admin = $this->usuario('30777666', 'admin');

        $this->actingAs($admin)->get(route('guardavida.index'))
            ->assertOk()->assertDontSee(route('guardavida.foto', $this->guardavida), false);

        $this->conFoto();

        $this->actingAs($admin)->get(route('guardavida.index'))
            ->assertOk()->assertSee(route('guardavida.foto', $this->guardavida), false);
    }

    public function test_las_fotos_del_listado_se_buscan_en_una_sola_consulta(): void
    {
        $this->conFoto();
        $otro = Guardavida::forceCreate(['funcion' => 'Guardavida', 'playa_id' => $this->guardavida->playa_id, 'user_id' => $this->usuario('30999111', 'guardavida')->id]);

        \DB::enableQueryLog();
        $fotos = Guardavida::fotosPersonalesDe([$this->guardavida->user_id, $otro->user_id]);

        $this->assertCount(1, \DB::getQueryLog());
        $this->assertSame([$this->guardavida->user_id], array_keys($fotos), 'quien no tiene foto no figura');
    }

    public function test_sin_foto_la_ruta_responde_404(): void
    {
        $this->actingAs($this->guardavida->user)->get(route('guardavida.foto', $this->guardavida))->assertNotFound();
    }
}
