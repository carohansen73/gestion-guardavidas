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
        <div class="bg-gradient-to-r from-sky-600 to-sky-600 dark:from-blue-700 dark:to-teal-700 text-gray-50 rounded-2xl shadow-lg p-6 transform transition hover:scale-105 duration-300">
            <div class="flex justify-content-between align-center">
                <h2 class="text-2xl md:text-3xl font-bold ">Bandera del día</h2>
                @if(!$bandera)
                    <a href="{{ route('bandera.create') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-50 text-sky-600 rounded-xl">
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
                            class="w-8 h-8 py-2 px-2 bg-gray-50 text-gray-800 rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                @endif
            </div>
            @if(!$bandera)
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
                <a href="{{ route('guardavida.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="w-6 h-6 py-1 px-1 border-1 border-sky-600 text-sky-600 rounded-xl hover:bg-sky-600 hover:text-white bg:shadow-md">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>
            </div>

            <div class="flex flex-column items-center justify-center text-center py-4 px-2">
               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-sky-600 w-12 h-12 mb-3 inline-block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288" />
                </svg>
                <h2 class="title-font font-medium text-3xl text-gray-900">100</h2>
                <p class="leading-relaxed">Guardavidas registrados</p>
            </div>

            <div class="bg-gray-50 rounded-xl shadow-md mb-2">
              <ul>
                    <li class="border-b border-gray-200 p-3">
                        <a href="{{ route('guardavida.index') }}" class="flex justify-content-between align-center text-sky-600">
                            <p>Guardavidas</h2>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="3"
                                stroke="currentColor"
                                class="w-6 h-6 py-1 px-1 text-sky-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </li>
                    <li class="border-b border-gray-200 p-3">
                          <a href="{{ route('bandera.create') }}" class="flex justify-content-between align-center text-sky-600">
                            <p>Asistencia</h2>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="3"
                                stroke="currentColor"
                                class="w-6 h-6 py-1 px-1 text-sky-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </li>
                    <li class="p-3">
                        <a href="{{ route('bandera.create') }}" class="flex justify-content-between align-center text-sky-600">
                            <p>Licencias</h2>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="3"
                                stroke="currentColor"
                                class="w-6 h-6 py-1 px-1 text-sky-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>

                    </li>
                </ul>
            </div>

        </div> <!-- End card white -->
    </aside>
@endsection
