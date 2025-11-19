@extends('layouts.app')
@section('content')

<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>

<section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10">
    @if (Route::currentRouteName() !== 'licencia.edit')
        <h2 class="mb-3 text-gray-700 text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl section-title">Editar licencia</h2>

        <div class="mb-4 rounded-md bg-blue-50 p-3 text-sm text-blue-800 border border-blue-200">
            <p class="text-sm text-gray-500 dark:text-gray-400">Recordá que la <strong class="font-medium text-gray-800 dark:text-white"> Playa, Puesto y Turno </strong>
                se asignan automáticamente de acuerdo al guardavidas seleccionado. <br>
            Si necesitás modificar esta información, podrás hacerlo desde la edición de la licencia.
            </p>
        </div>
    @else
        <h2 class="mb-3 text-gray-700 text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl section-title">Registrar licencia</h2>
        <div class="mb-4 rounded-md bg-yellow-50 p-3 text-sm text-yellow-800 border border-yellow-200">
            ⚠️ Si cambiás el <strong>guardavidas</strong>, recordá actualizar los campos
            <strong>Playa</strong>, <strong>Puesto</strong> y <strong>Turno</strong>
            para que coincidan con el nuevo guardavidas seleccionado.
        </div>
    @endif


<form action="{{ isset($licencia) ? route('licencia.update', $licencia->id) :
    route('licencia.store') }}" method="POST" class="bg-white rounded shadow-md" enctype="multipart/form-data">
    @csrf
    @if(isset($licencia))
        @method('PUT')
    @endif

    <div class="container px-2 py-4 mx-auto">

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-12">
            <div class="pb-12  px-4 py-2">
                <div class=" grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-8">
                    <!-- Guardavida -->
                    <div class="sm:col-span-4">
                        <label for="guardavida_id" class="block text-sm font-medium text-gray-900 dark:text-white">Guardavida</label>
                        <div class="mt-2">
                            <select id="guardavida_id" name="guardavida_id"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                                @foreach($guardavidas as $guardavida)
                                <option value="{{ $guardavida->id }}"
                                    @if( isset($licencia) && $licencia->guardavida_id == $guardavida->id )
                                        selected
                                    @endif >
                                        {{ $guardavida->nombre }} {{ $guardavida->apellido }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                     <!-- Tipo Licencia -->
                    <div class="sm:col-span-4">
                        <label for="bandera_id" class="block text-sm font-medium text-gray-900 dark:text-white">Licencia</label>
                        <div class="mt-2">
                            <select name="tipo_licencia" id="tipo_licencia"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 font-medium shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
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
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white">¿Avisó en tiempo?</label>
                        <div class="mt-2 flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="en_tiempo" value="1" required
                                    {{ old('en_tiempo', $licencia->en_tiempo ?? '') == true ? 'checked' : '' }}
                                    class="text-sky-600 border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2">Si</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="radio" name="en_tiempo" value="0"
                                {{ old('en_tiempo', $licencia->en_tiempo ?? '') == false ? 'checked' : '' }}
                                    class="text-sky-600 border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>

                    <!-- Fecha inicio -->
                    <div class="sm:col-span-3">
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-900 dark:text-white">Fecha desde</label>
                        <div class="mt-2">
                            <input id="fecha_inicio" type="date" name="fecha_inicio"
                            class="block w-full max-w-xs rounded-md bg-white px-2 py-1 text-sm text-gray-900
                            shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 dark:bg-gray-700
                            dark:text-white dark:outline-gray-500" value="{{ old('fecha_inicio', $licencia?->fecha_inicio?->format('Y-m-d') ) }}" />
                        </div>
                    </div>

                    <!-- Fecha fin -->
                    <div class="sm:col-span-3">
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-900 dark:text-white">Fecha hasta</label>
                        <div class="mt-2">
                            <input id="fecha_fin" type="date" name="fecha_fin"
                            class="block w-full max-w-xs rounded-md bg-white px-2 py-1 text-sm text-gray-900
                            shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 dark:bg-gray-700
                            dark:text-white dark:outline-gray-500" value="{{ old('fecha_fin', $licencia?->fecha_fin?->format('Y-m-d') ?? '')  }}" />
                        </div>
                    </div>



                    @if (Route::currentRouteName() === 'licencia.edit')
                        @include('ui.licencias.partials.edit_only-fields')
                    @endif

                    <div class="sm:col-span-8">
                        <label for="archivo" class="block text-sm font-medium text-gray-900 dark:text-white">
                            Archivo (foto o PDF de la licencia - opcional)
                        </label>
                        <input type="file" name="archivo" id="archivo"
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-white dark:bg-gray-700 dark:text-white dark:border-gray-500">
                        @if(isset($licencia) && !empty($licencia->archivo))

                            <p class="mt-2 text-sm text-gray-600">
                                Archivo actual:
                                <a href="{{ asset('storage/' . $licencia->archivo) }}" target="_blank" class="text-indigo-600 underline">Ver archivo</a>
                            </p>

                        @endif
                    </div>


                    <!-- Detalles -->
                    <div class="col-span-full">
                        <label for="detalles" class="block text-sm font-medium text-gray-900 dark:text-white">Detalles</label>
                        <div class="mt-2">
                            <textarea id="detalle" name="detalle" rows="4"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                                {{ old('detalle', $licencia->detalle ?? '') }}
                            </textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- Botones -->
        <div class="m-6 mb-6 flex items-center justify-end gap-x-6">
            <button type="button" class="text-sm font-semibold text-gray-900 dark:text-white" onclick="window.history.back()">Cancelar</button>
            <button type="submit"
            class="rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500">
            Guardar
            </button>
        </div>

    </div>

</form>

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
