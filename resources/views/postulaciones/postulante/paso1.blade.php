@extends('layouts.postulante')
@section('content')

@include('postulaciones.postulante._pasos', ['paso' => 1])

@php
    $input = 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $tieneObraSocial = old('tiene_obra_social', $perfil->tieneObraSocial() ? 1 : 0);
@endphp

<form method="POST" action="{{ route('postulacion.guardar', 1) }}" class="glass rounded-xl px-6 pb-6 space-y-10">
    @csrf

    <x-postulacion-encabezado :paso="1" titulo="Datos personales">
        Estos datos se guardan en tu perfil y se van a precargar la próxima vez que te inscribas.
    </x-postulacion-encabezado>

    {{-- Cada parte del formulario va en su propia sección (x-postulacion-seccion): poco espacio
         adentro y mucho más entre partes (space-y-10 del <form>). --}}

    {{-- Nombre, apellido, DNI y email salen de la cuenta: no se editan acá. --}}
    <x-postulacion-seccion titulo="Datos de tu cuenta" icono="shield-check" descripcion="Estos datos deberás modificarlos desde el perfil">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 rounded-md bg-gray-50 dark:bg-gray-700 p-3 text-sm text-gray-700 dark:text-gray-200">
            <div><span class="text-gray-500 dark:text-gray-400">Nombre:</span> {{ Auth::user()->name }} {{ Auth::user()->lastname }}</div>
            <div><span class="text-gray-500 dark:text-gray-400">DNI:</span> {{ Auth::user()->dni }}</div>
            <div class="sm:col-span-2"><span class="text-gray-500 dark:text-gray-400">Email:</span> {{ Auth::user()->email }}</div>
        </div>
    </x-postulacion-seccion>

    <x-postulacion-seccion titulo="Información personal" icono="user">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="fecha_nacimiento" class="{{ $label }}">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="{{ $input }}"
                    value="{{ old('fecha_nacimiento', $perfil->fecha_nacimiento?->format('Y-m-d')) }}">
            </div>
            <div>
                <label for="genero" class="{{ $label }}">Género</label>
                <select name="genero" id="genero" class="{{ $input }}">
                    <option value="">Seleccionar…</option>
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
                    <option value="">Seleccionar…</option>
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
    </x-postulacion-seccion>

    <x-postulacion-seccion titulo="Domicilio" icono="map-pin">
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
                <label for="piso_dpto" class="{{ $label }}">Piso / Depto <span class="text-gray-400">(opcional)</span></label>
                <input type="text" name="piso_dpto" id="piso_dpto" class="{{ $input }}" value="{{ old('piso_dpto', $perfil->piso_dpto) }}">
            </div>
        </div>
    </x-postulacion-seccion>

    <x-postulacion-seccion titulo="Indumentaria" icono="shirt">
        <x-slot:ayuda>Elegí el <strong> talle </strong> que usas habitualmente</x-slot:ayuda>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach (['talle_remera' => 'Remera', 'talle_pantalon' => 'Pantalón', 'talle_campera' => 'Campera', 'talle_traje_bano' => 'Traje de baño'] as $campo => $titulo)
                <div>
                    <label for="{{ $campo }}" class="{{ $label }}">{{ $titulo }}</label>
                    <input type="text" name="{{ $campo }}" id="{{ $campo }}" class="{{ $input }}" value="{{ old($campo, $perfil->$campo) }}">
                </div>
            @endforeach
        </div>
    </x-postulacion-seccion>

    <x-postulacion-seccion titulo="Cobertura médica" icono="heart-pulse" descripcion="Estos datos deberás modificarlos desde el perfil">
        <div x-data="{ conObraSocial: {{ $tieneObraSocial ? 'true' : 'false' }} }">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="hidden" name="tiene_obra_social" value="0">
                <input type="checkbox" name="tiene_obra_social" value="1" x-model="conObraSocial" class="rounded border-gray-300">
                Tengo obra social / prepaga
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
    </x-postulacion-seccion>

    <div class="flex justify-between items-center">
        <a href="{{ route('postulacion.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">Volver</a>
        <button type="submit" class="bg-sky-500 hover:bg-sky-400 text-white rounded-full px-3 sm:px-5 md:px-5 lg:px-5 py-2 shadow">Guardar y continuar</button>
    </div>
</form>

@endsection
