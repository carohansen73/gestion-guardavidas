<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrancoExcepcion extends Model
{
    protected $table = 'franco_excepciones';

    protected $fillable = [
        'guardavida_id',
        'fecha',
        'tipo',
        'motivo',
        'cargado_por_user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function guardavida()
    {
        return $this->belongsTo(Guardavida::class);
    }

    public function cargadoPor()
    {
        return $this->belongsTo(User::class, 'cargado_por_user_id');
    }
}
