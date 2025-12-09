<x-index-table>
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left cursor-pointer" onclick="toggleSort()">Guardavidas</th>
                    <th class="px-4 py-2 text-left">Función</th>
                    <th class="px-4 py-2 text-left">Rol</th>
                    <th class="px-4 py-2 text-left">Playa - puesto</th>
                    <th class="px-4 py-2 text-left">Edit</th>
                    <th class="px-4 py-2 text-left">Activo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @foreach ($registros as $registro)
                    <tr class="registro-item-tabla
                    {{ !$registro->user->enabled ? 'opacity-60 bg-gray-100 dark:bg-gray-700' : ' hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                        data-playa="{{ $registro->playa->id ?? '' }}">

                        <td class="px-4 py-2">
                            <a href="{{ route('guardavida.show', $registro) }}" class="{{ !$registro->user->enabled ? 'text-gray-600 hover:text-gray-400 dark:text-gray-200 dark:hover:text-gray-100' : 'text-sky-600 hover:text-sky-500 dark:text-sky-300 hover:dark:text-sky-200' }}">
                                {{ $registro->apellido }} {{ $registro->nombre }}
                            </a>
                        </td>
                        @php
                            $color = match($registro->funcion) {
                                'Encargado' => 'text-red-600 dark:text-red-400',
                                'Jefe_de_playa' => 'text-orange-600 dark:text-orange-400',
                                'Guardavida' => 'text-sky-600 dark:text-sky-400',
                                'Timonel' => 'text-teal-600 dark:text-teal-400',
                                default => 'text-gray-600 dark:text-gray-400'
                            };
                        @endphp
                        <td class="px-4 py-2 {{ $color }}">{{ str_replace('_', ' ', $registro->funcion) }}</td>

                        <td class="px-4 py-2">{{ $registro->user->getRoleNames()->first() }} </td>
                        <td class="px-4 py-2">{{ $registro->playa->nombre ?? '-' }} - {{ $registro->puesto->nombre ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <div class="flex space-x-2">
                                @if ($registro->user->enabled)

                                    @can('editar_guardavida')
                                    <a href="{{ route('guardavida.edit', $registro) }}"
                                        class="text-yellow-600 hover:underline">
                                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="text-sky-500 dark:text-sky-400 w-7 h-7  rounded p-1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>
                                 @endif
                                @endcan
                            </td>
                         <td class="px-4 py-2">
                                @can('eliminar_guardavida')
                                   {{-- Switch de habilitado --}}
                                    <form action="{{ route('user.toggle', $registro->user->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                                {{ $registro->user->enabled ? 'bg-sky-500' : 'bg-gray-400' }}">
                                            <span class="sr-only">Toggle habilitado</span>
                                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                                {{ $registro->user->enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </x-index-table>
