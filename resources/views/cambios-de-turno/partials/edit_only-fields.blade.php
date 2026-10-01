
<!-- Turno Nuevo -->
<div class="sm:col-span-4">
    <label class="block text-sm font-medium text-gray-900 dark:text-white">Turno nuevo</label>
    <div class="mt-2 flex gap-4">
        <label class="inline-flex items-center">
            <input type="radio" name="turno_nuevo" value="M"
                {{ old('turno_nuevo', $cambioDeTurno->turno_nuevo ?? '') == 'M' ? 'checked' : '' }}
                class="text-sky-600 border-gray-300 focus:ring-indigo-500">
            <span class="ml-2">Mañana</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="turno_nuevo" value="T"
                {{ old('turno_nuevo', $cambioDeTurno->turno_nuevo ?? '') == 'T' ? 'checked' : '' }}
                class="text-sky-600 border-gray-300 focus:ring-indigo-500">
            <span class="ml-2">Tarde</span>
        </label>
    </div>
</div>

<!-- Función del guardavidas -->
<div class="sm:col-span-4">
    <label for="funcion"
    class="block text-sm font-medium dark:text-white">Función del guardavidas</label>
    <div class="mt-2 relative overflow-hidden">
    <select id="funcion" name="funcion" class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
        <option value="Guardavida" {{ old('funcion', $cambioDeTurno->funcion ) == 'Guardavida' ? 'selected' : '' }}>Guardavida</option>
        <option value="Timonel" {{ old('funcion', $cambioDeTurno->funcion ) == 'Timonel' ? 'selected' : '' }}>Timonel</option>
        <option value="Encargado" {{ old('funcion', $cambioDeTurno->funcion ) == 'Encargado' ? 'selected' : '' }}>Encargado</option>
        <option value="Jefe_de_playa" {{ old('funcion', $cambioDeTurno->funcion ) == 'Jefe_de_playa' ? 'selected' : '' }}>Jefe de playa</option>
    </select>
    </div>
</div>


 <!-- Playa -->
<div class="sm:col-span-4">
    <label for="playa_id" class="block text-sm font-medium dark:text-white">Playa</label>
    <div class="mt-2">
        <select id="playa_id" name="playa_id"
        class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
            @foreach($playas as $playa)
                <option value="{{ $playa->id }}"
                    @if( isset($cambioDeTurno) && $cambioDeTurno->playa_id == $playa->id )
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
                    @if( isset($cambioDeTurno) && $cambioDeTurno->puesto_id == $puesto->id )
                        selected
                    @endif>
                    {{ $puesto->nombre }}
                </option>
            @endforeach
        @endforeach
        </select>
    </div>
</div>
