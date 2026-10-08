@extends('layouts.app')

@section('content')

<section class="sm:px-4 sm:py-10">
    <x-form-tarjeta action="{{ isset($bandera) ? route('bandera.update', $bandera->id) : route('bandera.store') }}" method="POST">
        @csrf
        @if(isset($bandera))
            @method('PUT')
        @endif

        <x-form-encabezado :titulo="isset($bandera) ? 'Editar bandera' : 'Registrar bandera'" icono="flag">
            Indicá la playa, el momento y la bandera que se izó.
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

        <x-form-seccion titulo="Bandera" icono="flag">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">
                    <!-- Playa -->
                    <div class="sm:col-span-3">
                        <label for="playa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Playa</label>
                        <div>
                            <select id="playa_id" name="playa_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                @foreach($playas as $playa)
                                <option value="{{ $playa->id }}"
                                    @if( isset($bandera) && $bandera->playa_id == $playa->id )
                                        selected
                                    @elseif(!isset($bandera) && isset($guardavidaAuth) && $guardavidaAuth->playa_id == $playa->id)
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
                    <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha y Hora</label>
                    <div>
                        <input id="fecha" type="datetime-local" name="fecha"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" value="{{ old('fecha', $bandera->fecha ?? '') }}" />
                    </div>
                </div>

                <!-- Bandera -->
                <div class="sm:col-span-2">
                    <label for="bandera_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bandera</label>
                    <div>
                        <select id="bandera_id" name="bandera_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                        @foreach($banderas as $b)
                                <option value="{{ $b->id }}"
                                    {{ old('bandera_id', $bandera?->bandera_id ?? '') == $b->id ? 'selected' : '' }}>
                                    {{ $b->codigo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </x-form-seccion>

        <x-form-seccion titulo="Detalles" icono="align-left" descripcion="Opcional: cualquier observación sobre las condiciones.">
            <div>
                <label for="detalles" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Detalles</label>
                <div>
                    <textarea id="detalles" name="detalles" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">{{ old('detalles', $bandera->detalles ?? '') }}</textarea>
                </div>
            </div>
        </x-form-seccion>

        <!-- Botones -->
        <div class="flex items-center justify-end gap-x-6">
            <button type="button" class="text-sm text-gray-500 dark:text-gray-400 hover:underline" onclick="window.history.back()">Cancelar</button>
            <button type="submit" class="bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar</button>
        </div>
    </x-form-tarjeta>
</section>

@vite(['resources/js/filterPuestoByPlaya.js'])
@endsection
