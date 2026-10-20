{{-- Tarjetas del listado de asistencias para celular + paginación. Se incluye desde index.blade.php y también es
     lo único que devuelve el controlador a la búsqueda en vivo (petición AJAX), que reemplaza este bloque. --}}
<div class="space-y-3">
    @forelse ($guardavidas as $g)
        @php
            $r = $resumen[$g->id] ?? null;
            $porcentaje = $r['porcentaje'] ?? null;
            $faltas = $r['faltas'] ?? 0;
            $fuera = $r['fuera_de_rango'] ?? 0;
            $colorPorcentaje = match (true) {
                $porcentaje === null => 'text-gray-400 dark:text-gray-500',
                $porcentaje >= 90 => 'text-emerald-600 dark:text-emerald-400',
                $porcentaje >= 75 => 'text-amber-600 dark:text-amber-400',
                default => 'text-red-600 dark:text-red-400',
            };
            $nombre = trim("{$g->apellido} {$g->nombre}") ?: 'Sin nombre';
        @endphp

        <x-tarjeta-listado :href="route('asistencias.guardavida', $g->id)" :titulo="$nombre" :playa="$g->playa->id ?? ''">
            <x-slot:lead>
                <x-icono-listado icono="fa-user" color="teal" size="w-10 h-10" />
            </x-slot:lead>

            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="truncate text-base font-semibold text-gray-900 dark:text-white">{{ $nombre }}</h3>
                    <p class="truncate text-sm text-gray-500 dark:text-gray-300">
                        {{ $g->playa->nombre ?? 'Sin playa' }} · {{ $g->puesto->nombre ?? 'Sin puesto' }}
                    </p>
                    @if ($faltas > 0 || $fuera > 0)
                        <div class="mt-1 flex flex-wrap gap-1.5">
                            @if ($faltas > 0)
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900/40 dark:text-red-300">
                                    {{ $faltas }} {{ $faltas === 1 ? 'falta' : 'faltas' }}
                                </span>
                            @endif
                            @if ($fuera > 0)
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                    {{ $fuera }} fuera de rango
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="shrink-0 text-right">
                    <p class="text-2xl font-bold leading-none {{ $colorPorcentaje }}">{{ $porcentaje !== null ? $porcentaje.'%' : '-' }}</p>
                    <p class="mt-1 text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">asistencia</p>
                </div>
            </div>
        </x-tarjeta-listado>
    @empty
        <p class="rounded-xl border border-gray-200 bg-white px-4 py-6 text-center text-sm text-gray-500 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">No hay personal para mostrar con esos filtros.</p>
    @endforelse
</div>

@if (method_exists($guardavidas, 'links'))
    <div class="paginacion mt-4">
        {{ $guardavidas->links() }}
    </div>
@endif
