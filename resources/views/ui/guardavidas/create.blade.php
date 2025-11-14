@extends('layouts.app')

@section('content')


<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>


<section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10">
    <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">Registrar Guardavida</h2>

        {{-- <form action="{{ route('bandera.store') }}" method="POST" class="bg-white rounded shadow-md ">
            @csrf --}}


<form action="{{ isset($guardavida) ? route('guardavida.update', $guardavida->id) :
    route('guardavida.store') }}" method="POST" class="bg-white rounded shadow-md ">
    @csrf
    @if(isset($guardavida))
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
                             <h3 class="sm:col-span-6 text-gray-900 dark:text-white text-lg font-medium ">
                                Registro de usuario
                            </h3>

                             <!-- Nombre -->
                            <div class="sm:col-span-3">
                                <label for="nombre" class="block text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                                <div class="mt-2">
                                    <input id="nombre" type="text" name="nombre" placeholder="Nombre"
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                                    value="{{ old('nombre', $guardavida->nombre ?? '') }}" required/>
                                    @error('nombre')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Apellido -->
                            <div class="sm:col-span-3">
                                <label for="apellido" class="block text-sm font-medium text-gray-900 dark:text-white">Apellido</label>
                                <div class="mt-2">
                                    <input id="apellido" type="text" name="apellido" placeholder="Apellido"
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                                    value="{{ old('apellido', $guardavida->apellido ?? '') }}" required/>
                                    @error('apellido')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                <div class="mt-2">
                                    <input id="email" type="email" name="email" placeholder="usuario@gmail.com"
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                                    value="{{ old('email', $guardavida->user->email ?? '') }}" required/>
                                    @error('email')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- CONTRASEÑA 123456789? Y QUE LUEGO LO CAMBIEN ?! obligatorio q lo cambien!
                            <div>
                                <label>Contraseña</label>
                                <input type="password" name="password" required>
                            </div> --}}
                            <div class="sm:col-span-3">
                                <label for="Rol" class="block text-sm font-medium text-gray-900 dark:text-white">Rol</label>
                                     <div class="mt-2 relative overflow-hidden">
                                        <select name="rol" id="rol-select"
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                                        required>
                                            <option class="w-auto" value="">Seleccione</option>
                                            <option value="guardavida" {{ old('rol', $rol ?? '' ) == 'guardavida' ? 'selected' : '' }}>Guardavida</option>
                                            <option value="encargado" {{ old('rol', $rol ?? '' ) == 'encargado' ? 'selected' : '' }}>Encargado</option>
                                            <option value="admin" {{ old('rol', $rol ?? '' ) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                        </select>
                                </div>
                            </div>
                        </div>
{{-- A PARTIR DE ACA, SOLO VISIBLE SI VA A AGREGAR UN GUARDAVIDA  --}}
                        <div id="guardavida-fields" style="display: none;" class=" grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-8 py-4">

                            <h3 class="sm:col-span-8 border-t border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-lg font-medium pt-4">
                                Información personal
                            </h3>

                            <!-- DNI -->
                            <div class="sm:col-span-4">
                                <label for="dni" class="block text-sm font-medium text-gray-900 dark:text-white">DNI</label>
                                <div class="mt-2">
                                    <input id="dni" type="number" name="dni" placeholder="Ej: 11111111"
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                                    value="{{ old('dni', $guardavida->dni ?? '') }}" />
                                    @error('dni')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- telefono -->
                            <div class="sm:col-span-4">
                                <label for="telefono" class="block text-sm font-medium text-gray-900 dark:text-white">Telefono</label>
                                <div class="mt-2">
                                    <input id="telefono" type="number" name="telefono" placeholder="2983111111"
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                                    value="{{ old('telefono', $guardavida->telefono ?? '') }}" />
                                    @error('telefono')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Direccion -->
                            <div class="sm:col-span-4">
                                <label for="direccion" class="block text-sm font-medium text-gray-900 dark:text-white">Dirección</label>
                                <div class="mt-2">
                                    <input id="direccion" type="text" name="direccion" placeholder="Ej. calle 11"
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                                    value="{{ old('direccion', $guardavida->direccion ?? '') }}" />
                                    @error('direccion')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>


                            <!-- numero -->
                            <div class="sm:col-span-2">
                                <label for="dni" class="block text-sm font-medium text-gray-900 dark:text-white">Número</label>
                                <div class="mt-2">
                                    <input id="numero" type="number" name="numero" placeholder="Ej: 1250"
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                                    value="{{ old('numero', $guardavida->numero ?? '') }}" />
                                    @error('numero')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- piso_dpto -->
                            <div class="sm:col-span-2">
                                <label for="piso_dpto" class="block text-sm font-medium text-gray-900 dark:text-white">Piso - Dpto</label>
                                <div class="mt-2">
                                    <input id="piso_dpto" type="text" name="piso_dpto" placeholder="Ej. 2-A"
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                                    value="{{ old('piso_dpto', $guardavida->piso_dpto ?? '') }}" />
                                    @error('piso_dpto')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <h3 class="sm:col-span-8 border-t border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-lg font-medium pt-4">
                                Información profesional
                            </h3>

                            <!-- Playa -->
                            <div class="sm:col-span-4">
                                <label for="playa_id" class="block text-sm font-medium text-gray-900 dark:text-white">Playa</label>
                                <div class="mt-2">
                                    <select id="playa_id" name="playa_id"
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                                        @foreach($playas as $playa)
                                        <option value="{{ $playa->id }}"
                                            @if( isset($guardavida) && $guardavida->playa_id == $playa->id )
                                                selected
                                            @elseif(!isset($guardavida) && isset($guardavidaAuth) && $guardavidaAuth->playa_id == $playa->id)
                                                selected
                                            @endif >
                                            {{ $playa->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Puesto
                             TODO: Necesito saber el puesto o el estado de la bandera es el mismo en todos los puestos?  -->
                        <div class="sm:col-span-4">
                            <label for="puesto_id" class="block text-sm font-medium text-gray-900 dark:text-white">Puesto</label>
                            <div class="mt-2">
                                <select id="puesto_id" name="puesto_id"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                                @foreach($puestos as $puesto)
                                    <option value="{{ $puesto->id }}" data-playa="{{ $puesto->playa_id }}"
                                        @if( isset($guardavida) && $guardavida->puesto_id == $puesto->id )
                                            selected
                                        @elseif(!isset($guardavida) && isset($guardavidaAuth) && $guardavidaAuth->puesto_id == $puesto->id)
                                            selected
                                        @endif
                                    >
                                        {{ $puesto->nombre }}
                                    </option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                          <!-- Función -->
                            <div class="sm:col-span-4">
                                <label for="funcion"
                                class="block text-sm font-medium text-gray-900 dark:text-white">Función</label>
                               <div class="mt-2 relative overflow-hidden">
                                <select id="funcion" name="funcion" class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                                    <option value="Guardavida">Guardavida</option>
                                    <option value="Timonel">Timonel</option>
                                    <option value="Encargado">Encargado</option>
                                    <option value="Jefe_de_playa">Jefe de playa</option>
                                </select>
                                </div>
                            </div>

                            <!-- Turno -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Turno</label>
                                <div class="mt-2 flex gap-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="turno" value="M"
                                            {{ old('turno', $guardavida->turno ?? '') == 'M' ? 'checked' : '' }}
                                            class="text-sky-600 border-gray-300 focus:ring-indigo-500">
                                        <span class="ml-2">Mañana</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="turno" value="T"
                                            {{ old('turno', $guardavida->turno ?? '') == 'T' ? 'checked' : '' }}
                                            class="text-sky-600 border-gray-300 focus:ring-indigo-500">
                                        <span class="ml-2">Tarde</span>
                                    </label>
                                </div>
                            </div>

                    </div> {{-- HASTA  ACA, SOLO VISIBLE SI VA A AGREGAR UN GUARDAVIDA  --}}


                </div>
            </div>

            <!-- Botones -->
            <div class="m-6 mb-6 mt-8 flex flex-col-reverse gap-4 md:flex-row md:items-center md:justify-end md:gap-x-6">
                <!-- Cancelar -->
                <button type="button"
                    class="text-sm font-semibold text-gray-900 dark:text-white"
                    onclick="window.history.back()">
                    Cancelar
                </button>

                <!-- Guardar -->
                <button type="submit"
                    class="w-full md:w-auto rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500">
                    Guardar
                </button>
            </div>

        </div>

    </form>


    <div class="py-4 w-full sm:hidden">
        <a href="{{ route('guardavida.index') }}" class="bg-sky-600 rounded flex py-4 px-4 h-full justify-between">
            <div class="flex">
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="text-gray-100 w-6 h-6 flex-shrink-0 mr-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <span class="title-font font-medium text-gray-100">Ver guardavidas</span>
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

 {{-- pasar a .js --}}

 <script>
    //si es guardavida/encargado inserto en user y en guardavida, si es admin solo user
    document.addEventListener('DOMContentLoaded', function() {
        const rolSelect = document.getElementById('rol-select');
        const guardavidaFields = document.getElementById('guardavida-fields');

        function toggleGuardavidaFields(){
            const isGuardavida = rolSelect.value === 'guardavida' || rolSelect.value === 'encargado';
            guardavidaFields.style.display = isGuardavida ? 'grid' : 'none';
        }

        rolSelect.addEventListener('change', toggleGuardavidaFields);

        toggleGuardavidaFields();
    });

</script>

@vite(['resources/js/filterPuestoByPlaya.js'])
@endsection



