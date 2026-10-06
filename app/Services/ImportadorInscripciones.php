<?php

namespace App\Services;

use App\Support\NormalizadorInscripcion as Norm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Migra las inscripciones del sistema viejo (tablas `inscriptions` + `users`)
 * al sistema nuevo (users + postulacion_*), SOLO datos (los archivos se
 * migran aparte).
 *
 * Reglas:
 *  - Solo inscripciones ENVIADAS (aceptada / en revisión / rechazada). Las
 *    "Pendiente" son borradores que nunca se enviaron: se dejan afuera.
 *  - La persona se identifica por DNI. Si ya existe un usuario con ese DNI NO se
 *    lo modifica (ni mail, ni contraseña, ni nombre): solo se le agrega la
 *    postulación (y su perfil si no tenía).
 *  - Única excepción: si el DNI no coincide pero el MAIL sí, es la misma persona
 *    con el DNI mal tipeado en el sistema nuevo; se corrige users.dni al del
 *    sistema viejo.
 *  - Quien no existe se crea con rol `postulante`, conservando su contraseña.
 *  - Es idempotente: volver a correrlo no duplica nada.
 *
 * Trabaja en dos pasos: planificar() (solo lee) y ejecutar()/sql() usan ese plan.
 */
class ImportadorInscripciones
{
    /** Estado viejo (sin tildes, en minúsculas) -> estado del sistema nuevo. */
    private const ESTADOS = [
        'aceptada' => 'aceptada',
        'rechazada' => 'rechazada',
        'revision pendiente' => 'pendiente',
    ];

    public function __construct(
        private readonly string $conexionVieja,
        private readonly int $temporadaId,
    ) {}

    /**
     * @return array{planes: list<array<string,mixed>>, omitidas: list<array<string,mixed>>, borradores: int}
     */
    public function planificar(): array
    {
        $filas = DB::connection($this->conexionVieja)->table('inscriptions as i')
            ->join('users as u', 'u.id', '=', 'i.user_id')
            ->select('i.*', 'u.email as user_email', 'u.password as user_password', 'u.created_at as user_created_at')
            ->orderBy('i.id')
            ->get();

        // Estado actual del sistema nuevo.
        $porDni = [];
        $porEmail = [];
        foreach (DB::table('users')->get(['id', 'dni', 'email']) as $u) {
            if (filled($u->dni)) {
                $porDni[$u->dni] = (object) ['id' => $u->id, 'dni' => $u->dni, 'email' => $u->email, 'nuevo' => false];
            }
            $porEmail[Str::lower($u->email)] = (object) ['id' => $u->id, 'dni' => $u->dni, 'email' => $u->email, 'nuevo' => false];
        }
        $guardavidas = DB::table('guardavidas')->get(['user_id', 'direccion', 'numero', 'piso_dpto'])->keyBy('user_id');
        $conPerfil = DB::table('postulacion_perfiles')->pluck('user_id')->flip()->all();
        $conPostulacion = DB::table('postulaciones')->where('temporada_id', $this->temporadaId)->pluck('user_id')->flip()->all();
        $playas = [];
        foreach (DB::table('playas')->get(['id', 'nombre']) as $p) {
            $playas[Norm::clavePlaya($p->nombre)] = ['id' => $p->id, 'nombre' => $p->nombre];
        }

        $planes = [];
        $omitidas = [];
        $borradores = 0;

        foreach ($filas as $f) {
            $claveEstado = Str::lower(Str::ascii(trim($f->status)));

            // Borrador: nunca se envió.
            if ($claveEstado === 'pendiente') {
                $borradores++;

                continue;
            }
            if (! isset(self::ESTADOS[$claveEstado])) {
                $omitidas[] = $this->omitida($f, "estado desconocido '{$f->status}'");

                continue;
            }

            $dni = Norm::dni($f->document_number);
            if (! $dni) {
                $omitidas[] = $this->omitida($f, 'sin DNI');

                continue;
            }
            $email = Str::lower(trim($f->user_email));

            // ---- usuario ----
            $notas = [];
            $usuario = $porDni[$dni] ?? null;
            $accion = 'existente';
            if (! $usuario && isset($porEmail[$email])) {
                $usuario = $porEmail[$email];
                if (isset($porDni[$dni]) && $porDni[$dni]->id !== $usuario->id) {
                    $omitidas[] = $this->omitida($f, "el DNI {$dni} ya lo tiene otro usuario");

                    continue;
                }
                $accion = 'existente_corregir_dni';
                $notas[] = "DNI corregido en users: {$usuario->dni} -> {$dni}";
            }

            // Ya se planificó (como usuario nuevo) otra inscripción de esta misma persona.
            if ($usuario && ($usuario->nuevo ?? false)) {
                $omitidas[] = $this->omitida($f, 'la misma persona (DNI o mail) ya tiene otra inscripción en este lote');

                continue;
            }

            $datosUsuario = null;
            if (! $usuario) {
                $accion = 'crear';
                $datosUsuario = [
                    'name' => Norm::texto($f->name, 255),
                    'lastname' => Norm::texto($f->lastname, 255),
                    'dni' => $dni,
                    'email' => trim($f->user_email),
                    'password' => $f->user_password,
                    'enabled' => 1,
                    'must_change_psw' => 0,
                    'created_at' => $f->user_created_at ?? now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
                $usuario = (object) ['id' => null, 'dni' => $dni, 'email' => trim($f->user_email), 'nuevo' => true];
                $porDni[$dni] = $usuario;
                $porEmail[$email] = $usuario;
            }

            $usuarioId = $usuario->id;
            $existeUsuario = $usuarioId !== null;

            // ---- perfil (datos fijos de la persona) ----
            $grupo = Norm::grupoSanguineo($f->blood_type);
            if ($grupo === null && filled($f->blood_type)) {
                $notas[] = "grupo sanguíneo no interpretable: '{$f->blood_type}' (queda vacío)";
            }
            $libreta = Norm::texto($f->lifeguard_book_number, 50);
            if ($libreta !== null && mb_strlen(trim($f->lifeguard_book_number)) > 50) {
                $notas[] = 'N° de libreta recortado a 50 caracteres: '.trim($f->lifeguard_book_number);
            }
            $gv = $existeUsuario ? $guardavidas->get($usuarioId) : null;
            $perfilDatos = [
                'telefono' => Norm::texto($f->phone, 30),
                'direccion' => Norm::texto($gv->direccion ?? null, 255),
                'numero' => Norm::texto($gv->numero ?? null, 10),
                'piso_dpto' => Norm::texto($gv->piso_dpto ?? null, 255),
                'fecha_nacimiento' => $f->birthday,
                'genero' => null,
                'grupo_sanguineo' => $grupo,
                'numero_libreta' => $libreta,
                'talle_remera' => Norm::talle($f->shirt_size),
                'talle_pantalon' => Norm::talle($f->pants_size),
                'talle_campera' => Norm::talle($f->jacket_size),
                'talle_traje_bano' => Norm::talle($f->swimwear_size),
                'obra_social_nombre' => Norm::obraSocial($f->health_insurance),
                'obra_social_numero_afiliado' => Norm::texto($f->member_id, 255),
            ];
            $crearPerfil = ! ($existeUsuario && isset($conPerfil[$usuarioId]));
            if (! $crearPerfil) {
                $notas[] = 'ya tenía perfil: no se toca';
            }

            // ---- postulación ----
            $crearPostulacion = ! ($existeUsuario && isset($conPostulacion[$usuarioId]));
            if (! $crearPostulacion) {
                $notas[] = 'ya tenía postulación en esta temporada: no se toca';
            }
            $estado = self::ESTADOS[$claveEstado];
            $postulacionDatos = [
                'temporada_id' => $this->temporadaId,
                'estado' => $estado,
                'enviada_at' => $f->created_at,
                'disponible_desde' => $f->availability_from,
                'disponible_hasta' => $f->availability_to,
                'observaciones' => Norm::texto($f->observations, 65000),
                'fecha_revision' => in_array($estado, ['aceptada', 'rechazada'], true) ? $f->updated_at : null,
                'seleccionado' => 0,
                'created_at' => $f->created_at ?? now()->toDateTimeString(),
                'updated_at' => $f->updated_at ?? now()->toDateTimeString(),
            ];

            // ---- playas preferidas ----
            $playasPlan = [];
            foreach ([1 => $f->beach, 2 => $f->optional_beach] as $prioridad => $nombre) {
                $clave = Norm::clavePlaya($nombre);
                if ($clave === '') {
                    continue;
                }
                if (! isset($playas[$clave])) {
                    $notas[] = "playa desconocida '{$nombre}'";

                    continue;
                }
                // La segunda opción igual a la primera no se repite.
                if ($prioridad === 2 && $playasPlan && $playasPlan[0]['playa_id'] === $playas[$clave]['id']) {
                    continue;
                }
                $playasPlan[] = ['playa_id' => $playas[$clave]['id'], 'nombre' => $playas[$clave]['nombre'], 'prioridad' => count($playasPlan) + 1];
            }

            $planes[] = [
                'inscripcion_id' => $f->id,
                'apellido' => $f->lastname,
                'nombre' => $f->name,
                'estado_viejo' => $f->status,
                'estado' => $estado,
                'dni' => $dni,
                'email' => trim($f->user_email),
                'accion_usuario' => $accion,
                'usuario_id' => $usuarioId,
                'dni_anterior' => $accion === 'existente_corregir_dni' ? $usuario->dni : null,
                'usuario' => $datosUsuario,
                // Datos de la cuenta vieja: el SQL de producción los usa si, al ejecutarse, la persona todavía no existe.
                'origen' => [
                    'name' => Norm::texto($f->name, 255),
                    'lastname' => Norm::texto($f->lastname, 255),
                    'password' => $f->user_password,
                    'created_at' => $f->user_created_at ?? now()->toDateTimeString(),
                ],
                'crear_perfil' => $crearPerfil,
                'perfil' => $perfilDatos,
                'crear_postulacion' => $crearPostulacion,
                'postulacion' => $postulacionDatos,
                'playas' => $playasPlan,
                'blood_type_original' => $f->blood_type,
                'notas' => $notas,
            ];
        }

        return ['planes' => $planes, 'omitidas' => $omitidas, 'borradores' => $borradores];
    }

    /**
     * Escribe el plan en la base, todo dentro de una transacción (si algo falla
     * no queda nada a medias).
     *
     * @param  list<array<string,mixed>>  $planes
     * @return array<string,int>
     */
    public function ejecutar(array $planes): array
    {
        $rolId = DB::table('roles')->where('name', 'postulante')->where('guard_name', 'web')->value('id');
        if (! $rolId) {
            throw new RuntimeException("No existe el rol 'postulante' en esta base.");
        }

        $cuenta = ['usuarios_creados' => 0, 'dni_corregidos' => 0, 'perfiles' => 0, 'postulaciones' => 0, 'playas' => 0];
        $ahora = now()->toDateTimeString();

        DB::transaction(function () use ($planes, $rolId, $ahora, &$cuenta) {
            foreach ($planes as $p) {
                $usuarioId = $p['usuario_id'];

                if ($p['accion_usuario'] === 'crear') {
                    $usuarioId = DB::table('users')->insertGetId($p['usuario']);
                    DB::table('model_has_roles')->insert(['role_id' => $rolId, 'model_type' => 'App\\Models\\User', 'model_id' => $usuarioId]);
                    $cuenta['usuarios_creados']++;
                } elseif ($p['accion_usuario'] === 'existente_corregir_dni') {
                    DB::table('users')->where('id', $usuarioId)->update(['dni' => $p['dni'], 'updated_at' => $ahora]);
                    DB::table('guardavidas')->where('user_id', $usuarioId)->update(['dni' => $p['dni']]);
                    $cuenta['dni_corregidos']++;
                }

                if ($p['crear_perfil']) {
                    DB::table('postulacion_perfiles')->insert(['user_id' => $usuarioId] + $p['perfil'] + ['created_at' => $ahora, 'updated_at' => $ahora]);
                    $cuenta['perfiles']++;
                }

                if ($p['crear_postulacion']) {
                    $postulacionId = DB::table('postulaciones')->insertGetId(['user_id' => $usuarioId] + $p['postulacion']);
                    $cuenta['postulaciones']++;
                    foreach ($p['playas'] as $pl) {
                        DB::table('postulacion_playas')->insert(['postulacion_id' => $postulacionId, 'playa_id' => $pl['playa_id'], 'prioridad' => $pl['prioridad']]);
                        $cuenta['playas']++;
                    }
                }
            }
        });

        return $cuenta;
    }

    /**
     * SQL para correr a mano en producción (phpMyAdmin), equivalente a ejecutar().
     *
     * NO depende de cómo estaba la base cuando se generó: carga los datos viejos
     * en una tabla temporal y, AL EJECUTARSE, decide para cada persona:
     *  - si ya existe un usuario con ese DNI o ese mail, no se lo toca (solo se le
     *    agrega la postulación y el perfil, con el domicilio de su ficha de
     *    guardavida si la tiene);
     *  - si no existe, se crea con rol `postulante` y su contraseña vieja.
     * Así sigue andando aunque en producción se hayan cargado usuarios nuevos
     * después de la copia con la que se armó. Va en una transacción y es
     * repetible: correrlo dos veces no duplica ni modifica nada.
     *
     * @param  list<array<string,mixed>>  $planes
     */
    public function sql(array $planes): string
    {
        $q = fn ($v) => $v === null ? 'NULL' : DB::getPdo()->quote((string) $v);
        $t = $this->temporadaId;
        $col = ' COLLATE utf8mb4_unicode_ci'; // las tablas de la app son unicode_ci; la base puede tener otra por defecto
        $tipoUsuario = $q('App\\Models\\User');

        $sql = [
            '-- Importación de inscripciones del sistema viejo (solo datos). Generado '.now()->format('Y-m-d H:i'),
            '-- Correr en phpMyAdmin con cotejamiento utf8mb4. Es una sola transacción y se puede repetir sin duplicar.',
            '-- Decide al ejecutarse: usuarios que ya existen (por DNI o mail) NO se modifican.',
            'SET NAMES utf8mb4;', 'START TRANSACTION;', '',
        ];

        // ---- 1) correcciones explícitas de DNI mal tipeado (mismo mail, mismo nombre) ----
        $correcciones = array_values(array_filter($planes, fn ($p) => $p['accion_usuario'] === 'existente_corregir_dni'));
        if ($correcciones) {
            $sql[] = '-- 1) Corrección de DNI mal tipeado en usuarios que ya existen. Solo actúa si el DNI sigue siendo el viejo.';
            foreach ($correcciones as $p) {
                $antes = $p['dni_anterior'] === null ? 'dni IS NULL' : 'dni = '.$q($p['dni_anterior']);
                $sql[] = 'UPDATE users SET dni = '.$q($p['dni']).' WHERE email = '.$q($p['email']).' AND '.$antes.';';
            }
            $sql[] = 'UPDATE guardavidas g JOIN users u ON u.id = g.user_id SET g.dni = u.dni WHERE u.email IN ('
                .implode(', ', array_map(fn ($p) => $q($p['email']), $correcciones)).');';
            $sql[] = '';
        }

        // ---- 2) tabla temporal con los datos viejos (una fila por inscripción) ----
        $sql[] = '-- 2) Datos del sistema viejo, en una tabla temporal (desaparece sola al cerrar la conexión)';
        $sql[] = 'DROP TEMPORARY TABLE IF EXISTS imp_usuario;';
        $sql[] = 'DROP TEMPORARY TABLE IF EXISTS imp_inscripciones;';
        $sql[] = "CREATE TEMPORARY TABLE imp_inscripciones (
  dni VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, nombre VARCHAR(255) NULL, apellido VARCHAR(255) NULL,
  password VARCHAR(255) NULL, usuario_creado_at DATETIME NULL,
  telefono VARCHAR(30) NULL, fecha_nacimiento DATE NULL, grupo_sanguineo VARCHAR(3) NULL, numero_libreta VARCHAR(50) NULL,
  talle_remera VARCHAR(10) NULL, talle_pantalon VARCHAR(10) NULL, talle_campera VARCHAR(10) NULL, talle_traje_bano VARCHAR(10) NULL,
  obra_social_nombre VARCHAR(255) NULL, obra_social_numero_afiliado VARCHAR(255) NULL,
  estado VARCHAR(20) NOT NULL, enviada_at DATETIME NULL, disponible_desde DATE NULL, disponible_hasta DATE NULL,
  observaciones TEXT NULL, fecha_revision DATETIME NULL, postulacion_creada_at DATETIME NULL, postulacion_actualizada_at DATETIME NULL,
  playa_1 INT NULL, playa_2 INT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $cols = ['dni', 'email', 'nombre', 'apellido', 'password', 'usuario_creado_at', 'telefono', 'fecha_nacimiento', 'grupo_sanguineo',
            'numero_libreta', 'talle_remera', 'talle_pantalon', 'talle_campera', 'talle_traje_bano', 'obra_social_nombre',
            'obra_social_numero_afiliado', 'estado', 'enviada_at', 'disponible_desde', 'disponible_hasta', 'observaciones',
            'fecha_revision', 'postulacion_creada_at', 'postulacion_actualizada_at', 'playa_1', 'playa_2'];
        $fila = fn (array $p) => '('.implode(', ', array_map($q, [
            $p['dni'], $p['email'], $p['origen']['name'], $p['origen']['lastname'], $p['origen']['password'], $p['origen']['created_at'],
            $p['perfil']['telefono'], $p['perfil']['fecha_nacimiento'], $p['perfil']['grupo_sanguineo'], $p['perfil']['numero_libreta'],
            $p['perfil']['talle_remera'], $p['perfil']['talle_pantalon'], $p['perfil']['talle_campera'], $p['perfil']['talle_traje_bano'],
            $p['perfil']['obra_social_nombre'], $p['perfil']['obra_social_numero_afiliado'],
            $p['postulacion']['estado'], $p['postulacion']['enviada_at'], $p['postulacion']['disponible_desde'], $p['postulacion']['disponible_hasta'],
            $p['postulacion']['observaciones'], $p['postulacion']['fecha_revision'], $p['postulacion']['created_at'], $p['postulacion']['updated_at'],
            $p['playas'][0]['playa_id'] ?? null, $p['playas'][1]['playa_id'] ?? null,
        ])).')';
        foreach (array_chunk($planes, 40) as $grupo) {
            $sql[] = 'INSERT INTO imp_inscripciones ('.implode(', ', $cols).") VALUES\n".implode(",\n", array_map($fila, $grupo)).';';
        }
        $sql[] = '';

        // ---- 3) usuarios nuevos: SOLO si no existe nadie con ese DNI ni ese mail ----
        $sql[] = '-- 3) Usuarios nuevos (rol postulante, con su contraseña actual). Si ya existe alguien con ese DNI o mail, se saltea.';
        $sql[] = "INSERT INTO users (name, lastname, dni, email, password, enabled, must_change_psw, created_at, updated_at)
SELECT t.nombre, t.apellido, t.dni, t.email, t.password, 1, 0, t.usuario_creado_at, NOW()
FROM imp_inscripciones t
WHERE NOT EXISTS (SELECT 1 FROM users u WHERE u.dni = t.dni{$col} OR u.email = t.email{$col});";
        $sql[] = '';

        // ---- 4) a quién corresponde cada inscripción (por DNI, o por mail si el DNI no coincide) ----
        $sql[] = '-- 4) A qué usuario corresponde cada inscripción (primero por DNI, si no por mail)';
        $sql[] = 'CREATE TEMPORARY TABLE imp_usuario (dni VARCHAR(20) NOT NULL, user_id BIGINT UNSIGNED NULL, PRIMARY KEY (dni)) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
        $sql[] = "INSERT INTO imp_usuario (dni, user_id)
SELECT t.dni, COALESCE(
  (SELECT u.id FROM users u WHERE u.dni = t.dni{$col} LIMIT 1),
  (SELECT u.id FROM users u WHERE u.email = t.email{$col} LIMIT 1))
FROM imp_inscripciones t;";
        $sql[] = '';

        // ---- 5) rol: solo a quien no tiene NINGÚN rol (los que ya son guardavida/encargado/admin no se tocan) ----
        $sql[] = '-- 5) Rol postulante, solo para usuarios que no tienen ningún rol (no se toca a guardavidas, encargados ni admins)';
        $sql[] = "INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id)
SELECT (SELECT id FROM roles WHERE name = 'postulante' AND guard_name = 'web'), {$tipoUsuario}, m.user_id
FROM imp_usuario m
WHERE m.user_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM model_has_roles x WHERE x.model_id = m.user_id AND x.model_type = {$tipoUsuario});";
        $sql[] = '';

        // ---- 6) perfiles ----
        $sql[] = '-- 6) Perfiles (datos fijos). IGNORE: si ya tiene perfil no se toca. El domicilio sale de su ficha de guardavida, si la tiene.';
        $sql[] = "INSERT IGNORE INTO postulacion_perfiles
  (user_id, telefono, direccion, numero, piso_dpto, fecha_nacimiento, genero, grupo_sanguineo, numero_libreta,
   talle_remera, talle_pantalon, talle_campera, talle_traje_bano, obra_social_nombre, obra_social_numero_afiliado, created_at, updated_at)
SELECT m.user_id, t.telefono, LEFT(NULLIF(TRIM(g.direccion), ''), 255), LEFT(NULLIF(TRIM(g.numero), ''), 10), LEFT(NULLIF(TRIM(g.piso_dpto), ''), 255),
       t.fecha_nacimiento, NULL, t.grupo_sanguineo, t.numero_libreta,
       t.talle_remera, t.talle_pantalon, t.talle_campera, t.talle_traje_bano, t.obra_social_nombre, t.obra_social_numero_afiliado, NOW(), NOW()
FROM imp_inscripciones t
JOIN imp_usuario m ON m.dni = t.dni
LEFT JOIN guardavidas g ON g.user_id = m.user_id
WHERE m.user_id IS NOT NULL;";
        $sql[] = '';

        // ---- 7) postulaciones ----
        $sql[] = "-- 7) Postulaciones de la temporada {$t}. IGNORE: hay UNIQUE(usuario, temporada), si ya tenía una no se toca";
        $sql[] = "INSERT IGNORE INTO postulaciones
  (user_id, temporada_id, estado, enviada_at, disponible_desde, disponible_hasta, observaciones, fecha_revision, seleccionado, created_at, updated_at)
SELECT m.user_id, {$t}, t.estado, t.enviada_at, t.disponible_desde, t.disponible_hasta, t.observaciones, t.fecha_revision, 0,
       t.postulacion_creada_at, t.postulacion_actualizada_at
FROM imp_inscripciones t
JOIN imp_usuario m ON m.dni = t.dni
WHERE m.user_id IS NOT NULL;";
        $sql[] = '';

        // ---- 8) playas preferidas (solo de las postulaciones que acaba de crear este script) ----
        $sql[] = '-- 8) Playas preferidas (1 = primera opción, 2 = segunda). Los ids de playa son los de producción.';
        foreach ([1 => 'playa_1', 2 => 'playa_2'] as $prioridad => $campo) {
            $sql[] = "INSERT IGNORE INTO postulacion_playas (postulacion_id, playa_id, prioridad)
SELECT p.id, t.{$campo}, {$prioridad}
FROM imp_inscripciones t
JOIN imp_usuario m ON m.dni = t.dni
JOIN postulaciones p ON p.user_id = m.user_id AND p.temporada_id = {$t} AND p.created_at = t.postulacion_creada_at
WHERE m.user_id IS NOT NULL AND t.{$campo} IS NOT NULL;";
        }
        $sql[] = '';
        $sql[] = 'COMMIT;';

        return implode("\n", $sql)."\n";
    }

    /**
     * SQL para DESHACER la importación en producción. Es conservador: solo borra
     * lo que se pudo crear con esta importación y nunca toca a un guardavida ni a
     * una postulación que ya fue seleccionada. NO correrlo si ya se empezó a
     * trabajar con las postulaciones (selección, etc.).
     *
     * @param  list<array<string,mixed>>  $planes
     */
    public function sqlDeshacer(array $planes): string
    {
        $q = fn ($v) => DB::getPdo()->quote((string) $v);
        $t = $this->temporadaId;
        $tipoUsuario = $q('App\\Models\\User');
        $lista = fn (array $valores) => implode(', ', array_map($q, $valores));

        $dnis = array_column($planes, 'dni');
        // Usuarios que esta importación pudo crear (no existían): se reconocen por mail Y por la fecha de alta del sistema viejo,
        // así un usuario que ya existía con ese mail (otro created_at) nunca se borra.
        $nuevos = array_values(array_filter($planes, fn ($p) => $p['accion_usuario'] === 'crear'));
        $correcciones = array_filter($planes, fn ($p) => $p['accion_usuario'] === 'existente_corregir_dni');

        $sql = ['-- DESHACER la importación de inscripciones. Correr solo si hay que revertir (y antes de trabajar con las postulaciones).',
            'SET NAMES utf8mb4;', 'START TRANSACTION;', ''];

        $sql[] = '-- Postulaciones importadas que todavía no fueron seleccionadas (las playas y documentos se borran en cascada)';
        $sql[] = "DELETE p FROM postulaciones p JOIN users u ON u.id = p.user_id WHERE p.temporada_id = {$t} AND p.seleccionado = 0 AND u.dni IN (".$lista($dnis).');';

        $sql[] = '-- Perfiles de esas personas que no tienen postulaciones en ninguna otra temporada (o sea, que existen por esta importación)';
        $sql[] = 'DELETE pf FROM postulacion_perfiles pf JOIN users u ON u.id = pf.user_id WHERE u.dni IN ('.$lista($dnis).")
  AND NOT EXISTS (SELECT 1 FROM postulaciones p2 WHERE p2.user_id = pf.user_id);";

        if ($nuevos) {
            $pares = implode(' OR ', array_map(fn ($p) => '(u.email = '.$q($p['email']).' AND u.created_at = '.$q($p['origen']['created_at']).')', $nuevos));
            $sql[] = '-- Usuarios nuevos (rol postulante) creados por la importación: solo si siguen sin ficha de guardavida ni postulaciones';
            $sql[] = "DELETE mhr FROM model_has_roles mhr JOIN users u ON u.id = mhr.model_id AND mhr.model_type = {$tipoUsuario}
JOIN roles r ON r.id = mhr.role_id AND r.name = 'postulante'
WHERE ({$pares})
  AND NOT EXISTS (SELECT 1 FROM guardavidas g WHERE g.user_id = u.id)
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.user_id = u.id);";
            $sql[] = "DELETE u FROM users u
WHERE ({$pares})
  AND NOT EXISTS (SELECT 1 FROM guardavidas g WHERE g.user_id = u.id)
  AND NOT EXISTS (SELECT 1 FROM model_has_roles x WHERE x.model_id = u.id AND x.model_type = {$tipoUsuario})
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.user_id = u.id);";
        }

        foreach ($correcciones as $p) {
            $sql[] = '-- DNI corregido: volver al valor anterior';
            $sql[] = 'UPDATE users SET dni = '.($p['dni_anterior'] === null ? 'NULL' : $q($p['dni_anterior'])).' WHERE email = '.$q($p['email']).' AND dni = '.$q($p['dni']).';';
            $sql[] = 'UPDATE guardavidas g JOIN users u ON u.id = g.user_id SET g.dni = u.dni WHERE u.email = '.$q($p['email']).';';
        }
        $sql[] = '';
        $sql[] = 'COMMIT;';

        return implode("\n", $sql)."\n";
    }

    /** @return array<string,mixed> */
    private function omitida(object $f, string $motivo): array
    {
        return ['inscripcion_id' => $f->id, 'apellido' => $f->lastname, 'nombre' => $f->name, 'dni' => $f->document_number, 'motivo' => $motivo];
    }
}
