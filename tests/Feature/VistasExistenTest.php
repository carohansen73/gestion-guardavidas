<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * Red de seguridad para reorganizar carpetas de vistas: toda vista que el
 * código referencia por nombre (view(), @include, @extends) tiene que
 * existir. Detecta de inmediato una referencia que quedó apuntando a la
 * carpeta vieja — algo que de otro modo solo se vería al abrir la pantalla.
 *
 * No usa base de datos. Ignora los nombres dinámicos (con variables) y los
 * comentarios de Blade.
 */
class VistasExistenTest extends TestCase
{
    /**
     * Referencias rotas que ya existían antes de reorganizar las vistas.
     * `components.table`: App\View\Components\Table apunta a una vista que
     * no existe, pero ninguna pantalla usa <x-table> (los componentes reales
     * son x-index-table / x-index-table-links), así que nunca se renderiza.
     */
    private const EXCEPCIONES_CONOCIDAS = ['components.table'];

    public function test_todas_las_vistas_referenciadas_existen(): void
    {
        $faltantes = [];

        foreach ($this->archivosFuente() as $archivo) {
            $codigo = File::get($archivo);

            // Los comentarios de Blade pueden mencionar vistas que ya no existen.
            $codigo = preg_replace('/\{\{--.*?--\}\}/s', '', $codigo);

            preg_match_all(
                '/(?:\bview\(|@include\(|@includeIf\(|@extends\(|@includeWhen\([^,]+,\s*)\s*[\'"]([A-Za-z0-9_.\-]+)[\'"]/',
                $codigo,
                $coincidencias
            );

            foreach ($coincidencias[1] as $nombre) {
                if (! View::exists($nombre) && ! in_array($nombre, self::EXCEPCIONES_CONOCIDAS, true)) {
                    $faltantes[] = $nombre.'  (en '.str_replace(base_path().DIRECTORY_SEPARATOR, '', $archivo).')';
                }
            }
        }

        $this->assertSame([], array_values(array_unique($faltantes)), "Vistas referenciadas que no existen:\n".implode("\n", array_unique($faltantes)));
    }

    /** Los pasos de la postulación se arman con un nombre dinámico: view("...paso{$paso}"). */
    public function test_existen_las_vistas_dinamicas_de_postulacion(): void
    {
        foreach ([1, 2, 3, 4] as $paso) {
            $this->assertTrue(View::exists("postulaciones.postulante.paso{$paso}"));
        }
    }

    /** @return list<string> */
    private function archivosFuente(): array
    {
        $archivos = [];
        foreach ([app_path(), base_path('routes'), resource_path('views')] as $dir) {
            foreach (File::allFiles($dir) as $f) {
                if (str_ends_with($f->getFilename(), '.php')) {
                    $archivos[] = $f->getPathname();
                }
            }
        }

        return $archivos;
    }
}
