
<section class="text-gray-600 body-font">
    <div class="container px-4 py-4 mx-auto">
        <div class="flex flex-col text-center w-full mb-2">
            <h1 class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900">Registrar un evento</h1>
        </div>
        <div class="flex flex-wrap text-center">
             <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                <a href="{{ route('guardavida.create') }}">
                <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                    </svg>
                    {{-- SOLO ADMIN? JEFE dE PLAYA? ENCARGADO? --}}
                    <p class="leading-none">Guardavida</p>
                </div>
                </a>
            </div>
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12  text-sky-600 mx-auto mb-3 mb-3 inline-block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                    <p class="leading-none">Asistencia</p>
                </div>
            </div>
           <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
            <a href="{{ route('intervencion.create') }}">
                <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288" />
                    </svg>
                    <p class="leading-none">Intervencion</p>
                </div>
            </a>

            </div>
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                 <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                       class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p class="leading-none">Novedad de Material</p>
                </div>
            </div>
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                 <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                    <p class="leading-none">Cambio de turno</p>
                </div>
            </div>
           <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
             <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer  bg-white dark:bg-gray-600 transition ease-in duration-300">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                    </svg>

                    <p class="leading-none">Licencia</p>
                </div>
            </div>

        </div>
    </div>
</section>
