{{-- Círculo con el color de la bandera del día (clases .bandera-* de resources/css/style.css) y el ícono de bandera. --}}
@props(['bandera', 'size' => 'w-12 h-12'])

@php
    $color = $bandera?->color;
    // "Niño perdido" no define fondo (solo fill blanco): se le da uno neutro para que se vea.
    $fondo = $color === 'bandera-perdido' || ! $color ? 'bg-gray-400 dark:bg-gray-500' : $color;
    // El amarillo y el celeste claro (modo oscuro) piden un ícono oscuro para leerse.
    $icono = match ($color) {
        'bandera-dudoso' => 'text-gray-900',
        'bandera-bueno' => 'text-white dark:text-sky-950',
        default => 'text-white',
    };
@endphp

<div {{ $attributes->merge(['class' => "{$size} {$fondo} shrink-0 flex items-center justify-center rounded-full shadow-sm"]) }}
    title="{{ $bandera?->codigo }}">
    <i class="fas fa-flag text-lg {{ $icono }}" aria-hidden="true"></i>
</div>
