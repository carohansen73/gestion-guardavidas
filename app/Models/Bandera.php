<?php

namespace App\Models;

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
}
