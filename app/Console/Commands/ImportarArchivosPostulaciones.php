<?php

namespace App\Console\Commands;

use App\Models\Temporada;
use App\Services\ImportadorArchivos;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Copia los archivos del sistema viejo (fotos, DNI, CV, etc.) a las postulaciones ya importadas.
 *
 * Por defecto es un SIMULACRO: lee, verifica que cada archivo exista y muestra el resumen,
 * pero no copia nada ni escribe en la base. Con --ejecutar copia los archivos y crea las filas.
 * Para producción (sin consola) genera un .sql con --sql; los archivos se suben por FTP.
 */
class ImportarArchivosPostulaciones extends Command
{
    protected $signature = 'postulaciones:importar-archivos
        {--bd= : Base de MySQL donde está cargado el dump del sistema viejo (tabla inscriptions y users)}
        {--conexion=inscripciones_viejo : Nombre de la conexión con los datos viejos (si no se pasa --bd debe estar configurada)}
        {--temporada= : Id de la temporada de las postulaciones}
        {--origen= : Carpeta con las carpetas de archivos del sistema viejo (por defecto storage/app/importacion)}
        {--ejecutar : Copia los archivos y crea las filas. Sin esto es un simulacro y no se guarda nada}
        {--sql= : Ruta donde guardar el SQL de las filas para correr en producción}
        {--sql-deshacer= : Ruta donde guardar el SQL para revertir las filas en producción}
        {--informe= : Ruta donde guardar el informe en CSV (una fila por documento)}';

    protected $description = 'Copia los archivos del sistema viejo a las postulaciones importadas (simulacro salvo --ejecutar)';

    public function handle(): int
    {
        $temporada = Temporada::find((int) $this->option('temporada'));
        if (! $temporada) {
            $this->error('Indicá una temporada válida con --temporada=ID.');

            return self::FAILURE;
        }

        $origen = $this->option('origen') ?: storage_path('app/importacion');
        if (! is_dir($origen)) {
            $this->error("No existe la carpeta de origen: {$origen}");

            return self::FAILURE;
        }

        $conexion = $this->option('conexion');
        if ($bd = $this->option('bd')) {
            config(["database.connections.{$conexion}" => array_merge(config('database.connections.'.config('database.default')), ['database' => $bd])]);
            DB::purge($conexion);
        }

        $importador = new ImportadorArchivos($conexion, $temporada->id, $origen);

        try {
            ['items' => $items, 'sin_postulacion' => $sinPostulacion] = $importador->planificar();
        } catch (Throwable $e) {
            $this->error('No se pudieron leer los datos: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->resumen($items, $sinPostulacion, $temporada);

        if ($ruta = $this->option('informe')) {
            $this->informe($ruta, $items, $sinPostulacion);
            $this->line("Informe: {$ruta}");
        }
        if ($ruta = $this->option('sql')) {
            file_put_contents($ruta, $importador->sql($items));
            $this->line("SQL para producción: {$ruta}");
        }
        if ($ruta = $this->option('sql-deshacer')) {
            file_put_contents($ruta, $importador->sqlDeshacer($items));
            $this->line("SQL para deshacer: {$ruta}");
        }

        if (! $this->option('ejecutar')) {
            $this->newLine();
            $this->warn('SIMULACRO: no se copió ningún archivo ni se escribió en la base. Para hacerlo de verdad agregá --ejecutar.');

            return self::SUCCESS;
        }

        try {
            $cuenta = $importador->ejecutar($items);
        } catch (Throwable $e) {
            $this->error('La importación falló y no se crearon filas: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('IMPORTADO: '.$cuenta['archivos_copiados'].' archivos copiados ('.number_format($cuenta['bytes'] / 1048576, 1, ',', '.').' MB) y sus filas creadas.');

        return self::SUCCESS;
    }

    /** @param list<array<string,mixed>> $items */
    private function resumen(array $items, array $sinPostulacion, Temporada $temporada): void
    {
        $contar = fn (string $r) => count(array_filter($items, fn ($i) => $i['resultado'] === $r));
        $peso = array_sum(array_map(fn ($i) => $i['resultado'] === 'copiar' ? $i['tamano'] : 0, $items));

        $this->info("Temporada: {$temporada->nombre} (id {$temporada->id})");
        $this->table(['Concepto', 'Cantidad'], [
            ['Documentos referenciados por las inscripciones', count($items)],
            ['   -> a copiar', $contar('copiar')],
            ['   -> ya existían (no se tocan)', $contar('ya_existia')],
            ['   -> archivo NO encontrado en la carpeta de origen', $contar('falta')],
            ['   -> archivo vacío (0 bytes)', $contar('vacio')],
            ['   -> nombre roto en el sistema viejo (sin extensión)', $contar('roto')],
            ['   -> formato no reconocible', $contar('formato_desconocido')],
            ['Peso a copiar (MB)', number_format($peso / 1048576, 1, ',', '.')],
            ['Inscripciones sin postulación en esta base', count($sinPostulacion)],
        ]);

        foreach ($items as $i) {
            if (! in_array($i['resultado'], ['copiar', 'ya_existia'], true)) {
                $this->warn("  {$i['resultado']}: {$i['apellido']}, {$i['nombre']} — {$i['tipo']} ({$i['archivo']})");
            } elseif ($i['notas']) {
                $this->line("  nota: {$i['apellido']}, {$i['nombre']} — {$i['tipo']}: ".implode('; ', $i['notas']));
            }
        }
        foreach ($sinPostulacion as $s) {
            $this->warn("  SIN POSTULACIÓN: inscripción {$s['inscripcion_id']} ({$s['apellido']}, {$s['nombre']}, DNI {$s['dni']})");
        }
    }

    /** @param list<array<string,mixed>> $items */
    private function informe(string $ruta, array $items, array $sinPostulacion): void
    {
        $f = fopen($ruta, 'w');
        fwrite($f, "\xEF\xBB\xBF");
        fputcsv($f, ['inscripcion_id', 'apellido', 'nombre', 'dni', 'estado_viejo', 'tipo', 'archivo_viejo', 'resultado', 'destino', 'tamano_bytes', 'notas'], ';');
        foreach ($items as $i) {
            fputcsv($f, [$i['inscripcion_id'], $i['apellido'], $i['nombre'], $i['dni'], $i['estado_viejo'], $i['tipo'], $i['archivo'], $i['resultado'], $i['destino'], $i['tamano'], implode(' | ', $i['notas'])], ';');
        }
        foreach ($sinPostulacion as $s) {
            fputcsv($f, [$s['inscripcion_id'], $s['apellido'], $s['nombre'], $s['dni'], '', '', '', 'SIN POSTULACION', '', '', ''], ';');
        }
        fclose($f);
    }
}
