{{-- Listado para celular: tocar la tarjeta abre el detalle; los tres puntos abren el menú inferior (en index.blade.php). --}}
<div class="space-y-4 sm:hidden">
    <section class="text-gray-600 dark:text-gray-100 body-font pt-3">
        <div class="space-y-3">
            @forelse ($intervenciones as $intervencion)
                @php
                    $puedeModificar = $intervencion->modificablePor(auth()->user());
                    $titulo = $intervencion->tipo_intervencion.' - '.$intervencion->fecha->format('d/m/Y');
                    $acciones = array_values(array_filter([
                        ['texto' => 'Ver detalle', 'icono' => 'fa-eye', 'href' => route('intervencion.show', $intervencion)],
                        auth()->user()->can('editar_intervencion') && $puedeModificar ? ['texto' => 'Editar', 'icono' => 'fa-pen', 'href' => route('intervencion.edit', $intervencion)] : null,
                        auth()->user()->can('eliminar_intervencion') && $puedeModificar ? [
                            'texto' => 'Eliminar', 'icono' => 'fa-trash', 'peligro' => true,
                            'form' => route('intervencion.destroy', $intervencion), 'confirmar' => '¿Seguro que querés eliminar esta intervención?',
                        ] : null,
                    ]));
                @endphp

                <x-tarjeta-listado :href="route('intervencion.show', $intervencion)" :titulo="$titulo" :acciones="$acciones"
                    :playa="$intervencion->playa->id ?? ''" :fecha="$intervencion->fecha->format('Y-m-d')">
                    <x-slot:lead>
                        <x-icono-listado icono="fa-life-ring" color="red" />
                    </x-slot:lead>

                    <h3 class="truncate text-base font-semibold text-gray-900 dark:text-white">{{ $intervencion->tipo_intervencion }}</h3>
                    <p class="truncate text-sm text-gray-600 dark:text-gray-200">
                        {{ $intervencion->playa->nombre ?? '-' }}{{ $intervencion->puesto ? " - {$intervencion->puesto->nombre}" : '' }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-300">
                        {{ $intervencion->fecha->format('d/m/Y') }} · {{ $intervencion->fecha->format('H:i') }}
                    </p>
                    @if (filled($intervencion->detalles))
                        <p class="mt-1 line-clamp-1 text-xs italic text-gray-500 dark:text-gray-400">{{ Str::limit($intervencion->detalles, 80) }}</p>
                    @endif
                    @if ($intervencion->traslado)
                        <span class="mt-1 inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-900/40 dark:text-red-300">
                            <i class="fas fa-truck-medical" aria-hidden="true"></i> Hubo traslado
                        </span>
                    @endif
                </x-tarjeta-listado>
            @empty
                <p class="rounded-xl border border-gray-200 bg-white px-4 py-6 text-center text-sm text-gray-500 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">No hay intervenciones para mostrar.</p>
            @endforelse
        </div>
    </section>

    @can('agregar_intervencion')
        <a href="{{ route('intervencion.create') }}" class="btn fixed z-40 flex items-center bg-sky-500 dark:bg-sky-500 bottom-24 right-8 rounded-full px-3 py-3 shadow">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="text-sky-500 w-6 h-6 z-50 bg-gray-100 rounded me-2 dark:bg-gray-700">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span class="text-gray-100 text-lg"> Agregar</span>
        </a>
    @endcan
</div>
