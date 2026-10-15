@php
    $puedeEditar = auth()->user()->can('editar_guardavida');
    $puedeDarDeBaja = auth()->user()->can('eliminar_guardavida');
@endphp

{{-- Listado para celular: tocar la tarjeta abre la ficha; el botón de tres puntos abre el menú inferior
     (<x-menu-inferior/>, en index.blade.php). La baja la resuelve el modal de index.blade.php vía el evento `menu-accion`. --}}
<div class="space-y-4 sm:hidden">
    <section class="text-gray-600 dark:text-gray-100 body-font pt-4">
        <div class="space-y-3">
            @forelse ($guardavidasHabilitados as $registro)
                @php
                    $nombreCompleto = $registro->apellido.' '.$registro->nombre;
                    $acciones = array_values(array_filter([
                        ['texto' => 'Ver ficha', 'icono' => 'fa-eye', 'href' => route('guardavida.show', $registro)],
                        $puedeEditar ? ['texto' => 'Editar', 'icono' => 'fa-pen', 'href' => route('guardavida.edit', $registro)] : null,
                        $puedeDarDeBaja ? [
                            'texto' => 'Dar de baja', 'icono' => 'fa-ban', 'peligro' => true, 'evento' => 'baja',
                            'datos' => ['url' => route('guardavida.baja', $registro), 'nombre' => $registro->user->lastname.', '.$registro->user->name],
                        ] : null,
                    ]));
                @endphp

                <x-tarjeta-listado :href="route('guardavida.show', $registro)" :titulo="$nombreCompleto" :acciones="$acciones" :playa="$registro->playa->id ?? ''">
                    <x-slot:lead>
                        <x-avatar-guardavida :guardavida="$registro" size="w-12 h-12" icono="text-xl" />
                    </x-slot:lead>

                    <div class="flex items-center gap-2 min-w-0">
                        <h3 class="truncate text-base font-semibold text-gray-900 dark:text-white">{{ $nombreCompleto }}</h3>
                        <x-role-badge :rol="\App\Enums\RolUsuario::principal($registro->user)" :label="false" size="w-3.5 h-3.5" class="shrink-0" />
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-200">
                        {{ str_replace('_', ' ', $registro->funcion) }}@if ($registro->turno) - T.{{ $registro->turno }}@endif
                    </p>
                    <p class="truncate text-sm text-gray-500 dark:text-gray-300">
                        {{ $registro->playa?->nombre ?? '-' }}{{ $registro->puesto ? " - {$registro->puesto->nombre}" : ' (sin puesto)' }}
                    </p>

                    {{-- Contacto a mano (links aparte de la tarjeta: no se pueden anidar <a>). Alineado con el texto: 1rem + 3rem + 0.75rem. --}}
                    <x-slot:pie>
                        <div class="flex flex-col gap-0.5 pb-3 pl-[4.75rem] pr-4 text-sm">
                            @if (filled($registro->telefono))
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $registro->telefono) }}"
                                    class="inline-flex w-fit items-center gap-2 py-0.5 text-sky-600 hover:underline dark:text-sky-300">
                                    <i class="fas fa-phone w-4 text-center text-xs" aria-hidden="true"></i>{{ $registro->telefono }}
                                </a>
                            @endif
                            <a href="mailto:{{ $registro->user->email }}"
                                class="inline-flex min-w-0 items-center gap-2 py-0.5 text-gray-600 hover:underline dark:text-gray-300">
                                <i class="fas fa-envelope w-4 text-center text-xs" aria-hidden="true"></i><span class="truncate">{{ $registro->user->email }}</span>
                            </a>
                        </div>
                    </x-slot:pie>
                </x-tarjeta-listado>
            @empty
                <p class="rounded-xl border border-gray-200 bg-white px-4 py-6 text-center text-sm text-gray-500 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">No hay guardavidas para mostrar.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $guardavidasHabilitados->links() }}
        </div>

    </section>

    @can('agregar_guardavida')
        <a href="{{ route('guardavida.create') }}" class="btn fixed z-40 flex items-center bg-sky-500 dark:bg-sky-500 bottom-24 right-8 rounded-full px-3 py-3 shadow">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="text-sky-500 w-6 h-6 z-50 bg-gray-100 rounded me-2 dark:bg-gray-700">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span class="text-gray-100 text-lg"> Agregar</span>
        </a>
    @endcan
</div>
