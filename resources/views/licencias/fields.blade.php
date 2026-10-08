@extends('layouts.app')
@section('content')

@php $esEdicion = Route::currentRouteName() === 'licencia.edit'; @endphp

<section class="sm:px-4 sm:py-10">
    <x-form-tarjeta action="{{ isset($licencia) ? route('licencia.update', $licencia->id) : route('licencia.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($licencia))
            @method('PUT')
        @endif

        <x-form-encabezado :titulo="$esEdicion ? 'Editar licencia' : 'Registrar licencia'" icono="file-text">
            {{ $esEdicion ? 'Modificá los datos de la licencia.' : 'Completá los datos de la licencia del guardavidas.' }}
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

        <x-form-seccion titulo="Licencia" icono="users">
            @if ($esEdicion)
                <div class="mb-1 rounded-md bg-yellow-50 p-3 text-sm text-yellow-800 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-200 dark:border-yellow-800">
                    ⚠️ Si cambiás el <strong>guardavidas</strong>, recordá actualizar los campos
                    <strong>Playa</strong>, <strong>Puesto</strong> y <strong>Turno</strong>
                    para que coincidan con el nuevo guardavidas seleccionado.
                </div>
            @else
                <div class="mb-1 rounded-md bg-blue-50 p-3 text-sm text-blue-800 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-800">
                    Recordá que la <strong>Playa, Puesto y Turno</strong> se asignan automáticamente de acuerdo al guardavidas seleccionado.
                    Si necesitás modificar esta información, podrás hacerlo desde la edición de la licencia.
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
                                @if( isset($licencia) && $licencia->guardavida_id == $guardavida->id )
                                    selected
                                @endif >
                                    {{ $guardavida->apellido }}, {{ $guardavida->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                 <!-- Tipo Licencia -->
                <div class="sm:col-span-4">
                    <label for="bandera_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Licencia</label>
                    <div>
                        <select name="tipo_licencia" id="tipo_licencia"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                            <option value="">Seleccione un tipo</option>
                            <option value="Capacitación"{{ old('tipo_licencia', $licencia->tipo_licencia ?? '') == 'Capacitación' ? 'selected' : '' }}>
                                Capacitación
                            </option>
                            <option value="Enfermedad" {{ old('tipo_licencia', $licencia->tipo_licencia ?? '') == 'Enfermedad' ? 'selected' : '' }}>
                                Enfermedad
                            </option>
                            <option value="Evento deportivo" {{ old('tipo_licencia', $licencia->tipo_licencia ?? '') == 'Evento deportivo' ? 'selected' : '' }}>
                                Evento deportivo
                            </option>
                            <option value="Exámen" {{ old('tipo_licencia', $licencia->tipo_licencia ?? '') == 'Exámen' ? 'selected' : '' }}>
                                Exámen
                            </option>
                            <option value="Fallecimiento familiar" {{ old('tipo_licencia', $licencia->tipo_licencia ?? '') == 'Fallecimiento familiar' ? 'selected' : '' }}>
                            Fallecimiento familiar
                            </option>
                            <option value="Lesión" {{ old('tipo_licencia', $licencia->tipo_licencia ?? '') == 'Lesión' ? 'selected' : '' }}>
                            Lesión
                            </option>
                            <option value="Licencia médica" {{ old('tipo_licencia', $licencia->tipo_licencia ?? '') == 'Licencia médica' ? 'selected' : '' }}>
                            Licencia médica
                            </option>
                            <option value="Permiso especial" {{ old('tipo_licencia', $licencia->tipo_licencia ?? '') == 'Permiso especial' ? 'selected' : '' }}>
                            Permiso especial
                            </option>
                            <option value="Otro" {{ old('tipo_licencia', $licencia->tipo_licencia ?? '') == 'Otro' ? 'selected' : '' }}>
                                Otro
                            </option>
                        </select>
                    </div>
                </div>

                  <!-- En tiempo -->
                <div class="sm:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">¿Avisó en tiempo?</label>
                    <div class="mt-2 flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="en_tiempo" value="1" required
                                {{ old('en_tiempo', $licencia->en_tiempo ?? '') == true ? 'checked' : '' }}
                                class="text-sky-600 border-gray-300 focus:ring-indigo-500 dark:border-gray-600">
                            <span class="ml-2">Si</span>
                        </label>

                        <label class="inline-flex items-center">
                            <input type="radio" name="en_tiempo" value="0"
                            {{ old('en_tiempo', $licencia->en_tiempo ?? '') == false ? 'checked' : '' }}
                                class="text-sky-600 border-gray-300 focus:ring-indigo-500 dark:border-gray-600">
                            <span class="ml-2">No</span>
                        </label>
                    </div>
                </div>
            </div>
        </x-form-seccion>

        <x-form-seccion titulo="Período" icono="calendar">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-8">
                <!-- Fecha inicio -->
                <div class="sm:col-span-3">
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha desde</label>
                    <div>
                        <input id="fecha_inicio" type="date" name="fecha_inicio"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" value="{{ old('fecha_inicio', $licencia?->fecha_inicio?->format('Y-m-d') ) }}" />
                    </div>
                </div>

                <!-- Fecha fin -->
                <div class="sm:col-span-3">
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha hasta</label>
                    <div>
                        <input id="fecha_fin" type="date" name="fecha_fin"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" value="{{ old('fecha_fin', $licencia?->fecha_fin?->format('Y-m-d') ?? '')  }}" />
                    </div>
                </div>
            </div>
        </x-form-seccion>

        @if ($esEdicion)
            <x-form-seccion titulo="Playa, puesto y turno" icono="map-pin">
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-8">
                    @include('licencias.partials.edit_only-fields')
                </div>
            </x-form-seccion>
        @endif

        <x-form-seccion titulo="Archivo adjunto" icono="paperclip">
            <div>
                <label for="archivo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Archivo (foto o PDF de la licencia - opcional)
                </label>
                <input type="file" name="archivo" id="archivo"
                    accept=".jpg,.jpeg,.png,.pdf"
                    class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-white dark:bg-gray-700 dark:text-white dark:border-gray-500">
                @if(isset($licencia) && !empty($licencia->archivo))

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        Archivo actual:
                        <a href="{{ asset('storage/' . $licencia->archivo) }}" target="_blank" class="text-indigo-600 underline">Ver archivo</a>
                    </p>

                @endif
            </div>
        </x-form-seccion>

        <x-form-seccion titulo="Detalles" icono="align-left">
            <div>
                <label for="detalle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Detalles</label>
                <div>
                    <textarea id="detalle" name="detalle" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">{{ old('detalle', $licencia->detalle ?? '') }}</textarea>
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
         <a href="{{ route('licencia.index') }}" class="bg-sky-600 rounded flex py-4 px-4 h-full justify-between">
             <div class="flex">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                 <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                 </svg>
                 <span class="title-font font-medium text-gray-100">Ver licencias</span>
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
