@extends('layouts.app')
@section('content')

<div class="text-gray-600 dark:text-gray-100 body-font sm:px-4">
    <div class="flex justify-between align-center mb-4 sm:mt-4">
        <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl">Temporadas</h2>
        @can('agregar_temporada')
            <a href="{{ route('temporada.create') }}" class="btn hidden sm:flex align-center bg-sky-500 dark:bg-sky-700 hover:bg-sky-400 dark:hover:bg-sky-600 rounded-full px-3 py-2 shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-sky-500 dark:text-sky-700 w-5 h-5 bg-gray-100 dark:bg-gray-200 rounded me-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span class="text-gray-100 dark:text-gray-200">Agregar</span>
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded my-2">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded my-2">{{ session('error') }}</div>
    @endif
</div>

<x-index-table :registros="$temporadas" movil>
    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
        <tr>
            <th class="px-4 py-2 text-left">Nombre</th>
            <th class="px-4 py-2 text-left">Postulación</th>
            <th class="px-4 py-2 text-left">Temporada</th>
            <th class="px-4 py-2 text-left">Estado</th>
            <th class="px-4 py-2 text-left">Acciones</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
        @forelse ($temporadas as $temporada)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-4 py-2">{{ $temporada->nombre }}</td>
                <td class="px-4 py-2">{{ $temporada->fecha_inicio_postulacion->format('d/m/y') }} - {{ $temporada->fecha_fin_postulacion->format('d/m/y') }}</td>
                <td class="px-4 py-2">{{ $temporada->fecha_inicio->format('d/m/y') }} - {{ $temporada->fecha_fin->format('d/m/y') }}</td>
                <td class="px-4 py-2">
                    @if($temporada->fecha_inicio->isPast() && $temporada->fecha_fin->isFuture())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Activa</span>
                    @elseif($temporada->fecha_inicio_postulacion->isPast() && $temporada->fecha_fin_postulacion->isFuture())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300">Postulación abierta</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">Fuera de fecha</span>
                    @endif
                </td>
                <td class="px-4 py-2">
                    <div class="flex space-x-2">
                        @can('editar_temporada')
                            <a href="{{ route('temporada.edit', $temporada) }}"
                                class="text-sky-500 hover:text-sky-400 dark:text-sky-400 hover:dark:text-sky-300 w-7 h-7 inline-flex items-center justify-center rounded p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </a>
                        @endcan
                        @can('eliminar_temporada')
                            <form action="{{ route('temporada.destroy', $temporada) }}" method="POST"
                                onsubmit="return confirm('¿Seguro que deseas eliminar esta temporada?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-400 dark:text-red-400 hover:dark:text-red-300 w-7 h-7 inline-flex items-center justify-center rounded p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No hay temporadas cargadas todavía.</td>
            </tr>
        @endforelse
    </tbody>
</x-index-table>
@endsection
