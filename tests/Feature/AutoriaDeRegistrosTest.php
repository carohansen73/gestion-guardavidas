<?php

namespace Tests\Feature;

use App\Models\Bandera;
use App\Models\BanderaTipo;
use App\Models\CambioDeTurno;
use App\Models\Guardavida;
use App\Models\Intervencion;
use App\Models\Licencia;
use App\Models\Material;
use App\Models\NovedadMaterial;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\Temporada;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Editar o borrar un registro operativo es cosa de quien lo cargó; para tocar los de otros hace falta el permiso
 * gestionar_ajenos_<recurso> (por defecto solo admin). Se suma a la regla de temporada cerrada.
 */
class AutoriaDeRegistrosTest extends TestCase
{
    use RefreshDatabase;

    private Playa $playa;

    private Guardavida $ficha;

    private BanderaTipo $tipo;

    private Puesto $puesto;

    private Material $material;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable

        Temporada::create([
            'nombre' => 'Actual', 'fecha_inicio_postulacion' => '2026-08-01', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-10-01', 'fecha_fin' => '2027-03-31',
        ]);
        $this->playa = Playa::forceCreate(['nombre' => 'Playa Norte', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $this->puesto = $puesto = Puesto::forceCreate(['nombre' => 'Puesto N', 'latitud' => -38.0, 'longitud' => -58.0, 'playa_id' => $this->playa->id, 'qr_encriptado' => 'x']);
        $this->tipo = BanderaTipo::forceCreate(['codigo' => 'V', 'color' => 'green']);
        $this->material = Material::forceCreate(['nombre' => 'Torre', 'playa_id' => $this->playa->id]);
        $this->ficha = Guardavida::forceCreate([
            'funcion' => 'Guardavida', 'user_id' => $this->usuario('30000099', 'guardavida')->id,
            'playa_id' => $this->playa->id, 'puesto_id' => $puesto->id, 'turno' => 'M',
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

    /** Un registro de cada módulo cargado por $autor (null = sin autor, como los anteriores a la columna). */
    private function registros(?User $autor): array
    {
        $id = $autor?->id;
        // banderas e intervenciones tienen user_id obligatorio en la base: si no hay autor, las carga otro admin.
        $obligatorio = $id ?? $this->usuario('4'.random_int(1000000, 9999999), 'admin')->id;
        $hoy = now();

        return [
            'bandera' => Bandera::forceCreate(['fecha' => $hoy, 'turno' => 'M', 'bandera_id' => $this->tipo->id, 'playa_id' => $this->playa->id, 'user_id' => $obligatorio]),
            'intervencion' => Intervencion::forceCreate(['fecha' => $hoy, 'tipo_intervencion' => 'Rescate', 'victimas' => 1, 'codigo' => 'A', 'bandera_id' => $this->tipo->id, 'traslado' => false, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'user_id' => $obligatorio]),
            'novedad_material' => NovedadMaterial::forceCreate(['tipo_novedad' => 'Rotura', 'material_id' => $this->material->id, 'fecha' => $hoy, 'playa_id' => $this->playa->id, 'user_id' => $id]),
            'licencia' => Licencia::forceCreate(['fecha_inicio' => $hoy->toDateString(), 'fecha_fin' => $hoy->toDateString(), 'tipo_licencia' => 'Otro', 'en_tiempo' => true, 'turno' => 'M', 'guardavida_id' => $this->ficha->id, 'playa_id' => $this->playa->id, 'user_id' => $id]),
            'cambio_turno' => CambioDeTurno::forceCreate(['fecha' => $hoy->toDateString(), 'turno_nuevo' => 'T', 'guardavida_id' => $this->ficha->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'funcion' => 'Guardavida', 'user_id' => $id]),
        ];
    }

    public function test_el_autor_toca_lo_suyo_y_otro_encargado_no_toca_lo_ajeno(): void
    {
        $autor = $this->usuario('30000001', 'encargado');
        $otro = $this->usuario('30000002', 'encargado');

        foreach ($this->registros($autor) as $nombre => $registro) {
            $this->assertTrue($autor->can('update', $registro), "$nombre: el autor edita");
            $this->assertTrue($autor->can('delete', $registro), "$nombre: el autor borra");
            $this->assertFalse($otro->can('update', $registro), "$nombre: otro encargado no edita");
            $this->assertFalse($otro->can('delete', $registro), "$nombre: otro encargado no borra");
        }
    }

    public function test_el_admin_toca_lo_ajeno(): void
    {
        $admin = $this->usuario('30000003', 'admin');

        foreach ($this->registros($this->usuario('30000001', 'encargado')) as $nombre => $registro) {
            $this->assertTrue($admin->can('update', $registro), "$nombre: admin edita lo ajeno");
            $this->assertTrue($admin->can('delete', $registro), "$nombre: admin borra lo ajeno");
        }
    }

    public function test_un_registro_viejo_sin_autor_solo_lo_toca_quien_gestiona_ajenos(): void
    {
        $encargado = $this->usuario('30000002', 'encargado');
        $admin = $this->usuario('30000003', 'admin');
        $registros = $this->registros(null);

        foreach (['licencia', 'novedad_material', 'cambio_turno'] as $clave) {
            $this->assertNull($registros[$clave]->user_id);
            $this->assertFalse($encargado->can('update', $registros[$clave]), "$clave sin autor: el encargado no");
            $this->assertTrue($admin->can('update', $registros[$clave]), "$clave sin autor: el admin sí");
        }
    }

    public function test_el_permiso_de_ajenos_se_reparte_desde_los_roles_y_es_por_modulo(): void
    {
        $autor = $this->usuario('30000001', 'encargado');
        $otro = $this->usuario('30000002', 'encargado');
        $registros = $this->registros($autor);

        Role::findByName('encargado')->givePermissionTo('gestionar_ajenos_licencia');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->assertTrue($otro->fresh()->can('update', $registros['licencia']));
        $this->assertFalse($otro->fresh()->can('update', $registros['intervencion']), 'el permiso no se filtra a otros módulos');
    }

    public function test_el_autor_se_completa_solo_al_crear(): void
    {
        $encargado = $this->usuario('30000001', 'encargado');
        $this->actingAs($encargado);

        $cambio = CambioDeTurno::create(['fecha' => now()->toDateString(), 'turno_nuevo' => 'T', 'guardavida_id' => $this->ficha->id, 'playa_id' => $this->playa->id, 'puesto_id' => $this->puesto->id, 'funcion' => 'Guardavida']);
        $licencia = Licencia::create(['fecha_inicio' => now()->toDateString(), 'fecha_fin' => now()->toDateString(), 'tipo_licencia' => 'Otro', 'en_tiempo' => true, 'turno' => 'M', 'guardavida_id' => $this->ficha->id, 'playa_id' => $this->playa->id]);

        $this->assertSame($encargado->id, $cambio->fresh()->user_id);
        $this->assertSame($encargado->id, $licencia->fresh()->user_id);
        $this->assertTrue($licencia->user->is($encargado));
    }

    public function test_las_rutas_respetan_la_autoria(): void
    {
        $autor = $this->usuario('30000001', 'encargado');
        $otro = $this->usuario('30000002', 'encargado');
        $licencia = $this->registros($autor)['licencia'];

        $this->actingAs($otro)->delete(route('licencia.destroy', $licencia))->assertForbidden();
        $this->get(route('licencia.edit', $licencia))->assertForbidden();
        $this->assertNotNull(Licencia::find($licencia->id));

        $this->actingAs($autor)->get(route('licencia.edit', $licencia))->assertOk();
    }
}
