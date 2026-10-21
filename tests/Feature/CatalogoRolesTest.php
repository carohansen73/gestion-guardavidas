<?php

namespace Tests\Feature;

use App\Models\Playa;
use App\Models\Puesto;
use App\Models\Rol;
use App\Models\Temporada;
use App\Models\User;
use App\Services\CatalogoRoles;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Qué significa cada rol (plantel, temporada, a quién toca la selección, quién puede asignarlo) sale de la tabla
 * `roles`, no de listas escritas en el código: un rol nuevo con sus columnas ya se comporta como corresponde.
 */
class CatalogoRolesTest extends TestCase
{
    use RefreshDatabase;

    private CatalogoRoles $catalogo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesYPermisosSeeder::class); // SQLite en memoria: descartable
        $this->catalogo = app(CatalogoRoles::class);
    }

    private function usuario(string $dni, string ...$roles): User
    {
        $user = User::create([
            'name' => "N{$dni}", 'lastname' => "A{$dni}", 'dni' => $dni, 'email' => "u{$dni}@test.com",
            'password' => 'password', 'enabled' => true, 'must_change_psw' => false,
        ]);
        $user->assignRole($roles);

        return $user;
    }

    public function test_las_listas_de_roles_salen_de_las_columnas(): void
    {
        $this->assertEqualsCanonicalizing(['guardavida', 'encargado', 'personal'], $this->catalogo->plantel());
        $this->assertEqualsCanonicalizing(['guardavida', 'encargado'], $this->catalogo->plantelConTemporada());
        $this->assertSame(['personal'], $this->catalogo->plantelSinTemporada());
        $this->assertEqualsCanonicalizing(['superadmin', 'admin', 'rrhh'], $this->catalogo->administrativos());
        $this->assertEqualsCanonicalizing(['superadmin', 'admin', 'rrhh', 'personal'], $this->catalogo->intocables());
    }

    public function test_cada_uno_puede_asignar_solo_roles_de_rango_igual_o_menor_al_suyo(): void
    {
        $nombres = fn (User $u) => $this->catalogo->asignablesPara($u)->pluck('name')->all();

        $this->assertEqualsCanonicalizing(['admin', 'rrhh', 'encargado', 'guardavida', 'personal'], $nombres($this->usuario('1', 'admin')));
        // Un superadmin tiene también admin: no puede asignar superadmin ni postulante (los maneja el sistema).
        $this->assertEqualsCanonicalizing(['admin', 'rrhh', 'encargado', 'guardavida', 'personal'], $nombres($this->usuario('2', 'admin', 'superadmin')));
        // Un encargado solo entrega guardavida y encargado: ni admin, ni rrhh, ni personal.
        $this->assertEqualsCanonicalizing(['encargado', 'guardavida'], $nombres($this->usuario('3', 'encargado')));
    }

    public function test_quien_puede_gestionar_a_quien(): void
    {
        $admin = $this->usuario('1', 'admin');
        $encargado = $this->usuario('2', 'encargado');
        $rrhh = $this->usuario('3', 'rrhh');
        $super = $this->usuario('4', 'admin', 'superadmin');

        $this->assertTrue($admin->puedeGestionarA($encargado));
        $this->assertTrue($admin->puedeGestionarA($this->usuario('5', 'admin')));
        $this->assertFalse($encargado->puedeGestionarA($admin));
        $this->assertFalse($encargado->puedeGestionarA($rrhh));
        $this->assertFalse($admin->puedeGestionarA($super), 'a un superadmin no se lo toca desde la app');
        $this->assertFalse($super->puedeGestionarA($super));
    }

    public function test_el_encargado_no_da_de_alta_a_nadie(): void
    {
        $encargado = $this->usuario('2', 'encargado');

        $this->actingAs($encargado)->get(route('guardavida.create'))->assertForbidden();
        foreach (['guardavida', 'personal', 'rrhh', 'admin'] as $rol) {
            $this->post(route('guardavida.store'), [
                'nombre' => 'Ana', 'apellido' => 'Gómez', 'dni' => '28111222', 'email' => "ana-$rol@test.com", 'rol' => $rol,
            ])->assertForbidden();
        }
        $this->assertSame(0, User::where('dni', '28111222')->count());
    }

    public function test_un_rol_de_plantel_creado_en_la_tabla_funciona_sin_tocar_codigo(): void
    {
        Temporada::create([
            'nombre' => '2026/27', 'fecha_inicio_postulacion' => '2026-08-01', 'fecha_fin_postulacion' => '2026-08-31',
            'fecha_inicio' => '2026-10-01', 'fecha_fin' => '2027-03-31',
        ]);
        $playa = Playa::forceCreate(['nombre' => 'Costa Azul', 'color' => '#111111', 'lat' => -38.0, 'lon' => -58.0]);
        $puesto = Puesto::forceCreate(['nombre' => 'Puesto 1', 'latitud' => -38.0, 'longitud' => -58.0, 'playa_id' => $playa->id, 'qr_encriptado' => 'x']);

        // "Brigadista": plantel fijo, sin temporada, que el sistema nunca toca.
        Rol::create([
            'name' => 'brigadista', 'etiqueta' => 'Brigadista', 'descripcion' => 'fijo', 'color' => 'rose', 'rango' => 20,
            'es_plantel' => true, 'usa_temporada' => false, 'asignable' => true, 'intocable' => true,
        ]);
        $this->catalogo->olvidar();
        $admin = $this->usuario('1', 'admin');

        $this->actingAs($admin)->get(route('guardavida.create'))->assertOk()->assertSee('Brigadista (fijo)');

        $this->post(route('guardavida.store'), [
            'nombre' => 'Bruno', 'apellido' => 'Brigada', 'dni' => '28111222', 'email' => 'bruno@test.com', 'rol' => 'brigadista',
            'telefono' => '2983000000', 'playa_id' => $playa->id, 'puesto_id' => $puesto->id,
            'funcion' => 'Guardavida', 'turno' => 'M', 'fecha_alta' => '2026-12-01',
        ])->assertRedirect(route('guardavida.index'))->assertSessionHasNoErrors();

        $bruno = User::where('dni', '28111222')->firstOrFail();
        $this->assertNotNull($bruno->guardavida, 'por ser rol de plantel, tiene ficha');
        $this->assertNull($bruno->guardavida->periodos()->sole()->temporada_id, 'sin temporada');
        $this->assertTrue($this->catalogo->userEstaEnPlantel($bruno), 'cuenta como plantel mientras esté habilitado');

        $bruno->update(['enabled' => false]);
        $this->assertFalse($this->catalogo->userEstaEnPlantel($bruno->fresh()), 'deshabilitado, ya no');
        $this->assertContains('brigadista', $this->catalogo->intocables());
    }

    public function test_la_etiqueta_y_el_color_del_badge_salen_de_la_tabla(): void
    {
        Rol::create(['name' => 'auditoria', 'etiqueta' => 'Auditoría', 'color' => 'rose', 'icono' => 'star']);
        $this->catalogo->olvidar();

        $this->blade('<x-role-badge rol="auditoria" />')->assertSee('Auditoría')->assertSee('text-rose-600', false);
        $this->blade('<x-role-badge rol="rrhh" />')->assertSee('RRHH')->assertSee('text-teal-600', false);
        // Un rol sin fila ni etiqueta no rompe la pantalla.
        $this->blade('<x-role-badge rol="inexistente" />')->assertSee('Inexistente');
    }

    public function test_el_rol_principal_es_el_de_mayor_rango(): void
    {
        $this->assertSame('admin', $this->usuario('1', 'guardavida', 'admin')->rolPrincipal()->name);
        $this->assertSame('superadmin', $this->usuario('2', 'admin', 'superadmin')->rolPrincipal()->name);
        $this->assertNull($this->usuario('3')->rolPrincipal());
    }

    public function test_cambiar_el_rol_respeta_la_jerarquia(): void
    {
        $encargado = $this->usuario('2', 'encargado');
        $admin = $this->usuario('1', 'admin');
        $guardavida = $this->usuario('3', 'guardavida');

        // Un encargado puede ascender a encargado a un guardavida, pero no a admin ni a personal, ni tocar a un admin.
        $this->actingAs($encargado)->put(route('rol.update', $guardavida), ['role' => 'encargado'])->assertSessionHasNoErrors();
        $this->assertTrue($guardavida->fresh()->hasRole('encargado'));
        $this->put(route('rol.update', $guardavida), ['role' => 'admin'])->assertSessionHasErrors('role');
        $this->put(route('rol.update', $guardavida), ['role' => 'personal'])->assertSessionHasErrors('role');
        $this->put(route('rol.update', $admin), ['role' => 'guardavida'])->assertForbidden();
        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }

    public function test_el_superadmin_crea_un_rol_desde_la_pantalla_de_permisos(): void
    {
        $super = $this->usuario('4', 'admin', 'superadmin');

        $this->actingAs($super)->get(route('permisos.index'))->assertOk()->assertSee('Crear un rol nuevo');

        $this->post(route('permisos.roles.store'), [
            'name' => 'auditoria', 'etiqueta' => 'Auditoría', 'descripcion' => 'solo consulta', 'color' => 'rose',
            'icono' => 'star', 'rango' => 50,
        ])->assertRedirect(route('permisos.index'))->assertSessionHasNoErrors();

        $rol = Rol::where('name', 'auditoria')->firstOrFail();
        $this->assertTrue($rol->asignable);
        $this->assertTrue($rol->intocable, 'un rol nuevo no lo toca la selección');
        $this->assertFalse($rol->es_plantel);
        // Aparece en la matriz de permisos y en los selectores de quien puede asignarlo (rango 50: admin sí, encargado no).
        $this->get(route('permisos.index'))->assertSee('Auditoría');
        $this->assertContains('auditoria', $this->catalogo->asignablesPara($this->usuario('1', 'admin'))->pluck('name')->all());
        $this->assertNotContains('auditoria', $this->catalogo->asignablesPara($this->usuario('2', 'encargado'))->pluck('name')->all());
    }

    public function test_crear_un_rol_valida_el_nombre_y_es_solo_para_superadmin(): void
    {
        $datos = ['name' => 'auditoria', 'etiqueta' => 'Auditoría', 'color' => 'rose', 'icono' => 'star', 'rango' => 50];

        $this->actingAs($this->usuario('1', 'admin'))->post(route('permisos.roles.store'), $datos)->assertForbidden();

        $this->actingAs($this->usuario('4', 'admin', 'superadmin'));
        $this->post(route('permisos.roles.store'), ['name' => 'Con Espacios'] + $datos)->assertSessionHasErrors('name');
        $this->post(route('permisos.roles.store'), ['name' => 'admin'] + $datos)->assertSessionHasErrors('name');
        $this->post(route('permisos.roles.store'), ['rango' => 95] + $datos)->assertSessionHasErrors('rango');
        $this->post(route('permisos.roles.store'), ['color' => 'verde-fosforito'] + $datos)->assertSessionHasErrors('color');
        $this->assertNull(Rol::where('name', 'auditoria')->first());
    }
}
