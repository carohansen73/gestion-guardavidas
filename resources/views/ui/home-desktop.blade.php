@extends('layouts.app')

<x-slot name="header"></x-slot>

@section('content')

@if(session('show_guardavida_setup'))
    @include('ui.partials.modal-setup')
@endif

@if(session('show_franco_setup'))
    @include('ui.partials.aviso-franco-pendiente')
@endif

<div class="px-6 pt-6">
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
</div>

<div class="flex flex-col md:flex-row gap-6 px-6 pb-6">

    <!-- Columna principal -->
    <main class="w-full md:w-2/3 space-y-8">

        {{-- Bandera del día (o carrusel de banderas por playa si es admin) --}}
        @include('ui.partials.bandera-desktop')

        <section>
            <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">
                Acciones rápidas
            </h2>
            @if($isMobile)
                @include('ui.partials.mobile-buttons-create')
            @else
                @include('ui.partials.desktop-buttons-create')
            @endif
        </section>

        @unless($isMobile)
            <section>
                <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">
                    Resumen general
                </h2>
                @include('ui.partials.desktop-cards')
            </section>
        @endunless

    </main>

    <!-- Aside lateral -->
    <aside class="w-full md:w-1/3">
        <div class="rounded-2xl border border-gray-100 dark:border-gray-700/60 bg-white dark:bg-gray-800 shadow-sm p-4">

            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800 dark:text-white">Guardavidas</h2>
                @can('agregar_guardavida')
                    <a href="{{ route('guardavida.create') }}"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-sky-600 dark:text-sky-400 hover:bg-sky-50 dark:hover:bg-sky-900/30 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                @endcan
            </div>

            <div class="flex flex-col items-center text-center py-2 mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 148.42 68.38" class="text-sky-600 dark:text-sky-400 h-10 mb-1 inline-block">
                    <path d="M-292.54,525.76a19.7,19.7,0,0,1,1.82,1c11.9,7.89,24.19,8.33,36.9,2,3.25-1.61,6.63-3,10-4.42,12.47-5.47,24.7-4.68,36.75,1.33,4,2,7.89,4.17,12,5.84,9.5,3.86,18.76,2.3,27.75-1.88a39.39,39.39,0,0,0,9.58-6.14c1-.9,1.95-1.21,2.83.22,1.54,2.52,3.83,4.52,5.19,7.47a61.59,61.59,0,0,1-24.51,12.28,43.78,43.78,0,0,1-20.64.09c-4.73-1.13-8.89-3.51-12.93-6a37.63,37.63,0,0,0-18.67-5.71c-6.34-.25-12.38,1.55-18.16,4.13-3.45,1.55-6.86,3.2-10.31,4.75-14.79,6.62-28.57,3.78-41.78-4.47-1.55-1-1.92-2-.65-3.57C-295.69,530.5-294.2,528.14-292.54,525.76Z" transform="translate(298.18 -476.39)" fill="currentColor"/><path d="M-271.79,514.06a81.68,81.68,0,0,1,23-10.12,82.59,82.59,0,0,1,27.41-3c2.86.19,3.24-.28,3-3a9.81,9.81,0,0,0-5.94-8.56,20.43,20.43,0,0,0-12.38-2c-2.61.47-3,.14-3.63-2.62-.18-.8-.17-1.65-.31-2.47-.93-5.39-.95-5.75,4.35-5.92,6.41-.21,12.72.52,18.49,4,8.7,5.21,11.9,15.13,10.76,24.41-.24,1.95-1,3.82-1.34,5.76-.6,3.4-.87,3.36-4.47,2.7a66.77,66.77,0,0,0-12.71-1.41c-12.19.13-23.89,2.51-34.7,8.45-1.3.72-2.67,1.3-3.95,2.05s-2.16.39-2.89-.78C-268.53,519.2-270,516.85-271.79,514.06Z" transform="translate(298.18 -476.39)" fill="currentColor"/><path d="M-184.27,496.44a13.66,13.66,0,0,1,13.63,14.05c-.13,7.37-6.44,14.18-13.57,13.75-8.48-.51-14.14-5.63-14.18-14.2A13.59,13.59,0,0,1-184.27,496.44Z" transform="translate(298.18 -476.39)" fill="currentColor"/>
                </svg>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totales['guardavidas'] }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300">Guardavidas registrados</p>
            </div>

            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @can('ver_guardavida')
                    <li>
                        <a href="{{ route('guardavida.index') }}"
                            class="flex items-center justify-between py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-sky-600 dark:hover:text-sky-400 transition">
                            Guardavidas
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-300 dark:text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </li>
                @endcan
                <li>
                    <a href="{{ route('asistencias.index') }}"
                        class="flex items-center justify-between py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-sky-600 dark:hover:text-sky-400 transition">
                        Asistencia
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-300 dark:text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </li>
                @can('ver_licencia')
                    <li>
                        <a href="{{ route('licencia.index') }}"
                            class="flex items-center justify-between py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-sky-600 dark:hover:text-sky-400 transition">
                            Licencias
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-300 dark:text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </li>
                @endcan
                @can('ver_cambio_turno')
                    <li>
                        <a href="{{ route('cambio-de-turno.index') }}"
                            class="flex items-center justify-between py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-sky-600 dark:hover:text-sky-400 transition">
                            Cambios de turno
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-300 dark:text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </aside>
</div>

@include('ui.partials.panel-admin')

@endsection
