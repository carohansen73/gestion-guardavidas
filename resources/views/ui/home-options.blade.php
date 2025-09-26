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


<div class="flex flex-col justify-between h-screen">
    {{-- BANDERA DEL DIA --}}
    <section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10">
        {{-- <div class="container  "> --}}
            <div class="col-12"></div>

            {{-- <div class="flex flex-wrap lg:w-4/5 sm:mx-auto sm:mb-2 -mx-2"> --}}

            {{-- <div class="p-2 w-full"> --}}
                <div class="bg-gray-200 dark:bg-gray-600 rounded flex p-4 h-full ">

                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-sky-500 dark:text-sky-300 w-6 h-6 flex-shrink-0 mr-4">
                        <path fill-rule="evenodd" d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054A8.25 8.25 0 0 0 18 4.524l3.11-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" clip-rule="evenodd" />
                    </svg>
                    <div class="flex flex-col ">
                        <span class="title-font font-medium text-gray-500 dark:text-gray-300 ">Bandera del día </span>
                        <p class="title-font font-medium text-sky-500 dark:text-sky-300"> Mar bueno </p>
                    </div>
                </div>
            {{-- </div> --}}
        {{-- </div> --}}
    </section>

{{-- OPCIONES --}}
<section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10">








        <div class="py-2  w-full">
            <a href="{{ route('intervencion.create') }}" class="bg-yellow-500 rounded flex p-4 h-full justify-between">
                <span class="title-font font-medium">Registrar asistencia</span>
                <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="3"
                stroke="currentColor"
                class=" w-6 h-6 flex-shrink-0 mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>
        <div class="py-2 w-full">
            <a href="{{ route('intervencion.create') }}" class="border-2 border-gray-200 rounded flex p-4 h-full justify-between">
                <span class="title-font font-medium ">Ingresar registro</span>
                <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="3"
                stroke="currentColor"
                class=" w-6 h-6 flex-shrink-0 mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>
         <div class="py-2  w-full">
            <a href="{{ route('intervencion.create') }}" class="border-2 border-gray-200 rounded flex p-4 h-full justify-between">
                <span class="title-font font-medium ">Ver registros</span>
                <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="3"
                stroke="currentColor"
                class=" w-6 h-6 flex-shrink-0 mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>












</section>

</div>


@endsection
