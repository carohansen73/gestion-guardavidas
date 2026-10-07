@extends('layouts.app')
@section('content')

@php
    $input = 'rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm';
@endphp

<div class="text-gray-600 dark:text-gray-100 body-font sm:px-4">
    <div class="flex flex-wrap justify-between items-center gap-2 mb-2 sm:mt-4">
        <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl">Cerrar la selección</h2>
        <a href="{{ route('postulaciones.seleccion', ['temporada' => $temporadaId]) }}" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Volver a la selección</a>
    </div>
    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
        Estos guardavidas siguen en el plantel pero <strong>no fueron seleccionados</strong> en la temporada elegida (no se postularon o se decidió no volver a elegirlos).
        Al cerrar pasan a rol <strong>postulante</strong>: conservan su usuario, su historial (asistencias, licencias, intervenciones) y pueden volver a postularse.
        Destildá a quien quieras mantener como guardavida.
    </p>

    <x-session-alerts />

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-md bg-red-100 text-red-800 text-sm dark:bg-red-900/40 dark:text-red-300">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="GET" action="{{ route('postulaciones.seleccion.cierre') }}" class="flex items-end gap-3 mb-4">
        <div>
            <label for="temporada" class="block text-xs text-gray-500 dark:text-gray-400">Temporada</label>
            <select name="temporada" id="temporada" class="{{ $input }}" onchange="this.form.submit()">
                @foreach ($temporadas as $t)
                    <option value="{{ $t->id }}" @selected($t->id === $temporadaId)>{{ $t->nombre }}</option>
                @endforeach
            </select>
        </div>
        <span class="text-sm">Seleccionados en esta temporada: <strong>{{ $seleccionadas }}</strong></span>
    </form>

    @if ($seleccionadas === 0)
        <div class="mb-4 p-3 rounded-md bg-amber-100 text-amber-800 text-sm dark:bg-amber-900/40 dark:text-amber-300">
            Todavía no seleccionaste a nadie en esta temporada. Hacé primero la selección: si cerrás ahora, todo el plantel quedaría como postulante.
        </div>
    @endif
</div>

@if ($seleccionadas > 0)
    <form method="POST" action="{{ route('postulaciones.seleccion.cerrar') }}">
        @csrf
        <input type="hidden" name="temporada" value="{{ $temporadaId }}">

        <x-index-table :registros="$candidatos" movil>
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left w-10"></th>
                    <th class="px-4 py-2 text-left">Apellido y nombre</th>
                    <th class="px-4 py-2 text-left">DNI</th>
                    <th class="px-4 py-2 text-left">Último puesto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @forelse ($candidatos as $guardavida)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-2">
                            <input type="checkbox" name="guardavidas[]" value="{{ $guardavida->id }}" checked
                                class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        </td>
                        <td class="px-4 py-2">{{ $guardavida->user->lastname }}, {{ $guardavida->user->name }}</td>
                        <td class="px-4 py-2">{{ $guardavida->user->dni }}</td>
                        <td class="px-4 py-2">{{ $guardavida->playa?->nombre }} · {{ $guardavida->puesto?->nombre }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No queda nadie por cerrar: todo el plantel fue seleccionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-index-table>

        @if ($candidatos->isNotEmpty())
            <div class="sm:px-4 pb-8 -mt-6 sm:-mt-8">
                <div class="flex flex-wrap items-center justify-end gap-4">
                    <div>
                        <label for="hasta" class="block text-xs text-gray-500 dark:text-gray-400">Fecha de baja (último día en el plantel)</label>
                        <input type="date" id="hasta" name="hasta" value="{{ old('hasta', now()->toDateString()) }}" required class="{{ $input }}">
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="entiendo" value="1" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        Entiendo que los tildados pasan a postulante y pierden el acceso a la operación del sistema.
                    </label>
                    <button type="submit" class="px-4 py-2 rounded-md bg-red-500 hover:bg-red-400 text-white text-sm font-medium"
                        onclick="return confirm('¿Pasar a postulante a los guardavidas tildados?')">
                        Cerrar selección
                    </button>
                </div>
            </div>
        @endif
    </form>
@endif

@endsection
