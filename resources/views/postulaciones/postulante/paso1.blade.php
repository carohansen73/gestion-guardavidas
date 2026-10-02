@extends('layouts.postulante')
@section('content')

@include('postulaciones.postulante._pasos', ['paso' => 1])

@php
    $input = 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $tieneObraSocial = old('tiene_obra_social', $perfil->tieneObraSocial() ? 1 : 0);
@endphp

<form method="POST" action="{{ route('postulacion.guardar', 1) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 space-y-10">
    @csrf



     {{-- <div className="mb-6 flex items-start gap-3 border-b border-border pb-5">
          <span className="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-primary/15 text-primary">
            {(() => {
              const Icon = STEPS[step].icon;
              return <Icon className="h-5 w-5" />;
            })()}
          </span>
          <div>
            <p className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Paso {step + 1} de 4</p>
            <h3 className="font-display text-xl font-bold">{STEPS[step].title}</h3>
          </div>
        </div>
 --}}





    <div class="mb-6 border-b border-border pb-3">
        <div class="mb-2 flex items-start gap-3  ">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-sky-600/15 text-sky-600">
                <svg class="lucide lucide-user-round h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-tsd-source="/src/routes/postulacion.tsx:188:22"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path>
                </svg>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Paso 1 de 4</p>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Datos personales</h2>
            </div>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Estos datos se guardan en tu perfil y se van a precargar la próxima vez que te inscribas.
        </p>
    </div>



    {{-- Cada parte del formulario (encabezado + campos) va en su propio <section>:
         adentro queda poco espacio (space-y-3) y entre partes mucho más (space-y-10 del <form>). --}}

    {{-- Nombre, apellido, DNI y email salen de la cuenta: no se editan acá. --}}
    <section class="space-y-3">
    <div class="mb-2 flex items-start gap-3  ">
        <svg class="lucide lucide-shield-check h-5 w-5 shrink-0 text-sky-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-tsd-source="/src/routes/postulacion.tsx:432:66"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide dark:text-white">Datos de tu cuenta</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Estos datos deberás modificarlos desde el perfil</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 rounded-md bg-gray-50 dark:bg-gray-700 p-3 text-sm text-gray-700 dark:text-gray-200">
        <div><span class="text-gray-500 dark:text-gray-400">Nombre:</span> {{ Auth::user()->name }} {{ Auth::user()->lastname }}</div>
        <div><span class="text-gray-500 dark:text-gray-400">DNI:</span> {{ Auth::user()->dni }}</div>
        <div class="sm:col-span-2"><span class="text-gray-500 dark:text-gray-400">Email:</span> {{ Auth::user()->email }}</div>
    </div>
    </section>



    <section class="space-y-3">
    <div class="mb-2 flex items-start gap-3  ">
        <svg class="lucide lucide-user-round mt-0.5 h-5 w-5 shrink-0 text-sky-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-tsd-source="/src/routes/postulacion.tsx:434:7"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide dark:text-white">Información personal</p>
        </div>
    </div>

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
    </section>


    <section class="space-y-3">
    <div class="mb-2 flex items-start gap-2.5  ">
        <svg class="lucide lucide-map-pin mt-0.5 h-5 w-5 shrink-0 text-sky-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"  aria-hidden="true" data-tsd-source="/src/routes/postulacion.tsx:434:7"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
        <p class="text-xs font-semibold uppercase dark:text-white">Domicilio</p>
    </div>

    <div>
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
    </div>
    </section>



    <section class="space-y-3">
    <div class="mb-2 flex items-start gap-3  ">
        <svg class="lucide lucide-shirt mt-0.5 h-5 w-5 shrink-0 text-sky-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-tsd-source="/src/routes/postulacion.tsx:434:7"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"></path></svg>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide dark:text-white">Indumentaria</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Elegí el <strong> talle </strong> que usas habitualmente</p>
        </div>
    </div>
    <div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach (['talle_remera' => 'Remera', 'talle_pantalon' => 'Pantalón', 'talle_campera' => 'Campera', 'talle_traje_bano' => 'Traje de baño'] as $campo => $titulo)
                <div>
                    <label for="{{ $campo }}" class="{{ $label }}">{{ $titulo }}</label>
                    <input type="text" name="{{ $campo }}" id="{{ $campo }}" class="{{ $input }}" value="{{ old($campo, $perfil->$campo) }}">
                </div>
            @endforeach
        </div>
    </div>
    </section>


    <section class="space-y-3">
    <div class="mb-2 flex items-start gap-3  ">
        <svg class="lucide lucide-heart-pulse mt-0.5 h-5 w-5 shrink-0 text-sky-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-tsd-source="/src/routes/postulacion.tsx:434:7"><path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"></path><path d="M3.22 13H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"></path></svg>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide dark:text-white">Cobertura médica</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Estos datos deberás modificarlos desde el perfil</p>
        </div>
    </div>


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
    </section>

    <div class="flex justify-between items-center">
        <a href="{{ route('postulacion.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">Volver</a>
        <button type="submit" class="bg-sky-500 hover:bg-sky-400 text-white rounded-full px-3 sm:px-5 md:px-5 lg:px-5 py-2 shadow">Guardar y continuar</button>
    </div>
</form>

@endsection
