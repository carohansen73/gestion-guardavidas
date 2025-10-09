<div class="hidden sm:block overflow-x-auto space-y-12">
    <div class="-900/10 pb-12 dark:border-white/10 rounded-lg dark:bg-gray-600 px-4 py-4">
        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg shadow">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left cursor-pointer" onclick="toggleSort()">Fecha</th>
                    <th class="px-4 py-2 text-left">Bandera</th>
                    <th class="px-4 py-2 text-left">Turno</th>
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
                        <td class="px-4 py-2">{{ $registro->bandera->codigo }}</td>
                        <td class="px-4 py-2">{{ $registro->turno }}</td>
                        <td class="px-4 py-2">{{ $registro->playa->nombre ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <div class="flex space-x-2">
                                <a href="{{ route('bandera.show', $registro) }}"
                                class="text-blue-600 hover:underline">Ver</a>
                                {{-- TODO permisos!!
                                @can('editar banderas') --}}
                                <a href="{{ route('bandera.edit', $registro) }}"
                                    class="text-yellow-600 hover:underline">
                                    Editar
                                </a>
                                {{-- @endcan --}}
                                {{-- @can('eliminar banderas') --}}
                                <form action="{{ route('bandera.destroy', $registro) }}" method="POST"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar esta intervención?')">
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
