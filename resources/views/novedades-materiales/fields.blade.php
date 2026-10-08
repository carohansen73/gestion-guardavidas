@extends('layouts.app')
@section('content')

<section class="sm:px-4 sm:py-10">
    <x-form-tarjeta action="{{ isset($novedadDeMaterial) ? route('novedad-de-material.update', $novedadDeMaterial->id) : route('novedad-de-material.store') }}" method="POST">
        @csrf
        @if(isset($novedadDeMaterial))
            @method('PUT')
        @endif

        <x-form-encabezado :titulo="isset($novedadDeMaterial) ? 'Editar novedad de materiales' : 'Registrar novedad de materiales'" icono="package">
            Registrá una pérdida, rotura u otra novedad del material de la playa.
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

        <x-form-seccion titulo="Lugar y momento" icono="map-pin">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">
                    <!-- Playa -->
                    <div class="sm:col-span-3">
                        <label for="playa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Playa</label>
                        <div>
                            <select id="playa_id" name="playa_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
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
                <div class="sm:col-span-3">
                    <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha</label>
                    <div>
                        <input id="fecha" type="datetime-local" name="fecha"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" value="{{ old('fecha', $novedadDeMaterial->fecha ?? '') }}" />
                    </div>
                </div>
            </div>
        </x-form-seccion>

        <x-form-seccion titulo="Material" icono="package">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">
                <!-- Material -->
                <div class="sm:col-span-3">
                    <label for="bandera_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Material</label>
                    <div>
                        <select name="material_id" id="material" onchange="toggleOtro(this.value)"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
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
                            <input type="text" name="nuevo_material_nombre" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" placeholder="Nombre nuevo material">
                            <input type="text" name="nuevo_material_detalle" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" placeholder="Detalle nuevo material(opcional)">
                        </div>
                        <script>
                            function toggleOtro(value) {
                                document.getElementById('otro-material').classList.toggle('hidden', value !== 'otro');
                            }
                        </script>
                    </div>
                </div>

                <!-- Tipo Novedad -->
                <div class="sm:col-span-3">
                    <label for="bandera_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de novedad</label>
                    <div>
                        <select name="tipo_novedad" id="tipo_novedad"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
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
            </div>
        </x-form-seccion>

        <x-form-seccion titulo="Detalles" icono="align-left">
            <div>
                <label for="detalles" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Detalles</label>
                <div>
                    <textarea id="detalles" name="detalles" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">{{ old('detalles', $novedadDeMaterial->detalles ?? '') }}</textarea>
                </div>
            </div>
        </x-form-seccion>

        <!-- Botones -->
        <div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-end sm:gap-x-6">
            <button type="button" class="text-sm text-gray-500 dark:text-gray-400 hover:underline" onclick="window.history.back()">Cancelar</button>
            <button type="submit" class="w-full sm:w-auto bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar</button>
        </div>
    </x-form-tarjeta>
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
    <input type="text" name="nuevo_material" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" placeholder="Especificar nuevo material">
</div>

<script>
    function toggleOtro(value) {
        document.getElementById('otro-material').classList.toggle('hidden', value !== 'otro');
    }
</script> --}}
