<?php

namespace App\Models;

use App\Enums\TipoNovedad;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NovedadMaterial extends Model
{
    /** @use HasFactory<\Database\Factories\NovedadMaterialFactory> */
    use HasFactory;

    protected $table = 'novedad_materials';

      protected $fillable = [
        'tipo_novedad',
        'material_id',
        'fecha',
        'playa_id',
        'detalles',
    ];

    protected $casts = [
        'tipo_novedad' => TipoNovedad::class,
        'fecha' => 'datetime',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

     public function playa()
    {
        return $this->belongsTo(Playa::class);
    }
}
