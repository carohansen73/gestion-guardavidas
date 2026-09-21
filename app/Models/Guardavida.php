<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
