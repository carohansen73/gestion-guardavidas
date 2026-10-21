<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Los roles dejan de estar descriptos en el código (enum, listas de nombres): cada rol lleva en su fila cómo se
 * muestra y qué significa para el sistema. Un rol nuevo funciona solo con crear la fila y tildar permisos.
 *
 *  - etiqueta / descripcion / color / icono: cómo se ve en listados y selectores (color e icono, de un set fijo).
 *  - es_plantel: tiene ficha de guardavidas y cuenta para el presentismo.
 *  - usa_temporada: pasa por postulación/selección cada temporada. Un rol de plantel que NO la usa (personal fijo)
 *    se da de baja deshabilitando el usuario, sin volverlo postulante.
 *  - asignable: se puede elegir en los formularios de alta/edición (superadmin y postulante los maneja el sistema).
 *  - intocable: la selección y el cierre de temporada nunca le cambian el rol. Por defecto sí, para que un rol
 *    nuevo no sea tocado por esos procesos hasta que se decida lo contrario.
 *  - rango: quién puede asignarlo y gestionar a quien lo tiene (solo con rango igual o mayor); también ordena
 *    cuál se muestra como "rol principal" si alguien tiene más de uno.
 */
return new class extends Migration
{
    /** Valores de los roles que ya existían. */
    private const ROLES = [
        'superadmin' => ['etiqueta' => 'Superadmin', 'descripcion' => null, 'color' => 'fuchsia', 'icono' => 'sparkles', 'es_plantel' => 0, 'usa_temporada' => 1, 'asignable' => 0, 'intocable' => 1, 'rango' => 100],
        'admin' => ['etiqueta' => 'Administrador', 'descripcion' => null, 'color' => 'sky', 'icono' => 'shield-check', 'es_plantel' => 0, 'usa_temporada' => 1, 'asignable' => 1, 'intocable' => 1, 'rango' => 80],
        'rrhh' => ['etiqueta' => 'RRHH', 'descripcion' => 'solo consulta', 'color' => 'teal', 'icono' => 'user', 'es_plantel' => 0, 'usa_temporada' => 1, 'asignable' => 1, 'intocable' => 1, 'rango' => 60],
        'encargado' => ['etiqueta' => 'Encargado', 'descripcion' => null, 'color' => 'amber', 'icono' => 'star', 'es_plantel' => 1, 'usa_temporada' => 1, 'asignable' => 1, 'intocable' => 0, 'rango' => 40],
        'guardavida' => ['etiqueta' => 'Guardavida', 'descripcion' => null, 'color' => 'emerald', 'icono' => 'lifebuoy', 'es_plantel' => 1, 'usa_temporada' => 1, 'asignable' => 1, 'intocable' => 0, 'rango' => 20],
        'personal' => ['etiqueta' => 'Personal', 'descripcion' => 'solo ficha, sin temporada', 'color' => 'violet', 'icono' => 'user', 'es_plantel' => 1, 'usa_temporada' => 0, 'asignable' => 1, 'intocable' => 1, 'rango' => 50],
        'postulante' => ['etiqueta' => 'Postulante', 'descripcion' => null, 'color' => 'slate', 'icono' => 'user', 'es_plantel' => 0, 'usa_temporada' => 1, 'asignable' => 0, 'intocable' => 0, 'rango' => 10],
    ];

    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('etiqueta', 60)->nullable()->after('guard_name');
            $table->string('descripcion', 120)->nullable()->after('etiqueta');
            $table->string('color', 20)->default('slate')->after('descripcion');
            $table->string('icono', 30)->default('user')->after('color');
            $table->boolean('es_plantel')->default(false)->after('icono');
            $table->boolean('usa_temporada')->default(true)->after('es_plantel');
            $table->boolean('asignable')->default(true)->after('usa_temporada');
            $table->boolean('intocable')->default(true)->after('asignable');
            $table->unsignedSmallInteger('rango')->default(60)->after('intocable');
        });

        foreach (self::ROLES as $nombre => $valores) {
            DB::table('roles')->where('name', $nombre)->where('guard_name', 'web')->update($valores);
        }
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['etiqueta', 'descripcion', 'color', 'icono', 'es_plantel', 'usa_temporada', 'asignable', 'intocable', 'rango']);
        });
    }
};
