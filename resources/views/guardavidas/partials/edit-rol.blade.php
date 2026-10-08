<x-form-tarjeta action="{{ route('rol.update', $guardavida->user) }}" method="POST">
    @csrf
    @method('PUT')

    <x-form-encabezado titulo="Rol del usuario" icono="shield-check" etiqueta="{{ $guardavida->apellido }}, {{ $guardavida->nombre }}">
        Define qué puede ver y hacer en el sistema.
    </x-form-encabezado>

    <x-form-seccion titulo="Rol" icono="key-round">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-8">
            <div class="sm:col-span-4">
                <label for="Rol" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol</label>
                <div class="relative overflow-hidden">
                    <select name="role" id="rol-select"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                    required>
                        <option class="w-auto" value="">Seleccione</option>
                        <option value="guardavida" {{ old('rol', $rol ) == 'guardavida' ? 'selected' : '' }}>Guardavida</option>
                        <option value="encargado" {{ old('rol', $rol ) == 'encargado' ? 'selected' : '' }}>Encargado</option>
                        <option value="admin" {{ old('rol', $rol ) == 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                </div>
            </div>
        </div>
    </x-form-seccion>

    <!-- Botones -->
    <div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-end sm:gap-x-6">
        <button type="button" class="text-sm text-gray-500 dark:text-gray-400 hover:underline" onclick="window.history.back()">Cancelar</button>
        <button type="submit" class="w-full sm:w-auto bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar</button>
    </div>
</x-form-tarjeta>

@php
    $enPlantel = $guardavida->user?->hasAnyRole(['guardavida', 'encargado']) && ! $guardavida->user?->hasAnyRole(['admin', 'superadmin']);
    $periodos = $guardavida->periodos()->with('temporada')->get();
@endphp

{{-- Períodos de alta y baja: de acá sale desde cuándo y hasta cuándo se le cuentan faltas --}}
<x-form-tarjeta tag="div" class="mt-8">
    <x-form-encabezado titulo="Períodos en el plantel" icono="calendar">
        Desde cuándo y hasta cuándo se le cuentan las faltas.
    </x-form-encabezado>

            @if ($periodos->isEmpty())
                <p class="text-sm">Sin períodos registrados: se le cuenta el presentismo como siempre.</p>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-800 dark:text-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-left">Desde</th>
                            <th class="px-3 py-2 text-left">Hasta</th>
                            <th class="px-3 py-2 text-left">Temporada</th>
                            <th class="px-3 py-2 text-left">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($periodos as $periodo)
                            <tr>
                                <td class="px-3 py-2">{{ $periodo->desde?->format('d/m/Y') ?? 'Antes del registro' }}</td>
                                <td class="px-3 py-2">{{ $periodo->hasta?->format('d/m/Y') ?? 'Sigue en el plantel' }}</td>
                                <td class="px-3 py-2">{{ $periodo->temporada?->nombre ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $periodo->motivo }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        @endif
</x-form-tarjeta>

@if ($enPlantel && auth()->user()->can('eliminar_guardavida'))
    <x-form-tarjeta action="{{ route('guardavida.baja', $guardavida) }}" method="POST" class="mt-8"
        onsubmit="return confirm('¿Dar de baja a este guardavida?')">
        @csrf

        <x-form-encabezado titulo="Dar de baja" icono="shield-check">
            Deja de aparecer en listados, selectores y asistencias a partir de esa fecha, y se le cierra el acceso a la operación.
            Conserva todo su historial y puede volver a postularse la próxima temporada.
        </x-form-encabezado>

        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label for="fecha-baja" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Último día de trabajo</label>
                <input id="fecha-baja" type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
            </div>
            <div class="grow max-w-md">
                <label for="motivo-baja" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo (opcional)</label>
                <input id="motivo-baja" type="text" name="motivo" maxlength="255" placeholder="Ej. renuncia"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
            </div>
            <button type="submit" class="bg-red-600 hover:bg-red-500 text-white rounded-full px-5 py-2 shadow">Dar de baja</button>
        </div>
    </x-form-tarjeta>
@endif
