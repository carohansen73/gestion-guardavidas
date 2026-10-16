{{-- Contenido del detalle de una intervención (lo muestra el modal y, como respaldo, show-fields). Solo lectura:
     "Editar" aparece solo si el usuario puede modificarla. --}}
@php
    $bandera = $intervencion->bandera;
    $puedeEditar = auth()->user()->can('editar_intervencion') && $intervencion->modificablePor(auth()->user());
@endphp

<div class="flex items-center gap-4 border-b border-gray-200 px-5 py-5 pr-14 dark:border-gray-700">
    <x-icono-listado icono="fa-life-ring" color="red" size="w-14 h-14" />
    <div class="min-w-0">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $intervencion->tipo_intervencion }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ $intervencion->playa?->nombre ?? '-' }}{{ $intervencion->puesto ? ' · '.$intervencion->puesto->nombre : '' }}
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $intervencion->fecha->format('d/m/Y') }} · {{ $intervencion->fecha->format('H:i') }} hs</p>
    </div>
</div>

<div class="space-y-5 px-5 py-5">
    <dl class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-4">
        <x-detalle-dato label="Víctimas">{{ $intervencion->victimas ?? '-' }}</x-detalle-dato>
        <x-detalle-dato label="Código">{{ $intervencion->codigo ?: '-' }}</x-detalle-dato>
        <x-detalle-dato label="Traslado">
            @if ($intervencion->traslado)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-900/40 dark:text-red-300">
                    <i class="fas fa-truck-medical" aria-hidden="true"></i> Sí
                </span>
            @else
                No
            @endif
        </x-detalle-dato>
        <x-detalle-dato label="Bandera">
            @if ($bandera)
                <span class="inline-flex items-center gap-2">
                    <x-icono-bandera :bandera="$bandera->bandera" size="w-7 h-7" class="[&>i]:!text-xs" />
                    {{ $bandera->bandera?->codigo }}
                </span>
            @else
                <span class="text-gray-500 dark:text-gray-400">Sin registrar</span>
            @endif
        </x-detalle-dato>
    </dl>

    @if ($bandera && ($bandera->viento_intensidad || $bandera->viento_direccion || $bandera->temperatura))
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Condiciones del mar:
            @if ($bandera->viento_intensidad || $bandera->viento_direccion)
                viento {{ trim($bandera->viento_intensidad.' '.$bandera->viento_direccion) }}
            @endif
            @if ($bandera->temperatura) · {{ $bandera->temperatura }}º @endif
        </p>
    @endif

    <div>
        <h4 class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Guardavidas que intervinieron</h4>
        @forelse ($intervencion->guardavidas as $guardavida)
            @if ($loop->first) <ul class="grid grid-cols-1 gap-2 sm:grid-cols-2"> @endif
                <li class="flex items-center gap-2.5">
                    <x-avatar-guardavida :guardavida="$guardavida" size="w-8 h-8" icono="text-sm" />
                    <span class="truncate text-sm text-gray-900 dark:text-gray-100">{{ $guardavida->nombre }} {{ $guardavida->apellido }}</span>
                </li>
            @if ($loop->last) </ul> @endif
        @empty
            <p class="text-sm italic text-gray-500 dark:text-gray-400">No se registraron guardavidas.</p>
        @endforelse
    </div>

    <div>
        <h4 class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Otras fuerzas</h4>
        @forelse ($intervencion->fuerzas as $fuerza)
            @if ($loop->first) <div class="flex flex-wrap gap-2"> @endif
                <span class="rounded-full bg-sky-100 px-3 py-1 text-sm font-medium text-sky-800 dark:bg-sky-900/40 dark:text-sky-200">{{ $fuerza->nombre }}</span>
            @if ($loop->last) </div> @endif
        @empty
            <p class="text-sm italic text-gray-500 dark:text-gray-400">No intervinieron otras fuerzas.</p>
        @endforelse
    </div>

    <div>
        <h4 class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Detalles</h4>
        @if (filled($intervencion->detalles))
            <p class="whitespace-pre-line break-words text-sm leading-relaxed text-gray-800 dark:text-gray-200">{{ $intervencion->detalles }}</p>
        @else
            <p class="text-sm italic text-gray-500 dark:text-gray-400">Sin detalles registrados.</p>
        @endif
    </div>

    @if ($intervencion->user)
        <p class="text-xs text-gray-500 dark:text-gray-400">Registrada por {{ $intervencion->user->name }} {{ $intervencion->user->lastname }}</p>
    @endif
</div>

@if ($puedeEditar)
    <div class="flex justify-end border-t border-gray-200 px-5 py-3 dark:border-gray-700">
        <a href="{{ route('intervencion.edit', $intervencion) }}"
            class="inline-flex items-center gap-2 rounded-md bg-sky-600 px-5 py-2 text-sm font-medium text-white shadow hover:bg-sky-500 dark:bg-sky-700 dark:hover:bg-sky-600">
            <i class="fas fa-pen" aria-hidden="true"></i> Editar
        </a>
    </div>
@endif
