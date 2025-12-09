@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')

<div class="text-gray-600 dark:text-gray-100 body-font px-4 ">
    <div class="flex justify-between align-center mb-sm-4">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white mt-3 "> Guardavidas bloqueados </h1>

        <a href="{{ route('guardavida.create') }}" class="btn hidden sm:flex align-center bg-sky-500 dark:bg-sky-700 hover:bg-sky-400 dark:hover:bg-sky-600 rounded-full px-3 py-2 shadow">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="text-sky-500 dark:text-sky-700 w-5 h-5 bg-gray-100 dark:bg-gray-200 rounded me-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span class="text-gray-100 dark:text-gray-200"> Agregar</span>
        </a>
    </div>
    {{-- Acomodar js sin fecha para guardavidas! --}}
    @include('ui.guardavidas.partials.filtros-de-busqueda')

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded my-2">
            {{ session('success') }}
        </div>
    @endif

</div>

<div x-data="{ selectedId: null }">
    {{-- Lista para Mobile --}}
    <div class="space-y-4 sm:hidden">
    <section class="text-gray-600 dark:text-gray-100 body-font px-4 py-4 mb-16">
        <div id="accordion-collapse" data-accordion="collapse" class="bg-gray-100 dark:bg-gray-700">
            @foreach ($guardavidasDeshabilitados as $registro)
                <div class="registro-item-lista rounded "
                    data-playa="{{ $registro->playa->id ?? '' }}">

                    <div class="flex items-center justify-between w-full px-4 py-2 font-medium text-left text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $registro->apellido }} {{ $registro->nombre }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-200">
                            {{ str_replace('_', ' ', $registro->funcion) }}
                            </p>
                            <span class="text-sm text-gray-500 dark:text-gray-300">
                            {{ $registro->playa->nombre }}-{{ $registro->puesto->nombre }}
                            </span>
                            <p class="text-sm text-gray-700 dark:text-gray-400 mt-1 line-clamp-2">
                                {!!$registro->user->email !!}
                            </p>
                        </div>
                        @can('eliminar_guardavida')
                        <form action="{{ route('user.toggle', $registro->user->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                    {{ $registro->user->enabled ? 'bg-sky-500' : 'bg-gray-400' }}">
                                <span class="sr-only">Toggle habilitado</span>
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                    {{ $registro->user->enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </form>
                        @else
                        TODO btn disabled
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <a href="{{ route('guardavida.create') }}" class="btn fixed z-40 flex align-content-center bg-sky-500 dark:bg-sky-700 bottom-24 right-8 rounded-full px-3 py-3 shadow">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        class="text-sky-500 w-6 h-6 z-50 bg-gray-100 rounded me-2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span class="text-gray-100 text-lg"> Agregar</span>
    </a>
</div>


    {{-- Tabla para Desktop --}}
    {{-- @include('ui.guardavidas.partials.index-desktop') --}}




</div> <!-- selectedId -->


<script src="{{ asset('js/table-intervenciones.js') }}"></script>
@endsection
