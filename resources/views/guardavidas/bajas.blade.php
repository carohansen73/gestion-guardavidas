@extends('layouts.app')
@section('content')

@php
    $input = 'rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm';
@endphp

<div x-data="{ reingreso: null }">

    <div class="text-gray-600 dark:text-gray-100 body-font sm:px-4">
        <div class="flex flex-wrap justify-between items-center gap-2 mb-2 sm:mt-4">
            <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl">Dados de baja</h2>
            <a href="{{ route('guardavida.index') }}" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Volver a guardavidas</a>
        </div>
        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
            Personas que tuvieron un puesto en el plantel y hoy están fuera de él (baja o no seleccionadas en la temporada).
            Conservan su historial y pueden volver a postularse. Desde acá se las puede <strong>volver a dar de alta</strong> directamente.
        </p>

        <x-session-alerts />

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-md bg-red-100 text-red-800 text-sm dark:bg-red-900/40 dark:text-red-300">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="GET" action="{{ route('guardavidas.bajas') }}" class="flex flex-wrap items-end gap-3 mb-4">
            <div>
                <label for="buscar" class="block text-xs text-gray-500 dark:text-gray-400">Buscar</label>
                <input type="text" name="buscar" id="buscar" value="{{ $buscar }}" placeholder="Nombre, apellido o DNI" class="{{ $input }}">
            </div>
            <button type="submit" class="px-3 py-2 rounded-md bg-sky-500 hover:bg-sky-400 text-white text-sm">Filtrar</button>
        </form>
    </div>

    <x-index-table :registros="$guardavidas" movil paginar>
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
            <tr>
                <th class="px-4 py-2 text-left">Apellido y nombre</th>
                <th class="px-4 py-2 text-left">DNI</th>
                <th class="px-4 py-2 text-left">Último puesto</th>
                <th class="px-4 py-2 text-left">Última baja</th>
                <th class="px-4 py-2 text-left"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
            @forelse ($guardavidas as $guardavida)
                @php $ultimo = $guardavida->periodos->sortBy('id')->last(); @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-2">{{ $guardavida->user->lastname }}, {{ $guardavida->user->name }}</td>
                    <td class="px-4 py-2">{{ $guardavida->user->dni }}</td>
                    <td class="px-4 py-2">{{ $guardavida->playa?->nombre }}{{ $guardavida->puesto ? ' · '.$guardavida->puesto->nombre : '' }}</td>
                    <td class="px-4 py-2">
                        @if ($ultimo?->hasta)
                            {{ $ultimo->hasta->format('d/m/Y') }}
                            @if ($ultimo->motivo)
                                <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $ultimo->motivo }}</span>
                            @endif
                        @else
                            <span class="text-xs text-gray-500 dark:text-gray-400">Sin fecha registrada</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @can('agregar_guardavida')
                            <button type="button"
                                @click="reingreso = { url: @js(route('guardavida.reincorporar', $guardavida)), nombre: @js($guardavida->user->lastname.', '.$guardavida->user->name) }"
                                class="text-sky-500 hover:text-sky-400 dark:text-sky-400">Volver a dar de alta</button>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No hay personas dadas de baja.</td>
                </tr>
            @endforelse
        </tbody>
    </x-index-table>

    @can('agregar_guardavida')
        <div x-show="reingreso" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" @keydown.escape.window="reingreso = null">
            <div class="w-full max-w-md rounded-lg bg-white dark:bg-gray-800 shadow-lg p-6" @click.outside="reingreso = null">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Volver a dar de alta</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    <strong x-text="reingreso && reingreso.nombre"></strong> vuelve al plantel con su playa y puesto anteriores
                    (se pueden cambiar después desde su ficha).
                </p>
                <form :action="reingreso && reingreso.url" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label for="reingreso-fecha" class="block text-sm font-medium text-gray-900 dark:text-white">Primer día de trabajo</label>
                        <input id="reingreso-fecha" type="date" name="fecha" value="{{ now()->toDateString() }}" required class="mt-1 w-full {{ $input }}">
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="reingreso = null" class="px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Cancelar</button>
                        <button type="submit" class="rounded-md bg-sky-500 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-400">Dar de alta</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan
</div>

@endsection
