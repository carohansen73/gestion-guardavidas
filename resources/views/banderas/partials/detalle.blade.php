{{-- Contenido del detalle de un registro de bandera (modal y respaldo show-fields). Solo lectura. --}}
@php
    $puedeEditar = auth()->user()->can('editar_bandera') && $bandera->modificablePor(auth()->user());
    $turno = ['M' => 'Mañana', 'T' => 'Tarde'][$bandera->turno] ?? $bandera->turno;
@endphp

<div class="flex items-center gap-4 border-b border-gray-200 px-5 py-5 pr-14 dark:border-gray-700">
    <x-icono-bandera :bandera="$bandera->bandera" size="w-14 h-14" />
    <div class="min-w-0">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $bandera->bandera?->codigo ?? 'Bandera' }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $bandera->playa?->nombre ?? '-' }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $bandera->fecha->format('d/m/Y') }} · {{ $bandera->fecha->format('H:i') }} hs</p>
    </div>
</div>

<div class="space-y-5 px-5 py-5">
    <dl class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3">
        <x-detalle-dato label="Turno">{{ $turno ?: '-' }}</x-detalle-dato>
        <x-detalle-dato label="Viento">{{ trim($bandera->viento_intensidad.' '.$bandera->viento_direccion) ?: '-' }}</x-detalle-dato>
        <x-detalle-dato label="Temperatura">{{ $bandera->temperatura !== null && $bandera->temperatura !== '' ? $bandera->temperatura.'º' : '-' }}</x-detalle-dato>
    </dl>

    <div>
        <h4 class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Detalles</h4>
        @if (filled($bandera->detalles))
            <p class="whitespace-pre-line break-words text-sm leading-relaxed text-gray-800 dark:text-gray-200">{{ $bandera->detalles }}</p>
        @else
            <p class="text-sm italic text-gray-500 dark:text-gray-400">Sin detalles registrados.</p>
        @endif
    </div>

    @if ($bandera->user)
        <p class="text-xs text-gray-500 dark:text-gray-400">Registrada por {{ $bandera->user->name }} {{ $bandera->user->lastname }}</p>
    @endif
</div>

@if ($puedeEditar)
    <div class="flex justify-end border-t border-gray-200 px-5 py-3 dark:border-gray-700">
        <a href="{{ route('bandera.edit', $bandera) }}"
            class="inline-flex items-center gap-2 rounded-md bg-sky-600 px-5 py-2 text-sm font-medium text-white shadow hover:bg-sky-500 dark:bg-sky-700 dark:hover:bg-sky-600">
            <i class="fas fa-pen" aria-hidden="true"></i> Editar
        </a>
    </div>
@endif
