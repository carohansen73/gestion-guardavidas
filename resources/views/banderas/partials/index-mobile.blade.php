{{-- Listado para celular: tocar la tarjeta abre el detalle; los tres puntos abren el menú inferior (en index.blade.php). --}}
<div class="space-y-4 sm:hidden">
    <section class="text-gray-600 dark:text-gray-100 body-font pt-4">
        <div class="space-y-3">
            @forelse ($registros as $registro)
                @php
                    $puedeModificar = $registro->modificablePor(auth()->user());
                    $titulo = ($registro->bandera->codigo ?? 'Bandera').' - '.$registro->fecha->format('d/m/Y');
                    $acciones = array_values(array_filter([
                        ['texto' => 'Ver detalle', 'icono' => 'fa-eye', 'href' => route('bandera.show', $registro), 'detalle' => true],
                        auth()->user()->can('editar_bandera') && $puedeModificar ? ['texto' => 'Editar', 'icono' => 'fa-pen', 'href' => route('bandera.edit', $registro)] : null,
                        auth()->user()->can('eliminar_bandera') && $puedeModificar ? [
                            'texto' => 'Eliminar', 'icono' => 'fa-trash', 'peligro' => true,
                            'form' => route('bandera.destroy', $registro), 'confirmar' => '¿Seguro que querés eliminar este registro de bandera?',
                        ] : null,
                    ]));
                @endphp

                <x-tarjeta-listado modal :href="route('bandera.show', $registro)" :titulo="$titulo" :acciones="$acciones"
                    :playa="$registro->playa->id ?? ''" :fecha="$registro->fecha->format('Y-m-d')">
                    <x-slot:lead>
                        <x-icono-bandera :bandera="$registro->bandera" />
                    </x-slot:lead>

                    <h3 class="truncate text-base font-semibold text-gray-900 dark:text-white">{{ $registro->bandera->codigo ?? '-' }}</h3>
                    <p class="truncate text-sm text-gray-600 dark:text-gray-200">{{ $registro->playa->nombre ?? '-' }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-300">
                        {{ $registro->fecha->format('d/m/Y') }} · {{ $registro->fecha->format('H:i') }}
                        @if ($registro->turno) · T.{{ $registro->turno }}@endif
                    </p>
                    @if (filled($registro->detalles))
                        <p class="mt-1 line-clamp-1 text-xs italic text-gray-500 dark:text-gray-400">{{ Str::limit($registro->detalles, 80) }}</p>
                    @endif
                </x-tarjeta-listado>
            @empty
                <p class="rounded-xl border border-gray-200 bg-white px-4 py-6 text-center text-sm text-gray-500 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">No hay registros de bandera para mostrar.</p>
            @endforelse
        </div>
    </section>

    @can('agregar_bandera')
        <a href="{{ route('bandera.create') }}" class="btn fixed z-40 flex items-center bg-sky-500 dark:bg-sky-500 bottom-24 right-8 rounded-full px-3 py-3 shadow">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="text-sky-500 w-6 h-6 z-50 bg-gray-100 rounded me-2 dark:bg-gray-700">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span class="text-gray-100 text-lg"> Agregar</span>
        </a>
    @endcan
</div>
