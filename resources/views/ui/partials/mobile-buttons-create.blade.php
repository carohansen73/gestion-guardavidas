
<section class="text-gray-600 body-font">
    <div class="container px-4 py-4 mx-auto">
        <div class="flex flex-col text-center w-full mb-2">
            <h1 class=" mb-2 text-gray-700 text-3xl font-bold tracking-tight text-heading md:text-4xl lg:text-5xl">Registrar un evento</h1>
        </div>
        <div class="flex flex-wrap justify-center items-center text-center">
            @can('agregar_guardavida')
                <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                    <a href="{{ route('guardavida.create') }}">
                        <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 148.42 68.38"
                             class="bg-sky-100 rounded-xl p-2 w-12 h-12 text-sky-600 mx-auto mb-3 inline-block">
                                <path
                                    d="M-292.54,525.76a19.7,19.7,0,0,1,1.82,1c11.9,7.89,24.19,8.33,36.9,2,3.25-1.61,6.63-3,10-4.42,12.47-5.47,24.7-4.68,36.75,1.33,4,2,7.89,4.17,12,5.84,9.5,3.86,18.76,2.3,27.75-1.88a39.39,39.39,0,0,0,9.58-6.14c1-.9,1.95-1.21,2.83.22,1.54,2.52,3.83,4.52,5.19,7.47a61.59,61.59,0,0,1-24.51,12.28,43.78,43.78,0,0,1-20.64.09c-4.73-1.13-8.89-3.51-12.93-6a37.63,37.63,0,0,0-18.67-5.71c-6.34-.25-12.38,1.55-18.16,4.13-3.45,1.55-6.86,3.2-10.31,4.75-14.79,6.62-28.57,3.78-41.78-4.47-1.55-1-1.92-2-.65-3.57C-295.69,530.5-294.2,528.14-292.54,525.76Z"
                                    transform="translate(298.18 -476.39)" fill="currentColor" />
                                <path
                                    d="M-271.79,514.06a81.68,81.68,0,0,1,23-10.12,82.59,82.59,0,0,1,27.41-3c2.86.19,3.24-.28,3-3a9.81,9.81,0,0,0-5.94-8.56,20.43,20.43,0,0,0-12.38-2c-2.61.47-3,.14-3.63-2.62-.18-.8-.17-1.65-.31-2.47-.93-5.39-.95-5.75,4.35-5.92,6.41-.21,12.72.52,18.49,4,8.7,5.21,11.9,15.13,10.76,24.41-.24,1.95-1,3.82-1.34,5.76-.6,3.4-.87,3.36-4.47,2.7a66.77,66.77,0,0,0-12.71-1.41c-12.19.13-23.89,2.51-34.7,8.45-1.3.72-2.67,1.3-3.95,2.05s-2.16.39-2.89-.78C-268.53,519.2-270,516.85-271.79,514.06Z"
                                    transform="translate(298.18 -476.39)" fill="currentColor" />
                                <path
                                    d="M-184.27,496.44a13.66,13.66,0,0,1,13.63,14.05c-.13,7.37-6.44,14.18-13.57,13.75-8.48-.51-14.14-5.63-14.18-14.2A13.59,13.59,0,0,1-184.27,496.44Z"
                                    transform="translate(298.18 -476.39)" fill="currentColor" />
                            </svg>

                            {{-- SOLO ADMIN? JEFE dE PLAYA? ENCARGADO? --}}
                            <p class="leading-none font-medium text-sky-800">Guardavida</p>
                        </div>
                    </a>
                </div>
            @endcan
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="bg-sky-100 rounded-xl p-2 w-12 h-12 text-sky-600 mx-auto mb-3 inline-block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                    <p class="leading-none font-medium text-sky-800">Fichar</p>
                </div>
            </div>
            @can('agregar_intervencion')
                <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                    <a href="{{ route('intervencion.create') }}">
                        <div class="px-4 py-8 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="bg-sky-100 rounded-xl p-2 w-12 h-12 text-sky-600 mx-auto mb-3 inline-block">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288" />
                            </svg>
                            <p class="leading-none font-medium text-sky-800">Intervención</p>
                        </div>
                    </a>
                </div>
            @endcan
            @can('agregar_novedad_material')
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                <a href="{{ route('novedad-de-material.create') }}">
                 <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                       class="bg-sky-100 rounded-xl p-2 w-12 h-12 text-sky-600 mx-auto mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p class="leading-none">Novedad de Material</p>
                </div>
                </a>
            </div>
            @endcan
            @can('agregar_cambio_turno')
                <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                    <a href="{{ route('cambio-de-turno.create') }}">
                        <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="bg-sky-100 rounded-xl p-2 w-12 h-12 text-sky-600 mx-auto mb-3 inline-block">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                            <p class="leading-none">Cambio de turno</p>
                        </div>
                    </a>
                </div>
            @endcan
            @can('agregar_licencia')
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                   <a href="{{ route('licencia.create') }}">
                        <div class="px-4 py-8 rounded-lg shadow hover:shadow-md cursor-pointer  bg-white dark:bg-gray-600 transition ease-in duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="bg-sky-100 rounded-xl p-2 w-12 h-12 text-sky-600 mx-auto mb-3 inline-block">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                            </svg>
                            <p class="leading-none">Licencia</p>
                        </div>
                   </a>
                </div>
            @endcan

        </div>
    </div>
</section>
