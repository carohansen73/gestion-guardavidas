@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')


<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>

<div :class="{'dark': darkMode}">
   <p class="text-gray-900 dark:text-gray-100">Hola!</p>
</div>






<section class="text-gray-600 body-font">
  <div class="container px-4 py-10 mx-auto">
    <div class="col-12"></div>

    {{-- <div class="flex flex-wrap lg:w-4/5 sm:mx-auto sm:mb-2 -mx-2"> --}}

      {{-- <div class="p-2 w-full"> --}}
        <div class="bg-gray-100 dark:bg-gray-600 rounded flex p-4 h-full ">

            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-sky-500 dark:text-sky-300 w-6 h-6 flex-shrink-0 mr-4">
                <path fill-rule="evenodd" d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054A8.25 8.25 0 0 0 18 4.524l3.11-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" clip-rule="evenodd" />
            </svg>
            <div class="flex flex-col ">
                <span class="title-font font-medium text-gray-500 dark:text-gray-300 ">Bandera del día </span>
                <p class="title-font font-medium text-sky-500 dark:text-sky-300"> Mar bueno </p>
            </div>
        </div>
      {{-- </div> --}}
  </div>
</section>



<section class="text-gray-600 body-font">
    <div class="container px-4 py-10 mx-auto">
        <div class="flex flex-col text-center w-full mb-10 -m-2">
            <h1 class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900">Registrar un evento</h1>
        </div>
        <div class="flex flex-wrap -m-4 text-center">
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer  bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                    </svg>
                    <p class="leading-none">Banderas</p>
                </div>
            </div>
           <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer  bg-yellow-500 dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-gray-800 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288" />
                    </svg>
                    <p class="leading-none">Intervenciones</p>
                </div>
            </div>
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                 <div class="  px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer  bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                       class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p class="leading-none">Novedades Materiales</p>
                </div>
            </div>
            <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
                 <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <p class="leading-none">Cambio de turno</p>
                </div>
            </div>
           <div class="p-2 lg:w-1/3 md:w-1/3 sm:w-1/2 w-1/2 ">
             <div class="px-4 py-6 rounded-lg shadow hover:shadow-md cursor-pointer  bg-white dark:bg-gray-600 transition ease-in duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="text-sky-600 w-10 h-10 sm:w-10 sm:h-10 lg:w-12 lg:h-12 mx-auto mb-3 mb-3 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                    <p class="leading-none">Licencias</p>
                </div>
            </div>

        </div>
    </div>
</section>



<div class="text-sky-500">Celeste Sky</div>
<div class="text-blue-500">Azul Blue</div>
<div class="text-red-500">Rojo</div>

@endsection
