{{-- Contenido del detalle de un cambio de turno (modal y respaldo show-fields). Solo lectura. --}}
@php
    $puedeEditar = auth()->user()->can('editar_cambio_turno') && $cambioDeTurno->modificablePor(auth()->user());
    $guardavida = trim(($cambioDeTurno->guardavida->nombre ?? '').' '.($cambioDeTurno->guardavida->apellido ?? '')) ?: '-';
    $turnoNuevo = ['M' => 'Mañana', 'T' => 'Tarde'][$cambioDeTurno->turno_nuevo] ?? $cambioDeTurno->turno_nuevo;
@endphp

<div class="flex items-center gap-4 border-b border-gray-200 px-5 py-5 pr-14 dark:border-gray-700">
    <x-icono-listado icono="fa-right-left" color="teal" size="w-14 h-14" />
    <div class="min-w-0">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $guardavida }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300">Cambió al turno {{ $turnoNuevo }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $cambioDeTurno->fecha->format('d/m/Y') }}</p>
    </div>
</div>

<div class="space-y-5 px-5 py-5">
    <dl class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3">
        <x-detalle-dato label="Playa">{{ $cambioDeTurno->playa->nombre ?? '-' }}</x-detalle-dato>
        <x-detalle-dato label="Puesto">{{ $cambioDeTurno->puesto->nombre ?? '-' }}</x-detalle-dato>
        <x-detalle-dato label="Función">{{ $cambioDeTurno->funcion ? str_replace('_', ' ', $cambioDeTurno->funcion) : '-' }}</x-detalle-dato>
    </dl>

    <div>
        <h4 class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Detalles</h4>
        @if (filled($cambioDeTurno->detalles))
            <p class="whitespace-pre-line break-words text-sm leading-relaxed text-gray-800 dark:text-gray-200">{{ $cambioDeTurno->detalles }}</p>
        @else
            <p class="text-sm italic text-gray-500 dark:text-gray-400">Sin detalles registrados.</p>
        @endif
    </div>
</div>

@if ($puedeEditar)
    <div class="flex justify-end border-t border-gray-200 px-5 py-3 dark:border-gray-700">
        <a href="{{ route('cambio-de-turno.edit', $cambioDeTurno) }}"
            class="inline-flex items-center gap-2 rounded-md bg-sky-600 px-5 py-2 text-sm font-medium text-white shadow hover:bg-sky-500 dark:bg-sky-700 dark:hover:bg-sky-600">
            <i class="fas fa-pen" aria-hidden="true"></i> Editar
        </a>
    </div>
@endif
