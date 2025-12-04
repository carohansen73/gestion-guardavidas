@extends('layouts.app')

@section('content')


<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">

    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
    <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 0 1 .162.819A8.97 8.97 0 0 0 9 6a9 9 0 0 0 9 9 8.97 8.97 0 0 0 3.463-.69.75.75 0 0 1 .981.98 10.503 10.503 0 0 1-9.694 6.46c-5.799 0-10.5-4.7-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 0 1 .818.162Z" clip-rule="evenodd" />
    </svg>


</button>

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" />
</svg>


<div class="text-gray-600 dark:text-gray-100 body-font px-4 ">
    <div class="flex justify-between align-center mb-sm-4">
        <h2 class="text-gray-700 text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl"> Asistencias </h2>
    </div>

    <div x-data="{ selectedId: null }">
{{--
        TODO: acomodar export!!! --}}
    <x-filtros-de-busqueda :playas="$playas" tipo="asistencia-general" />

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
    {{-- @include('ui.intervenciones.partials.index-mobile') --}}

    {{-- Tabla para Desktop --}}
   <x-index-table>
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
            <tr>
                {{-- <th class="px-4 py-2 text-left cursor-pointer" onclick="toggleSort()">Fecha</th> --}}
                <th class="px-4 py-2 text-left">Nombre</th>
                <th class="px-4 py-2 text-left">Puesto</th>
                <th class="px-4 py-2 text-left">Playa</th>
                <th class="px-4 py-2 text-left">Asistencias</th>
                {{-- <th class="px-4 py-2 text-left">Faltas</th> --}}
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
           @foreach ($guardavidas as $g)
                <tr class="registro-item-tabla hover:bg-gray-50 dark:hover:bg-gray-800"
                    data-playa="{{ $g->playa->id ?? '' }}">
                    {{-- <td class="px-4 py-2">{{ $intervencion->fecha->format('d/m/Y') }}</td> --}}
                    <td class="px-4 py-2">
                        <a href="{{ route('asistencias.guardavida', $g->id) }}" class="text-sky-600 hover:text-sky-400">
                                {{ $g->apellido }} {{ $g->nombre }}
                        </a>
                    </td>
                    <td class="px-4 py-2">{{ $g->puesto->nombre ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $g->playa->nombre ?? '-' }}</td>
                    <td class="px-4 py-2">0</td>
                </tr>
            @endforeach
        </tbody>
    </x-index-table>
{{-- End tabla --}}


          {{-- PAGINACIÓN --}}
                @if (method_exists($guardavidas, 'links'))
                    <div class="paginacion mt-4">
                        {{ $guardavidas->links() }}
                    </div>
                @endif

                {{-- VOLVER --}}
                <div class="text-center mt-4">
                    <a class="btn btn-secondary" onclick="window.history.back()">Volver</a>
                </div>


</div> <!-- selectedId -->


<script src="{{ asset('js/table-intervenciones.js') }}"></script>
@endsection
