@extends('layouts.dashboard')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/listadoTurnos.css') }}">

    <div class="container mx-auto mt-4 px-4">

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

        {{-- LISTA RESPONSIVE --}}
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-300 dark:border-gray-700">
                <thead class="bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-100">
                    <tr>
                        <th class="px-3 py-2 border border-gray-300 dark:border-gray-700">Guardavida</th>
                        <th class="px-3 py-2 border border-gray-300 dark:border-gray-700">Turno Nuevo</th>
                        <th class="px-3 py-2 border border-gray-300 dark:border-gray-700">Fecha</th>
                        <th class="px-3 py-2 border border-gray-300 dark:border-gray-700">Playa</th>
                        <th class="px-3 py-2 border border-gray-300 dark:border-gray-700">Puesto</th>
                        <th class="px-3 py-2 border border-gray-300 dark:border-gray-700">Función</th>
                        <th class="px-3 py-2 border border-gray-300 dark:border-gray-700">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registros as $cambio)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="px-3 py-2 border border-gray-300 dark:border-gray-700">
                                {{ $cambio->guardavida->nombre }} {{ $cambio->guardavida->apellido }}</td>
                            <td class="px-3 py-2 border border-gray-300 dark:border-gray-700">
                                @if ($cambio->turno_nuevo === 'M')
                                    <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded">Mañana</span>
                                @else
                                    <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded">Tarde</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 border border-gray-300 dark:border-gray-700">
                                {{ $cambio->fecha->format('d/m/Y') }}</td>
                            <td class="px-3 py-2 border border-gray-300 dark:border-gray-700">
                                {{ $cambio->playa->nombre ?? '-' }}</td>
                            <td class="px-3 py-2 border border-gray-300 dark:border-gray-700">
                                {{ $cambio->puesto->nombre ?? '-' }}</td>
                            <td class="px-3 py-2 border border-gray-300 dark:border-gray-700">
                                {{ Str::limit($cambio->funcion, 40) }}</td>
                            <td class="px-3 py-2 border border-gray-300 dark:border-gray-700">
                                {{ Str::limit($cambio->detalles, 40) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 dark:text-gray-400 py-4">No se registraron
                                cambios de turno.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN --}}
        <div class="mt-4">
            {{ $registros->links() }}
        </div>

    </div>
@endsection
