@extends('layouts.app')

{{-- Historial de asistencias de una persona (celular y escritorio: la misma pantalla, que se acomoda por ancho).
     El período es un mes (flechas) o fechas a mano; arriba el resumen, después el calendario de puntitos y la lista
     de días, y al tocar un día se abre el detalle con cada fichaje (hora, puesto, distancia al puesto y precisión
     del GPS). Lo de franco (solo admin/encargado) queda plegado al final. --}}
@section('content')
    @php
        $nombre = trim("{$guardavida->apellido} {$guardavida->nombre}") ?: 'Sin nombre';
        $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $hoy = \Carbon\Carbon::today()->toDateString();

        // Un estado = un color (punto del calendario y etiqueta). Clases completas para que Tailwind las encuentre.
        $meta = [
            'ASISTIÓ' => ['texto' => 'Asistió', 'punto' => 'bg-emerald-500', 'chip' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300', 'tile' => 'bg-emerald-100 dark:bg-emerald-900/40', 'num' => 'text-emerald-700 dark:text-emerald-300'],
            'FALTA' => ['texto' => 'Falta', 'punto' => 'bg-red-500', 'chip' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300', 'tile' => 'bg-red-100 dark:bg-red-900/40', 'num' => 'text-red-700 dark:text-red-300'],
            'FRANCO' => ['texto' => 'Franco', 'punto' => 'bg-sky-400', 'chip' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300', 'tile' => 'bg-sky-100 dark:bg-sky-900/40', 'num' => 'text-sky-700 dark:text-sky-300'],
            'LICENCIA' => ['texto' => 'Licencia', 'punto' => 'bg-violet-500', 'chip' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300', 'tile' => 'bg-violet-100 dark:bg-violet-900/40', 'num' => 'text-violet-700 dark:text-violet-300'],
        ];

        $hora = fn ($valor) => $valor ? \Carbon\Carbon::parse($valor)->format('H:i') : null;

        // Una línea por día para la lista, y el detalle completo (para el modal) indexado por fecha.
        $dias = collect($historial)->map(function ($h) use ($hora, $diasSemana, $meta) {
            $fecha = \Carbon\Carbon::parse($h['fecha']);
            $ingreso = $hora($h['ingreso'] ?? null);
            $egreso = $hora($h['egreso'] ?? null);

            $linea = match ($h['estado']) {
                'ASISTIÓ' => $ingreso.($egreso ? ' – '.$egreso : ' · Sin egreso').' · '.($h['puesto'] ?? '-'),
                'FALTA' => 'Sin fichaje ni licencia',
                default => $h['detalle'] ?? '',
            };

            return $h + [
                'dia' => $fecha->format('d'),
                'dia_semana' => mb_substr($diasSemana[$fecha->dayOfWeek], 0, 3),
                'linea' => $linea,
                'meta' => $meta[$h['estado']] ?? $meta['FALTA'],
                'modal' => [
                    'titulo' => $diasSemana[$fecha->dayOfWeek].' '.$fecha->format('d/m/Y'),
                    'estado' => ($meta[$h['estado']] ?? $meta['FALTA'])['texto'],
                    'chip' => ($meta[$h['estado']] ?? $meta['FALTA'])['chip'],
                    'detalle' => $h['detalle'] ?? ($h['estado'] === 'FALTA' ? 'No fichó, no tiene licencia y no era su franco.' : null),
                    'ingreso' => $ingreso,
                    'egreso' => $egreso,
                    'fichajes' => $h['fichajes'] ?? [],
                ],
            ];
        });
        $detalles = $dias->mapWithKeys(fn ($d) => [$d['fecha'] => $d['modal']]);
        $porFecha = $dias->keyBy('fecha');

        // Calendario: semanas completas (lunes a domingo) que cubren el período. Con períodos largos (una temporada)
        // no tiene sentido una grilla de veinte filas: queda solo la lista.
        $mostrarCalendario = $inicio->diffInDays($fin) <= 62;
        $primerDia = $inicio->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $ultimoDia = $fin->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $periodo = $inicio->format('d/m').' – '.$fin->format('d/m/Y');
        $r = $resumen;
        $pct = $r['porcentaje'] ?? null;
        // El porcentaje cambia de color según qué tan bien viene (mismos cortes que en la lista).
        $pctColor = match (true) {
            $pct === null => 'text-gray-400 dark:text-gray-500',
            $pct >= 90 => 'text-emerald-600 dark:text-emerald-400',
            $pct >= 75 => 'text-amber-600 dark:text-amber-400',
            default => 'text-red-600 dark:text-red-400',
        };
        $pctBarra = match (true) {
            $pct === null => 'bg-gray-400',
            $pct >= 90 => 'bg-emerald-500',
            $pct >= 75 => 'bg-amber-500',
            default => 'bg-red-500',
        };
        $rutaMes = fn ($mes) => route('asistencias.guardavida', [$guardavida->id, 'mes' => $mes]);
    @endphp

    <div class="text-gray-600 dark:text-gray-100 body-font sm:px-4"
        x-data="{ dia: null, detalles: @js($detalles), abierto: false, francoModal: false, francoAbierto: false,
            abrir(f) { this.dia = this.detalles[f] ?? null; } }"
        @keydown.escape.window="dia = null; francoModal = false">

        {{-- Volver (igual que en la ficha del guardavidas): conserva los filtros del listado --}}
        <button type="button" class="mb-3 inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-sky-100 sm:mt-4" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i>
            <span class="hover:underline">Volver</span>
        </button>

        {{-- Encabezado --}}
        <div class="mb-4 flex items-start gap-3">
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-2xl font-bold tracking-tight text-gray-700 dark:text-white md:text-3xl">{{ $nombre }}</h2>
                <p class="truncate text-sm text-gray-500 dark:text-gray-300">
                    {{ $guardavida->playa->nombre ?? 'Sin playa' }} · {{ $guardavida->puesto->nombre ?? 'Sin puesto' }}
                    · Franco: {{ $guardavida->dias_franco_nombres ?: 'no configurado' }}
                </p>
            </div>
            <a href="{{ route('export.playas', ['guardavida_id' => $guardavida->id, 'tipo' => 'asistencia-guardavida', 'inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()]) }}"
                title="Descargar Excel de este período" aria-label="Descargar Excel de este período"
                class="shrink-0 rounded bg-emerald-600 px-3 py-1 text-gray-100 hover:bg-emerald-500 hover:shadow-lg dark:bg-emerald-700 dark:hover:bg-teal-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
            </a>
        </div>

        @if (session('success'))
            <div class="my-2 rounded bg-green-100 p-3 text-green-700 dark:bg-green-900/40 dark:text-green-300">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="my-2 rounded bg-red-100 p-3 text-red-700 dark:bg-red-900/40 dark:text-red-300">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="my-2 rounded bg-red-100 p-3 text-red-700 dark:bg-red-900/40 dark:text-red-300">{{ $errors->first() }}</div>
        @endif

        @if ($avisoPlantel)
            <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-200">
                <i class="fas fa-circle-info me-1" aria-hidden="true"></i> {{ $avisoPlantel }}
            </div>
        @endif

        {{-- Mes (flechas) + período a mano --}}
        <div class="mb-4 max-w-md space-y-2">
            <div class="flex items-center overflow-hidden rounded-2xl border border-gray-300 bg-white shadow-sm dark:border-gray-600 dark:bg-gray-700">
                @if ($mesNav)
                    <a href="{{ $rutaMes($mesNav['anterior']) }}" aria-label="Mes anterior"
                        class="flex h-10 w-10 shrink-0 items-center justify-center text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                    </a>
                @endif

                <button type="button" @click="abierto = !abierto"
                    class="flex h-10 min-w-0 flex-1 items-center justify-center gap-1.5 px-2 text-sm font-medium text-gray-800 dark:text-gray-100">
                    <span class="truncate">{{ $mesNav['nombre'] ?? $periodo }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                        class="h-4 w-4 shrink-0 transition" :class="abierto ? 'rotate-180' : ''" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>

                @if ($mesNav)
                    @if ($mesNav['siguiente'])
                        <a href="{{ $rutaMes($mesNav['siguiente']) }}" aria-label="Mes siguiente"
                            class="flex h-10 w-10 shrink-0 items-center justify-center text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                        </a>
                    @else
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center text-gray-300 dark:text-gray-500" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                        </span>
                    @endif
                @endif
            </div>

            <form method="GET" x-show="abierto" x-cloak
                class="space-y-3 rounded-md border border-gray-200 bg-gray-50 p-3 dark:border-gray-600 dark:bg-gray-700">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Desde</label>
                        <input type="date" name="inicio" id="inicio" value="{{ $inicio->toDateString() }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hasta</label>
                        <input type="date" name="fin" id="fin" value="{{ $fin->toDateString() }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('asistencias.guardavida', $guardavida->id) }}" class="text-sm text-gray-600 hover:underline dark:text-gray-300">Mes actual</a>
                    <button class="rounded-full bg-sky-500 px-5 py-2 text-sm text-white shadow hover:bg-sky-400">Filtrar</button>
                </div>
            </form>
        </div>

        {{-- Resumen del período (el mismo cálculo que la lista de asistencias) --}}
        @if ($r)

            <section class="glass mb-4 rounded-3xl bg-white px-5 py-6 text-gray-600 dark:text-gray-300">
                <h2 class="text-lg text-gray-700 dark:text-gray-50">
                    <i class="fas fa-chart-line text-sky-600"></i>
                    Resumen
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $periodo }}</p>

                <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Asistencia
                            <i class="fas fa-percent text-sky-600 dark:text-sky-400" aria-hidden="true"></i>
                        </div>
                        <div class="mt-2 text-3xl font-bold {{ $pctColor }}">{{ $pct !== null ? $pct.'%' : '-' }}</div>
                        <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-600">
                            <div class="h-1.5 rounded-full {{ $pctBarra }}" style="width: {{ (int) min(max($pct ?? 0, 0), 100) }}%"></div>
                        </div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $pct !== null ? $r['asistencias'].' de '.$r['dias_habiles'].' días hábiles' : 'Sin días hábiles' }}
                        </div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Asistió
                            <i class="fas fa-check text-emerald-600 dark:text-emerald-400" aria-hidden="true"></i>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $r['asistencias'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Días con fichaje</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Faltas
                            <i class="fas fa-xmark text-red-600 dark:text-red-400" aria-hidden="true"></i>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $r['faltas'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Sin fichaje ni licencia</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Licencias
                            <i class="fas fa-file-medical text-violet-600 dark:text-violet-400" aria-hidden="true"></i>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $r['licencias'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Días de licencia</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Francos
                            <i class="fas fa-umbrella-beach text-sky-600 dark:text-sky-400" aria-hidden="true"></i>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $r['francos'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Días libres</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Fuera de rango
                            <i class="fas fa-location-dot text-amber-600 dark:text-amber-400" aria-hidden="true"></i>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $r['fuera_de_rango'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">+200m del puesto</div>
                    </div>
                </div>
            </section>
        @endif

        <div class="grid gap-4 {{ $mostrarCalendario ? 'lg:grid-cols-[22rem_minmax(0,1fr)] lg:items-start' : '' }}">

            {{-- Calendario de puntitos --}}
            @if ($mostrarCalendario)
                <section class="glass rounded-3xl p-4 sm:p-5">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h3 class="font-display text-sm font-bold uppercase tracking-wide">Calendario</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Tocá un día para ver el detalle</span>
                    </div>
                    <div class="grid grid-cols-7 gap-y-1 text-center">
                        @foreach (['L', 'M', 'M', 'J', 'V', 'S', 'D'] as $letra)
                            <span class="pb-1 text-[11px] font-medium text-gray-400 dark:text-gray-500">{{ $letra }}</span>
                        @endforeach

                        @for ($d = $primerDia->copy(); $d->lte($ultimoDia); $d->addDay())
                            @php
                                $f = $d->toDateString();
                                $enPeriodo = $d->betweenIncluded($inicio->copy()->startOfDay(), $fin->copy()->endOfDay());
                                $item = $porFecha->get($f);
                            @endphp

                            @if (! $enPeriodo)
                                <span class="h-11"></span>
                            @elseif ($item)
                                <button type="button" @click="abrir('{{ $f }}')"
                                    aria-label="{{ $item['modal']['titulo'] }}: {{ $item['modal']['estado'] }}"
                                    class="flex h-11 flex-col items-center justify-center gap-1 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 {{ $f === $hoy ? 'ring-1 ring-sky-500' : '' }}">
                                    <span class="text-xs {{ $f === $hoy ? 'font-bold text-sky-600 dark:text-sky-400' : 'text-gray-700 dark:text-gray-200' }}">{{ $d->day }}</span>
                                    <span class="h-2.5 w-2.5 rounded-full {{ $item['meta']['punto'] }} {{ ($item['fuera_de_rango'] ?? false) ? 'ring-2 ring-amber-400' : '' }}"></span>
                                </button>
                            @else
                                {{-- Día sin registro (fuera de su alta/baja en el plantel): sin punto --}}
                                <span class="flex h-11 flex-col items-center justify-center gap-1">
                                    <span class="text-xs text-gray-300 dark:text-gray-600">{{ $d->day }}</span>
                                </span>
                            @endif
                        @endfor
                    </div>

                    <div class="mt-3 flex flex-wrap gap-x-3 gap-y-1 border-t border-gray-200 pt-3 text-xs text-gray-600 dark:border-gray-700 dark:text-gray-300">
                        @foreach ($meta as $m)
                            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full {{ $m['punto'] }}"></span>{{ $m['texto'] }}</span>
                        @endforeach
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-amber-400"></span>Revisar ubicación</span>
                    </div>
                </section>
            @endif

            {{-- Lista de días (el más nuevo primero) --}}
            <section class="glass overflow-hidden rounded-3xl">
                <div class="flex items-center justify-between gap-3 px-4 pb-2 pt-4 sm:px-5 sm:pt-5">
                    <h3 class="font-display text-sm font-bold uppercase tracking-wide">Días</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $dias->count() }} {{ $dias->count() === 1 ? 'día' : 'días' }} · el más reciente primero</span>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($dias as $d)
                        <button type="button" @click="abrir('{{ $d['fecha'] }}')"
                            class="flex w-full items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700/50 sm:px-5">
                            {{-- Cuadradito del color del estado, como los íconos de las cards del panel --}}
                            <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-2xl {{ $d['meta']['tile'] }}">
                                <p class="text-lg font-bold leading-none {{ $d['meta']['num'] }}">{{ $d['dia'] }}</p>
                                <p class="mt-0.5 text-[10px] font-medium uppercase {{ $d['meta']['num'] }} opacity-80">{{ $d['dia_semana'] }}</p>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $d['meta']['chip'] }}">{{ $d['meta']['texto'] }}</span>
                                    @if ($d['fuera_de_rango'] ?? false)
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-300"
                                            title="El fichaje quedó a más de 200 m del puesto (según el GPS): revisar">⚠ Revisar ubicación</span>
                                    @endif
                                </div>
                                @if ($d['linea'] !== '')
                                    <p class="mt-1 truncate text-sm text-gray-500 dark:text-gray-300">{{ $d['linea'] }}</p>
                                @endif
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 shrink-0 text-gray-400 dark:text-gray-500" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                        </button>
                    @empty
                        <p class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-300">No hay días para mostrar en este período.</p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Franco (solo admin/encargado): plegado para que no tape el historial --}}
        @if ($esAdmin)
            <section class="glass mt-4 rounded-3xl">
                <button type="button" @click="francoAbierto = !francoAbierto"
                    class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left sm:px-5">
                    <span class="font-medium text-gray-800 dark:text-gray-100"><i class="fas fa-bed me-2 text-sky-600 dark:text-sky-400"></i>Franco y cambios de franco</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                        class="h-4 w-4 shrink-0 text-gray-500 transition dark:text-gray-400" :class="francoAbierto ? 'rotate-180' : ''" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>

                <div x-show="francoAbierto" x-cloak class="space-y-4 border-t border-gray-200 px-4 py-4 dark:border-gray-700">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm text-gray-700 dark:text-gray-200"><strong>Franco fijo:</strong> {{ $guardavida->dias_franco_nombres ?: 'No configurado' }}</p>
                        <button type="button" @click="francoModal = true"
                            class="rounded bg-sky-600 px-3 py-1 text-sm text-white hover:bg-sky-500">
                            <i class="fas fa-calendar-plus me-1"></i> Cargar cambio de franco
                        </button>
                    </div>

                    @if ($guardavida->francoHistorial->count() > 1)
                        <div>
                            <p class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Historial de franco</p>
                            <ul class="divide-y divide-gray-200 text-sm text-gray-700 dark:divide-gray-700 dark:text-gray-200">
                                @foreach ($guardavida->francoHistorial as $h)
                                    <li class="py-1">
                                        {{ $h->vigente_desde->format('d/m/Y') }} — {{ $h->vigente_hasta ? $h->vigente_hasta->format('d/m/Y') : 'hoy' }}:
                                        franco los <strong>{{ $h->dias_nombres }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($francoExcepciones && $francoExcepciones->count())
                        <div>
                            <p class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Cambios de franco cargados</p>
                            <ul class="divide-y divide-gray-200 text-sm text-gray-700 dark:divide-gray-700 dark:text-gray-200">
                                @foreach ($francoExcepciones as $exc)
                                    <li class="flex items-center justify-between gap-3 py-1">
                                        <span>
                                            {{ $exc->fecha->format('d/m/Y') }} —
                                            {{ $exc->tipo === 'agregado' ? 'pasa a ser franco' : 'deja de ser franco' }}
                                            @if ($exc->motivo)
                                                <span class="text-gray-500 dark:text-gray-400">({{ $exc->motivo }})</span>
                                            @endif
                                        </span>
                                        <form action="{{ route('franco-excepcion.destroy', $exc->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este cambio de franco?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-400 dark:text-red-400 dark:hover:text-red-300">Eliminar</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($francoIntercambios && $francoIntercambios->count())
                        <div>
                            <p class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Intercambios de franco entre compañeros</p>
                            <ul class="divide-y divide-gray-200 text-sm text-gray-700 dark:divide-gray-700 dark:text-gray-200">
                                @foreach ($francoIntercambios as $i)
                                    @php($esSolicitante = $i->guardavida_solicitante_id === $guardavida->id)
                                    <li class="py-1">
                                        @if ($esSolicitante)
                                            Le pidió a <strong>{{ $i->destinatario->apellido }} {{ $i->destinatario->nombre }}</strong>
                                        @else
                                            <strong>{{ $i->solicitante->apellido }} {{ $i->solicitante->nombre }}</strong> le pidió
                                        @endif
                                        cambiar el {{ $i->fecha_propia->format('d/m/Y') }} por el {{ $i->fecha_deseada->format('d/m/Y') }} —
                                        <span class="text-xs font-medium uppercase {{ $i->estado === 'pendiente' ? 'text-amber-600 dark:text-amber-400' : ($i->estado === 'aceptado' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400') }}">{{ $i->estado }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- Detalle de un día: cada fichaje con hora, puesto, distancia al puesto y precisión del GPS --}}
        <div x-show="dia" x-cloak class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 sm:items-center sm:p-4" @click.self="dia = null">
            <div class="max-h-[85vh] w-full overflow-y-auto rounded-t-3xl bg-white p-5 shadow-xl dark:bg-gray-800 sm:max-w-md sm:rounded-3xl" role="dialog" aria-modal="true">
                <template x-if="dia">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white" x-text="dia.titulo"></p>
                                <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-medium" :class="dia.chip" x-text="dia.estado"></span>
                            </div>
                            <button type="button" @click="dia = null" aria-label="Cerrar"
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <p x-show="dia.detalle" class="mt-3 text-sm text-gray-600 dark:text-gray-300" x-text="dia.detalle"></p>

                        <div x-show="dia.fichajes.length" class="mt-4 space-y-2">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <span x-text="dia.fichajes.length"></span>
                                <span x-text="dia.fichajes.length === 1 ? 'fichaje' : 'fichajes'"></span>
                                <span x-show="!dia.egreso" class="text-amber-600 dark:text-amber-400"> · sin egreso registrado</span>
                            </p>
                            <template x-for="(f, i) in dia.fichajes" :key="i">
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-700">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-base font-semibold text-gray-900 dark:text-white"><span x-text="f.hora"></span> hs</p>
                                        <span x-show="f.fuera_de_rango" class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">⚠ Revisar ubicación</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300" x-text="f.puesto"></p>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                        <span x-show="f.distancia !== null">A <strong x-text="f.distancia + ' m'"></strong> del puesto</span>
                                        <span x-show="f.distancia === null" class="text-gray-400 dark:text-gray-500">Sin distancia al puesto</span>
                                        <span x-show="f.precision !== null"> · GPS ± <span x-text="f.precision"></span> m</span>
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Cargar cambio de franco (admin/encargado) --}}
        @if ($esAdmin)
            <div x-show="francoModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="francoModal = false">
                <div class="max-h-[90vh] w-full max-w-sm overflow-y-auto rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-800" x-data="{ accion: 'mover' }">
                    <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200">Cargar cambio de franco</h2>

                    <div class="mb-3 rounded border border-amber-200 bg-amber-50 p-2 text-xs text-amber-800 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-200">
                        Si esto es parte de un intercambio con un compañero, acordate que este formulario solo actualiza a
                        <strong>{{ $guardavida->nombre }} {{ $guardavida->apellido }}</strong>. Andá también al perfil de la
                        otra persona y cargale el cambio que le corresponde — o, mejor, pedile que lo resuelvan juntos desde
                        "Cambios de Franco", así queda todo hecho en un solo paso.
                    </div>

                    <form action="{{ route('franco-excepcion.store', $guardavida->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">¿Qué querés hacer?</label>
                            <select name="accion" x-model="accion" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="mover">Correr el franco a otro día</option>
                                <option value="cancelar">Cancelar un franco, sin reemplazo (ej. vino a trabajar)</option>
                            </select>
                        </div>

                        <template x-if="accion === 'mover'">
                            <div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Día que deja de ser franco (tiene que ser su franco fijo: {{ $guardavida->dias_franco_nombres ?: 'sin configurar' }}):
                                    </label>
                                    <input type="date" name="fecha_origen" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Día que pasa a ser franco:</label>
                                    <input type="date" name="fecha_destino" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                        </template>

                        <template x-if="accion === 'cancelar'">
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Día que deja de ser franco:</label>
                                <input type="date" name="fecha" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                        </template>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo (opcional):</label>
                            <input type="text" name="motivo" maxlength="255" placeholder="Ej: se cambió con Fulano"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="mt-4 flex items-center justify-end gap-4">
                            <button type="button" @click="francoModal = false" class="text-sm text-gray-600 hover:underline dark:text-gray-300">Cancelar</button>
                            <button type="submit" class="rounded-full bg-sky-500 px-5 py-2 text-white shadow hover:bg-sky-400">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
