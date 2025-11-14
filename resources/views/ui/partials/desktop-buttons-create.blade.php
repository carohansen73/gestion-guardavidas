

<section class="text-gray-600 body-font my-1">
    <div class="  ">

        <div class="flex flex-wrap gap-1 justify-start text-center">
            {{-- <div class="p-2 lg:w-1/4 md:w-1/4 sm:w-1/2 w-1/2 ">
                <div class="flex justify-center items-center px-3 py-3 rounded-lg shadow hover:shadow-md cursor-pointer  bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-sky-600 w-9 h-9 py-2 px-2 inline-block bg-sky-500/50 rounded-xl mx-2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                    </svg>
                    <div class="text-start mx-2">
                        <p class="text-sm text-gray-400">Agregar</p>
                        <p class="text-gray-800">Banderas</p>
                    </div>

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="mx-2 w-8 h-8  py-1 px-1 rounded-xl">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
            </div> --}}
             @can('agregar_bandera')
                <div class="p-2 w-auto">
                    <a href="{{ route('bandera.create') }}">
                        <div class="flex items-center bg-white px-2 pe-4 py-3 rounded-lg shadow-md hover:shadow-xl cursor-pointer dark:bg-gray-600 transition ease-in duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="bg-sky-600/20 text-sky-600 w-9 h-9 py-2 px-2 inline-block rounded-xl mx-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <div class="text-start mx-2">
                            <p class="text-sm text-gray-400">Agregar</p>
                            <p class="text-gray-800">Bandera</p>
                        </div>

                    </div>
                    </a>
                </div>
            @endcan
            @can('agregar_intervencion')
                <div class="p-2 w-auto">
                    <a href="{{ route('intervencion.create') }}">
                        <div class="flex items-center bg-white px-2 pe-3 py-3 rounded-lg shadow-md hover:shadow-xl cursor-pointer dark:bg-gray-600 transition ease-in duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="bg-orange-600/20 text-orange-600 w-9 h-9 py-2 px-2 inline-block rounded-xl mx-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <div class="text-start mx-2">
                            <p class="text-sm text-gray-400">Agregar</p>
                            <p class="text-gray-800">Intervencion</p>
                        </div>

                    </div>
                    </a>
                </div>
            @endcan
            @can('agregar_novedad_material')
                <div class="p-2 w-auto">
                    <a href="{{ route('novedad-de-material.create') }}">
                        <div class="flex justify-center items-center bg-white px-2 pe-3 py-3 rounded-lg shadow-md hover:shadow-xl cursor-pointer dark:bg-gray-600 transition ease-in duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                               class="bg-purple-500/30 text-purple-600 w-9 h-9 py-2 px-2 inline-block rounded-xl mx-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <div class="text-start mx-2">
                                <p class="text-sm text-gray-400">Agregar</p>
                                <p class="text-gray-800">Novedad de Material</p>
                            </div>

                        </div>
                    </a>
                </div>
            @endcan
{{--
            <div class="p-2 w-auto">  <a href="{{ route('intervencion.create') }}">
                <div class="flex justify-center items-center px-2 py-3 rounded-lg shadow-md hover:shadow-xl cursor-pointer  bg-white dark:bg-gray-600 transition ease-in duration-300">
                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="text-sky-600 w-9 h-9 py-2 px-2 inline-block bg-sky-500/30 rounded-xl mx-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                    <div class="text-start mx-2">
                        <p class="text-sm text-gray-400">Agregar</p>
                        <p class="text-gray-800">Cambio de turno</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="mx-2 w-8 h-8  py-1 px-1 rounded-xl text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                </a>
            </div>
            <div class="p-2 w-auto">  <a href="{{ route('intervencion.create') }}">
                <div class="flex justify-center items-center px-2 py-3 rounded-lg shadow-md hover:shadow-xl cursor-pointer  bg-white dark:bg-gray-600 transition ease-in duration-300">
                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="text-purple-600 w-9 h-9 py-2 px-2 inline-block bg-purple-500/30 rounded-xl mx-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                    </svg>
                    <div class="text-start mx-2">
                        <p class="text-sm text-gray-400">Agregar</p>
                        <p class="text-gray-800">Licencia</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="mx-2 w-8 h-8  py-1 px-1 rounded-xl">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                </a>
            </div> --}}

        </div>
    </div>
</section>










