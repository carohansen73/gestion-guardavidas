<?php
namespace App\Enums;

enum TipoNovedad: string {

    case Daño = 'Daño';
    case Falla = 'Falla';
    case Faltante  = 'Faltante';
    case Pérdida = 'Pérdida';
    case Rotura = 'Rotura';

    public static function values() : array {
        return array_column(self::cases(), 'value');
    }
}
