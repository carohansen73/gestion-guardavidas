@extends('layouts.app')

@section('content')

<section class="sm:px-4 sm:py-10">
    <x-form-tarjeta action="{{ isset($guardavida) ? route('guardavida.update', $guardavida->id) : route('guardavida.store') }}" method="POST">
        @csrf
        @if(isset($guardavida))
            @method('PUT')
        @endif

        <x-form-encabezado titulo="Agregar usuario" icono="users">
            Alta de un guardavidas, encargado o administrador del sistema.
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

        <x-form-seccion titulo="Registro de usuario" icono="user" descripcion="El correo ingresado será su usuario para acceder al sistema. La contraseña inicial será del 1 al 9 y deberá cambiarla al ingresar por primera vez. Seleccione el rol correspondiente.">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-8">
                 <!-- Nombre -->
                <div class="sm:col-span-4">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                    <div>
                        <input id="nombre" type="text" name="nombre" placeholder="Nombre"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                        value="{{ old('nombre', $guardavida->nombre ?? '') }}" required/>
                        @error('nombre')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Apellido -->
                <div class="sm:col-span-4">
                    <label for="apellido" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</label>
                    <div>
                        <input id="apellido" type="text" name="apellido" placeholder="Apellido"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                        value="{{ old('apellido', $guardavida->apellido ?? '') }}" required/>
                        @error('apellido')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <div>
                        <input id="email" type="email" name="email" placeholder="usuario@gmail.com"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
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
                <div class="sm:col-span-4">
                    <label for="Rol" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol</label>
                         <div class="mt-2 relative overflow-hidden">
                            <select name="rol" id="rol-select"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                            required>
                                <option class="w-auto" value="">Seleccione</option>
                                <option value="guardavida" {{ old('rol', $rol ?? '' ) == 'guardavida' ? 'selected' : '' }}>Guardavida</option>
                                <option value="encargado" {{ old('rol', $rol ?? '' ) == 'encargado' ? 'selected' : '' }}>Encargado</option>
                                <option value="admin" {{ old('rol', $rol ?? '' ) == 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                    </div>
                </div>

                <!-- DNI: siempre visible/obligatorio (vive en users.dni, ya no
                     solo en guardavidas.dni) — cualquier usuario, sea admin,
                     guardavida o encargado, tiene DNI. -->
                <div class="sm:col-span-4">
                    <label for="dni" class="block text-sm font-medium text-gray-700 dark:text-gray-300">DNI</label>
                    <div>
                        <input id="dni" type="number" name="dni" placeholder="Ej: 11111111"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                        value="{{ old('dni', $guardavida->dni ?? '') }}" required/>
                        @error('dni')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </x-form-seccion>

        {{-- A PARTIR DE ACA, SOLO VISIBLE SI VA A AGREGAR UN GUARDAVIDAS (lo muestra el script de abajo según el rol) --}}
        <div id="guardavida-fields" style="display: none;" class="grid grid-cols-1 gap-y-14 sm:gap-y-16">
            <x-form-seccion titulo="Información personal" icono="user" descripcion="Complete los datos personales del guardavidas. Asegúrese de que la información sea correcta, ya que será utilizada para su identificación y gestión interna.">
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-8">
    <!-- telefono -->
    <div class="sm:col-span-4">
        <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefono</label>
        <div>
            <input id="telefono" type="tel" name="telefono" placeholder="2983111111"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
            value="{{ old('telefono', $guardavida->telefono ?? '') }}" />
            @error('telefono')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Direccion -->
    <div class="sm:col-span-4">
        <label for="direccion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección</label>
        <div>
            <input id="direccion" type="text" name="direccion" placeholder="Ej. calle 11"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
            value="{{ old('direccion', $guardavida->direccion ?? '') }}" />
            @error('direccion')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>


    <!-- numero -->
    <div class="sm:col-span-2">
        <label for="dni" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
        <div>
            <input id="numero" type="number" name="numero" placeholder="Ej: 1250"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
            value="{{ old('numero', $guardavida->numero ?? '') }}" />
            @error('numero')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- piso_dpto -->
    <div class="sm:col-span-2">
        <label for="piso_dpto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Piso - Dpto</label>
        <div>
            <input id="piso_dpto" type="text" name="piso_dpto" placeholder="Ej. 2-A"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
            value="{{ old('piso_dpto', $guardavida->piso_dpto ?? '') }}" />
            @error('piso_dpto')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>



    <!-- fecha de alta en el plantel -->
    <div class="sm:col-span-4">
        <label for="fecha_alta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de alta (primer día de trabajo)</label>
        <div>
            <input id="fecha_alta" type="date" name="fecha_alta" value="{{ old('fecha_alta', now()->toDateString()) }}"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" />
            @error('fecha_alta')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
                </div>
            </x-form-seccion>

            <x-form-seccion titulo="Información profesional" icono="life-buoy" descripcion="Indique correctamente la playa, puesto, función y turno asignados. Estos datos determinan dónde deberá fichar el guardavidas y cómo se gestionarán sus tareas.">
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-8">
        <!-- Playa -->
        <div class="sm:col-span-4">
            <label for="playa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Playa</label>
            <div>
                <select id="playa_id" name="playa_id"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
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
        <label for="puesto_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puesto</label>
        <div>
            <select id="puesto_id" name="puesto_id"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
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
            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Función</label>
           <div class="mt-2 relative overflow-hidden">
            <select id="funcion" name="funcion" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                <option value="Guardavida">Guardavida</option>
                <option value="Timonel">Timonel</option>
                <option value="Encargado">Encargado</option>
                <option value="Jefe_de_playa">Jefe de playa</option>
            </select>
            </div>
        </div>

        <!-- Turno -->
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Turno</label>
            <div class="mt-2 flex gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="turno" value="M"
                        {{ old('turno', $guardavida->turno ?? '') == 'M' ? 'checked' : '' }}
                        class="text-sky-600 border-gray-300 focus:ring-sky-500 dark:border-gray-600">
                    <span class="ml-2">Mañana</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="turno" value="T"
                        {{ old('turno', $guardavida->turno ?? '') == 'T' ? 'checked' : '' }}
                        class="text-sky-600 border-gray-300 focus:ring-sky-500 dark:border-gray-600">
                    <span class="ml-2">Tarde</span>
                </label>
            </div>
        </div>

        <!-- Franco fijo -->
        <div class="sm:col-span-8">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Franco fijo (día/s libres de todas las semanas)
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Opcional — podés dejarlo sin marcar si todavía no está definido qué día le va a tocar.
                El propio guardavida también puede configurarlo después desde su perfil.
            </p>
            <div>
                <x-dias-franco-checkboxes :seleccionados="old('dias_franco', [])" />
            </div>
        </div>
                </div>
            </x-form-seccion>
        </div>
        {{-- HASTA ACA, SOLO VISIBLE SI VA A AGREGAR UN GUARDAVIDAS --}}

        <!-- Botones -->
        <div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-end sm:gap-x-6">
            <button type="button" class="text-sm text-gray-500 dark:text-gray-400 hover:underline" onclick="window.history.back()">Cancelar</button>
            <button type="submit" class="w-full sm:w-auto bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar</button>
        </div>
    </x-form-tarjeta>

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



