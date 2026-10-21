@extends('layouts.app')

@section('content')

<div class="text-gray-600 dark:text-gray-100 body-font sm:px-4">
    <div class="flex justify-between align-center sm:mb-6">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white mt-3">Permisos por rol</h1>
    </div>

    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        Tildá o destildá qué puede hacer cada rol. Los cambios se aplican apenas guardás, a todos los usuarios que tengan ese rol.
    </p>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded my-2 dark:bg-green-900/40 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded my-2 dark:bg-red-900/40 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif
</div>

<div class="sm:px-4">
    <form action="{{ route('permisos.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Permiso</th>
                        @foreach ($roles as $role)
                            <th class="px-4 py-3 font-semibold text-center capitalize">{{ $role->nombre_visible }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permisos as $recurso => $permisosDelRecurso)
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <td colspan="{{ $roles->count() + 1 }}" class="px-4 py-2 font-semibold text-gray-800 dark:text-white capitalize">
                                {{ str_replace('_', ' ', $recurso) }}
                            </td>
                        </tr>
                        @foreach ($permisosDelRecurso as $permiso)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ ucfirst(str_replace('_', ' ', \Illuminate\Support\Str::before($permiso->name, '_'))) }}
                                    <span class="text-xs text-gray-400 dark:text-gray-500">({{ $permiso->name }})</span>
                                </td>
                                @foreach ($roles as $role)
                                    <td class="px-4 py-2 text-center">
                                        <input type="checkbox"
                                            name="permisos[{{ $role->id }}][]"
                                            value="{{ $permiso->id }}"
                                            class="rounded text-sky-600 focus:ring-sky-500"
                                            {{ $role->permissions->contains('id', $permiso->id) ? 'checked' : '' }}>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mt-4 mb-8">
            <button type="submit"
                class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-sky-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                Guardar
            </button>
        </div>
    </form>

    {{-- Rol nuevo: se crea acá y después se le tildan los permisos en la matriz de arriba. --}}
    <details class="mb-10 rounded-lg bg-white shadow dark:bg-gray-800" @if ($errors->any()) open @endif>
        <summary class="cursor-pointer px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-100">
            <i class="fas fa-plus me-1 text-sky-600" aria-hidden="true"></i> Crear un rol nuevo
        </summary>

        <form action="{{ route('permisos.roles.store') }}" method="POST"
            class="grid grid-cols-1 gap-x-6 gap-y-4 border-t border-gray-200 px-4 py-5 dark:border-gray-700 sm:grid-cols-6">
            @csrf

            @if ($errors->any())
                <div class="rounded bg-red-100 p-3 text-red-700 dark:bg-red-900/40 dark:text-red-300 sm:col-span-6">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="sm:col-span-2">
                <label for="rol-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre interno</label>
                <input id="rol-name" name="name" type="text" value="{{ old('name') }}" placeholder="auditoria" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minúsculas, sin espacios. No se cambia después.</p>
            </div>
            <div class="sm:col-span-2">
                <label for="rol-etiqueta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cómo se muestra</label>
                <input id="rol-etiqueta" name="etiqueta" type="text" value="{{ old('etiqueta') }}" placeholder="Auditoría" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="sm:col-span-2">
                <label for="rol-descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aclaración (opcional)</label>
                <input id="rol-descripcion" name="descripcion" type="text" value="{{ old('descripcion') }}" placeholder="solo consulta"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="sm:col-span-2">
                <label for="rol-color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color</label>
                <select id="rol-color" name="color"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach ($colores as $color)
                        <option value="{{ $color->value }}" @selected(old('color', 'slate') === $color->value)>{{ ucfirst($color->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label for="rol-icono" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ícono</label>
                <select id="rol-icono" name="icono"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach ($iconos as $valor => $nombre)
                        <option value="{{ $valor }}" @selected(old('icono', 'user') === $valor)>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label for="rol-rango" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rango (1 a 79)</label>
                <input id="rol-rango" name="rango" type="number" min="1" max="79" value="{{ old('rango', 60) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Quién puede asignarlo: solo usuarios de rango igual o mayor (guardavida 20, encargado 40, personal 50, administrador 80).</p>
            </div>

            <div class="space-y-2 sm:col-span-6" x-data="{ plantel: {{ old('es_plantel') ? 'true' : 'false' }} }">
                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="es_plantel" value="1" x-model="plantel" @checked(old('es_plantel'))
                        class="rounded border-gray-300 text-sky-600 dark:border-gray-600">
                    Es del plantel: tiene ficha de guardavidas y cuenta para el presentismo
                </label>
                <label class="ms-6 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300" x-show="plantel" x-cloak>
                    <input type="checkbox" name="usa_temporada" value="1" @checked(old('usa_temporada', true))
                        class="rounded border-gray-300 text-sky-600 dark:border-gray-600">
                    Pasa por la postulación y la selección cada temporada (si no, se da de baja deshabilitando al usuario)
                </label>
            </div>

            <div class="flex justify-end sm:col-span-6">
                <button type="submit" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-sky-700">
                    Crear rol
                </button>
            </div>
        </form>
    </details>
</div>

@endsection
