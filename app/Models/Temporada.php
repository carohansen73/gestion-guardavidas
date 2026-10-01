<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    protected $fillable = [
        'nombre',
        'fecha_inicio_postulacion',
        'fecha_fin_postulacion',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio_postulacion' => 'date',
        'fecha_fin_postulacion' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function postulaciones()
    {
        return $this->hasMany(Postulacion::class);
    }

    /** ¿Hoy cae dentro de la ventana de postulación de esta temporada? */
    public function postulacionAbierta(): bool
    {
        $hoy = Carbon::today();

        return $this->fecha_inicio_postulacion->lte($hoy) && $this->fecha_fin_postulacion->gte($hoy);
    }

    /**
     * La temporada cuya ventana OPERATIVA (fecha_inicio/fecha_fin) incluye
     * hoy. Es la que rige para cargar intervenciones/banderas/etc. y para
     * la restricción de escritura de guardavida/encargado fuera de
     * temporada.
     */
    public static function activa(): ?self
    {
        $hoy = Carbon::today();

        return static::whereDate('fecha_inicio', '<=', $hoy)
            ->whereDate('fecha_fin', '>=', $hoy)
            ->first();
    }

    /**
     * La temporada cuya ventana de POSTULACIÓN incluye hoy — puede ser una
     * temporada distinta a la operativa activa, ya que la postulación se
     * hace meses antes de que la temporada arranque.
     */
    public static function conPostulacionAbierta(): ?self
    {
        $hoy = Carbon::today();

        return static::whereDate('fecha_inicio_postulacion', '<=', $hoy)
            ->whereDate('fecha_fin_postulacion', '>=', $hoy)
            ->first();
    }
}
