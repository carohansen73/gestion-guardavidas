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
     * `reutilizable`: si la persona ya lo subió en una temporada anterior, se copia solo a la
     * inscripción nueva (documentos que no cambian: foto, DNI). Los que se renuevan o vencen
     * (antecedentes, declaración jurada, libreta, etc.) hay que subirlos cada temporada.
     * Para que otro tipo se reutilice, basta con poner su `reutilizable` en true.
     */
    public const TIPOS = [
        'foto_personal' => ['reutilizable' => true, 'label' => 'Foto personal', 'obligatorio' => true, 'mimes' => ['jpg', 'jpeg', 'png']],
        'dni_frente' => ['reutilizable' => true, 'label' => 'DNI (frente)', 'obligatorio' => true, 'mimes' => ['jpg', 'jpeg', 'png', 'pdf']],
        'dni_dorso' => ['reutilizable' => true, 'label' => 'DNI (dorso)', 'obligatorio' => true, 'mimes' => ['jpg', 'jpeg', 'png', 'pdf']],
        'curriculum' => ['reutilizable' => false, 'label' => 'Currículum', 'obligatorio' => true, 'mimes' => ['jpg', 'jpeg', 'png', 'pdf']],
        'libreta' => ['reutilizable' => false, 'label' => 'Libreta de guardavidas', 'obligatorio' => true, 'mimes' => ['pdf']],
        'antecedentes_penales' => ['reutilizable' => false, 'label' => 'Antecedentes penales', 'obligatorio' => true, 'mimes' => ['pdf']],
        'declaracion_jurada' => ['reutilizable' => false, 'label' => 'Declaración jurada firmada', 'obligatorio' => true, 'mimes' => ['jpg', 'jpeg', 'png', 'pdf']],
        'licencia_motonautica' => ['reutilizable' => false, 'label' => 'Licencia motonáutica', 'obligatorio' => false, 'mimes' => ['jpg', 'jpeg', 'png', 'pdf']],
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

    /** @return array<int,string> tipos que se copian de una temporada a la siguiente */
    public static function tiposReutilizables(): array
    {
        return array_keys(array_filter(self::TIPOS, fn ($config) => $config['reutilizable'] ?? false));
    }

    public function getLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo]['label'] ?? $this->tipo;
    }
}
