<?php

namespace App\Services;

use App\Support\NormalizadorInscripcion as Norm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Migra los ARCHIVOS (fotos, DNI, CV, libreta, antecedentes, declaración jurada,
 * licencia motonáutica) del sistema viejo a las postulaciones ya importadas
 * (ver ImportadorInscripciones, que migra los datos).
 *
 * En el sistema viejo cada archivo está en una carpeta con el nombre de su tipo
 * y la columna de `inscriptions` guarda solo el nombre del archivo. Acá se copian
 * (NO se mueven ni se tocan los originales) a la estructura nueva:
 *   storage/app/private/postulaciones/{temporada}/{postulacion}/{tipo}.{ext}
 * y se crea la fila de `postulacion_documentos`.
 *
 * Es idempotente: un documento que ya existe (misma postulación y tipo) no se toca.
 * Si un archivo falta, está vacío o es un nombre roto, se informa y se sigue.
 */
class ImportadorArchivos
{
    /** columna del sistema viejo => [carpeta en disco, tipo en el sistema nuevo]. La licencia se escribe distinto en columna y carpeta. */
    public const TIPOS = [
        'photography' => ['photography', 'foto_personal'],
        'front_document' => ['front_document', 'dni_frente'],
        'back_document' => ['back_document', 'dni_dorso'],
        'cv' => ['cv', 'curriculum'],
        'lifeguard_notebook' => ['lifeguard_notebook', 'libreta'],
        'criminal_record' => ['criminal_record', 'antecedentes_penales'],
        'declaration' => ['declaration', 'declaracion_jurada'],
        'motorcycle_licence_photo' => ['motorcycle_license_photo', 'licencia_motonautica'],
    ];

    private const EXTENSIONES = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'];

    private const POR_MIME = [
        'application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp',
    ];

    public function __construct(
        private readonly string $conexionVieja,
        private readonly int $temporadaId,
        private readonly string $origen,
    ) {}

    /**
     * @return array{items: list<array<string,mixed>>, sin_postulacion: list<array<string,mixed>>}
     */
    public function planificar(): array
    {
        $columnas = array_keys(self::TIPOS);
        $filas = DB::connection($this->conexionVieja)->table('inscriptions as i')
            ->join('users as u', 'u.id', '=', 'i.user_id')
            ->select(array_merge(['i.id', 'i.lastname', 'i.name', 'i.document_number', 'i.status', 'u.email as user_email'], array_map(fn ($c) => "i.{$c}", $columnas)))
            ->whereRaw("LOWER(i.status) <> 'pendiente'")
            ->orderBy('i.id')
            ->get();

        // A qué postulación corresponde cada persona en ESTA base (por DNI y, si no, por mail).
        $porDni = [];
        $porEmail = [];
        foreach (DB::table('users')->get(['id', 'dni', 'email']) as $u) {
            if (filled($u->dni)) {
                $porDni[$u->dni] = $u->id;
            }
            $porEmail[Str::lower($u->email)] = $u->id;
        }
        $postulacionDe = DB::table('postulaciones')->where('temporada_id', $this->temporadaId)->pluck('id', 'user_id')->all();
        $yaExisten = [];
        foreach (DB::table('postulacion_documentos')->get(['postulacion_id', 'tipo']) as $d) {
            $yaExisten[$d->postulacion_id.'|'.$d->tipo] = true;
        }

        $items = [];
        $sinPostulacion = [];

        foreach ($filas as $f) {
            $dni = Norm::dni($f->document_number);
            $email = trim($f->user_email);
            $userId = ($dni && isset($porDni[$dni])) ? $porDni[$dni] : ($porEmail[Str::lower($email)] ?? null);
            $postulacionId = $userId ? ($postulacionDe[$userId] ?? null) : null;

            if (! $postulacionId) {
                $sinPostulacion[] = ['inscripcion_id' => $f->id, 'apellido' => $f->lastname, 'nombre' => $f->name, 'dni' => $f->document_number];

                continue;
            }

            foreach (self::TIPOS as $columna => [$carpeta, $tipo]) {
                $archivo = trim((string) $f->{$columna});
                if ($archivo === '') {
                    continue; // no cargó este documento (la licencia, por ejemplo, es opcional)
                }

                $item = [
                    'inscripcion_id' => $f->id, 'apellido' => $f->lastname, 'nombre' => $f->name, 'dni' => $dni, 'email' => $email,
                    'postulacion_id' => $postulacionId, 'tipo' => $tipo, 'archivo' => $archivo,
                    'estado_viejo' => $f->status,
                    'resultado' => null, 'ext' => null, 'mime' => null, 'tamano' => null, 'origen_ruta' => null, 'destino' => null, 'notas' => [],
                ];

                $base = rtrim($this->origen, '\\/').DIRECTORY_SEPARATOR.$carpeta.DIRECTORY_SEPARATOR;
                $ruta = $base.$archivo;
                // El sistema viejo guardó algunos archivos sin extensión y con un punto al final del nombre
                // ("1785537758-." o "1787623129."). Al bajarlos a Windows ese punto se pierde, así que si no
                // está con el nombre exacto se prueba sin el punto final.
                if (! is_file($ruta) && rtrim($archivo, '.') !== $archivo && is_file($base.rtrim($archivo, '.'))) {
                    $ruta = $base.rtrim($archivo, '.');
                    $item['notas'][] = 'encontrado sin el punto final del nombre ("'.rtrim($archivo, '.').'")';
                }
                if (! is_file($ruta)) {
                    // Nombre terminado en "-." y sin archivo: en el sistema viejo se guardó sin contenido real.
                    $item['resultado'] = preg_match('/-\.$/', $archivo) ? 'roto' : 'falta';
                    $items[] = $item;

                    continue;
                }
                $tamano = filesize($ruta);
                if ($tamano === 0) {
                    $item['resultado'] = 'vacio';
                    $items[] = $item;

                    continue;
                }

                $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($ruta) ?: 'application/octet-stream';
                $extOriginal = Str::lower(pathinfo($archivo, PATHINFO_EXTENSION));
                // Extensión del nombre si es una conocida; si no ("pdf3", ninguna...) la que corresponde al contenido real.
                $ext = in_array($extOriginal, self::EXTENSIONES, true) ? $extOriginal : (self::POR_MIME[$mime] ?? null);
                if ($ext === null) {
                    $item['resultado'] = 'formato_desconocido';
                    $item['notas'][] = "no se pudo determinar el formato ({$mime})";
                    $items[] = $item;

                    continue;
                }
                if ($ext !== $extOriginal) {
                    $item['notas'][] = "extensión '{$extOriginal}' reemplazada por '{$ext}' según el contenido";
                }
                if (! isset(self::POR_MIME[$mime])) {
                    $item['notas'][] = "tipo de contenido inusual: {$mime}";
                }

                $destino = "postulaciones/{$this->temporadaId}/{$postulacionId}/{$tipo}.{$ext}";
                $item = array_merge($item, [
                    'resultado' => isset($yaExisten[$postulacionId.'|'.$tipo]) ? 'ya_existia' : 'copiar',
                    'ext' => $ext, 'mime' => $mime, 'tamano' => $tamano, 'origen_ruta' => $ruta, 'destino' => $destino,
                ]);
                $items[] = $item;
            }
        }

        return ['items' => $items, 'sin_postulacion' => $sinPostulacion];
    }

    /**
     * Copia los archivos y crea las filas. Primero copia todo; recién después
     * inserta las filas en una transacción (si una copia falla no queda ninguna
     * fila apuntando a un archivo que no está).
     *
     * @param  list<array<string,mixed>>  $items
     * @return array<string,int>
     */
    public function ejecutar(array $items): array
    {
        $disco = Storage::disk('local');
        $aCopiar = array_values(array_filter($items, fn ($i) => $i['resultado'] === 'copiar'));

        foreach ($aCopiar as $i) {
            $flujo = fopen($i['origen_ruta'], 'rb');
            try {
                $disco->put($i['destino'], $flujo);
            } finally {
                if (is_resource($flujo)) {
                    fclose($flujo);
                }
            }
        }

        $ahora = now()->toDateTimeString();
        DB::transaction(function () use ($aCopiar, $ahora) {
            foreach ($aCopiar as $i) {
                DB::table('postulacion_documentos')->insert([
                    'postulacion_id' => $i['postulacion_id'], 'tipo' => $i['tipo'], 'ruta' => $i['destino'],
                    'nombre_original' => $i['archivo'], 'mime' => $i['mime'], 'tamano' => $i['tamano'],
                    'created_at' => $ahora, 'updated_at' => $ahora,
                ]);
            }
        });

        return ['archivos_copiados' => count($aCopiar), 'bytes' => array_sum(array_column($aCopiar, 'tamano'))];
    }

    /**
     * SQL para producción: crea las filas de `postulacion_documentos` de los archivos
     * copiables. Igual que el SQL de datos, NO depende de ids de esta base: carga los
     * datos en una tabla temporal y al ejecutarse busca a cada persona por DNI (o mail)
     * y su postulación de la temporada. La carpeta en disco se arma con el id de la
     * postulación de PRODUCCIÓN, así que los ids deben coincidir con los de acá (hay
     * una consulta de control para eso). Repetible: INSERT IGNORE.
     *
     * @param  list<array<string,mixed>>  $items
     */
    public function sql(array $items): string
    {
        $t = $this->temporadaId;
        $col = ' COLLATE utf8mb4_unicode_ci';
        $q = fn ($v) => $v === null ? 'NULL' : DB::getPdo()->quote((string) $v);
        $copiables = array_values(array_filter($items, fn ($i) => in_array($i['resultado'], ['copiar', 'ya_existia'], true)));

        $sql = array_merge([
            '-- Documentos de las postulaciones (solo las filas de la base; los ARCHIVOS se suben por FTP aparte).',
            '-- Correr DESPUÉS de importar_postulaciones_PRODUCCION.sql y de subir la carpeta storage/app/private/postulaciones/'.$t.'/.',
            '-- Una sola transacción y repetible: lo que ya existe no se duplica.',
        ], $this->staging($copiables, $q, $col));

        $sql[] = "INSERT IGNORE INTO postulacion_documentos (postulacion_id, tipo, ruta, nombre_original, mime, tamano, created_at, updated_at)
SELECT p.id, d.tipo, CONCAT('postulaciones/{$t}/', p.id, '/', d.tipo, '.', d.ext), d.nombre_original, d.mime, d.tamano, NOW(), NOW()
FROM imp_documentos d
JOIN imp_doc_usuario m ON m.dni = d.dni
JOIN postulaciones p ON p.user_id = m.user_id AND p.temporada_id = {$t}
WHERE m.user_id IS NOT NULL;";
        $sql[] = '';
        $sql[] = 'COMMIT;';

        return implode("\n", $sql)."\n";
    }

    /**
     * SQL para deshacer en producción: borra SOLO las filas que coinciden con los
     * archivos importados (misma persona, tipo y nombre original). Los archivos
     * físicos se borran a mano (carpeta storage/app/private/postulaciones/{temporada}).
     *
     * @param  list<array<string,mixed>>  $items
     */
    public function sqlDeshacer(array $items): string
    {
        $t = $this->temporadaId;
        $col = ' COLLATE utf8mb4_unicode_ci';
        $q = fn ($v) => $v === null ? 'NULL' : DB::getPdo()->quote((string) $v);
        $copiables = array_values(array_filter($items, fn ($i) => in_array($i['resultado'], ['copiar', 'ya_existia'], true)));

        $sql = array_merge(['-- DESHACER la importación de documentos (solo las filas; borrá a mano la carpeta de archivos si hace falta).'], $this->staging($copiables, $q, $col));
        $sql[] = "DELETE d FROM postulacion_documentos d
JOIN postulaciones p ON p.id = d.postulacion_id AND p.temporada_id = {$t}
JOIN imp_doc_usuario m ON m.user_id = p.user_id
JOIN imp_documentos x ON x.dni = m.dni AND x.tipo = d.tipo AND x.nombre_original = d.nombre_original{$col};";
        $sql[] = '';
        $sql[] = 'COMMIT;';

        return implode("\n", $sql)."\n";
    }

    /**
     * Tablas temporales con los documentos y con el usuario de cada persona.
     *
     * @return list<string>
     */
    private function staging(array $copiables, callable $q, string $col): array
    {
        $sql = ['SET NAMES utf8mb4;', 'START TRANSACTION;', '',
            'DROP TEMPORARY TABLE IF EXISTS imp_doc_usuario;', 'DROP TEMPORARY TABLE IF EXISTS imp_documentos;',
            'CREATE TEMPORARY TABLE imp_documentos (dni VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, tipo VARCHAR(40) NOT NULL, ext VARCHAR(10) NOT NULL, nombre_original VARCHAR(255) NOT NULL, mime VARCHAR(100) NOT NULL, tamano INT UNSIGNED NOT NULL) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        ];
        foreach (array_chunk($copiables, 100) as $grupo) {
            $filas = array_map(fn ($i) => '('.implode(', ', array_map($q, [$i['dni'], $i['email'], $i['tipo'], $i['ext'], $i['archivo'], $i['mime'], $i['tamano']])).')', $grupo);
            $sql[] = "INSERT INTO imp_documentos (dni, email, tipo, ext, nombre_original, mime, tamano) VALUES\n".implode(",\n", $filas).';';
        }
        $sql[] = 'CREATE TEMPORARY TABLE imp_doc_usuario (dni VARCHAR(20) NOT NULL, user_id BIGINT UNSIGNED NULL, PRIMARY KEY (dni)) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
        $sql[] = "INSERT INTO imp_doc_usuario (dni, user_id)
SELECT d.dni, COALESCE(
  (SELECT u.id FROM users u WHERE u.dni = d.dni{$col} LIMIT 1),
  (SELECT u.id FROM users u WHERE u.email = d.email{$col} LIMIT 1))
FROM (SELECT DISTINCT dni, email FROM imp_documentos) d
GROUP BY d.dni;";
        $sql[] = '';

        return $sql;
    }
}
