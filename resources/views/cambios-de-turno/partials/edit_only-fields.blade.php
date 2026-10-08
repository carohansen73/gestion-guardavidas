
<!-- Turno Nuevo -->
<div class="sm:col-span-4">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Turno nuevo</label>
    <div class="mt-2 flex gap-4">
        <label class="inline-flex items-center">
            <input type="radio" name="turno_nuevo" value="M"
                {{ old('turno_nuevo', $cambioDeTurno->turno_nuevo ?? '') == 'M' ? 'checked' : '' }}
                class="text-sky-600 border-gray-300 focus:ring-indigo-500 dark:border-gray-600">
            <span class="ml-2">Mañana</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="turno_nuevo" value="T"
                {{ old('turno_nuevo', $cambioDeTurno->turno_nuevo ?? '') == 'T' ? 'checked' : '' }}
                class="text-sky-600 border-gray-300 focus:ring-indigo-500 dark:border-gray-600">
            <span class="ml-2">Tarde</span>
        </label>
    </div>
</div>

<!-- Función del guardavidas -->
<div class="sm:col-span-4">
    <label for="funcion"
    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Función del guardavidas</label>
    <div class="mt-2 relative overflow-hidden">
    <select id="funcion" name="funcion" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
        <option value="Guardavida" {{ old('funcion', $cambioDeTurno->funcion ) == 'Guardavida' ? 'selected' : '' }}>Guardavida</option>
        <option value="Timonel" {{ old('funcion', $cambioDeTurno->funcion ) == 'Timonel' ? 'selected' : '' }}>Timonel</option>
        <option value="Encargado" {{ old('funcion', $cambioDeTurno->funcion ) == 'Encargado' ? 'selected' : '' }}>Encargado</option>
        <option value="Jefe_de_playa" {{ old('funcion', $cambioDeTurno->funcion ) == 'Jefe_de_playa' ? 'selected' : '' }}>Jefe de playa</option>
    </select>
    </div>
</div>


 <!-- Playa -->
<div class="sm:col-span-4">
    <label for="playa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Playa</label>
    <div>
        <select id="playa_id" name="playa_id"
        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
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
    <label for="puesto_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puesto</label>
    <div>
        <select id="puesto_id" name="puesto_id"
        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
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
