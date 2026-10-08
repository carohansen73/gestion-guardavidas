<x-form-tarjeta action="{{ route('guardavida.update', $guardavida) }}" method="POST">
    @csrf
    @method('PUT')

    <x-form-encabezado titulo="Datos del guardavidas" icono="user" etiqueta="{{ $guardavida->apellido }}, {{ $guardavida->nombre }}">
        Información personal y profesional.
    </x-form-encabezado>

    <x-form-seccion titulo="Información personal" icono="user">
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

                 <!-- DNI -->
                 <div class="sm:col-span-4">
                     <label for="dni" class="block text-sm font-medium text-gray-700 dark:text-gray-300">DNI</label>
                     <div>
                         <input id="dni" type="number" name="dni" placeholder="Ej: 11111111"
                         class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                         value="{{ old('dni', $guardavida->dni ?? '') }}" />
                         @error('dni')
                             <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                         @enderror
                     </div>
                 </div>

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
                     <label for="numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
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
        </div>
    </x-form-seccion>

    <x-form-seccion titulo="Información profesional" icono="life-buoy">
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

                <!-- Puesto -->
                <div class="sm:col-span-4">
                    <label for="puesto_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puesto</label>
                    <div>
                        <select id="puesto_id" name="puesto_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
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
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Función</label>
                    <div class="mt-2 relative overflow-hidden">
                    <select id="funcion" name="funcion" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                        <option value="Guardavida" {{ old('funcion', $guardavida->funcion ) == 'Guardavida' ? 'selected' : '' }}>Guardavida</option>
                        <option value="Timonel" {{ old('funcion', $guardavida->funcion ) == 'Timonel' ? 'selected' : '' }}>Timonel</option>
                        <option value="Encargado" {{ old('funcion', $guardavida->funcion ) == 'Encargado' ? 'selected' : '' }}>Encargado</option>
                        <option value="Jefe_de_playa" {{ old('funcion', $guardavida->funcion ) == 'Jefe_de_playa' ? 'selected' : '' }}>Jefe de playa</option>
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
                                    class="text-sky-600 border-gray-300 focus:ring-indigo-500 dark:border-gray-600">
                                <span class="ml-2">Mañana</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="turno" value="T"
                                    {{ old('turno', $guardavida->turno ?? '') == 'T' ? 'checked' : '' }}
                                    class="text-sky-600 border-gray-300 focus:ring-indigo-500 dark:border-gray-600">
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
                            Opcional — podés dejarlo sin marcar si todavía no está definido qué día le toca.
                            El propio guardavida también puede configurarlo después desde su perfil.
                        </p>
                        <div>
                            <x-dias-franco-checkboxes :seleccionados="old('dias_franco', $guardavida->diasFrancoActuales())" />
                        </div>
                    </div>
        </div>
    </x-form-seccion>

        <!-- Botones -->
        <div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-end sm:gap-x-6">
            <button type="button" class="text-sm text-gray-500 dark:text-gray-400 hover:underline" onclick="window.history.back()">Cancelar</button>
            <button type="submit" class="w-full sm:w-auto bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar</button>
        </div>
</x-form-tarjeta>
