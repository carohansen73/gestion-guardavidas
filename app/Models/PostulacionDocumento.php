<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Un archivo subido por el postulante para una inscripción. Los archivos
 * viven en el disco `local` (privado) y se sirven solo por rutas con control
 * de acceso — son documentos sensibles (DNI, antecedentes penales).
 */
class PostulacionDocumento extends Model
{
    protected $table = 'postulacion_documentos';

    public const MAX_KB = 5120;

    /**
     * Tipos de documento. Un tipo nuevo se agrega acá, sin migración.
     * `obligatorio` define qué hace falta para poder enviar la inscripción
     * (la licencia motonáutica solo aplica a quien la tiene).
     */
    public const TIPOS = [
        'foto_personal' => ['label' => 'Foto personal', 'obligatorio' => true, 'mimes' => ['jpg', 'jpeg', 'png']],
        'dni_frente' => ['label' => 'DNI (frente)', 'obligatorio' => true, 'mimes' => ['jpg', 'jpeg', 'png', 'pdf']],
        'dni_dorso' => ['label' => 'DNI (dorso)', 'obligatorio' => true, 'mimes' => ['jpg', 'jpeg', 'png', 'pdf']],
        'curriculum' => ['label' => 'Currículum', 'obligatorio' => true, 'mimes' => ['jpg', 'jpeg', 'png', 'pdf']],
        'libreta' => ['label' => 'Libreta de guardavidas', 'obligatorio' => true, 'mimes' => ['pdf']],
        'antecedentes_penales' => ['label' => 'Antecedentes penales', 'obligatorio' => true, 'mimes' => ['pdf']],
        'declaracion_jurada' => ['label' => 'Declaración jurada firmada', 'obligatorio' => true, 'mimes' => ['jpg', 'jpeg', 'png', 'pdf']],
        'licencia_motonautica' => ['label' => 'Licencia motonáutica', 'obligatorio' => false, 'mimes' => ['jpg', 'jpeg', 'png', 'pdf']],
    ];

    protected $fillable = [
        'postulacion_id',
        'tipo',
        'ruta',
        'nombre_original',
        'mime',
        'tamano',
    ];

    public function postulacion()
    {
        return $this->belongsTo(Postulacion::class);
    }

    public function getLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo]['label'] ?? $this->tipo;
    }
}
