<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bandera extends Model
{
    /** @use HasFactory<\Database\Factories\BanderaFactory> */
    use HasFactory;

     protected $fillable = [
        'fecha',
        'turno',
        'bandera_id',
        'viento_direccion',
        'viento_intensidad',
        'temperatura',
        'playa_id',
        'detalles',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function playa()
    {
        return $this->belongsTo(Playa::class);
    }

    public function bandera()
    {
        return $this->belongsTo(BanderaTipo::class);
    }


    public static function boot()
    {
        parent::boot();

        static::saving(function ($bandera) {
            if ($bandera->fecha) {
                $hora = Carbon::parse($bandera->fecha)->format('H');

                // Asignar turno según hora (si cargan fuera de horario queda TT)
                if ($hora >= 5 && $hora < 13) {
                    $bandera->turno = 'M';
                } else {
                    $bandera->turno = 'T';
                }
            }
        });
    }
}

