@extends('layouts.app')

@section('content')

<section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10  ">

    <div class="flex justify-between align-center mb-sm-2">
        <h2 class="text-gray-700 text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl">Bandera</h2>
        <a href="{{ route('bandera.edit', $bandera) }}" class="btn hidden sm:flex align-center bg-sky-500 dark:bg-sky-700 hover:bg-sky-400 dark:hover:bg-sky-600 rounded-full px-3 py-2 shadow">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="text-sky-500 dark:text-sky-700 w-5 h-5 bg-gray-100 dark:bg-gray-200 rounded me-2 p-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>

            <span class="text-gray-100 dark:text-gray-200"> Editar</span>
        </a>
    </div>

    <div class="container bg-white rounded shadow-md  px-4 py-4 ">
        <div class="pb-12">
            <div class="flex items-center rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="bandera {{$bandera->bandera->color}} w-8 h-8 flex-shrink-0 mr-4 animate-ondear">
                    <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $bandera->bandera->codigo }}
                </h3>
            </div>

            <h3 class="text-base font-semibold text-gray-800 dark:text-white">
                {{ $bandera->playa->nombre }}
             {{-- - TODO: Especificar puesto? --}}
            </h3>
            <span class="text-sm text-gray-500 dark:text-gray-300">
                {{ $bandera->fecha->format('d/m/Y') }}
            </span>

            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Turno
                <span class="text-sm text-gray-500 dark:text-gray-300">
                    {!!$bandera->turno !!}
                </span>
            </p>
            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Viento
                <span class="text-sm text-gray-500 dark:text-gray-300">
                    {!!$bandera->viento_intensidad !!} {!!$bandera->viento_direccion !!}
                </span>
            </p>
                <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Temperatura
                <span class="text-sm text-gray-500 dark:text-gray-300">
                    {!!$bandera->temperatura !!}º
                </span>
            </p>
            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Detalles
                <span class="text-sm text-gray-500 dark:text-gray-300">
                    {!!$bandera->detalles !!}
                </span>
            </p>
        </div>
    </div>
</section>
@endsection
