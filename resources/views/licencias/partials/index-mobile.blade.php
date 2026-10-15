{{-- Listado para celular: tocar la tarjeta abre el detalle; los tres puntos abren el menú inferior (en index.blade.php). --}}
<div class="space-y-4 sm:hidden">
    <section class="text-gray-600 dark:text-gray-100 body-font pt-4">
        <div class="space-y-3">
            @forelse ($registros as $registro)
                @php
                    $puedeModificar = $registro->modificablePor(auth()->user());
                    $guardavida = trim(($registro->guardavida->nombre ?? '').' '.($registro->guardavida->apellido ?? '')) ?: '-';
                    $titulo = "Licencia por {$registro->tipo_licencia} - {$guardavida}";
                    $acciones = array_values(array_filter([
                        ['texto' => 'Ver detalle', 'icono' => 'fa-eye', 'href' => route('licencia.show', $registro)],
                        $registro->archivo ? ['texto' => 'Ver constancia', 'icono' => 'fa-file-lines', 'href' => route('licencia.constancia', $registro), 'blank' => true] : null,
                        auth()->user()->can('editar_licencia') && $puedeModificar ? ['texto' => 'Editar', 'icono' => 'fa-pen', 'href' => route('licencia.edit', $registro)] : null,
                        auth()->user()->can('eliminar_licencia') && $puedeModificar ? [
                            'texto' => 'Eliminar', 'icono' => 'fa-trash', 'peligro' => true,
                            'form' => route('licencia.destroy', $registro), 'confirmar' => '¿Seguro que querés eliminar esta licencia?',
                        ] : null,
                    ]));
                @endphp

                <x-tarjeta-listado :href="route('licencia.show', $registro)" :titulo="$titulo" :acciones="$acciones"
                    :playa="$registro->playa->id ?? ''" :fecha="$registro->fecha_inicio->format('Y-m-d')">
                    <x-slot:lead>
                        <x-icono-listado icono="fa-file-medical" color="violet" />
                    </x-slot:lead>

                    <h3 class="truncate text-base font-semibold text-gray-900 dark:text-white">Licencia por {{ $registro->tipo_licencia }}</h3>
                    <p class="truncate text-sm text-gray-600 dark:text-gray-200">{{ $guardavida }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-300">
                        {{ $registro->fecha_inicio->format('d/m/Y') }} - {{ $registro->fecha_fin->format('d/m/Y') }}
                    </p>
                    <p class="truncate text-sm text-gray-500 dark:text-gray-300">{{ $registro->playa->nombre ?? '-' }}</p>
                    @if (filled($registro->detalle))
                        <p class="mt-1 line-clamp-1 text-xs italic text-gray-500 dark:text-gray-400">{{ Str::limit($registro->detalle, 80) }}</p>
                    @endif
                </x-tarjeta-listado>
            @empty
                <p class="rounded-xl border border-gray-200 bg-white px-4 py-6 text-center text-sm text-gray-500 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">No hay licencias para mostrar.</p>
            @endforelse
        </div>
    </section>

    @can('agregar_licencia')
        <a href="{{ route('licencia.create') }}" class="btn fixed z-40 flex items-center bg-sky-500 bottom-24 right-8 rounded-full px-3 py-3 shadow">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="text-sky-500 w-6 h-6 z-50 bg-gray-100 rounded me-2 dark:bg-gray-700">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span class="text-gray-100 text-lg"> Agregar</span>
        </a>
    @endcan
</div>
