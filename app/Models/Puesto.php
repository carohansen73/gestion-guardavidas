<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class Puesto extends Model
{
    /** @use HasFactory<\Database\Factories\PuestoFactory> */
    use HasFactory;

    protected $fillable = [
        'nombre',
        'latitud',
        'longitud',
        'qr_encriptado',
        'playa_id'
    ];


    // Un puesto puede estar asignado a varios guardavidas
    public function guardavidas()
    {
        return $this->hasMany(Guardavida::class);
    }

    // Un puesto puede estar vinculado a muchas asistencias
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }




    public function playa()
    {
        return $this->belongsTo(Playa::class);  // Relación inversa
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($puesto) {
            if (!$puesto->qr_encriptado) {
                $data = [
                    'puesto_id'   => $puesto->id,
                    'puesto_name' => $puesto->nombre,
                    'puesto_lat'   => $puesto->latitud,
                    'puesto_lng'   => $puesto->longitud,
                    'playa_id'    => $puesto->playa_id,
                ];

                // Convertir a JSON y encriptar
                $encrypted = Crypt::encryptString(json_encode($data));
                $encoded = urlencode($encrypted);

                // Guardar la versión encriptada directamente en la DB
                $puesto->qr_encriptado = $encoded;
                $puesto->save();

                $qrImage = QrCode::format('svg')->size(300)->generate($encoded);
                $filePath = public_path("qr/puesto_{$puesto->nombre}_{$puesto->id}.svg");
                file_put_contents($filePath, $qrImage);
            };
        });
    }

}
