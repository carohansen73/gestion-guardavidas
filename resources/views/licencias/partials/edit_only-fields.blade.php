 <!-- Playa -->
<div class="sm:col-span-3">
    <label for="playa_id" class="block text-sm font-medium dark:text-white">Playa</label>
    <div class="mt-2">
        <select id="playa_id" name="playa_id"
        class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
            @foreach($playas as $playa)
                <option value="{{ $playa->id }}"
                    @if( isset($licencia) && $licencia->playa_id == $playa->id )
                        selected
                    @endif >
                    {{ $playa->nombre }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<!-- Puesto -->
<div class="sm:col-span-3">
    <label for="puesto_id" class="block text-sm font-medium dark:text-white">Puesto</label>
    <div class="mt-2">
        <select id="puesto_id" name="puesto_id"
        class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
        @foreach($playas as $playa)
            @foreach($playa->puestos as $puesto)
                <option value="{{ $puesto->id }}" data-playa="{{ $playa->id }}"
                    @if( isset($licencia) && $licencia->puesto_id == $puesto->id )
                        selected
                    @endif>
                    {{ $puesto->nombre }}
                </option>
            @endforeach
        @endforeach
        </select>
    </div>
</div>

<!-- Turno -->
<div class="sm:col-span-2">
    <label class="block text-sm font-medium text-gray-900 dark:text-white">Turno</label>
    <div class="mt-2 flex gap-4">
        <label class="inline-flex items-center">
            <input type="radio" name="turno" value="M"
                {{ old('turno', $licencia->turno ?? '') == 'M' ? 'checked' : '' }}
                class="text-sky-600 border-gray-300 focus:ring-indigo-500">
            <span class="ml-2">Mañana</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="turno" value="T"
                {{ old('turno', $licencia->turno ?? '') == 'T' ? 'checked' : '' }}
                class="text-sky-600 border-gray-300 focus:ring-indigo-500">
            <span class="ml-2">Tarde</span>
        </label>
    </div>
</div>
