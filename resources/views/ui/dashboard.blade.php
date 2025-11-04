@extends('layouts.app')

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

@section('content')

{{-- <button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>

<div :class="{'dark': darkMode}">
   <p class="text-gray-900 dark:text-gray-100">Dashboard</p>
</div> --}}



<div class="flex flex-col md:flex-row gap-6 px-6 py-6">

    <!-- 🟦 Columna principal (2/3 del ancho) -->
    <main class="w-full md:w-2/3  space-y-6">
        {{-- hasta aca tenia --}}




         <div class="grid grid-cols-12 gap-4 ">
              <div class="col-span-12 space-y-6 ">



        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">










<!-- Metric Intervenciones Start  -->
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <div class="flex justify-between">
        <div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-orange-600 p-2 inline-block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288" />
                </svg>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                Intervenciones
            </div>
            <span class="text-title-sm font-bold text-gray-800 dark:text-white/90">
                {{ $totalIntervenciones }}
            </span>
        </div>

        <div class="items-end text-end">
            @foreach ($intervencionesPorPlaya as $i)
              <div class="flex items-center gap-1  {!! $i->playa->color !!} rounded-full bg-error-50 py-0.5 pl-2 pr-2.5 my-1 text-sm font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                <span class=" px-1  text-sm"> {{ $i->sigla }} </span>
                {{ $i->porcentaje }}%
            </div>
            @endforeach
        </div>
    </div>
</div>
  <!-- Metric Intervenciones End -->


<!-- Metric Novedades de materiales Start  -->
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <div class="flex justify-between">
        <div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-teal-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-gray-600 p-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                Novedades de materiales
            </div>
            <span class="text-title-sm font-bold text-gray-800 dark:text-white/90">
                {{ $totalNovedadesMateriales }}
            </span>
        </div>

        <div class="items-end text-end">
            @foreach ($novedadesMaterialesPorPlaya as $i)
              <div class="flex items-center gap-1  {!! $i->playa->color !!} rounded-full bg-error-50 py-0.5 pl-2 pr-2.5 my-1 text-sm font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                <span class=" px-1  text-sm"> {{ $i->sigla }} </span>
                {{ $i->porcentaje }}%
            </div>
            @endforeach
        </div>
    </div>
</div>
  <!-- Metric Novedades de materiales End -->
        </div>






       </div>
    </div>






    </main>
    <!--  End Columna principal (2/3 del ancho) -->

    <!-- 🟨 Aside lateral (1/3 del ancho) -->
    <aside class="hidden md:block w-full md:w-1/3 ">

        <div class="container bg-white dark:bg-gray-600 w-full px-3 py-3 rounded my-4">
            <h3 class="flex items-center mb-4 text-xl font-semibold text-gray-900 dark:text-white">
                    Ultimas Novedades
                </h3>

        <ol class="relative border-s border-gray-200 dark:border-gray-700 mx-2 my-2">
        @foreach ($novedades as $index => $novedad)
                @php
            // $colores = [
            //     'orange' => 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-300',
            //     'teal' => 'bg-teal-100 dark:bg-teal-900 text-teal-800 dark:text-teal-300',
            //     'sky' => 'bg-sky-100 dark:bg-sky-900 text-sky-800 dark:text-sky-300',
            // ];
            // $colorClase = $colores[$novedad->color] ?? 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-300';
            @endphp

            <li class="mb-6 ms-6">
                <span class=" {!! $novedad->color !!}
                     absolute flex items-center justify-center w-6 h-6  rounded-full -start-3 ring-8 ring-white dark:ring-gray-900">
                     {!! $novedad->icono !!}
                </span>
                <h3 class="flex items-center mb-1  font-semibold text-gray-900 dark:text-white">
                     {{$novedad->titulo}}
                     @if ($loop->first)
                    <span class="bg-sky-100 text-sky-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 ms-3">
                        Latest
                    </span>
                    @endif
                </h3>
                <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                   {{$novedad->fecha}}
                </time>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400 ">
                    {{$novedad->playa->nombre}}
                </p>

                {{-- <a href="#" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700"><svg class="w-3.5 h-3.5 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                    <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                </svg> Download ZIP</a> --}}
            </li>
        @endforeach
        </ol>
        </div>


    </aside>
@endsection
