@extends('layouts.app')

@section('content')

<div class="text-gray-600 dark:text-gray-100 body-font px-4">
    <div class="flex justify-between align-center mb-sm-4">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white mt-3">Permisos por rol</h1>
    </div>

    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        Tildá o destildá qué puede hacer cada rol. Los cambios se aplican apenas guardás, a todos los usuarios que tengan ese rol.
    </p>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded my-2">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded my-2">
            {{ session('error') }}
        </div>
    @endif
</div>

<div class="px-4">
    <form action="{{ route('permisos.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Permiso</th>
                        @foreach ($roles as $role)
                            <th class="px-4 py-3 font-semibold text-center capitalize">{{ $role->name }}</th>
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
</div>

@endsection
