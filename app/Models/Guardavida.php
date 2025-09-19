<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardavida extends Model
{
    /** @use HasFactory<\Database\Factories\GuardavidaFactory> */
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'telefono',
        'direccion',
        'numero',
        'piso_dpto',
        'user_id',
        'playa_id',
        'puesto_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function playa()
    {
        return $this->belongsTo(Playa::class);
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }
}
