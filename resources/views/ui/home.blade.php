@extends('layouts.app')
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
@section('content')



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

        <div class="bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-300 rounded-2xl p-6 transform transition focus:scale-105 duration-300">
                <!-- Contenido principal: playa izquierda, bandera derecha -->

            <div class="flex justify-content-between align-center">
                <h2 class="text-2xl md:text-3xl font-bold">Bandera del día</h2>
                @if(!$bandera)
                     <a href="{{ route('bandera.create') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-800/60 text-white rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('bandera.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="3"
                            stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-800/60 text-white rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                @endif
            </div>

            @if($bandera)
                <!-- Izquierda: icono + playa -->

                <div class="grid grid-cols-3 mt-2">
                    <!-- Columna 1 -->
                    <div class="flex flex-col items-center rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        stroke="{{$bandera->bandera->borde}}"
                        class="bandera
                        text-{{$bandera->bandera->color}}-500 dark:text-{{$bandera->bandera->color}}-300
                            w-12 h-12 flex-shrink-0 mr-4 animate-ondear">
                        <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                        </svg>
                            <p class="text-sm font-medium">{{$bandera->bandera->codigo}}</p>
                    </div>

                    <!-- Columna 2 -->
                    <div class="flex flex-col items-center rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class=" w-12 h-12">
                        <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" />
                        </svg>
                        <p class="text-sm font-medium">32º</p>
                    </div>

                    <!-- Columna 3 -->
                    <div class="flex flex-col items-center rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-12 h-12">
                        <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 12h12a2 2 0 10-2-2m-10 4h8a2 2 0 11-2 2" />
                        </svg>
                        <p class="text-sm font-medium"> {{ $bandera->viento_intensidad }} {{ $bandera->viento_direccion }}</p>
                    </div>

                </div>

                @endif
            </div>

    </div>
@endif

@endauth



{{--

@if($rol !== 'admin' && $playaUsuario)
    <div class="px-4 py-4">
        <div class="bg-gradient-to-r from-blue-400 to-teal-400 dark:from-blue-700 dark:to-teal-700 text-sky-900 rounded-2xl shadow-lg p-6 transform transition hover:scale-105 duration-300">
            <div class="flex justify-content-between align-center">
                <h2 class="text-2xl md:text-3xl font-bold ">Bandera del día</h2>
                @if(!$bandera)
                    <a href="{{ route('bandera.create') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-800/60 text-white rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('bandera.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="3"
                            stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-800/60 text-white rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                @endif
            </div>

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
                                class="w-12 h-12">
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
                            class=" w-12 h-12">
                            <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" />
                            </svg>
                        </div>

                        <p class="text-sm font-medium">32º</p>
                    </div>

                     <!-- Columna 3 -->
                    <div class="flex flex-col items-center rounded-lg">
                        <div class="bg-gray-200/50 rounded mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            stroke="{{$bandera->bandera->borde}}"
                            class="bandera
                            text-{{$bandera->bandera->color}}-500 dark:text-{{$bandera->bandera->color}}-300
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
            <a href="{{ route('bandera.index') }}" class="bg-sky-600 rounded flex px-4 py-3 h-full justify-between">
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
