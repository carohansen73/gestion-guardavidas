 <!-- Playa -->
<div class="sm:col-span-4">
    <label for="playa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Playa</label>
    <div>
        <select id="playa_id" name="playa_id"
        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
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
<div class="sm:col-span-4">
    <label for="puesto_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puesto</label>
    <div>
        <select id="puesto_id" name="puesto_id"
        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
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
<div class="sm:col-span-8">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Turno</label>
    <div class="mt-2 flex gap-4">
        <label class="inline-flex items-center">
            <input type="radio" name="turno" value="M"
                {{ old('turno', $licencia->turno ?? '') == 'M' ? 'checked' : '' }}
                class="text-sky-600 border-gray-300 focus:ring-indigo-500 dark:border-gray-600">
            <span class="ml-2">Mañana</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="turno" value="T"
                {{ old('turno', $licencia->turno ?? '') == 'T' ? 'checked' : '' }}
                class="text-sky-600 border-gray-300 focus:ring-indigo-500 dark:border-gray-600">
            <span class="ml-2">Tarde</span>
        </label>
    </div>
</div>
