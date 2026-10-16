{{-- Tarjeta de un registro en los listados del celular. Tocarla abre `href` (la ficha / el detalle); el botón de
     tres puntos abre el menú inferior (<x-menu-inferior/>) con `acciones` (ver ese componente para el formato).
     Mantiene la clase `registro-item-lista` y los data-* que usa public/js/table-intervenciones.js para filtrar.
     Slots: `lead` (avatar o ícono a la izquierda), el contenido principal (slot por defecto) y `pie` (líneas extra
     debajo, fuera del link, ej. teléfono o email). --}}
@props(['href', 'titulo', 'acciones' => [], 'playa' => null, 'fecha' => null, 'modal' => false])

<div {{ $attributes->merge(['class' => 'registro-item-lista overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800']) }}
    data-playa="{{ $playa }}" @if ($fecha) data-fecha="{{ $fecha }}" @endif>

    <div class="flex items-center gap-1">
        <a href="{{ $href }}" @if ($modal) data-detalle @endif
            class="flex min-w-0 flex-1 items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ isset($pie) ? 'pb-1' : '' }}">
            @isset($lead)
                {{ $lead }}
            @endisset
            <div class="min-w-0 flex-1">
                {{ $slot }}
            </div>
        </a>

        @if (count($acciones))
            <button type="button" aria-label="Más acciones de {{ $titulo }}"
                @click="menu = { titulo: @js($titulo), acciones: @js(array_values($acciones)) }"
                class="mr-2 flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" class="h-5 w-5" aria-hidden="true">
                    <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                </svg>
            </button>
        @endif
    </div>

    @isset($pie)
        {{ $pie }}
    @endisset
</div>
