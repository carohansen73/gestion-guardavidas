<form action="{{ route('guardavida.update', $guardavida) }}" method="POST">
        @csrf
        @method('PUT')
    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-8 bg-white text-gray-600 rounded shadow-md pb-12 px-6 py-6">


        <h3 class="sm:col-span-6 text-gray-900 dark:text-white text-lg font-medium">
                Información personal
        </h3>
        <!-- Nombre -->
        {{-- <div class="sm:col-span-4">
            <label for="nombre" class="block text-sm font-medium dark:text-white">Nombre</label>
            <div class="mt-2">
                <input id="nombre" type="text" name="nombre" placeholder="Nombre"
                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                value="{{ old('nombre', $guardavida->nombre ?? '') }}" required/>
                @error('nombre')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div> --}}

        <!-- Apellido -->
        {{-- <div class="sm:col-span-4">
            <label for="apellido" class="block text-sm font-medium dark:text-white">Apellido</label>
            <div class="mt-2">
                <input id="apellido" type="text" name="apellido" placeholder="Apellido"
                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                value="{{ old('apellido', $guardavida->apellido ?? '') }}" required/>
                @error('apellido')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div> --}}

        <!-- DNI -->
        <div class="sm:col-span-4">
            <label for="dni" class="block text-sm font-medium dark:text-white">DNI</label>
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
            <label for="telefono" class="block text-sm font-medium dark:text-white">Telefono</label>
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
            <label for="direccion" class="block text-sm font-medium dark:text-white">Dirección</label>
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
            <label for="dni" class="block text-sm font-medium dark:text-white">Número</label>
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
            <label for="piso_dpto" class="block text-sm font-medium dark:text-white">Piso - Dpto</label>
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
            <label for="playa_id" class="block text-sm font-medium dark:text-white">Playa</label>
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

        <!-- Puesto -->
        <div class="sm:col-span-4">
            <label for="puesto_id" class="block text-sm font-medium dark:text-white">Puesto</label>
            <div class="mt-2">
                <select id="puesto_id" name="puesto_id"
                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                @foreach($playas as $playa)
                    @foreach($playa->puestos as $puesto)
                        <option value="{{ $puesto->id }}" data-playa="{{ $playa->id }}"
                            @if( isset($guardavida) && $guardavida->puesto_id == $puesto->id )
                                selected
                            @endif>
                            {{ $puesto->nombre }}
                        </option>
                    @endforeach
                @endforeach
                </select>
            </div>
        </div>

        <!-- Función -->
        <div class="sm:col-span-4">
            <label for="funcion"
            class="block text-sm font-medium dark:text-white">Función</label>
            <div class="mt-2 relative overflow-hidden">
            <select id="funcion" name="funcion" class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                <option value="Guardavida" {{ old('funcion', $guardavida->funcion ) == 'Guardavida' ? 'selected' : '' }}>Guardavida</option>
                <option value="Timonel" {{ old('funcion', $guardavida->funcion ) == 'Timonel' ? 'selected' : '' }}>Timonel</option>
                <option value="Encargado" {{ old('funcion', $guardavida->funcion ) == 'Encargado' ? 'selected' : '' }}>Encargado</option>
                <option value="Jefe_de_playa" {{ old('funcion', $guardavida->funcion ) == 'Jefe_de_playa' ? 'selected' : '' }}>Jefe de playa</option>
            </select>
            </div>
        </div>

        <!-- Botones -->
        <div class="sm:col-span-8">
            <div class="m-6 mb-6 flex items-center justify-end gap-x-6">
                <button type="button" class="text-sm font-semibold text-gray-900 dark:text-white" onclick="window.history.back()">Cancelar</button>
                <button type="submit"
                class="rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500">
                Guardar
                </button>
            </div>
        </div>
    </div>
</form>
