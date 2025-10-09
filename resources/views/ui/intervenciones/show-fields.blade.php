@extends('layouts.app')

@section('content')


<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>


<section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10  ">

    <div class="flex justify-between align-center mb-sm-2">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white my-2">Intervención</h1>
        <a href="{{ route('intervencion.edit', $intervencion) }}" class="btn hidden sm:flex align-center bg-sky-500 dark:bg-sky-700 hover:bg-sky-400 dark:hover:bg-sky-600 rounded-full px-3 py-2 shadow-md hover:shadow-xl">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="text-sky-500 dark:text-sky-700 w-5 h-5 bg-gray-100 dark:bg-gray-200 rounded me-2 p-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            <span class="text-gray-100 dark:text-gray-200"> Editar</span>
        </a>
    </div>

    <div class="container bg-white rounded shadow-md  px-4 py-4">

        <div class="pb-12">

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ $intervencion->tipo_intervencion }}
            </h3>
            <span class="text-sm text-gray-500 dark:text-gray-300">
                {{ $intervencion->fecha->format('d/m/Y') }}
            </span>

            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Codigo
                <span class="text-sm text-gray-500 dark:text-gray-300">
                {{ $intervencion->codigo }}
                  </span>
            </p>
            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Victimas
                <span class="text-sm text-gray-500 dark:text-gray-300">
                {{ $intervencion->victimas }}
                  </span>
            </p>
            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Traslado
                <span class="text-sm text-gray-500 dark:text-gray-300">
                {{ $intervencion->traslado }}
                </span>
            </p>

            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                {{ $intervencion->playa->nombre }} -  {{ $intervencion->puesto->nombre }}
            </p>

             <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Bandera
                <span class="text-sm text-gray-500 dark:text-gray-300">
                {{ $intervencion->bandera->codigo }}
                  </span>
            </p>

            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Detalles
                <span class="text-sm text-gray-500 dark:text-gray-300">
                    {!!$intervencion->detalles !!}
                </span>
            </p>

        </div>
    </div>
</section>
@endsection
