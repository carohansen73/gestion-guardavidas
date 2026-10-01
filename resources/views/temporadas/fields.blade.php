@extends('layouts.app')
@section('content')

<section class="text-gray-600 dark:text-gray-100 body-font sm:px-4 sm:py-10">
    <h2 class="mb-3 text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl section-title">
        {{ isset($temporada) ? 'Editar temporada' : 'Nueva temporada' }}
    </h2>

    <div class="mb-4 rounded-md bg-blue-50 dark:bg-blue-900/30 p-3 text-sm text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-800">
        La ventana de <strong>postulación</strong> es cuando alguien puede completar el formulario de inscripción
        (normalmente meses antes). La ventana <strong>operativa</strong> es la temporada en sí — la que se usa para
        cargar intervenciones, banderas, etc., y fuera de la cual guardavida/encargado no pueden crear/editar nada.
    </div>

    <form action="{{ isset($temporada) ? route('temporada.update', $temporada) : route('temporada.store') }}"
        method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded shadow-md">
        @csrf
        @if(isset($temporada))
            @method('PUT')
        @endif

        <div class="container px-4 py-6 mx-auto max-w-2xl">

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                <input type="text" name="nombre" id="nombre"
                    value="{{ old('nombre', $temporada->nombre ?? '') }}"
                    placeholder="Ej. Temporada 2026-27"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
            </div>

            <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="fecha_inicio_postulacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Postulación desde</label>
                    <input type="date" name="fecha_inicio_postulacion" id="fecha_inicio_postulacion"
                        value="{{ old('fecha_inicio_postulacion', isset($temporada) ? $temporada->fecha_inicio_postulacion->format('Y-m-d') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>
                <div>
                    <label for="fecha_fin_postulacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Postulación hasta</label>
                    <input type="date" name="fecha_fin_postulacion" id="fecha_fin_postulacion"
                        value="{{ old('fecha_fin_postulacion', isset($temporada) ? $temporada->fecha_fin_postulacion->format('Y-m-d') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Temporada desde</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio"
                        value="{{ old('fecha_inicio', isset($temporada) ? $temporada->fecha_inicio->format('Y-m-d') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Temporada hasta</label>
                    <input type="date" name="fecha_fin" id="fecha_fin"
                        value="{{ old('fecha_fin', isset($temporada) ? $temporada->fecha_fin->format('Y-m-d') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>
            </div>

            {{-- Modelo de declaración jurada: lo descargan los postulantes en el paso 3
                 del formulario. Cada temporada tiene el suyo (cambia de un año al otro). --}}
            <div class="mb-6">
                <label for="modelo_declaracion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Modelo de declaración jurada <span class="text-gray-400">(PDF, hasta 5 MB)</span>
                </label>

                @if (isset($temporada) && $temporada->declaracion_jurada_modelo)
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        Modelo cargado:
                        <a href="{{ route('temporada.modelo-declaracion', $temporada) }}" target="_blank"
                            class="text-sky-600 dark:text-sky-400 hover:underline">ver / descargar</a>.
                        Si subís otro archivo, reemplaza al actual.
                    </p>
                @else
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Todavía no hay un modelo cargado para esta temporada.</p>
                @endif

                <input type="file" name="modelo_declaracion" id="modelo_declaracion" accept=".pdf,application/pdf"
                    class="mt-2 block w-full text-sm text-gray-700 dark:text-gray-200">

                @if (isset($temporada) && $temporada->declaracion_jurada_modelo)
                    <label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <input type="checkbox" name="quitar_modelo" value="1" class="rounded border-gray-300">
                        Quitar el modelo actual (los postulantes dejan de verlo)
                    </label>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-sky-500 dark:bg-sky-700 hover:bg-sky-400 dark:hover:bg-sky-600 text-white px-4 py-2 rounded-full shadow-md">
                    Guardar
                </button>
                <a href="{{ route('temporada.index') }}" class="text-gray-500 dark:text-gray-400 hover:underline">Cancelar</a>
            </div>
        </div>
    </form>
</section>
@endsection
