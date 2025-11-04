@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')


<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>


<div class="text-gray-600 dark:text-gray-100 body-font px-4 ">

    <div class="flex justify-between align-center mb-sm-4">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white mt-3 "> Intervenciones </h1>
        @can('agregar_intervencion')
            <a href="{{ route('intervencion.create') }}" class="btn hidden sm:flex align-center bg-sky-500 dark:bg-sky-700 hover:bg-sky-400 dark:hover:bg-sky-600 rounded-full px-3 py-2 shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="text-sky-500 dark:text-sky-700 w-5 h-5 bg-gray-100 dark:bg-gray-200 rounded me-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span class="text-gray-100 dark:text-gray-200"> Agregar</span>
            </a>
        @endcan
    </div>

    <div x-data="{ selectedId: null }">

    <x-filtros-de-busqueda :playas="$playas" tipo="intervenciones" />

    @if (session('success'))
         <div class="bg-green-100 text-green-700 p-3 rounded my-2">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded my-2">
            {{ session('error') }}
        </div>
    @endif
</div>

<div x-data="{ selectedId: null }">

    {{-- Lista para Mobile --}}
    <div class="space-y-4 sm:hidden">
        <section class="text-gray-600 dark:text-gray-100 body-font py-3 px-4 mb-10">
            <div id="accordion-collapse" data-accordion="collapse" class="bg-white dark:bg-gray-600">
                @foreach ($intervenciones as $intervencion)
                    <div class=" rounded "
                    data-playa="{{ $intervencion->playa->id ?? '' }}"
                    data-fecha="{{ $intervencion->fecha->format('Y-m-d') }}">
                        <h2 id="accordion-collapse-heading-{{$intervencion->id}}">
                            <button type="button" class="flex items-center justify-between w-full px-4 py-2 font-medium text-left text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3"
                                data-accordion-target="#accordion-collapse-body-{{$intervencion->id}}" aria-expanded="true" aria-controls="accordion-collapse-body-1">
                                {{-- <a href="{{ route('intervencion.show', $intervencion) }}"> --}}
                                    <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $intervencion->tipo_intervencion }}
                                    </h3>
                                        <span class="text-sm text-gray-500 dark:text-gray-300">
                                        {{ $intervencion->fecha->format('d/m/Y') }}
                                        </span>
                                    <p class="text-sm text-gray-700 dark:text-gray-400 mt-1 line-clamp-2">
                                        {{ Str::limit($intervencion->detalles, 80) }}
                                    </p>
                                    </div>
                                {{-- </a> --}}
                                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                                </svg>
                            </button>
                        </h2>

                        <div id="accordion-collapse-body-{{$intervencion->id}}" class="hidden" aria-labelledby="accordion-collapse-heading-{{$intervencion->id}}">

                            <div class="px-4 py-2 border border-b-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                                {{--   <div class="w-full flex justify-end">
                                        <a href="{{ route('intervencion.edit', $intervencion->id) }}"
                                            class="bg-blue-600 text-white p-2 rounded-full shadow hover:bg-blue-700 transition"
                                            aria-label="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-5 h-5">
                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652l-9.193 9.193a4.5 4.5 0 01-1.897 1.13l-3.106.888a.375.375 0 01-.465-.465l.888-3.106a4.5 4.5 0 011.13-1.897l9.304-9.304z" />
                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M19.5 7.125L16.862 4.487" />
                                            </svg>
                                        </a>
                                    </div> --}}
                                {{-- Menu opciones - Editar eliminar --}}

                                <div class="w-full flex justify-end">
                                    <button class=""
                                    type="button"
                                    data-drawer-target="drawer-bottom-example"
                                    data-drawer-show="drawer-bottom-example"
                                    data-drawer-placement="bottom"
                                    aria-controls="drawer-bottom-example"
                                        @click="selectedId = {{ $intervencion->id }}">
                                        <!-- icono tres puntos -->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" class="bi bi-three-dots-vertical w-6 h-6" viewBox="0 0 16 16">
                                        <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                    Victimas
                                    <span class="text-sm text-gray-500 font-light dark:text-gray-300">
                                        {!!$intervencion->victimas !!}
                                    </span>
                                </p>
                                <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                    Código
                                    <span class="text-sm text-gray-500 font-light dark:text-gray-300">
                                        {!!$intervencion->codigo!!}
                                    </span>
                                </p>
                                <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                    Bandera
                                    <span class="text-sm text-gray-500 dark:text-gray-300">
                                        {!!$intervencion->bandera->codigo !!}
                                    </span>
                                </p>
                                <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                    ¿Hubo traslado?
                                    <span class="text-sm text-gray-500 dark:text-gray-300">
                                        @if($intervencion->traslado)
                                        Si
                                        @else
                                        No
                                        @endif
                                    </span>
                                </p>
                                <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                    Puesto
                                    <span class="text-sm text-gray-500 dark:text-gray-300">
                                        {!!$intervencion->puesto->nombre !!} - {!!$intervencion->playa->nombre !!}
                                    </span>
                                </p>
                                <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                    Detalles
                                    <span class="text-sm text-gray-500 dark:text-gray-300">
                                        {!!$intervencion->detalles !!}
                                    </span>
                                </p>
                                <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                    Guardavidas que intervinieron
                                    <ul>
                                        @foreach($intervencion->guardavidas as $guardavida)
                                        <li class="text-sm text-gray-500 dark:text-gray-300 px-2">
                                            {{ $guardavida->nombre }} {{ $guardavida->apellido }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </p>
                                <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                    ¿Intervinieron otras fuerzas?
                                    <ul>
                                        @foreach($intervencion->fuerzas as $fuerza)
                                            <li class="text-sm text-gray-500 dark:text-gray-300 px-2">
                                                {{ $fuerza->nombre }}
                                            </li>
                                        @endforeach
                                        @if ($intervencion->fuerzas->isEmpty())
                                            <li class="text-sm text-gray-500 dark:text-gray-300 px-2">
                                            No
                                            </li>
                                        @endif
                                    </ul>
                                </p>
                                <div class="flex justify-center py-3">

                                    {{-- BOTONES GROUP --}}
                                    <div class="inline-flex rounded-md shadow-xs" role="group">
                                        @can('editar_intervencion')
                                            <a  href="{{ route('intervencion.edit', $intervencion->id) }}"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 20 20"
                                                    stroke-width="1.5" stroke="currentColor"
                                                    class="w-3 h-3 me-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                Editar
                                            </a>
                                        @else
                                            <a  href="" disabled class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" stroke-width="1.5" stroke="currentColor" class="w-3 h-3 me-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                Editar
                                            </a>
                                        @endcan
                                        @can('eliminar_intervencion')
                                            <form action="{{ route('intervencion.destroy', $intervencion) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta intervención?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-red-300 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 20 20"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                        class="w-3 h-3 me-2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </form>
                                        @else
                                            <button disabled class="inline-flex items-center px-4 py-2 text-sm font-medium bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 ">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 20 20"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    class="w-3 h-3 me-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                Eliminar
                                            </button>
                                        @endcan
                                    </div>
                                    {{-- FIN --}}
                                </div> <!--flex-->
                            </div><!--px-4-->
                        </div><!--accordion-->

                </div>
                @endforeach
            </div>

        </section>
        @can('agregar_intervencion')
            <a href="{{ route('intervencion.create') }}" class="btn fixed z-40 flex align-content-center bg-sky-500 dark:bg-sky-700 bottom-24 right-8 rounded-full px-3 py-3 shadow">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="text-sky-500 w-6 h-6 z-50 bg-gray-100 rounded me-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span class="text-gray-100 text-lg"> Agregar</span>
            </a>
        @endcan
    </div>

    {{-- Tabla para Desktop --}}


<div class="hidden sm:block overflow-x-auto space-y-12">
    <div class="pb-12 dark:border-white/10 rounded-lg dark:bg-gray-600 px-4 py-2">
        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg shadow">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left cursor-pointer" onclick="toggleSort()">Fecha</th>
                        <th class="px-4 py-2 text-left">Tipo</th>
                        <th class="px-4 py-2 text-left">Víctimas</th>
                        <th class="px-4 py-2 text-left">Código</th>
                        <th class="px-4 py-2 text-left">Playa</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach ($intervenciones as $intervencion)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800"
                            data-playa="{{ $intervencion->playa->id ?? '' }}"
                            data-fecha="{{ $intervencion->fecha->format('Y-m-d') }}">
                            <td class="px-4 py-2">{{ $intervencion->fecha->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $intervencion->tipo_intervencion }}</td>
                            <td class="px-4 py-2">{{ $intervencion->victimas }}</td>
                            <td class="px-4 py-2">{{ $intervencion->codigo }}</td>
                            <td class="px-4 py-2">{{ $intervencion->playa->nombre ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <div class="flex space-x-2">
                                    <a href="{{ route('intervencion.show', $intervencion) }}"
                                    class="text-blue-600 hover:underline">Ver</a>
                                    @can('editar_intervencion')
                                        <a href="{{ route('intervencion.edit', $intervencion) }}"
                                        class="text-yellow-600 hover:underline">Editar</a>
                                    @endcan
                                    @can('eliminar_intervencion')
                                        <form action="{{ route('intervencion.destroy', $intervencion) }}" method="POST"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar esta intervención?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>





    <!-- drawer component -->
    <div id="drawer-bottom-example" class="fixed bottom-0 left-0 right-0 z-50 w-full p-4 overflow-y-auto transition-transform translate-y-full bg-white dark:bg-gray-800 " tabindex="-1"
        aria-labelledby="drawer-bottom-label">
        <h5 id="drawer-bottom-label" class="inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>Opciones de intervención
        </h5>

        <button type="button" data-drawer-hide="drawer-bottom-example" aria-controls="drawer-bottom-example" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 end-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white" >
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">Close menu</span>
        </button>
        {{-- Acciones --}}
        <div class="py-4">
            <ul class="space-y-3 font-medium">
                @can('editar_intervencion')
                <a :href="'{{ route('intervencion.edit', ':id') }}'.replace(':id', selectedId)">
                    <li class="py-2 inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor"
                            class="w-5 h-5 me-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        Editar
                    </li>
                </a>
                @else
                    <a href="" disabled>
                        <li class="py-2 inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 me-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            Editar
                        </li>
                    </a>
                @endcan
                <li class="py-2">
                    {{-- Lo puede eliminar el suuario que lo cargo?
                        auth()->id() === $intervencion->user_id || --}}
                  @can('eliminar_intervencion')
                        <form :action="`/intervencion/${selectedId}`" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta intervención?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex w-full py-3 text-left hover:bg-gray-100 rounded-lg text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-5 h-5 me-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>

                                Eliminar
                            </button>
                        </form>
                    @else
                        <button type="button"
                            class=" inline-flex text-red-400 font-medium cursor-not-allowed opacity-60"
                            disabled>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-5 h-5 me-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            Eliminar
                        </button>
                    @endcan
                </li>
            </ul>
        </div>
    </div>


</div> <!-- selectedId -->


<script src="{{ asset('js/table-intervenciones.js') }}"></script>
@endsection
