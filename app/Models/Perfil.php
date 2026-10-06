<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Datos personales de una persona (una sola fila por usuario): contacto,
 * domicilio, talles, etc. Los usan tanto las postulaciones (se precargan en
 * cada inscripción nueva) como los guardavidas (`Guardavida` los lee de acá).
 * Nombre, apellido, DNI y email NO viven acá: se leen de `users`.
 */
class Perfil extends Model
{
    protected $table = 'perfiles';

    public const GENEROS = ['Femenino', 'Masculino', 'Otro', 'Prefiero no decirlo'];

    public const GRUPOS_SANGUINEOS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    /** Campos que tienen que estar completos para poder enviar la inscripción. */
    public const OBLIGATORIOS = [
        'telefono' => 'Teléfono',
        'direccion' => 'Domicilio (calle)',
        'numero' => 'Domicilio (número)',
        'fecha_nacimiento' => 'Fecha de nacimiento',
        'genero' => 'Género',
        'grupo_sanguineo' => 'Grupo sanguíneo',
        'numero_libreta' => 'N° de libreta de guardavidas',
        'talle_remera' => 'Talle de remera',
        'talle_pantalon' => 'Talle de pantalón',
        'talle_campera' => 'Talle de campera',
        'talle_traje_bano' => 'Talle de traje de baño',
    ];

    protected $fillable = [
        'user_id',
        'telefono',
        'direccion',
        'numero',
        'piso_dpto',
        'fecha_nacimiento',
        'genero',
        'grupo_sanguineo',
        'numero_libreta',
        'talle_remera',
        'talle_pantalon',
        'talle_campera',
        'talle_traje_bano',
        'obra_social_nombre',
        'obra_social_numero_afiliado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** "Tiene obra social" no se guarda: es que haya un nombre cargado. */
    public function tieneObraSocial(): bool
    {
        return filled($this->obra_social_nombre);
    }
}
