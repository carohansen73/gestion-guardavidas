{{-- bg-yellow-500/50 border-l-4 border-yellow-500 text-yellow-900 --}}


@auth
    @php
        $rol = Auth::user()->getRoleNames()->first();
        $playaUsuario = Auth::user()->guardavida->playa->nombre ?? null;
    @endphp
@endauth



@if($rol !== 'admin' && $playaUsuario)
    @if($bandera)
    @php
        $fecha = \Carbon\Carbon::parse($bandera->fecha)->locale('es');
    @endphp
    {{-- Info de la Bandera del dia --}}
        <div class="p-2">
            <div class="bg-gradient-to-r from-sky-200 to-purple-200 dark:from-sky-700 dark:to-purple-700 text-black rounded shadow-sm p-6 transform transition hover:scale-105 duration-300 bg-caracoles dark:bg-caracoles"
            >
                <div class="flex justify-content-between align-center">
                    <div class="mb-2">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Bandera del día</h2>
                        <p class="text-sm text-gray-800 dark:text-gray-100 flex align-end">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 me-1">
                            <path d="M12.75 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM7.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM8.25 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM9.75 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM10.5 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM12.75 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM14.25 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 13.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
                            <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd" />
                            </svg>

                            {{ $fecha->translatedFormat('l') }}, {{ $fecha->format('H:i')}}</p>
                    </div>

                    <a href="{{ route('bandera.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="3"
                            stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-50 text-gray-800 rounded-full">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>

                <!-- Izquierda: icono + playa -->
               <div class="grid grid-cols-2 gap-2 mt-3 items-start auto-cols-max">

                       <!-- Columna 1 - Bandera -->
                    <div class="flex flex-col items-center bg-gray-100/80 dark:bg-sky-800 rounded-lg p-2 w-fit">
                        <div class="mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="bandera {{ $bandera->bandera->color }}
                                w-12 h-12 flex-shrink-0 animate-ondear">
                            <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium dark:text-gray-100">{{$bandera->bandera->codigo}}</p>
                    </div>

                    <!-- Columna 2 - Tº y Viento -->
                     <div class="flex flex-col bg-gray-100/80 dark:bg-sky-800 rounded-lg px-3 py-2 w-fit ml-auto items-end text-right text-gray-700 dark:text-gray-100">
                        <div class="flex mb-1 align-items-center">

                            <p class="text-sm font-medium">Temperatura: {{ $bandera->temperatura }}º</p>
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="size-5 text-amber-500 dark:text-amber-200 mx-1">
                            <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" />
                            </svg>
                        </div>


                        <div class="flex mb-1 align-items-center">

                            <p class="text-sm font-medium">Viento: {{ $bandera->viento_intensidad }} {{ $bandera->viento_direccion }}</p>
                             <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="size-5 text-gray-800 dark:text-gray-200 mx-1">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 12h12a2 2 0 10-2-2m-10 4h8a2 2 0 11-2 2" />
                            </svg>
                        </div>

                    </div>




                </div>

            </div>
        </div>

    @else
    {{-- aun no se cargo la bandera del dia --}}

    <div class="p-2">
        <div class="bg-yellow-500/50 border-l-4 border-yellow-500 text-yellow-900 dark:from-blue-700 dark:to-teal-700 text-black rounded shadow-sm p-6 transform transition hover:scale-105 duration-300">
            <div class="flex justify-content-between align-center">
                <div class="mb-2">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Bandera del día</h2>
                    <p class="text-sm text-gray-800 dark:text-gray-300">⚠️ No se ha cargado la bandera para hoy.</p>
                </div>

                @can('agregar_bandera')
                    <a href="{{ route('bandera.create') }}" class="">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 text-yellow-900 dark:text-white p-1 rounded-full">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                @endcan
            </div>

            @can('ver_bandera')
                <a href="{{ route('bandera.index') }}"
                    class="inline-flex items-center text-sm justify-center px-3 py-2 bg-gray-800/80 text-white rounded-full hover:bg-gray-700 transition mt-2">
                    Historial de banderas
                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="3"
                        stroke="currentColor"
                        class="w-5 h-5 ms-3 bg-white p-1 text-gray-900 dark:text-white rounded-xl">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @endcan
        </div>
    </div>
    @endif

@endif
