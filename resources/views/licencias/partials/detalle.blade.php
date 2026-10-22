{{-- Contenido del detalle de una licencia (modal y respaldo show-fields). Solo lectura. --}}
@php
    $puedeEditar = auth()->user()->can('update', $licencia);
    $guardavida = trim(($licencia->guardavida->nombre ?? '').' '.($licencia->guardavida->apellido ?? '')) ?: '-';
    $dias = $licencia->fecha_inicio->diffInDays($licencia->fecha_fin) + 1;
    $turno = ['M' => 'Mañana', 'T' => 'Tarde'][$licencia->turno] ?? $licencia->turno;
@endphp

<div class="flex items-center gap-4 border-b border-gray-200 px-5 py-5 pr-14 dark:border-gray-700">
    <x-icono-listado icono="fa-file-medical" color="violet" size="w-14 h-14" />
    <div class="min-w-0">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Licencia por {{ $licencia->tipo_licencia }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $guardavida }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $licencia->fecha_inicio->format('d/m/Y') }} al {{ $licencia->fecha_fin->format('d/m/Y') }} · {{ $dias }} {{ $dias === 1 ? 'día' : 'días' }}
        </p>
    </div>
</div>

<div class="space-y-5 px-5 py-5">
    <dl class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-4">
        <x-detalle-dato label="Playa">{{ $licencia->playa->nombre ?? '-' }}</x-detalle-dato>
        <x-detalle-dato label="Puesto">{{ $licencia->puesto->nombre ?? '-' }}</x-detalle-dato>
        <x-detalle-dato label="Turno">{{ $turno ?: '-' }}</x-detalle-dato>
        <x-detalle-dato label="Avisó en tiempo">{{ $licencia->en_tiempo ? 'Sí' : 'No' }}</x-detalle-dato>
    </dl>

    <div>
        <h4 class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Constancia</h4>
        @if ($licencia->archivo)
            <a href="{{ route('licencia.constancia', $licencia) }}" target="_blank" rel="noopener"
                class="inline-flex items-center gap-2 rounded-md border border-sky-600 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-400 dark:text-sky-300 dark:hover:bg-sky-900/30">
                <i class="fas fa-file-lines" aria-hidden="true"></i> Ver constancia
            </a>
        @else
            <p class="text-sm italic text-gray-500 dark:text-gray-400">Sin archivo adjunto.</p>
        @endif
    </div>

    <div>
        <h4 class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Detalles</h4>
        @if (filled($licencia->detalle))
            <p class="whitespace-pre-line break-words text-sm leading-relaxed text-gray-800 dark:text-gray-200">{{ $licencia->detalle }}</p>
        @else
            <p class="text-sm italic text-gray-500 dark:text-gray-400">Sin detalles registrados.</p>
        @endif
    </div>
</div>

@if ($puedeEditar)
    <div class="flex justify-end border-t border-gray-200 px-5 py-3 dark:border-gray-700">
        <a href="{{ route('licencia.edit', $licencia) }}"
            class="inline-flex items-center gap-2 rounded-md bg-sky-600 px-5 py-2 text-sm font-medium text-white shadow hover:bg-sky-500 dark:bg-sky-700 dark:hover:bg-sky-600">
            <i class="fas fa-pen" aria-hidden="true"></i> Editar
        </a>
    </div>
@endif
