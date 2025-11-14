<x-index-table>
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
            <tr>
                <th class="px-4 py-2 text-left cursor-pointer" onclick="toggleSort()">Fecha</th>
                <th class="px-4 py-2 text-left">Tipo</th>
                <th class="px-4 py-2 text-left">Víctimas</th>
                <th class="px-4 py-2 text-left">Código</th>
                <th class="px-4 py-2 text-left">Playa</th>
                <th class="px-4 py-2 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
            @foreach ($intervenciones as $intervencion)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800"
                    data-playa="{{ $intervencion->playa->id ?? '' }}"
                    data-fecha="{{ $intervencion->fecha->format('Y-m-d') }}">
                    <td class="px-4 py-2">{{ $intervencion->fecha->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">{{ $intervencion->tipo_intervencion }}</td>
                    <td class="px-4 py-2">{{ $intervencion->victimas }}</td>
                    <td class="px-4 py-2">{{ $intervencion->codigo }}</td>
                    <td class="px-4 py-2">{{ $intervencion->playa->nombre ?? '-' }}</td>
                    <td class="px-4 py-2">
                        <div class="flex space-x-2">
                            <a href="{{ route('intervencion.show', $intervencion) }}"
                            class="text-blue-600 hover:underline">Ver</a>
                            @can('editar_intervencion')
                                <a href="{{ route('intervencion.edit', $intervencion) }}"
                                class="text-yellow-600 hover:underline">Editar</a>
                            @endcan
                            @can('eliminar_intervencion')
                                <form action="{{ route('intervencion.destroy', $intervencion) }}" method="POST"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar esta intervención?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-index-table>
