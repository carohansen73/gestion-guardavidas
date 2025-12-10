@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')

<div class="text-gray-600 dark:text-gray-100 body-font px-4 ">
    <div class="flex justify-between align-center my-4">
        <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl"> Guardavidas </h2>
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


    {{-- FILTROS BACK --}}
    <div class="flex flex-wrap gap-2 align-content-center">
        @if(request()->is('guardavida'))
            <a href="{{ route('guardavidas.disabled')}}"
            class="playa-tag px-3 py-1 bg-orange-600 text-gray-100 rounded hover:bg-orange-400 hover:shadow-lg dark:bg-orange-600 dark:hover:bg-orange-500 dark:text-gray-200">
            Bloqueados
            </a>
            @else
            <a href="{{ route('guardavida.index')}}"
            class="playa-tag px-3 py-1 bg-sky-500 text-gray-100 rounded hover:bg-gray-300 hover:shadow-lg dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200">
            Habilitados
            </a>
        @endif

            <a href="{{ route('guardavidas.export') }}" class="px-3 py-1 bg-emerald-600 text-gray-100 rounded hover:bg-emerald-500 hover:shadow-lg dark:bg-emerald-700 dark:hover:bg-teal-500 dark:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </a>
    </div>

    <form method="GET" class="flex flex-col-reverse md:flex-row justify-between align-center ">

            <div class="flex flex-wrap gap-2 align-content-center">
                 <a href="{{ route('guardavida.index', ['playa_id' => 'all'] + request()->except('page')) }}"
                    class="  {{ request('playa_id') == 'all' ? 'bg-sky-600 text-white' : 'bg-gray-200 text-gray-700' }} playa-tag px-3 py-1 rounded hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200"
                    >
                    Todas
                 </a>
                @foreach($playas as $playa)
                     <a href="{{ route('guardavida.index', ['playa_id' => $playa->id] + request()->except('page')) }}"
                        class="{{ request('playa_id') == $playa->id ? 'bg-sky-600 text-white' : 'bg-gray-200 text-gray-700' }} playa-tag px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200"
                       >
                        {{ $playa->nombre }}
                     </a>
                @endforeach

            </div>
            {{-- Busqueda --}}
            <div class="relative flex w-full md:w-auto my-3 sm:!my-0">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder='Buscar... '
                    class="w-full px-3 py-2 border rounded"
                    oninput="applyFilters()">

                    <button type="submit" class="bg-sky-600 text-white px-3 py-2 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>

                    </button>
            </div>

    </form>



    {{-- @endrole --}}

    {{-- END filtros back --}}

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
    @include('ui.guardavidas.partials.index-mobile')

    {{-- Tabla para Desktop --}}
    @include('ui.guardavidas.partials.index-desktop')

</div> <!-- selectedId -->


{{-- <script src="{{ asset('js/table-intervenciones.js') }}"></script> --}}
@endsection
