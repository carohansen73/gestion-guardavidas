<div class="hidden sm:block overflow-x-auto space-y-12">
    <div class="pb-12 dark:border-white/10 rounded-lg dark:bg-gray-600 px-4 py-2">
        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg shadow">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left cursor-pointer" onclick="toggleSort()">Fecha</th>
                    <th class="px-4 py-2 text-left">Licencia</th>
                    <th class="px-4 py-2 text-left">Guardavida</th>
                    <th class="px-4 py-2 text-left">Playa</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @foreach ($registros as $registro)
                    <tr class="registro-item-tabla hover:bg-gray-50 dark:hover:bg-gray-800 "
                        data-playa="{{ $registro->playa->id ?? '' }}"
                        data-fecha="{{ $registro->fecha_inicio->format('Y-m-d') }}">
                        <td class="px-4 py-2">{{ $registro->fecha_inicio->format('d/m/y') }} - {{ $registro->fecha_fin->format('d/m/y') }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('licencia.show', $registro) }}" class="text-sky-600 hover:text-sky-400">
                                {{ $registro->tipo_licencia }}
                            </a>
                        </td>
                        <td class="px-4 py-2">
                            {{ $registro->guardavida->nombre }} {{ $registro->guardavida->apellido }}
                        </td>
                        <td class="px-4 py-2">{{ $registro->playa->nombre ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <div class="flex space-x-2">
                                <a href="{{ route('licencia.show', $registro) }}"
                                class="text-blue-600 hover:underline">Ver</a>
                                @can('editar_licencia')
                                    <a href="{{ route('licencia.edit', $registro) }}"
                                        class="text-yellow-600 hover:underline">
                                        Editar
                                    </a>
                                @endcan
                                @can('eliminar_licencia')
                                    <form action="{{ route('licencia.destroy', $registro) }}" method="POST"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar esta licencia?')">
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
        </table>
    </div>
</div>
