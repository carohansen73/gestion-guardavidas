{{-- Contenido del detalle de una novedad de material (modal y respaldo show-fields). Solo lectura. --}}
@php
    $puedeEditar = auth()->user()->can('update', $novedadDeMaterial);
    $material = trim(($novedadDeMaterial->material->nombre ?? '').' '.($novedadDeMaterial->material->detalle ?? '')) ?: '-';
@endphp

<div class="flex items-center gap-4 border-b border-gray-200 px-5 py-5 pr-14 dark:border-gray-700">
    <x-icono-listado icono="fa-toolbox" color="amber" size="w-14 h-14" />
    <div class="min-w-0">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $material }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $novedadDeMaterial->tipo_novedad?->value }} · {{ $novedadDeMaterial->playa->nombre ?? '-' }}</p>
        @if ($novedadDeMaterial->fecha)
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $novedadDeMaterial->fecha->format('d/m/Y') }} · {{ $novedadDeMaterial->fecha->format('H:i') }} hs</p>
        @endif
    </div>
</div>

<div class="space-y-5 px-5 py-5">
    <div>
        <h4 class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Detalles</h4>
        @if (filled($novedadDeMaterial->detalles))
            <p class="whitespace-pre-line break-words text-sm leading-relaxed text-gray-800 dark:text-gray-200">{{ $novedadDeMaterial->detalles }}</p>
        @else
            <p class="text-sm italic text-gray-500 dark:text-gray-400">Sin detalles registrados.</p>
        @endif
    </div>
</div>

@if ($puedeEditar)
    <div class="flex justify-end border-t border-gray-200 px-5 py-3 dark:border-gray-700">
        <a href="{{ route('novedad-de-material.edit', $novedadDeMaterial) }}"
            class="inline-flex items-center gap-2 rounded-md bg-sky-600 px-5 py-2 text-sm font-medium text-white shadow hover:bg-sky-500 dark:bg-sky-700 dark:hover:bg-sky-600">
            <i class="fas fa-pen" aria-hidden="true"></i> Editar
        </a>
    </div>
@endif
