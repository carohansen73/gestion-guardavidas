<x-form-encabezado :titulo="isset($intervencion) ? 'Editar intervención' : 'Registrar intervención'" icono="life-buoy">
    {{ isset($intervencion) ? 'Modificá los datos de la intervención.' : 'Completá los campos para registrar una intervención.' }}
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

<x-form-seccion titulo="Lugar y momento" icono="map-pin">
    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">
                  <!-- Playa -->
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Playa</label>
                        <div>
                            <select id="playa_id" name="playa_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                @foreach($playas as $playa)
                                <option value="{{ $playa->id }}"
                                    @if( isset($intervencion) && $intervencion->playa_id == $playa->id )
                                        selected
                                    @elseif(!isset($intervencion) && isset($guardavidaAuth) && $guardavidaAuth->playa_id == $playa->id)
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
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puesto</label>
                  <div>
                    <select id="puesto_id" name="puesto_id"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                       @foreach($puestos as $puesto)
                        <option value="{{ $puesto->id }}" data-playa="{{ $puesto->playa_id }}"
                            @if( isset($intervencion) && $intervencion->puesto_id == $puesto->id )
                                selected
                            @elseif(!isset($intervencion) && isset($guardavidaAuth) && $guardavidaAuth->puesto_id == $puesto->id)
                                selected
                            @endif
                        >
                            {{ $puesto->nombre }}
                        </option>
                    @endforeach
                    </select>
                  </div>
                </div>

                <!-- Fecha y hora -->
                <div class="sm:col-span-3">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha y hora</label>
                  <div>
                    <input id="fecha" type="datetime-local" name="fecha"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" value="{{ old('fecha', $intervencion->fecha ?? '') }}" />
                  </div>
                </div>
    </div>
</x-form-seccion>

<x-form-seccion titulo="Intervención" icono="life-buoy">
    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">
                <!-- Tipo de intervención -->
                <div class="sm:col-span-3">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de Intervención</label>
                  {{-- <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Ejemplo: Busqueda/extravío de personas, Asistencia médica, Primeros auxilios, Rescate, etc.</p> --}}


                  <div>
                    <input id="tipo_intervencion" type="text" name="tipo_intervencion" placeholder="Ej. rescate"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                      value="{{ old('tipo_intervencion', $intervencion->tipo_intervencion ?? '') }}" />
                    @error('tipo_intervencion')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                  </div>
                </div>

                <!-- Víctimas -->
                 <div class="sm:col-span-3">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Víctimas</label>
                  {{-- <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Ingrese la cantidad de víctimas.</p> --}}
                  <div>
                    <input id="victimas" type="number" name="victimas" min="0"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                       value="{{ old('victimas', $intervencion->victimas ?? '') }}"/>
                        @error('victimas')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                  </div>
                </div>

                  <!-- Traslado -->
                <div class="sm:col-span-3">
                     <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hubo traslado</label>
                     <div class="mt-2 flex gap-x-4">
                         <label class="flex items-center gap-x-2">
                         <input type="radio" name="traslado" value="1"
                             {{ old('traslado', $intervencion->traslado ?? '') == '1' ? 'checked' : '' }}
                             class="h-4 w-4 text-sky-600 focus:ring-sky-600 border-gray-300 dark:bg-gray-700 dark:border-gray-500" />
                         <span class="text-sm text-gray-700 dark:text-gray-300">Sí</span>
                         </label>
                         <label class="flex items-center gap-x-2">
                         <input type="radio" name="traslado" value="0"
                             {{ old('traslado', $intervencion->traslado ?? '') == '0' ? 'checked' : '' }}
                             class="h-4 w-4 text-sky-600 focus:ring-sky-600 border-gray-300 dark:bg-gray-700 dark:border-gray-500" />
                         <span class="text-sm text-gray-700 dark:text-gray-300">No</span>
                         </label>
                     </div>
                 </div>

                <!-- Código -->
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código</label>
                    {{-- <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Ingrese un número entre el 1 y el 6.</p> --}}
                    <div>
                        <input id="codigo" type="number" name="codigo"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                        value="{{ old('codigo', $intervencion->codigo ?? '') }}"/>
                    </div>
                </div>
    </div>
</x-form-seccion>

<x-form-seccion titulo="Quiénes intervinieron" icono="users">
    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">
                <!-- Fuerzas -->
                <div class="sm:col-span-3">
                    <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Intervinieron otras fuerzas</label>
                    <div>
                    <select id="fuerzas" name="fuerzas[]" multiple>
                        @foreach($fuerzas as $fuerza)
                            <option value="{{ $fuerza->id }}"
                                @if(collect(old('fuerzas', $intervencion?->fuerzas->pluck('id') ?? []))->contains($fuerza->id))
                                    selected
                                @endif>
                                {{ $fuerza->nombre }}
                            </option>
                        @endforeach
                    </select>
                    </div>
                </div>

                <!-- Lista de guardavidas -->
                 <div class="sm:col-span-3">
                    <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Guardavidas que intervinieron</label>
                    <div>
                    <select id="guardavidas" name="guardavidas[]" multiple>
                        @foreach($guardavidas as $g)
                            <option value="{{ $g->id }}" data-playa="{{ $g->playa_id }}"
                                @if(collect(old('guardavidas', $intervencion?->guardavidas->pluck('id') ?? []))->contains($g->id))
                                    selected
                                @endif>
                                {{ $g->apellido }}, {{ $g->nombre }}
                            </option>
                        @endforeach
                    </select>
                    </div>
                </div>
    </div>
</x-form-seccion>

<x-form-seccion titulo="Detalles" icono="align-left">
                        <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Detalles</label>
                <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Describa la intervención.</p>
                <div>
                    <textarea id="detalles" name="detalles" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">{{ old('detalles', $intervencion->detalles ?? '') }}</textarea>
                </div>
            </div>
</x-form-seccion>

<!-- Botones -->
<div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-end sm:gap-x-6">
    <button type="button" class="text-sm text-gray-500 dark:text-gray-400 hover:underline" onclick="window.history.back()">Cancelar</button>
    <button type="submit" class="w-full sm:w-auto bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar</button>
</div>

@vite(['resources/js/filterPuestoByPlaya.js'])
<!-- AlpineJS para hacerlo reactivo -->
{{-- <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}



<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">

<script>
new TomSelect("#fuerzas",{
    plugins: ['remove_button'],
    persist: false,
    create: false,
});

const tsGuardavidas = new TomSelect("#guardavidas",{
    plugins: ['remove_button'],
    persist: false,
    create: false,
});

// Acota el listado de guardavidas a la playa seleccionada
document.addEventListener('DOMContentLoaded', () => {
    const playaSelect = document.getElementById('playa_id');
    const guardavidaSelectEl = document.getElementById('guardavidas');

    const todosLosGuardavidas = Array.from(guardavidaSelectEl.options).map(opt => ({
        value: opt.value,
        text: opt.textContent.trim(),
        playa: opt.dataset.playa,
    }));

    function filtrarGuardavidasPorPlaya() {
        const playaSeleccionada = playaSelect.value;
        const seleccionados = tsGuardavidas.getValue();

        tsGuardavidas.clearOptions();
        todosLosGuardavidas
            .filter(g => g.playa === playaSeleccionada || seleccionados.includes(g.value))
            .forEach(g => tsGuardavidas.addOption({ value: g.value, text: g.text }));

        tsGuardavidas.refreshOptions(false);
        tsGuardavidas.setValue(seleccionados, true);
    }

    filtrarGuardavidasPorPlaya();
    playaSelect.addEventListener('change', filtrarGuardavidasPorPlaya);
});
</script>




