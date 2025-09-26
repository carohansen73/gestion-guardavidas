@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')


<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>




@if (session('success'))
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif


{{-- Lista para Mobile --}}
<div class="space-y-4 sm:hidden">
    <section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10">
        {{-- <section class="text-gray-600 dark:text-gray-100 body-font block bg-white dark:bg-gray-800 rounded-xl shadow p-4 hover:shadow-md transition"> --}}
        <div class="py-4  w-full">
            <a href="{{ route('intervencion.create') }}" class="bg-sky-600 rounded flex px-4 py-4 h-full justify-between">
                <span class="title-font font-medium text-gray-100">Registrar intervención</span>
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
        <div class="bg-white rounded shadow-md px-4">
            <ul>
                @foreach ($intervenciones as $intervencion)
                    <li class="border-b border-gray-900/10 pb-1 pt-3 dark:border-white/10">
                        <a href="{{ route('intervencion.show', $intervencion) }}">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $intervencion->tipo_intervencion }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-300">
                                {{ $intervencion->fecha->format('d/m/Y') }}
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-400 mt-1 line-clamp-2">
                                {{ Str::limit($intervencion->detalles, 80) }}
                            </p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
</div>

    {{-- Tabla para Desktop --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg shadow">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left">Fecha</th>
                    <th class="px-4 py-2 text-left">Tipo</th>
                    <th class="px-4 py-2 text-left">Víctimas</th>
                    <th class="px-4 py-2 text-left">Código</th>
                    <th class="px-4 py-2 text-left">Playa</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @foreach ($intervenciones as $intervencion)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-2">{{ $intervencion->fecha->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $intervencion->tipo_intervencion }}</td>
                        <td class="px-4 py-2">{{ $intervencion->victimas }}</td>
                        <td class="px-4 py-2">{{ $intervencion->codigo }}</td>
                        <td class="px-4 py-2">{{ $intervencion->playa->nombre ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <div class="flex space-x-2">
                                <a href="{{ route('intervencion.show', $intervencion) }}"
                                   class="text-blue-600 hover:underline">Ver</a>
                                <a href="{{ route('intervencion.edit', $intervencion) }}"
                                   class="text-yellow-600 hover:underline">Editar</a>
                                <form action="{{ route('intervencion.destroy', $intervencion) }}" method="POST"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar esta intervención?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



<div class="space-y-4 sm:hidden">
    <section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10 ">

        <div id="accordion-collapse" data-accordion="collapse" >


            @foreach ($intervenciones as $intervencion)


            <div class=" rounded shadow-md ">
                <h2 id="accordion-collapse-heading-{{$intervencion->id}}">
                    <button type="button" class="flex items-center justify-between w-full px-4 py-2 font-medium text-left text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3"
                    data-accordion-target="#accordion-collapse-body-{{$intervencion->id}}" aria-expanded="true" aria-controls="accordion-collapse-body-1">
                        <a href="{{ route('intervencion.show', $intervencion) }}">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $intervencion->tipo_intervencion }}
                            </h3>
                                <span class="text-sm text-gray-500 dark:text-gray-300">
                                {{ $intervencion->fecha->format('d/m/Y') }}
                                </span>
                            <p class="text-sm text-gray-700 dark:text-gray-400 mt-1 line-clamp-2">
                                {{ Str::limit($intervencion->detalles, 80) }}
                            </p>
                        </a>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                    </button>
                </h2>

                <div id="accordion-collapse-body-{{$intervencion->id}}" class="hidden" aria-labelledby="accordion-collapse-heading-{{$intervencion->id}}">

                    <div class="px-4 py-2 border border-b-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                         <div class="w-full flex justify-end">
                             <a href="{{ route('intervencion.edit', $intervencion->id) }}"
             class="bg-blue-600 text-white p-2 rounded-full shadow hover:bg-blue-700 transition"
             aria-label="Editar">
              <!-- Icono de editar (Heroicons) -->
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
                                {!!$intervencion->traslado !!}
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
                            <span class="text-sm text-gray-500 dark:text-gray-300">
                                {{-- {!!$intervencion->guardavidas->codigo !!} --}}
                                recorrer
                            </span>
                        </p>
                        <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                            Fuerzas
                             <span class="text-sm text-gray-500 dark:text-gray-300">
                                Recorer
                            </span>
                        </p>



                        {{-- BOTONES GROUP --}}
                        <div class="inline-flex rounded-md shadow-xs" role="group">
                            <a  href="{{ route('intervencion.edit', $intervencion->id) }}"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                <svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
                                </svg>
                                Editar
                            </a>
                            @if(auth()->id() === $intervencion->user_id || auth()->user()->rol === 'encargado')
                                <form action="{{ route('intervencion.destroy', $intervencion) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta intervención?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                <svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                                <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                                </svg>
                                Eliminar
                            </button>
                              </form>
            @endif
                        </div>
                        {{-- FIN --}}

                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </div>
</section>




@endsection
