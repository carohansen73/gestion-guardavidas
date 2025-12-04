@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')

<div class="text-gray-600 dark:text-gray-100 body-font px-4 ">
    <div class="flex justify-between align-center mb-sm-4">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white mt-3 "> Usuarios </h1>
        @can('agregar_guardavida')
        <a href="{{ route('guardavida.create') }}" class="btn hidden sm:flex align-center bg-sky-500 dark:bg-sky-700 hover:bg-sky-400 dark:hover:bg-sky-600 rounded-full px-3 py-2 shadow-md hover:shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="text-sky-500 dark:text-sky-700 w-5 h-5 bg-gray-100 dark:bg-gray-200 rounded me-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span class="text-gray-100 dark:text-gray-200"> Agregar</span>
        </a>
        @endcan
    </div>
    {{-- TODO filtros de busqueda para usuarios sin playa!! --}}
    {{-- @include('ui.guardavidas.partials.filtros-de-busqueda') --}}

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded my-2">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded my-2">
            {{ session('error') }}
        </div>
    @endif

</div>

<div x-data="{ selectedId: null }">

    {{-- Lista para Mobile --}}
    @include('admin.usuarios.partials.index-mobile')

    {{-- Tabla para Desktop --}}
    @include('admin.usuarios.partials.index-desktop')

</div> <!-- selectedId -->


<script src="{{ asset('js/table-intervenciones.js') }}"></script>
@endsection
