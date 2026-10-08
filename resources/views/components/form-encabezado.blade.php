@props(['titulo', 'paso' => null, 'total' => 4, 'icono' => null, 'etiqueta' => null])

{{-- Encabezado de un formulario: ícono + (opcional) "Paso N de 4" o una etiqueta corta + título + descripción (slot).
     En los pasos de la postulación se pasa `paso` y el ícono sale solo; en cualquier otro formulario se pasa `icono`
     (ver x-form-icono) y, si hace falta, `etiqueta` (ej. "Nuevo registro"). Va como primer hijo de <x-form-tarjeta>. --}}
@php
    $iconosPaso = [1 => 'user', 2 => 'calendar', 3 => 'upload', 4 => 'clipboard-check'];
    $icono = $icono ?? ($paso ? ($iconosPaso[$paso] ?? 'user') : 'file-text');
    $linea = $paso ? "Paso {$paso} de {$total}" : $etiqueta;
@endphp
<header class="border-b border-gray-200 dark:border-gray-700 pb-4">
    <div class="mb-2 flex items-start gap-3">
        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-sky-600/15 text-sky-600">
            <x-form-icono :nombre="$icono" class="h-5 w-5" />
        </span>
        <div>
            @if ($linea)
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $linea }}</p>
            @endif
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">{{ $titulo }}</h2>
        </div>
    </div>
    @if (trim((string) $slot) !== '')
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $slot }}
        </p>
    @endif
</header>
