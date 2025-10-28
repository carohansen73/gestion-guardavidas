<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materiales';

    protected $fillable = [
        'nombre',
        'detalle',
        'playa_id'
    ];

    //  public function playa()
    // {
    //     return $this->belongsTo(Playa::class);  // Relación inversa
    // }
}
