<?php

namespace Tests\Feature;

use App\Services\ReductorImagenes;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ReductorImagenesTest extends TestCase
{
    private function jpegGrande(int $ancho, int $alto): UploadedFile
    {
        $img = imagecreatetruecolor($ancho, $alto);
        // ruido para que no comprima trivialmente, como una foto real
        for ($i = 0; $i < 4000; $i++) {
            imagesetpixel($img, random_int(0, $ancho - 1), random_int(0, $alto - 1), random_int(0, 0xFFFFFF));
        }
        $ruta = tempnam(sys_get_temp_dir(), 'img').'.jpg';
        imagejpeg($img, $ruta, 100);

        return new UploadedFile($ruta, 'foto.jpg', 'image/jpeg', null, true);
    }

    public function test_una_foto_grande_se_achica_a_1600_de_lado_maximo_y_pasa_a_webp(): void
    {
        $archivo = $this->jpegGrande(4000, 3000);

        $contenido = (new ReductorImagenes)->reducir($archivo);

        $this->assertNotNull($contenido);
        $this->assertLessThan($archivo->getSize(), strlen($contenido));
        [$ancho, $alto, $tipo] = getimagesizefromstring($contenido);
        $this->assertSame(IMAGETYPE_WEBP, $tipo);
        $this->assertSame(1600, $ancho);
        $this->assertSame(1200, $alto);
    }

    public function test_un_pdf_no_se_toca(): void
    {
        $ruta = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($ruta, "%PDF-1.4\ncontenido");

        $this->assertNull((new ReductorImagenes)->reducir(new UploadedFile($ruta, 'dni.pdf', 'application/pdf', null, true)));
    }

    public function test_un_archivo_roto_con_extension_de_imagen_devuelve_null_y_no_falla(): void
    {
        $ruta = tempnam(sys_get_temp_dir(), 'bad');
        file_put_contents($ruta, 'no soy una imagen');

        $this->assertNull((new ReductorImagenes)->reducir(new UploadedFile($ruta, 'foto.jpg', 'image/jpeg', null, true)));
    }

    public function test_un_png_con_transparencia_pasa_a_webp_sin_perderla(): void
    {
        $img = imagecreatetruecolor(2400, 1800);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        imagefill($img, 0, 0, imagecolorallocatealpha($img, 255, 0, 0, 127));
        $ruta = tempnam(sys_get_temp_dir(), 'png').'.png';
        imagepng($img, $ruta, 0);

        $archivo = new UploadedFile($ruta, 'logo.png', 'image/png', null, true);
        $contenido = (new ReductorImagenes)->reducir($archivo);

        $this->assertNotNull($contenido);
        [$ancho, , $tipo] = getimagesizefromstring($contenido);
        $this->assertSame(IMAGETYPE_WEBP, $tipo);
        $this->assertSame(1600, $ancho);
        $this->assertSame(127, (imagecolorat(imagecreatefromstring($contenido), 0, 0) >> 24) & 127);
    }
}
