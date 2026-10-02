@props(['titulo', 'icono' => 'user', 'descripcion' => null])

{{-- Una parte del formulario: subtítulo con ícono (+ descripción opcional) y su contenido (slot). --}}
<section class="space-y-3">
    <div class="mb-2">
        <div class="flex items-center gap-3">
            <x-postulacion-icono :nombre="$icono" class="h-5 w-5 shrink-0 text-sky-600" />
            <p class="text-xs font-semibold uppercase tracking-wide dark:text-white">{{ $titulo }}</p>
        </div>

        @if ($descripcion)
            <p class="mt-0.5 pl-8 text-xs text-gray-500 dark:text-gray-400">{{ $descripcion }}</p>
        @elseif (isset($ayuda) && trim((string) $ayuda) !== '')
            <p class="mt-0.5 pl-8 text-xs text-gray-500 dark:text-gray-400">{{ $ayuda }}</p>
        @endif
    </div>

    {{ $slot }}
</section>
