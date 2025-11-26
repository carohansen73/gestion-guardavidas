@extends('layouts.app')

@section('content')


<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>

<div class="text-gray-600 dark:text-gray-100 body-font px-4 ">
    <div class="flex justify-between align-center mb-sm-4">
        <h2 class="text-gray-700 text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl"> Asistencias </h2>
    </div>

    <div x-data="{ selectedId: null }">
{{--
        TODO: acomodar export!!! --}}
    <x-filtros-de-busqueda :playas="$playas" tipo="asistencias" />

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
                    <a class="btn btn-secondary" href="{{ url('dashboard') }}">Volver</a>
                </div>


</div> <!-- selectedId -->


<script src="{{ asset('js/table-intervenciones.js') }}"></script>
@endsection
