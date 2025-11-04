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



{{-- <button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>

<div :class="{'dark': darkMode}">
   <p class="text-gray-900 dark:text-gray-100">Dashboard</p>
</div> --}}
@auth
    @php
        $rol = Auth::user()->getRoleNames()->first();
        $playaUsuario = Auth::user()->guardavida->playa->nombre ?? null;
    @endphp
@endauth


<div class="flex flex-col md:flex-row gap-6 px-6 py-6">

    <!-- 🟦 Columna principal (2/3 del ancho) -->
    <main class="w-full md:w-2/3  space-y-6">


@if($rol !== 'admin' && $playaUsuario)
    <div class="p-2">
        <div class="bg-gray-200 dark:from-blue-700 dark:to-teal-700 text-black rounded shadow-sm p-6 transform transition hover:scale-105 duration-300">
            <div class="flex justify-content-between align-center">
                <h2 class="text-2xl md:text-3xl font-bold text-black">Bandera del día</h2>
                @if(!$bandera)
                    @can('agregar_bandera')
                        <a href="{{ route('bandera.create') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-8 h-8 py-2 px-2 bg-gray-50 text-sky-600 rounded-xl">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </a>
                    @endcan
                @else
                    <a href="{{ route('bandera.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="3"
                            stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-50 text-gray-800 rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                @endif
            </div>
            @if(!$bandera)
                @can('ver_bandera')
                <a href="{{ route('bandera.index') }}"
                    class="inline-flex items-center text-sm justify-center px-3 py-2 bg-gray-800/80 text-white rounded-full hover:bg-gray-700 transition mt-2">
                    Historial de banderas
                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="3"
                        stroke="currentColor"
                        class="w-5 h-5 ms-3 bg-white p-1 text-dark rounded-xl">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
                @endcan
            @endif
            @if($bandera)
                <!-- Izquierda: icono + playa -->
                <div class="grid grid-cols-3 mt-2">

                     <!-- Columna 1 -->
                    <div class="flex flex-col items-center rounded-lg">
                        <div class="bg-gray-200/50 rounded mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-12 h-12 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 12h12a2 2 0 10-2-2m-10 4h8a2 2 0 11-2 2" />
                            </svg>
                        </div>

                        <p class="text-sm font-medium"> {{ $bandera->viento_intensidad }} {{ $bandera->viento_direccion }}</p>
                    </div>

                    <!-- Columna 3 -->
                    <div class="flex flex-col items-center rounded-lg">
                        <div class="bg-gray-200/50 rounded mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class=" w-12 h-12 text-amber-500">
                            <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" />
                            </svg>
                        </div>

                        <p class="text-sm font-medium">{{ $bandera->temperatura }}º</p>
                    </div>

                     <!-- Columna 3 -->
                    <div class="flex flex-col items-center rounded-lg">
                        <div class="bg-gray-200/50 rounded mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="bandera {{ $bandera->bandera->color }}
                                w-12 h-12 flex-shrink-0 animate-ondear">
                            <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                            </svg>
                        </div>

                        <p class="text-sm font-medium">{{$bandera->bandera->codigo}}</p>
                    </div>
                </div>
                @endif


        </div>
    </div>
@endif


{{-- @if($rol !== 'admin' && $playaUsuario)
    <div class="px-4 py-4">
        <div class="bg-gradient-to-r from-gray-100 to-{{$bandera->color}}-400 dark:from-gray-500 dark:to-{{$bandera->color}}-500 text-sky-900 dark:text-gray-200 rounded-2xl shadow-lg p-6 transform transition hover:scale-105 duration-300">
            <div class="flex md:flex-row md:items-center justify-between gap-1">
                <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="bandera
                    text-{{$bandera->color}}-500 dark:text-{{$bandera->color}}-300
                        w-12 h-12 flex-shrink-0 mr-4 animate-ondear">
                        <path fill-rule="evenodd" d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054A8.25 8.25 0 0 0 18 4.524l3.11-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold mb-2">Bandera del día</h2>
                        <p class="text-sm font-medium"> Mar {{ $bandera->codigo }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif --}}


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
                <h2 class="font-bold">Guardavidas</h2>
                @can('agregar_guardavida')
                <a href="{{ route('guardavida.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="w-6 h-6 py-1 px-1 border-1 border-sky-600 text-sky-600 rounded-xl hover:bg-sky-600 hover:text-white bg:shadow-md">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>
                @endcan
            </div>

            <div class="flex flex-column items-center justify-center text-center py-4 px-2">
                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 148.42 68.38"  class="text-sky-600 h-15 h-14  mb-3 inline-block">
                        <path d="M-292.54,525.76a19.7,19.7,0,0,1,1.82,1c11.9,7.89,24.19,8.33,36.9,2,3.25-1.61,6.63-3,10-4.42,12.47-5.47,24.7-4.68,36.75,1.33,4,2,7.89,4.17,12,5.84,9.5,3.86,18.76,2.3,27.75-1.88a39.39,39.39,0,0,0,9.58-6.14c1-.9,1.95-1.21,2.83.22,1.54,2.52,3.83,4.52,5.19,7.47a61.59,61.59,0,0,1-24.51,12.28,43.78,43.78,0,0,1-20.64.09c-4.73-1.13-8.89-3.51-12.93-6a37.63,37.63,0,0,0-18.67-5.71c-6.34-.25-12.38,1.55-18.16,4.13-3.45,1.55-6.86,3.2-10.31,4.75-14.79,6.62-28.57,3.78-41.78-4.47-1.55-1-1.92-2-.65-3.57C-295.69,530.5-294.2,528.14-292.54,525.76Z" transform="translate(298.18 -476.39)" fill="currentColor"/><path d="M-271.79,514.06a81.68,81.68,0,0,1,23-10.12,82.59,82.59,0,0,1,27.41-3c2.86.19,3.24-.28,3-3a9.81,9.81,0,0,0-5.94-8.56,20.43,20.43,0,0,0-12.38-2c-2.61.47-3,.14-3.63-2.62-.18-.8-.17-1.65-.31-2.47-.93-5.39-.95-5.75,4.35-5.92,6.41-.21,12.72.52,18.49,4,8.7,5.21,11.9,15.13,10.76,24.41-.24,1.95-1,3.82-1.34,5.76-.6,3.4-.87,3.36-4.47,2.7a66.77,66.77,0,0,0-12.71-1.41c-12.19.13-23.89,2.51-34.7,8.45-1.3.72-2.67,1.3-3.95,2.05s-2.16.39-2.89-.78C-268.53,519.2-270,516.85-271.79,514.06Z" transform="translate(298.18 -476.39)" fill="currentColor"/><path d="M-184.27,496.44a13.66,13.66,0,0,1,13.63,14.05c-.13,7.37-6.44,14.18-13.57,13.75-8.48-.51-14.14-5.63-14.18-14.2A13.59,13.59,0,0,1-184.27,496.44Z" transform="translate(298.18 -476.39)" fill="currentColor"/>
                    </svg>

                <h2 class="title-font font-medium text-3xl text-gray-900">100</h2>
                <p class="leading-relaxed">Guardavidas registrados</p>
            </div>

            <div class="border border-gray-200  rounded-xl shadow-md mb-2">
                <ul>
                    @can('ver_guardavida')
                    <li class="border-b border-gray-200 p-3 text-sky-600 hover:bg-sky-600 hover:text-white rounded-t-xl">
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
                    <li class="border-b border-gray-200 p-3 text-sky-600 hover:bg-sky-600 hover:text-white">
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
                       <li class="border-b border-gray-200 p-3 text-sky-600 hover:bg-sky-600 hover:text-white">
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
                        <li class="p-3 text-sky-600 hover:bg-sky-600 hover:text-white rounded-b-xl">
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



        <div class="container bg-white dark:bg-gray-600 w-full px-3 py-3 rounded my-4">

        {{-- <ol class="relative border-s border-gray-200 dark:border-gray-700">
            <li class="mb-10 ms-6">
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                    </svg>
                </span>
                <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white">Flowbite Application UI v2.0.0 <span class="bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300 ms-3">Latest</span></h3>
                <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">Released on January 13th, 2022</time>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">Get access to over 20+ pages including a dashboard layout, charts, kanban board, calendar, and pre-order E-commerce & Marketing pages.</p>

                <a href="#" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700"><svg class="w-3.5 h-3.5 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                    <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                </svg> Download ZIP</a>
            </li>
            <li class="mb-10 ms-6">
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                    </svg>
                </span>
                <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">Flowbite Figma v1.3.0</h3>
                <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">Released on December 7th, 2021</time>
                <p class="text-base font-normal text-gray-500 dark:text-gray-400">All of the pages and components are first designed in Figma and we keep a parity between the two versions even as we update the project.</p>
            </li>
            <li class="ms-6">
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                    </svg>
                </span>
                <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">Flowbite Library v1.2.2</h3>
                <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">Released on December 2nd, 2021</time>
                <p class="text-base font-normal text-gray-500 dark:text-gray-400">Get started with dozens of web components and interactive elements built on top of Tailwind CSS.</p>
            </li>
        </ol> --}}


        <ol class="relative border-s border-gray-200 dark:border-gray-700 mx-2 my-2">
        @foreach ($novedades as $index => $novedad)
                @php
            // $colores = [
            //     'orange' => 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-300',
            //     'teal' => 'bg-teal-100 dark:bg-teal-900 text-teal-800 dark:text-teal-300',
            //     'sky' => 'bg-sky-100 dark:bg-sky-900 text-sky-800 dark:text-sky-300',
            // ];
            // $colorClase = $colores[$novedad->color] ?? 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-300';
            @endphp

            <li class="mb-6 ms-6">
                <span class=" {!! $novedad->color !!}
                     absolute flex items-center justify-center w-6 h-6  rounded-full -start-3 ring-8 ring-white dark:ring-gray-900">
                     {!! $novedad->icono !!}
                </span>
                <h3 class="flex items-center mb-1  font-semibold text-gray-900 dark:text-white">
                     {{$novedad->titulo}}
                     @if ($loop->first)
                    <span class="bg-sky-100 text-sky-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 ms-3">
                        Latest
                    </span>
                    @endif
                </h3>
                <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                   {{$novedad->fecha}}
                </time>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400 ">
                    {{$novedad->playa->nombre}}
                </p>

                {{-- <a href="#" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700"><svg class="w-3.5 h-3.5 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                    <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                </svg> Download ZIP</a> --}}
            </li>
        @endforeach
        </ol>
        </div>


    </aside>
@endsection
