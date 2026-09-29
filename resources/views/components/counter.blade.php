@props([
    'value',
    'label',
    'description' => null,
    'iconBg' => 'bg-sky-100 dark:bg-sky-900/40',
    'valueId' => null,
])

<div class="glass min-w-0 rounded-3xl p-3">

    <div class="relative grid h-10 w-10 place-items-center rounded-2xl {{ $iconBg }}">
        {{-- Slot para pasar html / otro componente (el SVG, y opcionalmente
             un badge tipo notificación posicionado "absolute" encima —
             el "relative" de acá arriba es lo que lo ancla a este div). --}}
        {{ $slot }}
    </div>

    <div class="mt-3">
        <span @if($valueId) id="{{ $valueId }}" @endif class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-3">
             {{ $value }}
        </span>
        <p class="truncate text-sm font-semibold">{{ $label }}</p>
        @if($description)
            <p class="text-xs text-gray-800">{{ $description }}</p>
        @endif

          <!-- TODO Ocultar mobile
        Porcentaje por playa -->
                {{-- <div class="flex flex-col justify-end items-end text-end" id="porcentajeIntervencionesPorPlaya">
                </div> --}}
    </div>
</div>
