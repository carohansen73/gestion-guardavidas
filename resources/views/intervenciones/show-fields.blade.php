@extends('layouts.app')

@section('content')

<section class="text-gray-600 dark:text-gray-100 body-font px-4   ">

    <div class="flex justify-between align-center my-3">
        <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-3xl">Detalle de la intervención</h2>
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
                Playa y puesto:
                <span class="text-sm text-gray-500 dark:text-gray-300">
                {{ $intervencion->playa->nombre }} -  {{ $intervencion->puesto->nombre }}.
                </span>
            </p>
            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Codigo:
                <span class="text-sm text-gray-500 dark:text-gray-300">
                {{ $intervencion->codigo }}
                  </span>
            </p>
            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Victimas:
                <span class="text-sm text-gray-500 dark:text-gray-300">
                {{ $intervencion->victimas }}
                  </span>
            </p>
            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Traslado:
                <span class="text-sm text-gray-500 dark:text-gray-300">
                    @if($intervencion->traslado)
                        Si.
                    @else
                        No.
                    @endif
                </span>
            </p>
            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Bandera:
                <span class="text-sm text-gray-500 dark:text-gray-300">
                    @if($intervencion->bandera)
                        {{ $intervencion->bandera->bandera->codigo }} <br>
                        <ul class="list-disc ms-5">
                            @if($intervencion->bandera->viento_intensidad || $intervencion->bandera->viento_direccion)
                            <li>
                                <span class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 ">
                                    Viento:
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-300">
                                {{ $intervencion->bandera->viento_intensidad }} {{ $intervencion->bandera->viento_direccion }}
                                </span>
                            </li>
                            @endif
                            @if($intervencion->bandera->temperatura)
                            <li>
                                <span class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 ">
                                    Temperatura:
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-300">
                                {{ $intervencion->bandera->temperatura }}º
                                </span>
                            </li>
                            @endif
                        </ul>

                    @else
                        No había una bandera registrada al momento de la intervención.
                    @endif
                </span>
            </p>


            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Guardavidas que intervinieron:
                <span class="text-sm text-gray-500 dark:text-gray-300">
                    @if($intervencion->guardavidas->isNotEmpty())
                     <ul>
                        @foreach($intervencion->guardavidas as $guardavida)
                            <li class="text-sm text-gray-500 dark:text-gray-300 px-2">
                                {{ $guardavida->nombre }} {{ $guardavida->apellido }}
                            </li>
                        @endforeach
                        </ul>
                    @else
                        No se registraron guardavidas.
                    @endif
                </span>
            </p>

            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Fuerzas que intervinieron:
                <span class="text-sm text-gray-500 dark:text-gray-300">
                    @forelse($intervencion->fuerzas as $fuerza)
                        <li class="text-sm text-gray-500 dark:text-gray-300 px-2">{{ $fuerza->nombre }}</li>
                    @empty
                        No intervinieron otras fuerzas.
                    @endforelse
                </span>
            </p>

            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                Detalles:
                <span class="text-sm text-gray-500 dark:text-gray-300">
                    @if($intervencion->detalles)
                    {!!$intervencion->detalles !!}
                    @else
                    Sin detalles registrados.
                    @endif
                </span>
            </p>

        </div>
    </div>
</section>
@endsection
