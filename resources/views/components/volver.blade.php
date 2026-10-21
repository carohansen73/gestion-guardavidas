{{-- "← Volver" para pantallas de detalle y formularios. Va arriba de todo, antes del título. Vuelve a la página anterior
     (así se conservan los filtros del listado). `href` es el respaldo para cuando no hay historial (se abrió el link
     directo en una pestaña nueva): sin él, el botón no hace nada en ese caso. Se le puede pasar `class` (ej. `class="mb-4"`). --}}
@props(['href' => null, 'texto' => 'Volver'])

<button type="button"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-sky-100']) }}
    @if ($href) onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = @js($href); }"
    @else onclick="window.history.back()" @endif>
    <i class="fas fa-arrow-left" aria-hidden="true"></i>
    <span class="hover:underline">{{ $texto }}</span>
</button>
