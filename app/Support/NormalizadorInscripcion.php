<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Limpieza de los datos del sistema viejo de inscripciones (texto libre) para
 * guardarlos en el formato del sistema nuevo. Todas las funciones son puras:
 * si no se puede interpretar con seguridad un valor, devuelven null en vez de
 * adivinar (el dato queda vacío y se completa a mano).
 */
class NormalizadorInscripcion
{
    /** Solo los dígitos del DNI ("23.974.841" -> "23974841"). */
    public static function dni(?string $valor): ?string
    {
        $digitos = preg_replace('/\D+/', '', (string) $valor);

        return $digitos !== '' ? $digitos : null;
    }

    /**
     * "0+", "O positivo", "cero positivo", "Grupo A RH+", "A- (negativo)"... ->
     * uno de A+ A- B+ B- AB+ AB- O+ O-. Sin letra o sin signo ("A", "Rh",
     * "no sabe", "M", "-") no se puede saber: null.
     */
    public static function grupoSanguineo(?string $valor): ?string
    {
        $s = mb_strtolower(trim((string) $valor));
        $s = str_replace(['"', "'", '.', '(', ')'], '', $s);
        if ($s === '') {
            return null;
        }

        if (str_contains($s, 'positivo') || preg_match('/\bpos\b|\+/', $s)) {
            $signo = '+';
        } elseif (str_contains($s, 'negativo') || preg_match('/\bneg\b|-/', $s)) {
            $signo = '-';
        } else {
            return null;
        }

        $letra = preg_replace('/positivo|negativo|\bpos\b|\bneg\b|grupo|factor|rh|[+\-\s]+/', '', $s);
        $letras = ['a' => 'A', 'b' => 'B', 'ab' => 'AB', 'o' => 'O', '0' => 'O', '00' => 'O', 'cero' => 'O'];

        return isset($letras[$letra]) ? $letras[$letra].$signo : null;
    }

    /**
     * Nombre de la obra social, o null si no tiene. "No", "Ninguna" y un "Si"
     * suelto (sin decir cuál) quedan en null.
     */
    public static function obraSocial(?string $valor): ?string
    {
        $texto = trim((string) $valor);
        $clave = trim(Str::lower(Str::ascii($texto)), " .-_");

        $sinObraSocial = ['', 'no', 'ninguna', 'ninguno', 'no tiene', 'no posee', 'no tengo', 'sin obra social', 'si', 's/n', 'n/a', 'na'];
        if (in_array($clave, $sinObraSocial, true) || preg_match('/^no( |$)/', $clave)) {
            return null;
        }

        return mb_substr($texto, 0, 255);
    }

    /** Talle en mayúsculas ("l" -> "L", "extra large" -> "XL"), máx. 10 caracteres. */
    public static function talle(?string $valor): ?string
    {
        $texto = trim((string) $valor);
        if ($texto === '') {
            return null;
        }

        $equivalencias = [
            'extra large' => 'XL', 'extra-large' => 'XL', 'large' => 'L', 'medium' => 'M',
            'small' => 'S', 'extra small' => 'XS',
        ];
        $clave = Str::lower(Str::ascii($texto));

        return mb_substr($equivalencias[$clave] ?? mb_strtoupper($texto), 0, 10);
    }

    /** Recorta a $max caracteres (null si queda vacío). */
    public static function texto(?string $valor, int $max): ?string
    {
        $texto = trim((string) $valor);

        return $texto === '' ? null : mb_substr($texto, 0, $max);
    }

    /** Clave para comparar nombres de playa sin tildes ni mayúsculas. */
    public static function clavePlaya(?string $nombre): string
    {
        return Str::lower(Str::ascii(trim((string) $nombre)));
    }
}
