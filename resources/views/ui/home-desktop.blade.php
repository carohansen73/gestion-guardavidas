@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}
{{-- @auth
@php

    $playa = Auth::user()->guardavida?->playa?->nombre;
    $rol = Auth::user()->getRoleNames()->first();
    if ($rol !== 'admin' && $playa) {
        $header = '<div class="">
            <div class="bg-gradient-to-r from-blue-400 to-teal-400 dark:from-blue-700 dark:to-teal-700 text-white rounded-b-2xl shadow-lg p-6 transform transition hover:scale-105 duration-300">
                <div class="flex justify-center  md:items-center ">
                    <div class="flex items-center justify-center gap-2">
                        <!-- Icono de playa animado -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 animate-bounce text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        <div>
                            <p class="text-lg md:text-xl font-bold">'.$playa.'</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
@endphp
  @endauth --}}


    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

@section('content')

@auth
    @php
        $rol = Auth::user()->getRoleNames()->first();
        $playaUsuario = Auth::user()->guardavida->playa->nombre ?? null;
    @endphp
@endauth

@if(session('show_guardavida_setup'))
    @include('ui.partials.modal-setup')
@endif

<div class="flex flex-col md:flex-row gap-6 px-6 py-6">

    <!-- 🟦 Columna principal (2/3 del ancho) -->
    <main class="w-full md:w-2/3  space-y-6">

        {{-- Incluye Banderaa del día o coleccion de banderas si es admin --}}
        @include('ui.partials.bandera-desktop')


        @if($isMobile)
            @include('ui.partials.mobile-buttons-create')
        @else
            @include('ui.partials.desktop-buttons-create')
        @endif

        @if($isMobile)
        @else
            @include('ui.partials.desktop-cards')
        @endif


    </main>
<!-- 🟨 Aside lateral (1/3 del ancho) -->
    <aside class="hidden md:block w-full md:w-1/3 ">

        <div class="container bg-white dark:bg-gray-600 w-full px-3 py-3 rounded">

            <div class="flex justify-content-between align-center">
                <h2 class="font-bold dark:text-white">Guardavidas</h2>
                @can('agregar_guardavida')
                <a href="{{ route('guardavida.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="w-6 h-6 py-1 px-1 border-1 border-sky-600 text-sky-600 dark:text-white dark:border-white rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 bg:shadow-md">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>
                @endcan
            </div>

            <div class="flex flex-column items-center justify-center text-center py-4 px-2">
                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 148.42 68.38"  class="text-sky-600 h-15 h-14  mb-3 inline-block">
                        <path d="M-292.54,525.76a19.7,19.7,0,0,1,1.82,1c11.9,7.89,24.19,8.33,36.9,2,3.25-1.61,6.63-3,10-4.42,12.47-5.47,24.7-4.68,36.75,1.33,4,2,7.89,4.17,12,5.84,9.5,3.86,18.76,2.3,27.75-1.88a39.39,39.39,0,0,0,9.58-6.14c1-.9,1.95-1.21,2.83.22,1.54,2.52,3.83,4.52,5.19,7.47a61.59,61.59,0,0,1-24.51,12.28,43.78,43.78,0,0,1-20.64.09c-4.73-1.13-8.89-3.51-12.93-6a37.63,37.63,0,0,0-18.67-5.71c-6.34-.25-12.38,1.55-18.16,4.13-3.45,1.55-6.86,3.2-10.31,4.75-14.79,6.62-28.57,3.78-41.78-4.47-1.55-1-1.92-2-.65-3.57C-295.69,530.5-294.2,528.14-292.54,525.76Z" transform="translate(298.18 -476.39)" fill="currentColor"/><path d="M-271.79,514.06a81.68,81.68,0,0,1,23-10.12,82.59,82.59,0,0,1,27.41-3c2.86.19,3.24-.28,3-3a9.81,9.81,0,0,0-5.94-8.56,20.43,20.43,0,0,0-12.38-2c-2.61.47-3,.14-3.63-2.62-.18-.8-.17-1.65-.31-2.47-.93-5.39-.95-5.75,4.35-5.92,6.41-.21,12.72.52,18.49,4,8.7,5.21,11.9,15.13,10.76,24.41-.24,1.95-1,3.82-1.34,5.76-.6,3.4-.87,3.36-4.47,2.7a66.77,66.77,0,0,0-12.71-1.41c-12.19.13-23.89,2.51-34.7,8.45-1.3.72-2.67,1.3-3.95,2.05s-2.16.39-2.89-.78C-268.53,519.2-270,516.85-271.79,514.06Z" transform="translate(298.18 -476.39)" fill="currentColor"/><path d="M-184.27,496.44a13.66,13.66,0,0,1,13.63,14.05c-.13,7.37-6.44,14.18-13.57,13.75-8.48-.51-14.14-5.63-14.18-14.2A13.59,13.59,0,0,1-184.27,496.44Z" transform="translate(298.18 -476.39)" fill="currentColor"/>
                    </svg>
                <h2 class="title-font font-medium text-3xl text-gray-900 dark:text-gray-300">100</h2>
                <p class="leading-relaxed dark:text-gray-100">Guardavidas registrados</p>
            </div>

            <div class="border border-gray-200 dark:border-gray-400 rounded-xl shadow-md mb-2">
                <ul>
                    @can('ver_guardavida')
                    <li class="border-b border-gray-200 p-3 text-sky-600 dark:text-sky-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 rounded-t-xl">
                        <a href="{{ route('guardavida.index') }}" class="flex justify-content-between align-center ">
                            <p>Guardavidas</h2>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="3"
                                stroke="currentColor"
                                class="w-6 h-6 py-1 px-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </li>
                    @endcan
                    <li class="border-b border-gray-200 p-3 text-sky-600 dark:text-sky-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900">
                          <a href="{{ route('bandera.create') }}" class="flex justify-content-between align-center">
                            <p>Asistencia</h2>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="3"
                                stroke="currentColor"
                                class="w-6 h-6 py-1 px-1 ">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </li>
                    @can('ver_licencia')
                       <li class="border-b border-gray-200 p-3 text-sky-600 dark:text-sky-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900">
                            <a href="{{ route('licencia.index') }}" class="flex justify-content-between align-center">
                                <p>Licencias</h2>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="3"
                                    stroke="currentColor"
                                    class="w-6 h-6 py-1 px-1 ">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </li>
                    @endcan
                    @can('ver_cambio_turno')
                        <li class="p-3 text-sky-600 dark:text-sky-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 rounded-b-xl">
                            <a href="{{ route('cambio-de-turno.index') }}" class="flex justify-content-between align-center">
                                <p>Cambios de turno</h2>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="3"
                                    stroke="currentColor"
                                    class="w-6 h-6 py-1 px-1 ">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>

        </div> <!-- End card white -->


    </aside>
@endsection
