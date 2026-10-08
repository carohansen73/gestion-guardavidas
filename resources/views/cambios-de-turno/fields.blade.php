@extends('layouts.app')
@section('content')

@php $esCreacion = Route::currentRouteName() == 'cambio-de-turno.create'; @endphp

<section class="sm:px-4 sm:py-10">
    <x-form-tarjeta action="{{ isset($cambioDeTurno) ? route('cambio-de-turno.update', $cambioDeTurno->id) : route('cambio-de-turno.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($cambioDeTurno))
            @method('PUT')
        @endif

        <x-form-encabezado :titulo="$esCreacion ? 'Registrar cambio de turno' : 'Editar cambio de turno'" icono="arrow-left-right">
            {{ $esCreacion ? 'Indicá a qué guardavidas se le cambia el turno y cuándo.' : 'Modificá los datos del cambio de turno.' }}
        </x-form-encabezado>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded dark:bg-red-900/40 dark:text-red-300">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-form-seccion titulo="Cambio" icono="arrow-left-right">
            @if ($esCreacion)
                <div class="mb-1 rounded-md bg-blue-50 p-3 text-sm text-blue-800 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-800">
                    Recordá que la <strong>Playa, Puesto, Turno y Función</strong> se asignan automáticamente de acuerdo al guardavidas seleccionado.
                    Si necesitás modificar esta información, podrás hacerlo desde la edición del cambio de turno.
                </div>
            @else
                <div class="mb-1 rounded-md bg-yellow-50 p-3 text-sm text-yellow-800 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-200 dark:border-yellow-800">
                    ⚠️ Si cambiás el <strong>guardavidas</strong>, recordá actualizar los campos
                    <strong>Playa</strong>, <strong>Puesto</strong>, <strong>Turno</strong> y <strong>Función</strong>
                    para que coincidan con el nuevo guardavidas seleccionado.
                </div>
            @endif

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-8">
                <!-- Guardavida -->
                <div class="sm:col-span-4">
                    <label for="guardavida_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Guardavida</label>
                    <div>
                        <select id="guardavida_id" name="guardavida_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                            @foreach($guardavidas as $guardavida)
                            <option value="{{ $guardavida->id }}"
                                @if( isset($cambioDeTurno) && $cambioDeTurno->guardavida_id == $guardavida->id )
                                    selected
                                @endif >
                                    {{ $guardavida->nombre }} {{ $guardavida->apellido }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Fecha-->
                <div class="sm:col-span-4">
                    <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha</label>
                    <div>
                        <input id="fecha" type="date" name="fecha"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" value="{{ old('fecha', $cambioDeTurno?->fecha?->format('Y-m-d') ) }}" />
                    </div>
                </div>
            </div>
        </x-form-seccion>

        @if (Route::currentRouteName() === 'cambio-de-turno.edit')
            <x-form-seccion titulo="Turno, función y ubicación" icono="map-pin">
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-8">
                    @include('cambios-de-turno.partials.edit_only-fields')
                </div>
            </x-form-seccion>
        @endif

        <x-form-seccion titulo="Detalles" icono="align-left">
            <div>
                <label for="detalles" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Detalles</label>
                <div>
                    <textarea id="detalles" name="detalles" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">{{ old('detalles', $cambioDeTurno->detalles ?? '') }}</textarea>
                </div>
            </div>
        </x-form-seccion>

        <!-- Botones -->
        <div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-end sm:gap-x-6">
            <button type="button" class="text-sm text-gray-500 dark:text-gray-400 hover:underline" onclick="window.history.back()">Cancelar</button>
            <button type="submit" class="w-full sm:w-auto bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar</button>
        </div>
    </x-form-tarjeta>

    <div class="py-4 w-full sm:hidden">
         <a href="{{ route('cambio-de-turno.index') }}" class="bg-sky-600 rounded flex py-4 px-4 h-full justify-between">
             <div class="flex">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                 <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                 </svg>
                 <span class="title-font font-medium text-gray-100">Ver Cambios de turno</span>
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

@vite(['resources/js/filterPuestoByPlaya.js'])
@endsection
