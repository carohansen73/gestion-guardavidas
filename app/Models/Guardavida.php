<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Guardavida extends Model
{
    use HasFactory;

    protected $perPage = 10;

    /** Datos personales que viven en `perfiles` (ver más abajo). */
    public const DATOS_PERSONALES = ['telefono', 'direccion', 'numero', 'piso_dpto'];

    protected $table = 'guardavidas'; // tu tabla real

    protected $fillable = [
        'funcion',
        'nombre',
        'apellido',
        'dni',
        'user_id',
        'playa_id',
        'puesto_id',
        'turno',
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

    /** Períodos de alta -> baja en el plantel (ver GuardavidaPeriodo). */
    public function periodos()
    {
        return $this->hasMany(GuardavidaPeriodo::class)->orderBy('desde')->orderBy('id');
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

    public function francoHistorial()
    {
        return $this->hasMany(GuardavidaFrancoHistorial::class)->orderByDesc('vigente_desde');
    }

    // ******************** Esquema de franco (días fijos por semana) ************

    /** Nombres en español de un conjunto de días (0=domingo..6=sábado), ej. "Sábado, Domingo". */
    public static function nombresDeDias(array $dias): string
    {
        $nombres = [
            0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado',
        ];

        return collect($dias)
            ->sort()
            ->map(fn ($d) => $nombres[$d] ?? '?')
            ->implode(', ');
    }

    /**
     * Esquema de franco vigente en una fecha puntual (para reportes de
     * períodos pasados, que no tienen por qué coincidir con el esquema
     * actual). Devuelve [] si no había ningún esquema cargado en esa fecha.
     *
     * Si la relación francoHistorial ya viene cargada (ej. desde
     * ResumenAsistenciaService, para no hacer una consulta por día), la
     * reutiliza en memoria en vez de volver a consultar la base.
     */
    public function diasFrancoVigentesEn($fecha): array
    {
        $fecha = Carbon::parse($fecha)->startOfDay();

        $historial = $this->relationLoaded('francoHistorial')
            ? $this->francoHistorial
            : $this->francoHistorial()->get();

        $vigente = $historial->first(function (GuardavidaFrancoHistorial $h) use ($fecha) {
            return $h->vigente_desde->lte($fecha)
                && ($h->vigente_hasta === null || $h->vigente_hasta->gte($fecha));
        });

        return $vigente->dias_franco ?? [];
    }

    /** Esquema de franco vigente hoy. */
    public function diasFrancoActuales(): array
    {
        return $this->diasFrancoVigentesEn(now());
    }

    /** Nombres del esquema vigente hoy, o null si no configuró ninguno. */
    public function getDiasFrancoNombresAttribute(): ?string
    {
        $dias = $this->diasFrancoActuales();

        return $dias === [] ? null : self::nombresDeDias($dias);
    }

    /**
     * Da de baja el esquema de franco vigente (si había uno) y da de alta el
     * nuevo a partir de hoy — así los reportes de fechas anteriores siguen
     * usando el esquema que regía en ese momento.
     */
    public function establecerDiasFranco(array $dias, ?int $porUserId = null): void
    {
        $dias = collect($dias)->map(fn ($d) => (int) $d)->unique()->sort()->values()->all();

        DB::transaction(function () use ($dias, $porUserId) {
            $vigente = $this->francoHistorial()->whereNull('vigente_hasta')->first();

            if ($vigente) {
                // Si ya se había cargado hoy, no dejamos un período de 0 días: lo reemplazamos.
                if ($vigente->vigente_desde->isToday()) {
                    $vigente->delete();
                } else {
                    $vigente->update(['vigente_hasta' => now()->subDay()->toDateString()]);
                }
            }

            $this->francoHistorial()->create([
                'dias_franco' => $dias,
                'vigente_desde' => now()->toDateString(),
                'vigente_hasta' => null,
                'creado_por_user_id' => $porUserId,
            ]);
        });
    }

    // ******************** Alta / baja en el plantel (períodos) ******************
    /** Períodos usando la relación cargada si la hay (así el presentismo no consulta una vez por día). */
    private function periodosCargados()
    {
        return $this->relationLoaded('periodos') ? $this->periodos : $this->periodos()->get();
    }

    public function periodoAbierto(): ?GuardavidaPeriodo
    {
        return $this->periodosCargados()->first(fn (GuardavidaPeriodo $p) => $p->hasta === null);
    }

    /**
     * ¿Estaba en el plantel ese día? Es lo que usa el presentismo para no
     * contar faltas fuera de las fechas de trabajo. Sin ningún período
     * registrado (guardavidas anteriores a este registro) se mantiene el
     * comportamiento de siempre: cuenta si hoy tiene rol de guardavida o encargado.
     */
    public function enPlantelEl($fecha): bool
    {
        $periodos = $this->periodosCargados();

        if ($periodos->isEmpty()) {
            return (bool) $this->user?->hasAnyRole(['guardavida', 'encargado']);
        }

        return $periodos->contains(fn (GuardavidaPeriodo $p) => $p->cubre($fecha));
    }

    /**
     * Da de alta en el plantel a partir de una fecha (si ya tiene un período
     * abierto, no hace nada). Si es un guardavida anterior a este registro,
     * antes deja asentado su período "histórico" hasta el día previo al alta,
     * para no cambiar los reportes de fechas pasadas. `$esNuevo` = acaba de crearse la fila
     * (no hay nada anterior que conservar).
     */
    public function darDeAlta(Carbon $desde, ?int $temporadaId = null, ?string $motivo = null, bool $esNuevo = false): GuardavidaPeriodo
    {
        if ($abierto = $this->periodos()->whereNull('hasta')->first()) {
            return $abierto;
        }

        if (! $esNuevo && ! $this->periodos()->exists()) {
            $this->periodos()->create([
                'desde' => null,
                'hasta' => $desde->copy()->subDay()->toDateString(),
                'motivo' => 'Anterior al registro de altas',
            ]);
        }

        $periodo = $this->periodos()->create([
            'temporada_id' => $temporadaId,
            'desde' => $desde->toDateString(),
            'hasta' => null,
            'motivo' => $motivo,
        ]);
        $this->unsetRelation('periodos');

        return $periodo;
    }

    /** Cierra el período abierto en esa fecha (la baja es inclusive: ese día todavía cuenta). */
    public function darDeBajaDelPeriodo(Carbon $hasta, ?string $motivo = null): void
    {
        $abierto = $this->periodos()->whereNull('hasta')->first();

        if ($abierto) {
            $fin = $abierto->desde && $hasta->lt($abierto->desde) ? $abierto->desde : $hasta;
            $abierto->update(['hasta' => $fin->toDateString(), 'motivo' => $motivo ?? $abierto->motivo]);
        } elseif (! $this->periodos()->exists()) {
            // Guardavida anterior a este registro: se asienta su período histórico, que termina acá.
            $this->periodos()->create(['desde' => null, 'hasta' => $hasta->toDateString(), 'motivo' => $motivo]);
        }

        $this->unsetRelation('periodos');
    }

    // ******************** Plantel activo (por rol) ******************************
    /**
     * Guardavidas del plantel actual: su usuario tiene rol `guardavida` o
     * `encargado`. Quien no fue seleccionado en la temporada vuelve a rol
     * `postulante` pero conserva su fila acá (historial de asistencias,
     * intervenciones, licencias), y por eso hay que filtrarlo de los
     * listados/selectores "de hoy". `$incluirIds` permite mantener a quienes
     * ya figuran en un registro viejo que se está editando (si no, al guardar
     * se perderían del registro).
     */
    public function scopeActivos($query, array $incluirIds = [])
    {
        return $query->where(function ($q) use ($incluirIds) {
            $q->whereHas('user', fn ($u) => $u->role(['guardavida', 'encargado']));

            if ($incluirIds !== []) {
                $q->orWhereIn('guardavidas.id', $incluirIds);
            }
        });
    }

    /**
     * Para reportes de PRESENTISMO de un período: el plantel actual MÁS
     * cualquiera que haya registrado asistencias en ese período, aunque
     * después haya vuelto a postulante. Así un reporte de una temporada
     * pasada (o del mes en curso) sigue incluyendo a todos los que trabajaron
     * en esas fechas, y quien no trabajó en el período y ya no está en el
     * plantel no aparece como "ausente" todos los días.
     */
    public function scopeActivosOConAsistenciaEn($query, $inicio, $fin)
    {
        return $query->where(function ($q) use ($inicio, $fin) {
            $q->whereHas('user', fn ($u) => $u->role(['guardavida', 'encargado']))
                ->orWhereHas('asistencias', fn ($a) => $a->whereBetween('fecha_hora', [$inicio, $fin]))
                // ...o que algún período de alta/baja se solape con el rango pedido.
                ->orWhereHas('periodos', fn ($p) => $p
                    ->where(fn ($d) => $d->whereNull('desde')->orWhere('desde', '<=', $fin))
                    ->where(fn ($h) => $h->whereNull('hasta')->orWhere('hasta', '>=', $inicio)));
        });
    }

    // ******************** Nombre/apellido/dni ***********************************
    // Estos accessors hacen que
    // $guardavida->nombre siga funcionando en toda la app sin tocar cada
    // vista/controller, pero leyendo de `user` — la columna de
    // guardavidas queda sin usarse (se borra más adelante, en un paso
    // aparte, una vez confirmado que todo anda bien así).
    public function getNombreAttribute()
    {
        return $this->user?->name;
    }

    public function getApellidoAttribute()
    {
        return $this->user?->lastname;
    }

    public function getDniAttribute()
    {
        return $this->user?->dni;
    }

    // ******************** Datos personales (viven en `perfiles`) ****************
    // Igual que nombre/apellido/dni: una sola fuente de verdad por persona.
    // Las columnas telefono/direccion/numero/piso_dpto de `guardavidas`
    // quedan sin usarse (se borran en un paso aparte). Para ESCRIBIR usar
    // guardarDatosPersonales().
    public function getTelefonoAttribute()
    {
        return $this->user?->perfil?->telefono;
    }

    public function getDireccionAttribute()
    {
        return $this->user?->perfil?->direccion;
    }

    public function getNumeroAttribute()
    {
        return $this->user?->perfil?->numero;
    }

    public function getPisoDptoAttribute()
    {
        return $this->user?->perfil?->piso_dpto;
    }

    /** Guarda telefono/direccion/numero/piso_dpto en el perfil de su usuario (lo crea si no existe). */
    public function guardarDatosPersonales(array $datos): void
    {
        $datos = Arr::only($datos, self::DATOS_PERSONALES);

        if ($datos === []) {
            return;
        }

        Perfil::updateOrCreate(['user_id' => $this->user_id], $datos);

        $this->user?->unsetRelation('perfil');
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
        return self::activos()->with(['puesto.playa'])
            ->where('user_id', $idUser)
            ->first();
    }

    public static function showGuardavidaId($id)
    {
        $guardavida = Guardavida::where('id', $id)->first();

        return $guardavida ?? null;
    }
}
