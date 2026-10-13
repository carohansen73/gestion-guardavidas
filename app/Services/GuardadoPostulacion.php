<?php

namespace App\Services;

use App\Models\Postulacion;
use App\Models\PostulacionDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Escritura de las partes de una postulación que comparten el formulario del
 * postulante (PostulacionController) y la edición del admin
 * (PostulacionAdminController): playas preferidas y documentos.
 */
class GuardadoPostulacion
{
    /** Reglas de validación de los archivos (`doc_{tipo}`); todos opcionales. */
    public static function reglasDocumentos(): array
    {
        $reglas = [];
        foreach (PostulacionDocumento::TIPOS as $tipo => $config) {
            $reglas["doc_{$tipo}"] = ['nullable', 'file', 'mimes:'.implode(',', $config['mimes']), 'max:'.PostulacionDocumento::MAX_KB];
        }

        return $reglas;
    }

    /**
     * detach + attach (no sync): con la restricción única (postulacion, prioridad), intercambiar el
     * orden de dos playas con sync chocaría a mitad de camino.
     */
    public function guardarPlayas(Postulacion $postulacion, ?int $playa1, ?int $playa2): void
    {
        DB::transaction(function () use ($postulacion, $playa1, $playa2) {
            $postulacion->playas()->detach();
            foreach ([1 => $playa1, 2 => $playa2] as $prioridad => $playaId) {
                if ($playaId) {
                    $postulacion->playas()->attach($playaId, ['prioridad' => $prioridad]);
                }
            }
        });
    }

    /** Guarda los archivos que vengan en el request (`doc_{tipo}`); los que no vengan quedan como estaban. */
    public function guardarDocumentos(Request $request, Postulacion $postulacion): void
    {
        foreach (PostulacionDocumento::TIPOS as $tipo => $config) {
            $archivo = $request->file("doc_{$tipo}");
            if (! $archivo) {
                continue; // subió solo algunos: el resto queda como estaba
            }

            $existente = $postulacion->documentos()->where('tipo', $tipo)->first();
            // El archivo puede estar compartido con la inscripción de otra temporada (foto/DNI
            // reutilizados): solo se borra del disco si ningún otro registro lo usa.
            if ($existente && ! PostulacionDocumento::where('ruta', $existente->ruta)->where('id', '!=', $existente->id)->exists()) {
                Storage::disk('local')->delete($existente->ruta);
            }

            $directorio = "postulaciones/{$postulacion->temporada_id}/{$postulacion->id}";

            // Las fotos se achican y se pasan a WebP para ahorrar espacio; los PDF (y lo que no se
            // pueda convertir) se guardan tal cual.
            $reducido = app(ReductorImagenes::class)->reducir($archivo);
            if ($reducido !== null) {
                $ruta = "{$directorio}/{$tipo}.webp";
                Storage::disk('local')->put($ruta, $reducido);
                $nombreOriginal = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME).'.webp';
            } else {
                $ruta = $archivo->storeAs($directorio, $tipo.'.'.strtolower($archivo->getClientOriginalExtension()), 'local');
                $nombreOriginal = $archivo->getClientOriginalName();
            }

            $postulacion->documentos()->updateOrCreate(['tipo' => $tipo], [
                'ruta' => $ruta,
                'nombre_original' => $nombreOriginal,
                'mime' => $reducido !== null ? 'image/webp' : ($archivo->getMimeType() ?? 'application/octet-stream'),
                'tamano' => $reducido !== null ? strlen($reducido) : $archivo->getSize(),
            ]);
        }
    }
}
