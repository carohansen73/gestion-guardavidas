@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')

<div class="text-gray-600 dark:text-gray-100 body-font sm:px-4 ">
    <div class="flex justify-between align-center mb-4 sm:mt-4">
        <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl"> Cambios de turno </h2>
        @can('agregar_cambio_turno')
            <a href="{{ route('cambio-de-turno.create') }}" class="btn hidden sm:flex align-center bg-sky-500 dark:bg-sky-700 hover:bg-sky-400 dark:hover:bg-sky-600 rounded-full px-3 py-2 shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="text-sky-500 dark:text-sky-700 w-5 h-5 bg-gray-100 dark:bg-gray-200 rounded me-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span class="text-gray-100 dark:text-gray-200"> Agregar</span>
            </a>
        @endcan
    </div>

    <x-filtros-de-busqueda :playas="$playas" :temporadas="$temporadas" :temporada="$temporada" tipo="cambio-de-turno" />

    <x-session-alerts/>

</div>

<div x-data="{ menu: null }">

    {{-- Lista para Mobile --}}
    @include('cambios-de-turno.partials.index-mobile')

    {{-- Tabla para Desktop --}}
    @include('cambios-de-turno.partials.index-desktop')

    {{-- Menú inferior de acciones (lo abren los tres puntos de cada tarjeta) --}}
    <x-menu-inferior />

</div> <!-- selectedId -->

<script src="{{ asset('js/table-intervenciones.js') }}"></script>
@endsection
