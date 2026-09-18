@extends('layouts.app')

@section('content')


<div class="text-gray-600 dark:text-gray-100 body-font px-4 ">
    <div class="flex justify-between align-center my-4">
        <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl"> Asistencias </h2>
    </div>

    <div x-data="{ selectedId: null }">
{{--
        TODO: acomodar export!!! --}}
    <x-filtros-de-busqueda :playas="$playas" tipo="asistencia-general" />

    <div class="flex justify-between items-end my-2 mx-0 px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm rounded">
        <form method="GET" class="flex gap-4 items-end flex-wrap">
            <div>
                <label for="inicio" class="text-sm text-gray-700 dark:text-gray-200">Desde:</label>
                <input type="date" name="inicio" id="inicio"
                    value="{{ $inicio->toDateString() }}"
                    class="border rounded p-1">
            </div>

            <div>
                <label for="fin" class="text-sm text-gray-700 dark:text-gray-200">Hasta:</label>
                <input type="date" name="fin" id="fin"
                    value="{{ $fin->toDateString() }}"
                    class="border rounded p-1">
            </div>

            <button class="bg-sky-600 hover:bg-sky-500 text-white px-4 py-1 rounded">
                Filtrar
            </button>

            <a href="{{ route('asistencias.index') }}"
                class="bg-gray-400 hover:bg-gray-300 text-white px-4 py-1 rounded">
                Mes actual
            </a>
        </form>
    </div>

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
   <x-index-table :registros="$guardavidas">
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
            <tr>
                <th class="px-4 py-2 text-left">Nombre</th>
                <th class="px-4 py-2 text-left">Puesto</th>
                <th class="px-4 py-2 text-left">Playa</th>
                <th class="px-4 py-2 text-left" title="Día franco fijo que configuró el guardavida (si lo hizo)">Día franco</th>
                <th class="px-4 py-2 text-right">Asistencias</th>
                <th class="px-4 py-2 text-right" title="Incluye el franco semanal aunque no esté configurado: si una semana no tiene franco explícito, la primera ausencia sin licencia de esa semana se cuenta como franco, no como falta">Francos</th>
                <th class="px-4 py-2 text-right" title="Ausencias sin asistencia, licencia ni franco (ya descontado 1 franco semanal por defecto)">Faltas</th>
                <th class="px-4 py-2 text-right">Licencias</th>
                <th class="px-4 py-2 text-right" title="Fichajes a más de 200m del puesto según GPS">Fuera de rango</th>
                <th class="px-4 py-2 text-right" title="Asistencias sobre días hábiles del período (excluye francos y licencias)">% Asistencia</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
           @foreach ($guardavidas as $g)
                @php($r = $resumen[$g->id] ?? null)
                <tr class="registro-item-tabla hover:bg-gray-50 dark:hover:bg-gray-800"
                    data-playa="{{ $g->playa->id ?? '' }}">
                    <td class="px-4 py-2">
                        <a href="{{ route('asistencias.guardavida', $g->id) }}" class="text-sky-600 hover:text-sky-400 dark:text-sky-400 dark:hover:text-sky-200">
                                {{ $g->apellido }} {{ $g->nombre }}
                        </a>
                    </td>
                    <td class="px-4 py-2">{{ $g->puesto->nombre ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $g->playa->nombre ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $g->dia_franco_nombre ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ $r['asistencias'] ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ $r['francos'] ?? '-' }}</td>
                    <td class="px-4 py-2 text-right {{ ($r['faltas'] ?? 0) > 0 ? 'text-red-600 dark:text-red-400 font-medium' : '' }}">{{ $r['faltas'] ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ $r['licencias'] ?? '-' }}</td>
                    <td class="px-4 py-2 text-right {{ ($r['fuera_de_rango'] ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400 font-medium' : '' }}">{{ $r['fuera_de_rango'] ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ isset($r['porcentaje']) ? ($r['porcentaje'] !== null ? $r['porcentaje'].'%' : '-') : '-' }}</td>
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
