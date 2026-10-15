{{-- Círculo con un ícono (Font Awesome) para el lado izquierdo de las tarjetas de los listados del celular. --}}
@props(['icono', 'color' => 'sky', 'size' => 'w-12 h-12'])

@php
    $fondo = match ($color) {
        'red' => 'bg-red-600 dark:bg-red-700',
        'amber' => 'bg-amber-500 dark:bg-amber-600',
        'teal' => 'bg-teal-600 dark:bg-teal-700',
        'violet' => 'bg-violet-600 dark:bg-violet-700',
        'gray' => 'bg-gray-500 dark:bg-gray-600',
        default => 'bg-sky-600 dark:bg-sky-700',
    };
@endphp

<div {{ $attributes->merge(['class' => "{$size} {$fondo} shrink-0 flex items-center justify-center rounded-full text-white"]) }}>
    <i class="fas {{ $icono }} text-lg" aria-hidden="true"></i>
</div>
