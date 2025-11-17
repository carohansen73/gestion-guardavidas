{{-- bg-yellow-500/50 border-l-4 border-yellow-500 text-yellow-900 --}}


@auth
    @php
        $rol = Auth::user()->getRoleNames()->first();
        $playaUsuario = Auth::user()->guardavida->playa->nombre ?? null;
    @endphp
@endauth



@if($rol !== 'admin' && $playaUsuario)
    @if($bandera)
    {{-- Info de la Bandera del dia --}}
        <div class="p-2">
            <div class="bg-gradient-to-r from-sky-100 to-purple-100 dark:from-sky-700 dark:to-purple-700 text-black rounded shadow-sm p-6 transform transition hover:scale-105 duration-300"
            >
                <div class="flex justify-content-between align-center">
                    <h2 class="text-2xl md:text-3xl font-bold text-black">Bandera del día</h2>

                    <a href="{{ route('bandera.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="3"
                            stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-50 text-gray-800 rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>

                <!-- Izquierda: icono + playa -->
                <div class="grid grid-cols-3 mt-2">

                       <!-- Columna 1 - Bandera -->
                    <div class="flex flex-col items-center rounded-lg">
                        <div class="bg-gray-200/50 rounded mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="bandera {{ $bandera->bandera->color }}
                                w-12 h-12 flex-shrink-0 animate-ondear">
                            <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium">{{$bandera->bandera->codigo}}</p>
                    </div>

                    <!-- Columna 2 - Tº -->
                    <div class="flex flex-col items-center rounded-lg">
                        <div class="bg-gray-200/50 rounded mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class=" w-12 h-12 text-amber-500">
                            <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium">{{ $bandera->temperatura }}º</p>
                    </div>

                     <!-- Columna 3 - Viento -->
                    <div class="flex flex-col items-center rounded-lg">
                        <div class="bg-gray-200/50 rounded mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-12 h-12 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 12h12a2 2 0 10-2-2m-10 4h8a2 2 0 11-2 2" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium"> {{ $bandera->viento_intensidad }} {{ $bandera->viento_direccion }}</p>
                    </div>


                </div>

            </div>
        </div>

    @else
    {{-- aun no se cargo la bandera del dia --}}

    <div class="p-2">
        <div class="bg-yellow-500/50 border-l-4 border-yellow-500 text-yellow-900 dark:from-blue-700 dark:to-teal-700 text-black rounded shadow-sm p-6 transform transition hover:scale-105 duration-300">
            <div class="flex justify-content-between align-center">
                <div>
                <h2 class="text-2xl md:text-3xl font-bold text-black">Bandera del día</h2>
                 <p>⚠️ No se ha cargado la bandera para hoy.</p>
                </div>

                @can('agregar_bandera')
                    <a href="{{ route('bandera.create') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-50 text-sky-600 rounded-xl">
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
                        class="w-5 h-5 ms-3 bg-white p-1 text-dark rounded-xl">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @endcan
        </div>
    </div>
    @endif

@endif
