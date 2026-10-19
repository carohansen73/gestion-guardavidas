@extends('layouts.app')
@section('content')

@php
    $colores = [
        'pendiente' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
        'aceptada' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
        'rechazada' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
        'incompleta' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
    ];
    $solapas = ['pendiente' => 'Pendientes', 'aceptada' => 'Aceptadas', 'rechazada' => 'Rechazadas', 'todas' => 'Todas'];
    $input = 'rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm';
@endphp

<div class="text-gray-600 dark:text-gray-100 body-font sm:px-4">
    <div class="flex justify-between align-center mb-4 sm:mt-4">
        <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl">Postulaciones</h2>
        @can('seleccionar_postulacion')
            <a href="{{ route('postulaciones.seleccion', ['temporada' => $temporadaId]) }}"
                class="px-3 py-2 rounded-md bg-sky-500 hover:bg-sky-400 text-white text-sm font-medium self-center">Selección de postulantes</a>
        @endcan
    </div>

    <x-session-alerts />

    {{-- Solapas por estado: se pueden tocar (son filtros). Van subrayadas, a diferencia de los datos
         informativos de otras pantallas, que son tarjetas. Los contadores son de toda la temporada. --}}
    <nav class="flex flex-wrap gap-x-6 gap-y-1 mb-4 border-b border-gray-200 dark:border-gray-700" aria-label="Filtrar por estado">
        @foreach ($solapas as $clave => $titulo)
            @php
                // "Pendientes" suma las incompletas (devueltas al postulante, aún sin resolver).
                $total = match ($clave) {
                    'todas' => collect($conteosPorEstado)->sum(),
                    'pendiente' => ($conteosPorEstado['pendiente'] ?? 0) + ($conteosPorEstado['incompleta'] ?? 0),
                    default => $conteosPorEstado[$clave] ?? 0,
                };
            @endphp
            <a href="{{ request()->fullUrlWithQuery(['estado' => $clave, 'page' => null]) }}"
                @if ($estado === $clave) aria-current="page" @endif
                class="-mb-px border-b-2 px-1 pb-2 pt-1 text-sm font-medium {{ $estado === $clave
                    ? 'border-sky-500 text-sky-600 dark:border-sky-400 dark:text-sky-400'
                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:border-gray-500 dark:hover:text-gray-200' }}">
                {{ $titulo }} <span class="ms-1 text-xs font-normal opacity-70">({{ $total }})</span>
            </a>
        @endforeach
    </nav>

    <form method="GET" action="{{ route('postulaciones.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <input type="hidden" name="estado" value="{{ $estado }}">
        <div>
            <label for="temporada" class="block text-xs text-gray-500 dark:text-gray-400">Temporada</label>
            <select name="temporada" id="temporada" class="{{ $input }}" onchange="this.form.submit()">
                @foreach ($temporadas as $temporada)
                    <option value="{{ $temporada->id }}" @selected($temporada->id === $temporadaId)>{{ $temporada->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="playa" class="block text-xs text-gray-500 dark:text-gray-400">Playa preferida</label>
            <select name="playa" id="playa" class="{{ $input }}" onchange="this.form.submit()">
                <option value="">Todas</option>
                @foreach ($playas as $playa)
                    <option value="{{ $playa->id }}" @selected((int) $playaId === $playa->id)>{{ $playa->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="buscar" class="block text-xs text-gray-500 dark:text-gray-400">Buscar</label>
            <input type="text" name="buscar" id="buscar" value="{{ $buscar }}" placeholder="Nombre, apellido o DNI" class="{{ $input }}">
        </div>
        <button type="submit" class="px-3 py-2 rounded-md bg-sky-500 hover:bg-sky-400 text-white text-sm">Filtrar</button>
    </form>

</div>

<x-index-table :registros="$postulaciones" movil paginar>
    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
        <tr>
            <th class="px-4 py-2 text-left">Apellido y nombre</th>
            <th class="px-4 py-2 text-left">DNI</th>
            <th class="px-4 py-2 text-left">Playas preferidas</th>
            <th class="px-4 py-2 text-left">Enviada</th>
            <th class="px-4 py-2 text-left">Estado</th>
            <th class="px-4 py-2 text-left"></th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
        @forelse ($postulaciones as $postulacion)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-2">{{ $postulacion->user->lastname }}, {{ $postulacion->user->name }}</td>
                <td class="px-4 py-2">{{ $postulacion->user->dni }}</td>
                <td class="px-4 py-2">
                    {{ $postulacion->playas->pluck('nombre')->implode(' / ') ?: '—' }}
                </td>
                <td class="px-4 py-2">{{ $postulacion->enviada_at?->format('d/m/y H:i') }}</td>
                <td class="px-4 py-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colores[$postulacion->estado] ?? '' }}">
                        {{ ucfirst($postulacion->estado) }}
                    </span>
                    @if ($postulacion->seleccionado)
                        <span class="ms-1 text-xs text-sky-600 dark:text-sky-400">seleccionado/a</span>
                    @endif
                    @if ($postulacion->modificada_at && $postulacion->estado === 'pendiente')
                        <span class="ms-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300"
                            title="El postulante modificó su inscripción después de enviarla">
                            Modificada {{ $postulacion->modificada_at->format('d/m H:i') }}
                        </span>
                    @endif
                </td>
                <td class="px-4 py-2">
                    <a href="{{ route('postulaciones.show', $postulacion) }}" class="text-sky-500 hover:text-sky-400 dark:text-sky-400">Ver</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                    No hay postulaciones para estos filtros.
                </td>
            </tr>
        @endforelse
    </tbody>
</x-index-table>

@endsection
