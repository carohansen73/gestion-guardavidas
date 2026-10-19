<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Una inscripción (persona + temporada). El historial año a año son las
 * filas de temporadas anteriores del mismo usuario. Nombre, apellido, DNI y
 * email se leen de `users`; los datos fijos (talles, domicilio, etc.) de
 * `Perfil`.
 */
class Postulacion extends Model
{
    protected $table = 'postulaciones';

    public const ESTADO_BORRADOR = 'borrador';

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_ACEPTADA = 'aceptada';

    public const ESTADO_RECHAZADA = 'rechazada';

    public const ESTADO_INCOMPLETA = 'incompleta';

    /** Estados que puede asignar el admin al revisar (incompleta = "pedir correcciones": el postulante puede volver a editarla). */
    public const ESTADOS_REVISION = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_INCOMPLETA,
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
        'modificada_at',
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
        'modificada_at' => 'datetime',
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
        return $this->hasOne(Perfil::class, 'user_id', 'user_id');
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
    /**
     * Copia a esta inscripción los documentos reutilizables (foto, DNI) que la persona ya subió en
     * una temporada anterior y todavía no tiene acá. NO se duplica el archivo: el registro nuevo
     * apunta a la misma `ruta` (ahorra almacenamiento). Reemplazar uno en esta temporada crea un
     * archivo nuevo y no toca el de la anterior; ver `guardarDocumentos()` (solo borra un archivo
     * si ningún otro registro lo usa).
     * Es idempotente. Devuelve cuántos copió.
     */
    public function reutilizarDocumentosAnteriores(): int
    {
        $faltan = array_diff(PostulacionDocumento::tiposReutilizables(), $this->documentos()->pluck('tipo')->all());
        if ($faltan === []) {
            return 0;
        }

        $anteriores = PostulacionDocumento::whereIn('tipo', $faltan)
            ->whereHas('postulacion', fn ($p) => $p->where('user_id', $this->user_id)->where('id', '!=', $this->id))
            ->orderByDesc('postulacion_id')
            ->get()
            ->unique('tipo');

        $disco = \Illuminate\Support\Facades\Storage::disk('local');
        $copiados = 0;

        foreach ($anteriores as $anterior) {
            if (! $disco->exists($anterior->ruta)) {
                continue; // el archivo viejo ya no está: se le va a pedir de nuevo
            }

            $this->documentos()->create([
                'tipo' => $anterior->tipo,
                'ruta' => $anterior->ruta,
                'nombre_original' => $anterior->nombre_original,
                'mime' => $anterior->mime,
                'tamano' => $anterior->tamano,
            ]);
            $copiados++;
        }

        return $copiados;
    }

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

    /**
     * La inscripción que se le muestra al usuario como "la actual": la de la temporada con inscripción
     * abierta (null si todavía no la empezó) o, si no hay ninguna abierta, la última ya enviada cuya
     * temporada operativa todavía no empezó (así ve su resultado —y si fue seleccionado— aunque la
     * inscripción haya cerrado). Cuando empieza la temporada deja de mostrarse.
     */
    public static function vigenteDe(int $userId): ?self
    {
        $con = ['temporada', 'perfil', 'playas', 'documentos', 'playaAsignada', 'puestoAsignado'];

        if ($abierta = Temporada::conPostulacionAbierta()) {
            return static::with($con)->where('user_id', $userId)->where('temporada_id', $abierta->id)->first();
        }

        return static::with($con)
            ->where('user_id', $userId)
            ->where('estado', '!=', self::ESTADO_BORRADOR)
            ->whereHas('temporada', fn ($t) => $t->whereDate('fecha_inicio', '>', now()->toDateString()))
            ->latest('id')
            ->first();
    }

    /**
     * Puede completarla o corregirla el postulante.
     * - Borrador: solo con la ventana de postulación abierta (cerrada, no se suman inscripciones nuevas).
     * - Ya enviada (pendiente) o devuelta por el equipo (incompleta): también con la ventana cerrada,
     *   hasta que empiece la temporada, para poder corregir lo que pida el admin.
     * Aceptada y rechazada ya no se tocan.
     */
    public function editablePorPostulante(): bool
    {
        if (! in_array($this->estado, self::ESTADOS_EDITABLES, true)) {
            return false;
        }

        if ($this->temporada->postulacionAbierta()) {
            return true;
        }

        return $this->estado !== self::ESTADO_BORRADOR
            && $this->temporada->fecha_inicio->gt(Carbon::today());
    }

    /**
     * Por cada paso del formulario (1 a 4), si ya tiene todo lo que ese paso pide. Se calcula con los
     * datos cargados y no con "el último paso visitado": la persona puede completar los pasos en cualquier orden.
     *
     * @return array<int,bool>
     */
    public function pasosCompletos(): array
    {
        $subidos = $this->documentos->pluck('tipo')->all();

        return [
            1 => collect(array_keys(Perfil::OBLIGATORIOS))->every(fn ($campo) => filled($this->perfil?->{$campo})),
            2 => filled($this->disponible_desde) && filled($this->disponible_hasta),
            3 => collect(array_keys(PostulacionDocumento::TIPOS))
                ->every(fn ($tipo) => ! $this->documentoRequerido($tipo) || in_array($tipo, $subidos, true)),
            4 => $this->estado !== self::ESTADO_BORRADOR,
        ];
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

        foreach (Perfil::OBLIGATORIOS as $campo => $label) {
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
