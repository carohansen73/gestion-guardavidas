@extends('layouts.app')

@section('content')
    <div class="container mx-auto sm:mt-4 sm:px-4">

        <h2 class="mb-4 text-xl font-semibold text-gray-800 dark:text-gray-100">Historial de Cambios de Turno</h2>

        {{-- FILTROS manejados desde laravel sin js (desde el contorller del metodo indexAdmin) --}}
        <form method="GET" action="{{ route('cambio-de-turno.index') }}" class="mb-4 flex flex-wrap gap-4 items-end">
            <div class="flex flex-col">
                <label for="playa_id" class="text-gray-700 dark:text-gray-300">Playa:</label>
                <select name="playa_id" id="playa_id" class="border rounded px-2 py-1 dark:bg-gray-700 dark:text-gray-200">
                    <option value="">Todas</option>
                    @foreach ($playas as $playa)
                        <option value="{{ $playa->id }}" {{ request('playa_id') == $playa->id ? 'selected' : '' }}>
                            {{ $playa->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label for="fecha" class="text-gray-700 dark:text-gray-300">Fecha:</label>
                <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}"
                    class="border rounded px-2 py-1 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Filtrar</button>
                <a href="{{ route('cambio-de-turno.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Limpiar</a>
            </div>
        </form>
    </div>

    <x-index-table :registros="$registros" movil paginar>
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
            <tr>
                <th class="px-4 py-2 text-left">Guardavida</th>
                <th class="px-4 py-2 text-left">Turno Nuevo</th>
                <th class="px-4 py-2 text-left">Fecha</th>
                <th class="px-4 py-2 text-left">Playa</th>
                <th class="px-4 py-2 text-left">Puesto</th>
                <th class="px-4 py-2 text-left">Función</th>
                <th class="px-4 py-2 text-left">Detalles</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
            @forelse ($registros as $cambio)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-2">{{ $cambio->guardavida->nombre }} {{ $cambio->guardavida->apellido }}</td>
                    <td class="px-4 py-2">
                        @if ($cambio->turno_nuevo === 'M')
                            <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded dark:text-blue-200">Mañana</span>
                        @else
                            <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded dark:text-yellow-200">Tarde</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $cambio->fecha->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">{{ $cambio->playa->nombre ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $cambio->puesto->nombre ?? '-' }}</td>
                    <td class="px-4 py-2">{{ Str::limit($cambio->funcion, 40) }}</td>
                    <td class="px-4 py-2">{{ Str::limit($cambio->detalles, 40) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No se registraron cambios de turno.</td>
                </tr>
            @endforelse
        </tbody>
    </x-index-table>
@endsection
