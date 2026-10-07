 {{-- <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Settings tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p> --}}

<form action="{{ route('rol.update', $guardavida->user) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-8 bg-white text-gray-600 rounded shadow-md pb-6 sm:pb-12 px-4 sm:px-6 py-6">

        <h3 class="sm:col-span-6 text-gray-900 dark:text-white text-lg font-medium ">
            Rol
        </h3>
        <div class="sm:col-span-3">
            <label for="Rol" class="block text-sm font-medium text-gray-900 dark:text-white">Rol</label>
            <div class="mt-2 relative overflow-hidden">
                <select name="role" id="rol-select"
                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                required>
                    <option class="w-auto" value="">Seleccione</option>
                    <option value="guardavida" {{ old('rol', $rol ) == 'guardavida' ? 'selected' : '' }}>Guardavida</option>
                    <option value="encargado" {{ old('rol', $rol ) == 'encargado' ? 'selected' : '' }}>Encargado</option>
                    <option value="admin" {{ old('rol', $rol ) == 'admin' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>
        </div>

        <!-- Botones -->
        <div class="sm:col-span-8">
            <div class="m-6 mb-6 flex items-center justify-end gap-x-6">
                <button type="button" class="text-sm font-semibold text-gray-900 dark:text-white" onclick="window.history.back()">Cancelar</button>
                <button type="submit"
                class="rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500">
                Guardar
                </button>
            </div>
        </div>

    </div>
</form>

@php
    $enPlantel = $guardavida->user?->hasAnyRole(['guardavida', 'encargado']) && ! $guardavida->user?->hasAnyRole(['admin', 'superadmin']);
    $periodos = $guardavida->periodos()->with('temporada')->get();
@endphp

{{-- Períodos de alta y baja: de acá sale desde cuándo y hasta cuándo se le cuentan faltas --}}
<div class="mt-6 bg-white text-gray-600 rounded shadow-md px-4 sm:px-6 py-6">
    <h3 class="text-gray-900 dark:text-white text-lg font-medium mb-3">Períodos en el plantel</h3>
    @if ($periodos->isEmpty())
        <p class="text-sm">Sin períodos registrados: se le cuenta el presentismo como siempre.</p>
    @else
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-gray-800">
                <tr>
                    <th class="px-3 py-2 text-left">Desde</th>
                    <th class="px-3 py-2 text-left">Hasta</th>
                    <th class="px-3 py-2 text-left">Temporada</th>
                    <th class="px-3 py-2 text-left">Detalle</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
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
</div>

@if ($enPlantel && auth()->user()->can('eliminar_guardavida'))
    <div class="mt-6 bg-white text-gray-600 rounded shadow-md px-4 sm:px-6 py-6">
        <h3 class="text-gray-900 dark:text-white text-lg font-medium mb-1">Dar de baja</h3>
        <p class="text-sm mb-4">
            Deja de aparecer en listados, selectores y asistencias a partir de esa fecha, y se le cierra el acceso a la operación.
            Conserva todo su historial y puede volver a postularse la próxima temporada.
        </p>
        <form action="{{ route('guardavida.baja', $guardavida) }}" method="POST" class="flex flex-wrap items-end gap-4"
            onsubmit="return confirm('¿Dar de baja a este guardavida?')">
            @csrf
            <div>
                <label for="fecha-baja" class="block text-sm font-medium text-gray-900">Último día de trabajo</label>
                <input id="fecha-baja" type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required
                    class="mt-1 rounded-md px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 sm:text-sm">
            </div>
            <div class="grow max-w-md">
                <label for="motivo-baja" class="block text-sm font-medium text-gray-900">Motivo (opcional)</label>
                <input id="motivo-baja" type="text" name="motivo" maxlength="255" placeholder="Ej. renuncia"
                    class="mt-1 block w-full rounded-md px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 sm:text-sm">
            </div>
            <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-red-500">Dar de baja</button>
        </form>
    </div>
@endif
