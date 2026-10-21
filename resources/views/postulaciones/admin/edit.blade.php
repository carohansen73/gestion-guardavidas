@extends('layouts.app')
@section('content')

@php
    $input = 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $tieneObraSocial = old('tiene_obra_social', $perfil->tieneObraSocial() ? 1 : 0);
@endphp

<section class="sm:px-4 sm:py-10">
    <x-volver :href="route('postulaciones.show', $postulacion)" class="mb-4" />

    <x-form-tarjeta method="POST" action="{{ route('postulaciones.update', $postulacion) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <x-form-encabezado icono="clipboard-check" etiqueta="{{ $postulacion->temporada->nombre }}"
            :titulo="'Datos de '.$postulacion->user->lastname.', '.$postulacion->user->name">
            Completá lo que quieras de esta inscripción. Nada es obligatorio: sirve para cargar a mano los datos y documentos de alguien que no pasó por el formulario.
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

        <x-form-seccion titulo="Información personal" icono="user" descripcion="Nombre, DNI y email se editan desde el usuario.">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="fecha_nacimiento" class="{{ $label }}">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="{{ $input }}"
                        value="{{ old('fecha_nacimiento', $perfil->fecha_nacimiento?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label for="genero" class="{{ $label }}">Género</label>
                    <select name="genero" id="genero" class="{{ $input }}">
                        <option value="">Sin dato</option>
                        @foreach ($generos as $genero)
                            <option value="{{ $genero }}" @selected(old('genero', $perfil->genero) === $genero)>{{ $genero }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="telefono" class="{{ $label }}">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="{{ $input }}" value="{{ old('telefono', $perfil->telefono) }}">
                </div>
                <div>
                    <label for="grupo_sanguineo" class="{{ $label }}">Grupo sanguíneo</label>
                    <select name="grupo_sanguineo" id="grupo_sanguineo" class="{{ $input }}">
                        <option value="">Sin dato</option>
                        @foreach ($gruposSanguineos as $grupo)
                            <option value="{{ $grupo }}" @selected(old('grupo_sanguineo', $perfil->grupo_sanguineo) === $grupo)>{{ $grupo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="numero_libreta" class="{{ $label }}">N° de libreta de guardavidas</label>
                    <input type="text" name="numero_libreta" id="numero_libreta" class="{{ $input }}" value="{{ old('numero_libreta', $perfil->numero_libreta) }}">
                </div>
            </div>
        </x-form-seccion>

        <x-form-seccion titulo="Domicilio" icono="map-pin">
            <div class="grid grid-cols-1 sm:grid-cols-6 gap-4">
                <div class="sm:col-span-3">
                    <label for="direccion" class="{{ $label }}">Calle</label>
                    <input type="text" name="direccion" id="direccion" class="{{ $input }}" value="{{ old('direccion', $perfil->direccion) }}">
                </div>
                <div class="sm:col-span-1">
                    <label for="numero" class="{{ $label }}">Número</label>
                    <input type="text" name="numero" id="numero" class="{{ $input }}" value="{{ old('numero', $perfil->numero) }}">
                </div>
                <div class="sm:col-span-2">
                    <label for="piso_dpto" class="{{ $label }}">Piso / Depto</label>
                    <input type="text" name="piso_dpto" id="piso_dpto" class="{{ $input }}" value="{{ old('piso_dpto', $perfil->piso_dpto) }}">
                </div>
            </div>
        </x-form-seccion>

        <x-form-seccion titulo="Indumentaria" icono="shirt">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach (['talle_remera' => 'Remera', 'talle_pantalon' => 'Pantalón', 'talle_campera' => 'Campera', 'talle_traje_bano' => 'Traje de baño'] as $campo => $titulo)
                    <div>
                        <label for="{{ $campo }}" class="{{ $label }}">{{ $titulo }}</label>
                        <input type="text" name="{{ $campo }}" id="{{ $campo }}" class="{{ $input }}" value="{{ old($campo, $perfil->$campo) }}">
                    </div>
                @endforeach
            </div>
        </x-form-seccion>

        <x-form-seccion titulo="Cobertura médica" icono="heart-pulse">
            <div x-data="{ conObraSocial: {{ $tieneObraSocial ? 'true' : 'false' }} }">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="hidden" name="tiene_obra_social" value="0">
                    <input type="checkbox" name="tiene_obra_social" value="1" x-model="conObraSocial" class="rounded border-gray-300 dark:border-gray-600">
                    Tiene obra social / prepaga
                </label>
                <div x-show="conObraSocial" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label for="obra_social_nombre" class="{{ $label }}">Obra social / prepaga</label>
                        <input type="text" name="obra_social_nombre" id="obra_social_nombre" class="{{ $input }}" value="{{ old('obra_social_nombre', $perfil->obra_social_nombre) }}">
                    </div>
                    <div>
                        <label for="obra_social_numero_afiliado" class="{{ $label }}">N° de afiliado</label>
                        <input type="text" name="obra_social_numero_afiliado" id="obra_social_numero_afiliado" class="{{ $input }}" value="{{ old('obra_social_numero_afiliado', $perfil->obra_social_numero_afiliado) }}">
                    </div>
                </div>
            </div>
        </x-form-seccion>

        <x-form-seccion titulo="Inscripción" icono="calendar" descripcion="Disponibilidad y playas de preferencia.">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="disponible_desde" class="{{ $label }}">Disponible desde</label>
                    <input type="date" name="disponible_desde" id="disponible_desde" class="{{ $input }}"
                        value="{{ old('disponible_desde', $postulacion->disponible_desde?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label for="disponible_hasta" class="{{ $label }}">Disponible hasta</label>
                    <input type="date" name="disponible_hasta" id="disponible_hasta" class="{{ $input }}"
                        value="{{ old('disponible_hasta', $postulacion->disponible_hasta?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label for="playa_1" class="{{ $label }}">Playa preferida</label>
                    <select name="playa_1" id="playa_1" class="{{ $input }}">
                        <option value="">Sin preferencia</option>
                        @foreach ($playas as $playa)
                            <option value="{{ $playa->id }}" @selected((int) old('playa_1', $playaIds[1] ?? 0) === $playa->id)>{{ $playa->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="playa_2" class="{{ $label }}">Segunda opción</label>
                    <select name="playa_2" id="playa_2" class="{{ $input }}">
                        <option value="">Sin segunda opción</option>
                        @foreach ($playas as $playa)
                            <option value="{{ $playa->id }}" @selected((int) old('playa_2', $playaIds[2] ?? 0) === $playa->id)>{{ $playa->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-form-seccion>

        <x-form-seccion titulo="Documentos" icono="upload" :descripcion="'Subí solo los que tengas. Máximo '.($maxKb / 1024).' MB cada uno; subir uno nuevo reemplaza al actual.'">
            <div class="space-y-4">
                @foreach ($tipos as $tipo => $config)
                    @php $doc = $postulacion->documento($tipo); @endphp
                    <div>
                        <label for="doc_{{ $tipo }}" class="{{ $label }}">{{ $config['label'] }}
                            <span class="text-gray-400">({{ strtoupper(implode(', ', $config['mimes'])) }})</span>
                        </label>
                        @if ($doc)
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                Cargado:
                                <a href="{{ route('postulaciones.documento', [$postulacion, $tipo]) }}" target="_blank"
                                    class="text-sky-600 dark:text-sky-400 hover:underline">{{ $doc->nombre_original }}</a>
                            </p>
                        @endif
                        <input type="file" name="doc_{{ $tipo }}" id="doc_{{ $tipo }}"
                            accept="{{ collect($config['mimes'])->map(fn ($m) => '.'.$m)->implode(',') }}"
                            class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-200">
                    </div>
                @endforeach
            </div>
        </x-form-seccion>

        <div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-end sm:gap-x-6">
            <a href="{{ route('postulaciones.show', $postulacion) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">Cancelar</a>
            <button type="submit" class="w-full sm:w-auto bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar</button>
        </div>
    </x-form-tarjeta>
</section>
@endsection
