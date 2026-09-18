<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardavida extends Model
{
    use HasFactory;

    protected $perPage = 10;

    protected $table = 'guardavidas'; // tu tabla real

    protected $fillable = [
        'funcion',
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
        'turno',
        'dia_franco',
    ];

    // Agregar accessor para contar asistencias
    protected $appends = ['asistencias_count', 'intervenciones_count', 'licencias_count'];

    // //------------------------------------------ Relaciones -------------------------------------------------------
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

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'guardavidas_id');
    }

    public function intervenciones()
    {
        return $this->belongsToMany(Intervencion::class, 'guardavidas_intervenciones', 'guardavida_id', 'intervencion_id');
    }

    public function licencias()
    {
        return $this->hasMany(Licencia::class, 'guardavida_id');
    }

    public function francoExcepciones()
    {
        return $this->hasMany(FrancoExcepcion::class);
    }

    public function intercambiosFrancoSolicitados()
    {
        return $this->hasMany(FrancoIntercambio::class, 'guardavida_solicitante_id');
    }

    public function intercambiosFrancoRecibidos()
    {
        return $this->hasMany(FrancoIntercambio::class, 'guardavida_destinatario_id');
    }

    /** Nombre del día franco fijo, o null si no lo configuró. */
    public function getDiaFrancoNombreAttribute(): ?string
    {
        if ($this->dia_franco === null) {
            return null;
        }

        return [
            0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado',
        ][$this->dia_franco] ?? null;
    }

    // ******************** Contadores ******************************************
    public function getAsistenciasCountAttribute()
    {
        return $this->asistencias()->count();
    }

    public function getIntervencionesCountAttribute()
    {
        return $this->intervenciones()->count();
    }

    public function getLicenciasCountAttribute()
    {
        return $this->licencias()->count();
    }

    public static function obtenerGuardavidas($idUser)
    {
        return self::with(['puesto.playa'])
            ->where('user_id', $idUser)
            ->first();
    }

    public static function showGuardavidaId($id)
    {
        $guardavida = Guardavida::where('id', $id)->first();

        return $guardavida ?? null;
    }
}
