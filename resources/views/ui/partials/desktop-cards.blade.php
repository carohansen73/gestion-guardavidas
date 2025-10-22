<section class="text-gray-600 body-font">
    <div class="container px-4 py-1 mx-auto">
        {{-- <div class="flex flex-col text-center w-full mb-20">
            <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">Registrar un acontecimiento</h1>
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">Seleccione una categoría</p>
        </div> --}}
        <div class="flex flex-wrap -m-4 text-center">
            <div class="p-2 md:w-1/3 sm:w-1/2 w-1/2 w-full">
                <a href="{{ route('bandera.index') }}">
                <div class="px-2 py-6 rounded-lg bg-white shadow-md hover:drop-shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="bg-teal-600/20 text-teal-600 p-2 w-12 h-12 mb-3 inline-block rounded-xl" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                    </svg>
                    <h2 class="title-font font-medium text-3xl text-gray-900">2.7K</h2>
                    <p class="leading-relaxed">Banderas</p>
                </div>
                </a>
            </div>
            <div class="p-2 md:w-1/3 sm:w-1/2 w-1/2 w-full">
                <a href="{{ route('intervencion.index') }}">
                    <div class="px-2 py-6 rounded-lg bg-white shadow-md hover:drop-shadow-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="bg-sky-600/20 text-sky-600 p-2 w-12 h-12 mb-3 inline-block rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288" />
                        </svg>
                        <h2 class="title-font font-medium text-3xl text-gray-900">1.3K</h2>
                        <p class="leading-relaxed">Intervenciones</p>
                    </div>
                </a>
            </div>
            <div class="p-2 md:w-1/3 sm:w-1/2 w-full">
                <a href="{{ route('novedad-de-material.index') }}">
                    <div class="px-2 py-6 rounded-lg bg-white shadow-md hover:drop-shadow-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="bg-purple-600/20 text-purple-600 p-2 w-12 h-12 mb-3 inline-block rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <h2 class="title-font font-medium text-3xl text-gray-900">74</h2>
                        <p class="leading-relaxed">Novedades Materiales</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>
