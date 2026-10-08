@extends('layouts.postulante')
@section('content')

@include('postulaciones.postulante._pasos', ['paso' => 2])

@php
    $input = 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
@endphp

<x-form-tarjeta method="POST" action="{{ route('postulacion.guardar', 2) }}">
    @csrf

    <x-form-encabezado :paso="2" :titulo="'Inscripción a '.$temporada->nombre">
        Indicá cuándo podés trabajar y, si querés, qué playas preferís. La asignación final la define el equipo según los cupos.
    </x-form-encabezado>

    {{-- Cada parte va en su propia sección (ver x-form-seccion): poco espacio adentro y
         mucho más entre partes (la separación la da <x-form-tarjeta>). --}}
    <x-form-seccion titulo="Disponibilidad" icono="calendar" descripcion="¿Entre qué fechas podés trabajar?">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="disponible_desde" class="{{ $label }}">Disponible desde</label>
                <input type="date" name="disponible_desde" id="disponible_desde" class="{{ $input }}"
                    value="{{ old('disponible_desde', $postulacion->disponible_desde?->format('Y-m-d')) }}">
            </div>
            <div>
                <label for="disponible_hasta" class="{{ $label }}">Disponible hasta</label>
                <input type="date" name="disponible_hasta" id="disponible_hasta" class="{{ $input }}"
                    value="{{ old('disponible_hasta', $postulacion->disponible_hasta?->format('Y-m-d')) }}">
            </div>
        </div>
    </x-form-seccion>

    <x-form-seccion titulo="Playas de preferencia" icono="map-pin" descripcion="Opcional. La segunda opción se tiene en cuenta si no hay cupo en la primera.">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="playa_1" class="{{ $label }}">Playa preferida <span class="text-gray-400">(opcional)</span></label>
                <select name="playa_1" id="playa_1" class="{{ $input }}">
                    <option value="">Sin preferencia</option>
                    @foreach ($playas as $playa)
                        <option value="{{ $playa->id }}" @selected((int) old('playa_1', $playaIds[1] ?? 0) === $playa->id)>{{ $playa->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="playa_2" class="{{ $label }}">Segunda opción <span class="text-gray-400">(opcional)</span></label>
                <select name="playa_2" id="playa_2" class="{{ $input }}">
                    <option value="">Sin segunda opción</option>
                    @foreach ($playas as $playa)
                        <option value="{{ $playa->id }}" @selected((int) old('playa_2', $playaIds[2] ?? 0) === $playa->id)>{{ $playa->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-form-seccion>

    <div class="flex justify-between items-center">
        <a href="{{ route('postulacion.paso', 1) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Anterior</a>
        <button type="submit" class="bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar y continuar</button>
    </div>
</x-form-tarjeta>

@endsection
