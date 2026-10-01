@extends('layouts.app')

<x-slot name="header"></x-slot>

@section('content')

@if(session('show_guardavida_setup'))
    @include('dashboard.partials.modal-setup')
@endif

@if(session('show_franco_setup'))
    @include('dashboard.partials.aviso-franco-pendiente')
@endif


   {{-- <div class="flex-1 px-4 pb-28 pt-5 lg:px-8 lg:pb-10"> --}}

<div class="">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ ucfirst(now()->locale('es')->isoFormat('dddd D [de] MMMM')) }}
            </p>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                Hola, {{ Auth::user()->name }} 👋
            </h1>
        </div>
        <x-role-badge :rol="\App\Enums\RolUsuario::principal(Auth::user())" size="w-4 h-4" />
    </div>

      <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
        Mostrando datos de
        <span class="tituloPlayaSeleccionada font-medium text-sky-600 dark:text-sky-400">{{ $esAdmin ? 'todas las playas' : (Auth::user()->guardavida->playa->nombre ?? 'tu playa') }}</span>
    </p>

    @if ($esAdmin)
        <!-- Filtro playas: va acá, antes de las cards, para que se entienda que las
             cards/gráfico de abajo se pueden filtrar por playa (en mobile, la
             columna del filtro quedaba después de las cards y no se entendía).
             Solo para admin/superadmin: el resto ya está limitado a la suya. -->
        <div class="mb-5 flex flex-wrap gap-2 items-center">
            <button
                class="btn-filtro bg-sky-600 text-white px-3 py-1 rounded-full text-xs font-medium transition"
                data-playa=""
                data-nombre="Todas">
                Todas
            </button>
            <!-- Botones por cada playa -->
            @foreach($playas as $playa)
                <button
                    class="btn-filtro bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 px-3 py-1 rounded-full text-xs font-medium transition"
                    data-playa="{{ $playa->id }}"
                    data-nombre="{{ $playa->nombre }}">
                    {{ $playa->nombre }}
                </button>
            @endforeach
        </div>
    @endif
</div>


{{-- Seccion inicial - Bandera del dia y counters guardavidas, intervencioens, licencias y novedades de materiales --}}
<section class="grid grid-cols-2 gap-3 lg:grid-cols-4 mb-3">

    {{-- Bandera del día (o carrusel de banderas por playa si es admin) --}}
    @if($esGuardavidaOEncargado)
        <div class="col-span-2 lg:row-span-2">
            @include('dashboard.partials.bandera')
        </div>
    @else
        @include('dashboard.partials.bandera')
    @endif

    {{-- "Mi turno": guardavida y encargado (no admin) — los dos fichan.
         Ocupa el ancho que le sobra al lado de la bandera en desktop/tablet
         (lg:), y se apila debajo en mobile (col-span-2 ya la hace ocupar la
         fila completa ahí). --}}
    @if($esGuardavidaOEncargado)
        @php
            // No hay columna "tipo" (ingreso/egreso) en asistencias — cada
            // fichaje es solo un evento con su fecha_hora. Se infiere el
            // estado por cantidad de fichajes de hoy: 0 = falta ingreso,
            // 1 = falta egreso, 2+ = turno completo.
            $totalFichajesHoy = $asistenciasHoyPropias->count();
            $ingresoHoy = $asistenciasHoyPropias->first();
            $egresoHoy = $totalFichajesHoy >= 2 ? $asistenciasHoyPropias->last() : null;
            $puedeFichar = $isMobile || $isTablet;
            $colorEstado = match(true) {
                $totalFichajesHoy === 0 => 'amber',
                $totalFichajesHoy === 1 => 'sky',
                default => 'emerald',
            };
        @endphp
        <div class="col-span-2 lg:row-span-2">
            @if($puedeFichar)
                <a href="{{ route('activeCamera') }}" class="glass block h-full rounded-3xl p-4 flex flex-col transition hover:-translate-y-0.5">
            @else
                <div class="glass h-full rounded-3xl p-4 flex flex-col">
            @endif
                <div class="flex items-center gap-3">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-{{ $colorEstado }}-100 dark:bg-{{ $colorEstado }}-900/40">
                        @if($totalFichajesHoy === 0)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-amber-600 dark:text-amber-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-{{ $colorEstado }}-600 dark:text-{{ $colorEstado }}-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold">Mi turno</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ Auth::user()->guardavida->turno == 'M' ? 'Mañana' : 'Tarde' }} · {{ Auth::user()->guardavida->puesto->nombre ?? 'Sin puesto asignado' }}
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    @if($totalFichajesHoy === 0)
                        <p class="text-lg font-bold text-amber-600 dark:text-amber-400">Pendiente de fichar</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Todavía no registraste tu ingreso de hoy.
                        </p>
                    @elseif($totalFichajesHoy === 1)
                        <p class="text-lg font-bold text-sky-600 dark:text-sky-400">Ingreso registrado</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Fichaste ingreso a las {{ $ingresoHoy->fecha_hora->format('H:i') }}{{ $ingresoHoy->puesto ? ' en '.$ingresoHoy->puesto->nombre : '' }}. Falta fichar tu egreso.
                        </p>
                    @else
                        <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Turno completo</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Ingreso {{ $ingresoHoy->fecha_hora->format('H:i') }} · Egreso {{ $egresoHoy->fecha_hora->format('H:i') }}.
                        </p>
                    @endif
                </div>

                @if($puedeFichar)
                    <p class="mt-auto pt-4 text-xs font-semibold text-{{ $colorEstado }}-600 dark:text-{{ $colorEstado }}-400">
                        @if($totalFichajesHoy === 0)
                            Tocá para fichar tu ingreso →
                        @elseif($totalFichajesHoy === 1)
                            Tocá para fichar tu egreso →
                        @else
                            Tocá para volver a escanear →
                        @endif
                    </p>
                @else
                    <div class="mt-auto pt-4">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            El escaneo QR está disponible desde celular o tablet.
                        </p>
                    </div>
                @endif
            @if($puedeFichar)
                </a>
            @else
                </div>
            @endif
        </div>
    @endif

    {{-- Atajos. --}}
    @can('agregar_intervencion')
        <a href="{{ route('intervencion.create') }}" class="col-span-2 lg:col-span-1 flex items-center gap-3 rounded-2xl bg-red-500 p-4 text-left text-slate-50 shadow-[var(--shadow-glass)] transition hover:-translate-y-0.5">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-slate-50/15">
               <svg class="lucide lucide-plus h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="M12 5v14"></path>
            </svg>
            </span>
            <span class="min-w-0">
               <span class="block text-sm font-bold">Agregar Nueva Intervención</span>
               <span class="block text-xs opacity-80">Rescate, primeros auxilios, etc.</span>
            </span>
        </a>
    @endcan

    @can('agregar_novedad_material')
        <a href="{{ route('novedad-de-material.create') }}" class="bg-gradient-to-r from-blue-600 to-sky-500 dark:from-blue-900 dark:to-sky-700 col-span-2 lg:col-span-1 flex items-center gap-3 rounded-2xl p-4 text-left text-slate-50 shadow-[var(--shadow-glass)] transition hover:-translate-y-0.5">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-slate-50/15">
                <svg class="lucide lucide-megaphone h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 6a13 13 0 0 0 8.4-2.8A1 1 0 0 1 21 4v12a1 1 0 0 1-1.6.8A13 13 0 0 0 11 14H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"></path><path d="M6 14a12 12 0 0 0 2.4 7.2 2 2 0 0 0 3.2-2.4A8 8 0 0 1 10 14"></path><path d="M8 6v8"></path></svg>
            </span>
            <span class="min-w-0">
                <span class="block text-sm font-bold">Registrar Novedad</span>
                <span class="block text-xs opacity-80">Actualización de materiales</span>
            </span>
        </a>
    @endcan

    @can('ver_novedad_material')
        <x-shortcut label="Novedades materiales" :value="$panelNovedadesMateriales" href="{{ route('novedad-de-material.index') }}">
            <x-icon name="megaphone" class="h-5 w-5 text-sky-600" />
        </x-shortcut>
    @endcan

    @can('ver_intervencion')
        <x-shortcut label="Intervenciones" :value="$panelIntervenciones" href="{{ route('intervencion.index') }}">
            <x-icon name="siren" class="h-5 w-5 text-sky-600" />
        </x-shortcut>
    @endcan
</section>




 {{-- Resumen numérico (counters).
      TODO: pensar mejor título/subtítulo (quedó el de placeholder). --}}

@php
    $tieneResumen = Auth::user()->can('ver_guardavida')
        || Auth::user()->can('ver_intervencion')
        || Auth::user()->can('ver_licencia')
        || Auth::user()->can('ver_novedad_material');
@endphp

@if($tieneResumen)
<section class="glass rounded-3xl !p-4 !py-4 sm:!p-5 md:!p-5 mb-3">

    <div class="mb-4 flex items-center justify-between gap-3">
        <h3 class="font-display text-sm font-bold uppercase tracking-wide">
            Resumen
        </h3>
        <span class="text-xs text-muted-foreground">Números de la temporada</span>
    </div>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @can('ver_guardavida')
            <x-counter label="Guardavidas activos" description="En la temporada" :value="$panelGuardavidasActivos"
                value-id="card-guardavidas-activos" icon-bg="bg-sky-100 dark:bg-sky-900/40">
                <x-icon name="lifeguard" class="h-5 w-5 text-sky-600 dark:text-sky-400" />
            </x-counter>
        @endcan

        @can('ver_intervencion')
            <x-counter label="Intervenciones" description="En la temporada" :value="$panelIntervenciones"
                value-id="card-intervenciones" icon-bg="bg-orange-100 dark:bg-orange-900/40">
                <x-icon name="siren" class="h-5 w-5 text-orange-600 dark:text-orange-400" />
            </x-counter>
        @endcan

        @can('ver_licencia')
            <x-counter label="Licencias activas" description="Hoy" :value="$licenciasActivasHoy"
                value-id="card-licencias-activas" icon-bg="bg-purple-100 dark:bg-purple-900/40">
                <x-icon name="calendar" class="h-5 w-5 text-purple-600 dark:text-purple-400" />
            </x-counter>
        @endcan

        {{-- Novedades de materiales: <x-counter> normal (número grande =
             temporada), pero conservando el badge rojo tipo notificación
             sobre el ícono — ahora muestra las de HOY, no las de temporada. --}}
        @can('ver_novedad_material')
            <x-counter label="Novedades de materiales" description="En la temporada" :value="$panelNovedadesMateriales"
                value-id="card-novedades" icon-bg="bg-amber-100 dark:bg-amber-900/40">
                <x-icon name="alert" class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                @if($novedadesMaterialesHoy > 0)
                    <span class="absolute -right-1 -top-1 grid h-5 w-5 place-items-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                        {{ $novedadesMaterialesHoy }}
                    </span>
                @endif
            </x-counter>
        @endcan
    </div>
</section>
@endif


@canany(['ver_bandera', 'ver_intervencion', 'ver_novedad_material'])
            <div class="rounded-2xl border border-gray-100 dark:border-gray-700/60 bg-white dark:bg-gray-800 shadow-sm p-card">
                <div class="mb-4 flex items-center justify-between gap-3">
        <h3 class="font-display text-sm font-bold uppercase tracking-wide">
            Últimas novedades
        </h3>
        <span class="text-xs text-muted-foreground">Actividad reciente</span>
    </div>

                <ol class="relative border-s border-gray-200 dark:border-gray-700 mx-2 my-2">
                    @forelse ($novedades as $index => $novedad)
                        {{--
                            $novedad->color trae valores heterogéneos según qué la generó
                            (BanderaObserver guarda el código de BanderaTipo, ej.
                            "bandera-bueno"; IntervencionObserver/NovedadMaterialObserver
                            guardan un nombre de color suelto como "orange"/"teal") — ninguno
                            de esos es una clase de Tailwind válida por sí sola, así que se
                            mapean acá a bg+texto reales. Para bandera, esto es lo que hace
                            que el color del ícono refleje el estado de la bandera del día.
                        --}}
                        @php
                            $coloresNovedad = [
                                'bandera-bueno' => 'text-sky-500',
                                'bandera-dudoso' => 'text-yellow-500',
                                'bandera-peligroso' => 'text-red-700 dark:text-red-500',
                                'bandera-rayos' => 'text-gray-900 dark:text-gray-100',
                                'bandera-prohibido' => 'text-red-700 dark:text-red-500',
                                'bandera-perdido' => 'text-gray-400 dark:text-gray-300',
                                'orange' => 'text-orange-500',
                                'teal' => 'text-blue-500',
                            ];
                            $colorClase = $coloresNovedad[$novedad->color] ?? 'text-gray-400';
                        @endphp

                        <li class="mb-5 ms-6">
                            <span class="{{ $colorClase }}
                                absolute flex items-center justify-center w-6 h-6 text-lg -start-3">
                                {!! $novedad->icono !!}
                            </span>
                            <div class="flex items-center justify-between gap-2 text-xs text-gray-400 dark:text-gray-500">
                                <div>
                                    <p class="flex items-center gap-2 mb-0.5 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{$novedad->titulo}}
                                    </p>
                                    <span class="truncate">{{ $novedad->playa->nombre }}{{ $novedad->referencia?->puesto ? ' · '.$novedad->referencia->puesto->nombre : '' }}</span>

                                </div>

                                <span class="bg-sky-100 text-sky-700 text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full dark:bg-sky-900/50 dark:text-sky-300">
                                    {{ $novedad->fecha->format('d/m H:i') }}
                                </span>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500 dark:text-gray-400 ms-0">No hay novedades para mostrar.</li>
                    @endforelse
                </ol>
            </div>
            @endcanany


@vite(['resources/js/dashboard-charts.js'])
@endsection
