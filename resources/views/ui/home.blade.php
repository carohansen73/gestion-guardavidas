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

@if($isMobile)
    {{-- Mobile partial --}}
    {{-- @include('intervencion.partials.mobile-list') --}}
    <p>Mobileeee</p>
@else
    {{-- Desktop partial --}}
     @include('ui.partials.desktop-buttons-create')
    <p>Destoooppp</p>
@endif


<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>

<div :class="{'dark': darkMode}">
   <p class="text-gray-900 dark:text-gray-100">Hola!</p>
</div>
@auth
@php
    $rol = Auth::user()->getRoleNames()->first();
    $playaUsuario = Auth::user()->guardavida->playa->nombre ?? null;
@endphp



@if($rol !== 'admin' && $playaUsuario)
    <div class="px-4 py-4">
        <div class="bg-gray-100 dark:bg-gray-600  text-gray-500 dark:text-gray-300 rounded-2xl p-6 transform transition focus:scale-105 duration-300">
            <!-- Contenido principal: playa izquierda, bandera derecha -->
            <div class="flex md:flex-row md:items-center justify-between gap-1">
                <!-- Izquierda: icono + playa -->
                <div class="flex items-center gap-1">
                    <!-- Icono de playa animado -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="bandera
                    text-{{$bandera->color}}-500 dark:text-{{$bandera->color}}-300
                        w-12 h-12 flex-shrink-0 mr-4 animate-ondear">
                        <path fill-rule="evenodd" d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054A8.25 8.25 0 0 0 18 4.524l3.11-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" clip-rule="evenodd" />
                    </svg>
                    <!-- Nombre de la playa -->
                    <div>
                        {{-- <p class="text-sm font-medium opacity-90">Tu playa</p> --}}
                        <h2 class="text-2xl md:text-3xl font-bold mb-2">Bandera del día</h2>
                        <p class="text-sm font-medium"> Mar {{ $bandera->codigo }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@endauth



{{-- @if($rol !== 'admin' && $playaUsuario)
<div class=" px-4 py-4">
    <div class="bg-gradient-to-r from-blue-400 to-teal-400 dark:from-blue-700 dark:to-teal-700 text-white rounded-2xl shadow-lg p-4 flex items-center gap-4 transform transition hover:scale-105 duration-300">
        <h2> Bienvenido!</h2>
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center">
                <!-- Icono de playa -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                <!-- Texto -->
                <div>
                    <p class="text-sm font-medium opacity-90">Tu playa</p>
                    <p class="text-lg font-bold">{{ $playaUsuario }}</p>
                </div>
            </div>
            <!-- Bandera del día -->
            <div class="flex items-center flex-col gap-2">
                <span class="w-8 h-8 rounded-full" style="background-color: blue;"></span>
                <p class="text-sm font-medium">{{ $bandera->codigo }}</p>
            </div>
        </div>
    </div>
</div>
@endif --}}

{{-- @if($rol !== 'admin' && $playaUsuario)
    <div class="px-4 py-4">
        <div class="bg-gradient-to-r from-blue-400 to-teal-400 dark:from-blue-700 dark:to-teal-700 text-sky-900 rounded-2xl shadow-lg p-6 transform transition hover:scale-105 duration-300">
            <div class="flex md:flex-row md:items-center justify-between gap-1">
                <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="bandera
                {{ $bandera->color }}
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



<section class="text-gray-600 body-font">
    <div class="container px-4 py-10 mx-auto">
        <div class="flex flex-col text-center w-full mb-10">
            <h1 class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900">Registrar un evento</h1>
        </div>
        <div class="flex flex-wrap -m-4 text-center">
             <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer  bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                    </svg>
                    <p class="leading-none">Banderas</p>
                </div>
            </div>
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-gradient-to-r from-blue-400 to-teal-400 dark:bg-gray-600 transition ease-in duration-300 text-sky-900">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class=" w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                    </svg>
                    <p class="leading-none">Asistencias</p>
                </div>
            </div>
           <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
            <a href="{{ route('intervencion.create') }}">
                <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288" />
                    </svg>
                    <p class="leading-none">Intervenciones</p>
                </div>
            </a>

            </div>
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                 <div class="  px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                       class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p class="leading-none">Novedades Materiales</p>
                </div>
            </div>
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                 <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                    <p class="leading-none">Cambio de turno</p>
                </div>
            </div>
           <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
             <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer  bg-white dark:bg-gray-600 transition ease-in duration-300">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                    </svg>

                    <p class="leading-none">Licencias</p>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- <div class="space-y-2 sm:hidden"> --}}
    <section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10">

        <div class="py-2  w-full">
            <a href="{{ route('intervencion.create') }}" class="bg-sky-600 rounded flex px-4 py-3 h-full justify-between">
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>

                    <span class="title-font font-medium text-gray-100">Ver intervenciones</span>
                </div>

                <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="3"
                stroke="currentColor"
                class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>

         <div class="py-2  w-full">
            <a href="{{ route('intervencion.create') }}" class="bg-sky-600 rounded flex px-4 py-3 h-full justify-between">
                <span class="title-font font-medium text-gray-100">Ver Novedades de matriales</span>
                <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="3"
                stroke="currentColor"
                class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>

         <div class="py-2  w-full">
            <a href="{{ route('intervencion.create') }}" class="bg-sky-600 rounded flex px-4 py-3 h-full justify-between">
                <span class="title-font font-medium text-gray-100">Mis Licencias </span>
                <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="3"
                stroke="currentColor"
                class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>


         <div class="py-2  w-full">
            <a href="{{ route('intervencion.create') }}" class="bg-sky-600 rounded flex px-4 py-3 h-full justify-between">
                <span class="title-font font-medium text-gray-100">Mis Cambios de turno </span>
                <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="3"
                stroke="currentColor"
                class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>

         <div class="py-2  w-full">
            <a href="{{ route('intervencion.create') }}" class="bg-sky-600 rounded flex px-4 py-3 h-full justify-between">
                <span class="title-font font-medium text-gray-100">Historial de banderas </span>
                <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="3"
                stroke="currentColor"
                class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>


    </section>
{{-- </div> --}}



<div class="text-sky-500">Celeste Sky</div>
<div class="text-blue-500">Azul Blue</div>
<div class="text-red-500">Rojo</div>

@endsection
