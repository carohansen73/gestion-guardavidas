<div class="space-y-4 sm:hidden">
    <section class="text-gray-600 dark:text-gray-100 body-font px-4 py-4 mb-16">
        <div id="accordion-collapse" data-accordion="collapse" class="bg-white dark:bg-gray-600">
            @foreach ($guardavidasHabilitados as $registro)
                <div class="registro-item-lista rounded "
                    data-playa="{{ $registro->playa->id ?? '' }}">
                    <h2 id="accordion-collapse-heading-{{$registro->id}}">
                        <button type="button" class="flex items-center justify-between w-full px-4 py-2 font-medium text-left text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3"
                            data-accordion-target="#accordion-collapse-body-{{$registro->id}}" aria-expanded="true" aria-controls="accordion-collapse-body-1">
                            {{-- <a href="{{ route('intervencion.show', $intervencion) }}"> --}}
                                <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $registro->apellido }} {{ $registro->nombre }}
                                </h3>
                                 <p class="text-sm text-gray-600 dark:text-gray-200">
                                    {{ str_replace('_', ' ', $registro->funcion) }}
                                 </p>
                                    <span class="text-sm text-gray-500 dark:text-gray-300">
                                    {{ $registro->playa->nombre }}-{{ $registro->puesto->nombre }}
                                    </span>

                                <p class="text-sm text-gray-700 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ Str::limit($registro->detalles, 80) }}
                                </p>
                                </div>
                            {{-- </a> --}}
                            <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                            </svg>
                        </button>
                    </h2>

                    <div id="accordion-collapse-body-{{$registro->id}}" class="hidden" aria-labelledby="accordion-collapse-heading-{{$registro->id}}">
                        <div class="px-4 py-2 border border-b-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900">

                            {{-- Menu opciones - Editar eliminar --}}
                            <div class="w-full flex justify-end">
                                <button class=""
                                type="button"
                                data-drawer-target="drawer-bottom-example"
                                data-drawer-show="drawer-bottom-example"
                                data-drawer-placement="bottom"
                                aria-controls="drawer-bottom-example"
                                    @click="selectedId = {{ $registro->id }}">
                                    <!-- icono tres puntos -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    class="bi bi-three-dots-vertical w-6 h-6" viewBox="0 0 16 16">
                                    <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                DNI
                                <span class="text-sm text-gray-500 dark:text-gray-300">
                                    {!!$registro->dni !!}
                                </span>
                            </p>
                            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                Telefono
                                <span class="text-sm text-gray-500 dark:text-gray-300">
                                    {!!$registro->telefono !!}
                                </span>
                            </p>
                             <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                Dirección
                                <span class="text-sm text-gray-500 dark:text-gray-300">
                                    {!!$registro->direccion !!} {!!$registro->numero !!} {!!$registro->piso_dpto !!}
                                </span>
                            </p>
                            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                Usuario
                                <span class="text-sm text-gray-500 dark:text-gray-300">
                                    {!!$registro->user->email !!}
                                </span>
                            </p>

                            {{-- BOTONES GROUP --}}
                            <div class="flex justify-center py-3">
                                <div class="inline-flex rounded-md shadow-xs" role="group">
                                    <a  href="{{ route('guardavida.edit', $registro) }}"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-200 hover:text-sky-600 focus:z-10 focus:ring-2 focus:ring-sky-600 focus:text-sky-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-sky-500 dark:focus:text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 20 20"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="w-3 h-3 me-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        Editar
                                    </a>
                                    @if(auth()->user()->hasrole('encargado') ||  auth()->user()->hasrole('admin'))
                                        <form action="{{ route('user.toggle', $registro->user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('¿Estás seguro de que querés bloquear a este usuario? Esta acción puede revertirse más tarde.')"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-red-300 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 20 20"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    class="w-3 h-3 me-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                Bloquear
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="inline-flex items-center px-4 py-2 text-sm font-medium bg-gray-100 border border-gray-200 rounded-e-lg hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 ">
                                             <svg xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 20 20"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    class="w-3 h-3 me-2"
                                                    disabled>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            Bloquear
                                        </button>
                                    @endif
                                </div>
                            </div> <!--flex-->
                            {{-- FIN BOTONES GROUP--}}
                        </div><!--px-4-->
                    </div><!--accordion-->

                </div>
            @endforeach
        </div>

    </section>

    <a href="{{ route('guardavida.create') }}" class="btn fixed z-40 flex align-content-center bg-sky-500 dark:bg-sky-700 bottom-24 right-8 rounded-full px-3 py-3 shadow">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        class="text-sky-500 w-6 h-6 z-50 bg-gray-100 rounded me-2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span class="text-gray-100 text-lg"> Agregar</span>
    </a>
</div>
