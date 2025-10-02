@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')


<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>


 <section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10">
  <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">Registrar Intervención</h2>

    <form action="{{ route('intervencion.store') }}" method="POST" class="bg-white rounded shadow-md ">
        @csrf

        <div class="container px-2 py-4 mx-auto">
            @include('ui.intervenciones.fields')
        </div>

    </form>

     <div class="py-4  w-full">
        <a href="{{ route('intervencion.index') }}" class="bg-sky-600 rounded flex py-4 px-4 h-full justify-between">
            <div class="flex">
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>

                <span class="title-font font-medium text-gray-100">Ver intervenciones</span>
            </div>

            <svg xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="3"
            stroke="currentColor"
            class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </a>
      </div>


 </section>

@endsection
