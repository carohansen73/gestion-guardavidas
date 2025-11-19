@extends('layouts.app')
@section('content')

<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>

<section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10">
    <h2 class="mb-3 text-gray-700 text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl section-title">
        @if(isset($novedadDeMaterial))
            Editar novedad de materiales
        @else
            Registrar Novedad de materiales
        @endif
    </h2>

        {{-- <form action="{{ route('bandera.store') }}" method="POST" class="bg-white rounded shadow-md ">
            @csrf --}}

<form action="{{ isset($novedadDeMaterial) ? route('novedad-de-material.update', $novedadDeMaterial->id) :
    route('novedad-de-material.store') }}" method="POST" class="bg-white rounded shadow-md ">
    @csrf
    @if(isset($novedadDeMaterial))
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

                <div class=" grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <!-- Playa -->
                    <div class="sm:col-span-3">
                        <label for="playa_id" class="block text-sm font-medium text-gray-900 dark:text-white">Playa</label>
                        <div class="mt-2">
                            <select id="playa_id" name="playa_id"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                                @foreach($playas as $playa)
                                <option value="{{ $playa->id }}"
                                    @if( isset($novedadDeMaterial) && $novedadDeMaterial->playa_id == $playa->id )
                                        selected
                                    @elseif(!isset($novedadDeMaterial) && isset($guardavidaAuth) && $guardavidaAuth->playa_id == $playa->id)
                                        selected
                                    @endif >
                                    {{ $playa->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Fecha y hora -->
                <div class="sm:col-span-2">
                    <label for="fecha" class="block text-sm font-medium text-gray-900 dark:text-white">Fecha</label>
                    <div class="mt-2">
                        <input id="fecha" type="datetime-local" name="fecha"
                        class="block w-full max-w-xs rounded-md bg-white px-2 py-1 text-sm text-gray-900
                        shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 dark:bg-gray-700
                        dark:text-white dark:outline-gray-500" value="{{ old('fecha', $novedadDeMaterial->fecha ?? '') }}" />
                    </div>
                </div>

                <!-- Material -->
                <div class="sm:col-span-2">
                    <label for="bandera_id" class="block text-sm font-medium text-gray-900 dark:text-white">Material</label>
                    <div class="mt-2">
                        <select name="material_id" id="material" onchange="toggleOtro(this.value)"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 font-medium shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                        <option value="">Seleccionar material</option>
                        @foreach($materiales as $material)
                                <option value="{{ $material->id }}"
                                    {{ old('material_id', $novedadDeMaterial?->material_id ?? '') == $material->id ? 'selected' : '' }}>
                                    {{ $material->nombre }} {{ $material->detalle }}
                                </option>
                        @endforeach
                            <option value="otro">Otro...</option>
                        </select>
                        <div id="otro-material" class="mt-2 hidden">
                            <input type="text" name="nuevo_material_nombre" class="border rounded px-2 py-1 w-full" placeholder="Nombre nuevo material">
                            <input type="text" name="nuevo_material_detalle" class="border rounded px-2 py-1 w-full" placeholder="Detalle nuevo material(opcional)">
                        </div>
                        <script>
                            function toggleOtro(value) {
                                document.getElementById('otro-material').classList.toggle('hidden', value !== 'otro');
                            }
                        </script>
                    </div>
                </div>

                <!-- Tipo Novedad -->
                <div class="sm:col-span-2">
                    <label for="bandera_id" class="block text-sm font-medium text-gray-900 dark:text-white">Tipo de novedad</label>
                    <div class="mt-2">
                        <select name="tipo_novedad" id="tipo_novedad"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 font-medium shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                            <option value="">Seleccione un tipo</option>
                            @foreach($tipoNovedad  as $tipo)
                                    <option value="{{ $tipo }}"
                                        {{ old('tipo_novedad', $novedadDeMaterial?->tipo_novedad->value ?? '') == $tipo ? 'selected' : '' }}>
                                        {{ $tipo }}
                                    </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Detalles -->
                <div class="col-span-full">
                    <label for="detalles" class="block text-sm font-medium text-gray-900 dark:text-white">Detalles</label>
                    <div class="mt-2">
                        <textarea id="detalles" name="detalles" rows="4"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                            {{ old('detalles', $novedadDeMaterial->detalles ?? '') }}
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

    {{-- <div class="py-4  w-full">
        <a href="{{ route('intervencion.index') }}" class="bg-sky-600 rounded flex py-4 px-4 h-full justify-between">
            <div class="flex">
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>

                <span class="title-font font-medium text-gray-100">Ver intervenciones</span>
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
    </div> --}}


 </section>

 @vite(['resources/js/filterPuestoByPlaya.js'])
@endsection





















{{--
<select name="material_id" id="material" onchange="toggleOtro(this.value)">
    <option value="">Seleccionar material</option>
    @foreach($materiales as $material)
        <option value="{{ $material->id }}">{{ $material->nombre }}</option>
    @endforeach
    <option value="otro">Otro...</option>
</select>

<div id="otro-material" class="mt-2 hidden">
    <input type="text" name="nuevo_material" class="border rounded px-2 py-1 w-full" placeholder="Especificar nuevo material">
</div>

<script>
    function toggleOtro(value) {
        document.getElementById('otro-material').classList.toggle('hidden', value !== 'otro');
    }
</script> --}}
