@extends('layouts.app')
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
@section('content')


{{--
<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>

<div :class="{'dark': darkMode}">
   <p class="text-gray-900 dark:text-gray-100">Hola!</p>
</div> --}}
 @include('ui.partials.bandera')



{{--

@if($rol !== 'admin' && $playaUsuario)
    <div class="px-4 py-4">
        <div class="bg-gradient-to-r from-blue-400 to-teal-400 dark:from-blue-700 dark:to-teal-700 text-sky-900 rounded-2xl shadow-lg p-6 transform transition hover:scale-105 duration-300">
            <div class="flex justify-content-between align-center">
                <h2 class="text-2xl md:text-3xl font-bold ">Bandera del día</h2>
                @if(!$bandera)
                    <a href="{{ route('bandera.create') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-800/60 text-white rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('bandera.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="3"
                            stroke="currentColor"
                            class="w-8 h-8 py-2 px-2 bg-gray-800/60 text-white rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                @endif
            </div>

            @if($bandera)
                <!-- Izquierda: icono + playa -->
                <div class="grid grid-cols-3 mt-2">

                     <!-- Columna 1 -->
                    <div class="flex flex-col items-center rounded-lg">
                        <div class="bg-gray-200/50 rounded mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-12 h-12">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 12h12a2 2 0 10-2-2m-10 4h8a2 2 0 11-2 2" />
                            </svg>
                        </div>

                        <p class="text-sm font-medium"> {{ $bandera->viento_intensidad }} {{ $bandera->viento_direccion }}</p>
                    </div>

                    <!-- Columna 3 -->
                    <div class="flex flex-col items-center rounded-lg">
                        <div class="bg-gray-200/50 rounded mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class=" w-12 h-12">
                            <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" />
                            </svg>
                        </div>

                        <p class="text-sm font-medium">32º</p>
                    </div>

                     <!-- Columna 3 -->
                    <div class="flex flex-col items-center rounded-lg">
                        <div class="bg-gray-200/50 rounded mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"

                            class="bandera
                            text-{{$bandera->bandera->color}}-500 dark:text-{{$bandera->bandera->color}}-300
                                w-12 h-12 flex-shrink-0 animate-ondear">
                            <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                            </svg>
                        </div>

                         <p class="text-sm font-medium">{{$bandera->bandera->codigo}}</p>
                    </div>
                </div>
                @endif
        </div>
    </div>
@endif --}}


@if($isMobile)

    @include('ui.partials.mobile-buttons-create')

@else

     @include('ui.partials.desktop-buttons-create')

@endif

@if($isMobile)



@else

     @include('ui.partials.desktop-cards')

@endif




{{-- <div class="space-y-2 sm:hidden"> --}}
    <section class="text-gray-600 dark:text-gray-100 body-font px-4 py-4 mb-10">

        <div class="py-2  w-full">
           @if(auth()->user()->can('ver_guardavida') || auth()->user()->can('ver_intervencion')
           || auth()->user()->can('ver_novedad_material') || auth()->user()->can('ver_licencia')
           || auth()->user()->can('ver_bandera') || auth()->user()->can('ver_cambio_turno') )

            <a class="bg-sky-600 rounded flex px-4 py-3 h-full justify-between">
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span class="title-font font-medium text-gray-100">Ver Registros</span>
                </div>

                <div class=" flex justify-end">
                    <button class=""
                    type="button"
                    data-drawer-target="drawer-bottom-example"
                    data-drawer-show="drawer-bottom-example"
                    data-drawer-placement="bottom"
                    aria-controls="drawer-bottom-example"
                        >
                        <!-- icono tres puntos -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" class="bi bi-three-dots-vertical text-gray-100 w-6 h-6" viewBox="0 0 16 16">
                        <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                        </svg>
                    </button>
                </div>
            </a>
            @endif
        </div>

    </section>
{{-- </div> --}}



<!-- options list -->
    <!-- drawer component -->
    <div id="drawer-bottom-example" class="fixed bottom-0 left-0 right-0 z-50 w-full p-4 overflow-y-auto transition-transform translate-y-full bg-white dark:bg-gray-800 shadow-md" tabindex="-1"
        aria-labelledby="drawer-bottom-label">
        <h5 id="drawer-bottom-label" class="inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>Listados
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
                @can('ver_guardavida')
                <li class="py-2">
                    <a href="{{ route('guardavida.index') }}"
                        class=" inline-flex  font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 148.42 68.38"
                             class="w-5 h-5 me-2">
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
                        Guardavidas
                    </a>
                </li>
                @endcan
                @can('ver_intervencion')
                <li class="py-2">
                    <a href="{{ route('intervencion.index') }}" class=" inline-flex font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-5 h-5 me-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288" />
                        </svg>
                        Intervenciones
                    </a>
                </li>
                @endcan
                @can('ver_bandera')
                <li class="py-2">
                    <a href="{{ route('bandera.index') }}" class=" inline-flex font-medium">
                       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-5 h-5 me-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                        </svg>

                        Banderas
                    </a>
                </li>
                @endcan
                @can('ver_novedad_material')
                 <li class="py-2">
                    <a href="{{ route('novedad-de-material.index') }}"
                        class=" inline-flex  font-medium  "
                        disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="w-5 h-5 me-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        Novedades de materiales
                    </a>
                </li>
                @endcan
                {{-- @can('ver_guardavida')
                <li class="py-2">
                    <a href="{{ route('bandera.index') }}"
                        class=" inline-flex  font-medium  "
                        disabled>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5 me-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        Asistencia
                    </a>
                </li>
                @endcan--}}
                @can('ver_licencia')
                 <li class="py-2">
                    <a href="{{ route('licencia.index') }}"
                        class="inline-flex font-medium"
                        disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-5 h-5 me-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                        Licencias
                    </a>
                </li>
                @endcan
                 @can('ver_cambio_turno')
                 <li class="py-2">
                    <a href="{{ route('cambio-de-turno.index') }}"
                        class="inline-flex font-medium"
                        disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-5 h-5 me-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Cambios de turno
                    </a>
                </li>
                @endcan


            </ul>
        </div>
    </div>


@endsection
