<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

/**
 * Achica las fotos de postulaciones (las de celular pesan 3-8 MB) para ahorrar almacenamiento:
 * las redimensiona y las convierte a WebP. Solo toca JPG/PNG; los PDF no se tocan.
 * Si algo falla (o GD no está disponible) devuelve null y guarda el archivo original.
 */
class ReductorImagenes
{
    /** Lado máximo en píxeles: alcanza de sobra para leer un DNI o reconocer una cara. */
    public const LADO_MAXIMO = 1600;

    private const CALIDAD_WEBP = 80;

    /**
     * @return string|null contenido WebP ya reducido, o null si no hay que tocar el original
     *                     (no es JPG/PNG, no se pudo procesar, o no se logra achicar).
     *                     El llamador debe guardarlo con extensión `.webp`.
     */
    public function reducir(UploadedFile $archivo): ?string
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            return null;
        }

        $tipo = @exif_imagetype($archivo->getRealPath());
        if (! in_array($tipo, [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
            return null;
        }

        $origen = $tipo === IMAGETYPE_JPEG
            ? @imagecreatefromjpeg($archivo->getRealPath())
            : @imagecreatefrompng($archivo->getRealPath());
        if (! $origen) {
            return null;
        }

        if ($tipo === IMAGETYPE_JPEG) {
            $origen = $this->aplicarOrientacion($origen, $archivo->getRealPath());
        }

        $ancho = imagesx($origen);
        $alto = imagesy($origen);
        $escala = min(1, self::LADO_MAXIMO / max($ancho, $alto));

        if ($escala < 1) {
            $nuevoAncho = max(1, (int) round($ancho * $escala));
            $nuevoAlto = max(1, (int) round($alto * $escala));
            $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

            if ($tipo === IMAGETYPE_PNG) {
                imagealphablending($destino, false);
                imagesavealpha($destino, true);
            }

            imagecopyresampled($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
            $origen = $destino;
        }

        ob_start();
        $convertida = $tipo === IMAGETYPE_PNG ? $this->aTrueColorConAlfa($origen) : $origen;
        imagewebp($convertida, null, self::CALIDAD_WEBP);
        $contenido = ob_get_clean();

        // Si no se logró ahorrar nada (una imagen que ya venía bien comprimida), se deja el original.
        if ($contenido === false || $contenido === '' || strlen($contenido) >= $archivo->getSize()) {
            return null;
        }

        return $contenido;
    }

    /** imagewebp() no acepta imágenes de paleta (PNG indexados): se pasan a color real conservando la transparencia. */
    private function aTrueColorConAlfa(\GdImage $imagen): \GdImage
    {
        if (! imageistruecolor($imagen)) {
            imagepalettetotruecolor($imagen);
        }
        imagealphablending($imagen, false);
        imagesavealpha($imagen, true);

        return $imagen;
    }

    /** Las fotos de celular traen la rotación en el EXIF; GD la ignora, así que hay que aplicarla a mano. */
    private function aplicarOrientacion(\GdImage $imagen, string $ruta): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $imagen;
        }

        $orientacion = @exif_read_data($ruta)['Orientation'] ?? 1;
        $grados = match ($orientacion) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($grados === 0) {
            return $imagen;
        }

        return imagerotate($imagen, $grados, 0) ?: $imagen;
    }
}
