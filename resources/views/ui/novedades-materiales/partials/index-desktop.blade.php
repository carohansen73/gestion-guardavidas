<div class="hidden sm:block overflow-x-auto space-y-12">
    <div class="pb-12 dark:border-white/10 rounded-lg dark:bg-gray-600 px-4 py-2">
        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg shadow">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left cursor-pointer" onclick="toggleSort()">Fecha</th>
                    <th class="px-4 py-2 text-left">Novedad</th>
                    <th class="px-4 py-2 text-left">Material</th>
                    <th class="px-4 py-2 text-left">Playa</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @foreach ($registros as $registro)
                    <tr class="registro-item-tabla hover:bg-gray-50 dark:hover:bg-gray-800 "
                        data-playa="{{ $registro->playa->id ?? '' }}"
                        data-fecha="{{ $registro->fecha->format('Y-m-d') }}">
                        <td class="px-4 py-2">{{ $registro->fecha->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $registro->tipo_novedad }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('novedad-de-material.show', $registro) }}" class="text-sky-600 hover:text-sky-400">
                                {{ $registro->material->nombre }} {{ $registro->material->detalle }}
                            </a>
                        </td>
                        <td class="px-4 py-2">{{ $registro->playa->nombre ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <div class="flex space-x-2">
                                <a href="{{ route('novedad-de-material.show', $registro) }}"
                                class="text-blue-600 hover:underline">Ver</a>
                                {{-- TODO permisos!!
                                @can('editar banderas') --}}
                                <a href="{{ route('novedad-de-material.edit', $registro) }}"
                                    class="text-yellow-600 hover:underline">
                                    Editar
                                </a>
                                {{-- @endcan --}}
                                {{-- @can('eliminar banderas') --}}
                                <form action="{{ route('novedad-de-material.destroy', $registro) }}" method="POST"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar esta novedad?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                                {{-- @endcan --}}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
