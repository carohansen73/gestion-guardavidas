<?php

namespace App\Console\Commands;

use App\Models\Temporada;
use App\Services\ImportadorInscripciones;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Migra las inscripciones del sistema viejo a postulaciones (solo datos).
 *
 * Por defecto es un SIMULACRO: lee, planifica y muestra el resumen, pero no
 * escribe nada. Con --ejecutar escribe en la base (en una transacción).
 *
 * Los datos viejos se leen de una base de MySQL (--bd) donde se cargó el dump
 * del sistema viejo. Para producción (sin consola) se genera un .sql con --sql.
 */
class ImportarPostulaciones extends Command
{
    protected $signature = 'postulaciones:importar
        {--bd= : Base de MySQL donde está cargado el dump del sistema viejo (tablas inscriptions y users)}
        {--conexion=inscripciones_viejo : Nombre de la conexión con los datos viejos (si no se pasa --bd debe estar configurada)}
        {--destino-bd= : Planifica contra OTRA base (una copia del estado de producción) en vez de la de la app. Solo simulacro: no se puede combinar con --ejecutar}
        {--temporada= : Id de la temporada a la que van las postulaciones}
        {--ejecutar : Escribe en la base. Sin esto es un simulacro y no se guarda nada}
        {--sql= : Ruta donde guardar el SQL equivalente para correr en producción}
        {--sql-deshacer= : Ruta donde guardar el SQL para revertir la importación en producción}
        {--informe= : Ruta donde guardar el informe en CSV (una fila por inscripción)}';

    protected $description = 'Migra las inscripciones del sistema viejo a postulaciones (solo datos; simulacro salvo --ejecutar)';

    public function handle(): int
    {
        $temporada = Temporada::find((int) $this->option('temporada'));
        if (! $temporada) {
            $this->error('Indicá una temporada válida con --temporada=ID.');

            return self::FAILURE;
        }

        if ($destino = $this->option('destino-bd')) {
            if ($this->option('ejecutar')) {
                $this->error('--destino-bd es solo para planificar/generar SQL: no se puede usar con --ejecutar.');

                return self::FAILURE;
            }
            // El estado actual del sistema nuevo (usuarios, guardavidas, playas...) se lee de esa base.
            $default = config('database.default');
            config(["database.connections.{$default}.database" => $destino]);
            DB::purge($default);
            $this->warn("Planificando contra la base '{$destino}' (solo lectura).");
        }

        $conexion = $this->option('conexion');
        if ($bd = $this->option('bd')) {
            // Misma conexión MySQL que la app, apuntando a la base con el dump viejo.
            config(["database.connections.{$conexion}" => array_merge(config('database.connections.'.config('database.default')), ['database' => $bd])]);
            DB::purge($conexion);
        }

        $importador = new ImportadorInscripciones($conexion, $temporada->id);

        try {
            ['planes' => $planes, 'omitidas' => $omitidas, 'borradores' => $borradores] = $importador->planificar();
        } catch (Throwable $e) {
            $this->error('No se pudieron leer los datos viejos: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->resumen($planes, $omitidas, $borradores, $temporada);

        if ($ruta = $this->option('informe')) {
            $this->informe($ruta, $planes, $omitidas);
            $this->line("Informe: {$ruta}");
        }
        if ($ruta = $this->option('sql')) {
            file_put_contents($ruta, $importador->sql($planes));
            $this->line("SQL para producción: {$ruta}");
        }
        if ($ruta = $this->option('sql-deshacer')) {
            file_put_contents($ruta, $importador->sqlDeshacer($planes));
            $this->line("SQL para deshacer: {$ruta}");
        }

        if (! $this->option('ejecutar')) {
            $this->newLine();
            $this->warn('SIMULACRO: no se escribió nada en la base. Para importar de verdad agregá --ejecutar.');

            return self::SUCCESS;
        }

        try {
            $cuenta = $importador->ejecutar($planes);
        } catch (Throwable $e) {
            $this->error('La importación falló y se deshizo completa (no quedó nada a medias): '.$e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('IMPORTADO:');
        $this->table(['Qué', 'Cantidad'], collect($cuenta)->map(fn ($n, $k) => [str_replace('_', ' ', $k), $n])->values()->all());

        return self::SUCCESS;
    }

    /** @param list<array<string,mixed>> $planes */
    private function resumen(array $planes, array $omitidas, int $borradores, Temporada $temporada): void
    {
        $contar = fn (callable $f) => count(array_filter($planes, $f));

        $this->info("Temporada destino: {$temporada->nombre} (id {$temporada->id})");
        $this->table(['Concepto', 'Cantidad'], [
            ['Borradores (estado Pendiente): NO se importan', $borradores],
            ['Inscripciones enviadas que se importan', count($planes)],
            ['   -> a usuarios que YA existen (por DNI), sin tocar el usuario', $contar(fn ($p) => $p['accion_usuario'] === 'existente')],
            ['   -> a usuarios que ya existen por mail (se corrige su DNI)', $contar(fn ($p) => $p['accion_usuario'] === 'existente_corregir_dni')],
            ['   -> usuarios NUEVOS (rol postulante)', $contar(fn ($p) => $p['accion_usuario'] === 'crear')],
            ['Postulaciones a crear', $contar(fn ($p) => $p['crear_postulacion'])],
            ['Perfiles a crear', $contar(fn ($p) => $p['crear_perfil'])],
            ['   estado aceptada', $contar(fn ($p) => $p['estado'] === 'aceptada')],
            ['   estado pendiente (en revisión)', $contar(fn ($p) => $p['estado'] === 'pendiente')],
            ['   estado rechazada', $contar(fn ($p) => $p['estado'] === 'rechazada')],
            ['Grupo sanguíneo que queda vacío (no interpretable)', $contar(fn ($p) => $p['perfil']['grupo_sanguineo'] === null && filled($p['blood_type_original']))],
            ['Omitidas por algún problema', count($omitidas)],
        ]);

        foreach ($omitidas as $o) {
            $this->warn("OMITIDA inscripción {$o['inscripcion_id']} ({$o['apellido']}, {$o['nombre']}, DNI {$o['dni']}): {$o['motivo']}");
        }
        foreach (array_filter($planes, fn ($p) => $p['accion_usuario'] === 'existente_corregir_dni') as $p) {
            $this->line("  DNI corregido: {$p['apellido']}, {$p['nombre']} ({$p['email']}): {$p['dni_anterior']} -> {$p['dni']}");
        }
    }

    /** @param list<array<string,mixed>> $planes */
    private function informe(string $ruta, array $planes, array $omitidas): void
    {
        $f = fopen($ruta, 'w');
        fwrite($f, "\xEF\xBB\xBF"); // BOM: para que Excel respete los acentos
        fputcsv($f, ['inscripcion_id', 'apellido', 'nombre', 'dni', 'email', 'estado_viejo', 'estado_nuevo', 'usuario', 'postulacion', 'perfil', 'grupo_sanguineo_original', 'grupo_sanguineo_nuevo', 'obra_social', 'notas'], ';');
        foreach ($planes as $p) {
            fputcsv($f, [
                $p['inscripcion_id'], $p['apellido'], $p['nombre'], $p['dni'], $p['email'], $p['estado_viejo'], $p['estado'],
                ['crear' => 'usuario nuevo', 'existente' => 'usuario existente (no se toca)', 'existente_corregir_dni' => 'existente: se corrige el DNI'][$p['accion_usuario']],
                $p['crear_postulacion'] ? 'se crea' : 'ya existía', $p['crear_perfil'] ? 'se crea' : 'ya existía',
                $p['blood_type_original'], $p['perfil']['grupo_sanguineo'], $p['perfil']['obra_social_nombre'], implode(' | ', $p['notas']),
            ], ';');
        }
        foreach ($omitidas as $o) {
            fputcsv($f, [$o['inscripcion_id'], $o['apellido'], $o['nombre'], $o['dni'], '', '', '', 'OMITIDA', '', '', '', '', '', $o['motivo']], ';');
        }
        fclose($f);
    }
}
