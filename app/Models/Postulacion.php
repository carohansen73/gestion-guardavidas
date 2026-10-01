<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Una inscripción (persona + temporada). El historial año a año son las
 * filas de temporadas anteriores del mismo usuario. Nombre, apellido, DNI y
 * email se leen de `users`; los datos fijos (talles, domicilio, etc.) de
 * `PostulacionPerfil`.
 */
class Postulacion extends Model
{
    protected $table = 'postulaciones';

    public const ESTADO_BORRADOR = 'borrador';
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_ACEPTADA = 'aceptada';
    public const ESTADO_RECHAZADA = 'rechazada';
    public const ESTADO_INCOMPLETA = 'incompleta';

    /** Estados que puede asignar el admin al revisar (incompleta se suma más adelante). */
    public const ESTADOS_REVISION = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_ACEPTADA,
        self::ESTADO_RECHAZADA,
    ];

    /** Estados en los que el postulante todavía puede corregir su inscripción. */
    private const ESTADOS_EDITABLES = [
        self::ESTADO_BORRADOR,
        self::ESTADO_PENDIENTE,
        self::ESTADO_INCOMPLETA,
    ];

    protected $fillable = [
        'user_id',
        'temporada_id',
        'estado',
        'enviada_at',
        'disponible_desde',
        'disponible_hasta',
        'observaciones',
        'revisado_por_user_id',
        'fecha_revision',
        'seleccionado',
        'playa_asignada_id',
        'puesto_asignado_id',
        'turno_asignado',
        'funcion_asignada',
    ];

    protected $casts = [
        'enviada_at' => 'datetime',
        'fecha_revision' => 'datetime',
        'disponible_desde' => 'date',
        'disponible_hasta' => 'date',
        'seleccionado' => 'boolean',
    ];

    // ------------------------------------------ Relaciones ------------------------------------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    public function revisadoPor()
    {
        return $this->belongsTo(User::class, 'revisado_por_user_id');
    }

    public function playaAsignada()
    {
        return $this->belongsTo(Playa::class, 'playa_asignada_id');
    }

    public function puestoAsignado()
    {
        return $this->belongsTo(Puesto::class, 'puesto_asignado_id');
    }

    /** Datos fijos de la persona (una fila por usuario, compartida entre temporadas). */
    public function perfil()
    {
        return $this->hasOne(PostulacionPerfil::class, 'user_id', 'user_id');
    }

    /** Playas preferidas, ordenadas por prioridad (1 = primera opción). */
    public function playas()
    {
        return $this->belongsToMany(Playa::class, 'postulacion_playas', 'postulacion_id', 'playa_id')
            ->withPivot('prioridad')
            ->orderByPivot('prioridad');
    }

    public function documentos()
    {
        return $this->hasMany(PostulacionDocumento::class);
    }

    // -------------------------------------------- Helpers --------------------------------------------
    public function documento(string $tipo): ?PostulacionDocumento
    {
        return $this->documentos->firstWhere('tipo', $tipo);
    }

    /**
     * ¿Hace falta este documento para poder enviar la inscripción? Los
     * obligatorios sí, salvo la declaración jurada: solo se exige cuando la
     * temporada ya tiene su modelo cargado (si no, el postulante no tendría
     * qué completar ni firmar).
     */
    public function documentoRequerido(string $tipo): bool
    {
        $config = PostulacionDocumento::TIPOS[$tipo] ?? null;
        if (! $config || ! $config['obligatorio']) {
            return false;
        }

        return $tipo !== 'declaracion_jurada' || filled($this->temporada->declaracion_jurada_modelo);
    }

    /** Puede corregirla el postulante: estado editable y ventana de postulación abierta. */
    public function editablePorPostulante(): bool
    {
        return in_array($this->estado, self::ESTADOS_EDITABLES, true)
            && $this->temporada->postulacionAbierta();
    }

    /**
     * Lo que todavía falta para poder enviar la inscripción (lista de
     * etiquetas legibles). Vacía = lista para enviar.
     *
     * @return list<string>
     */
    public function faltantes(): array
    {
        $faltantes = [];
        $perfil = $this->perfil;

        foreach (PostulacionPerfil::OBLIGATORIOS as $campo => $label) {
            if (blank($perfil?->{$campo})) {
                $faltantes[] = $label;
            }
        }

        if (! $this->disponible_desde || ! $this->disponible_hasta) {
            $faltantes[] = 'Disponibilidad (desde / hasta)';
        }

        $subidos = $this->documentos->pluck('tipo')->all();
        foreach (PostulacionDocumento::TIPOS as $tipo => $config) {
            if ($this->documentoRequerido($tipo) && ! in_array($tipo, $subidos, true)) {
                $faltantes[] = $config['label'];
            }
        }

        return $faltantes;
    }
}
