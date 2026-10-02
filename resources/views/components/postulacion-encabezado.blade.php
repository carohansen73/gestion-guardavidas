@props(['paso', 'titulo', 'total' => 4])

{{-- Encabezado de cada paso del formulario: ícono + "Paso N de 4" + título + descripción (slot).
     Un ícono por paso (ver x-postulacion-icono). --}}
@php
    $iconos = [1 => 'user', 2 => 'calendar', 3 => 'upload', 4 => 'clipboard-check'];
@endphp
<div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
    <div class="mb-2 flex items-start gap-3">
        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-sky-600/15 text-sky-600">
            <x-postulacion-icono :nombre="$iconos[$paso] ?? 'user'" class="h-5 w-5" />
        </span>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Paso {{ $paso }} de {{ $total }}</p>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">{{ $titulo }}</h2>
        </div>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">
        {{ $slot }}
    </p>
</div>
