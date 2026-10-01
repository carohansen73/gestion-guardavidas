<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Red de seguridad para refactors de vistas/estilos: entra como superadmin a
 * todas las pantallas GET que no llevan parámetros en la URL y comprueba que
 * ninguna explote (excepción o 5xx). No valida el contenido: solo que la
 * vista se pueda renderizar con la base vacía.
 *
 * Si se define la variable de entorno SMOKE_OUT, además vuelca el código de
 * respuesta de cada URL a ese archivo JSON (sirve para comparar antes/después).
 */
class PantallasCarganTest extends TestCase
{
    use RefreshDatabase;

    public function test_las_pantallas_sin_parametros_cargan_sin_error(): void
    {
        $this->seed(RolesYPermisosSeeder::class);
        $admin = User::create([
            'name' => 'Admin', 'lastname' => 'Prueba', 'dni' => '1', 'email' => 'admin@test.com',
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $admin->assignRole(['admin', 'superadmin']);

        $resultados = [];
        $fallos = [];
        foreach (Route::getRoutes() as $ruta) {
            $uri = $ruta->uri();
            if (! in_array('GET', $ruta->methods(), true) || str_contains($uri, '{')
                || preg_match('#^(api/|_|sanctum|storage|up$|logout|ping|force-password)#', $uri)) {
                continue;
            }

            try {
                $codigo = $this->actingAs($admin)->get('/'.ltrim($uri, '/'))->getStatusCode();
            } catch (\Throwable $e) {
                $codigo = 'EXC '.class_basename($e).': '.substr($e->getMessage(), 0, 120);
            }

            $resultados[$uri] = $codigo;
            if (! is_int($codigo) || $codigo >= 500) {
                $fallos[] = "/{$uri} => {$codigo}";
            }
        }

        if ($salida = getenv('SMOKE_OUT')) {
            ksort($resultados);
            file_put_contents($salida, json_encode($resultados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        $this->assertNotEmpty($resultados);
        $this->assertSame([], $fallos, "Pantallas que fallan:\n".implode("\n", $fallos));
    }
}
