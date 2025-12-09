
{{--
<form action="{{ isset($intervencion) ? route('intervencion.update', $intervencion->id) :
    route('intervencion.store') }}" method="POST" class="bg-white rounded shadow-md pb-3">
    @csrf
    {{-- @if(isset($intervencion))
        @method('PUT')
    @endif --}}

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

              <!-- Playa -->
            <div class="sm:col-span-3">
                <label class="text-sm/6 font-semibold text-gray-900 dark:text-white">Playa</label>
                    <div class="mt-2">
                        <select id="playa_id" name="playa_id"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-800 shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
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
          <label class="text-sm/6 font-semibold text-gray-900 dark:text-white">Puesto</label>
          <div class="mt-2">
            <select id="puesto_id" name="puesto_id"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-800 shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
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
        <div class="sm:col-span-2">
          <label class="text-sm/6 font-semibold text-gray-900 dark:text-white">Fecha y hora</label>
          <div class="mt-2">
            <input id="fecha" type="datetime-local" name="fecha"
              class="block w-full max-w-xs rounded-md bg-white px-2 py-1 text-sm text-gray-900
              shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 dark:bg-gray-700
              dark:text-white dark:outline-gray-500" value="{{ old('fecha', $intervencion->fecha ?? '') }}" />
          </div>
        </div>

        <!-- Tipo de intervención -->
        <div class="sm:col-span-2">
          <label class="text-sm/6 font-semibold text-gray-900 dark:text-white">Tipo de Intervención</label>
          {{-- <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Ejemplo: Busqueda/extravío de personas, Asistencia médica, Primeros auxilios, Rescate, etc.</p> --}}


          <div class="mt-2">
            <input id="tipo_intervencion" type="text" name="tipo_intervencion" placeholder="Ej. rescate"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-800 shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
              value="{{ old('tipo_intervencion', $intervencion->tipo_intervencion ?? '') }}" />
            @error('tipo_intervencion')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <!-- Víctimas -->
         <div class="sm:col-span-2">
          <label class="text-sm/6 font-semibold text-gray-900 dark:text-white">Víctimas</label>
          {{-- <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Ingrese la cantidad de víctimas.</p> --}}
          <div class="mt-2">
            <input id="victimas" type="number" name="victimas" min="0"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-800 shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
               value="{{ old('victimas', $intervencion->victimas ?? '') }}"/>
                @error('victimas')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
          </div>
        </div>


         <!-- Traslado -->
       <div class="sm:col-span-2">
            <label class="text-sm/6 font-semibold text-gray-900 dark:text-white">Hubo traslado</label>
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
        <div class="sm:col-span-2">
            <label class="text-sm/6 font-semibold text-gray-900 dark:text-white">Código</label>
            {{-- <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Ingrese un número entre el 1 y el 6.</p> --}}
            <div class="mt-2">
                <input id="codigo" type="number" name="codigo"
                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-800 shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                value="{{ old('codigo', $intervencion->codigo ?? '') }}"/>
            </div>
        </div>

        <!-- Bandera -->
        {{-- <div class="sm:col-span-2">
          <label for="bandera_id" class="block text-sm font-medium text-gray-900 dark:text-white">Bandera</label>
          <div class="mt-2">
            <select id="bandera_id" name="bandera_id"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 font-medium shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
               @foreach($banderas as $bandera)
                    <option value="{{ $bandera->id }}"
                        {{ old('bandera_id', $intervencion?->bandera_id ?? '') == $bandera->id ? 'selected' : '' }}>
                        {{ $bandera->codigo }}
                    </option>
                @endforeach
            </select>
          </div>
        </div> --}}

        <!-- Fuerzas -->
        <div class="sm:col-span-2">
            <label for="fecha" class="text-sm/6 font-semibold text-gray-900 dark:text-white">Intervinieron otras fuerzas</label>
            <div class="mt-2">
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
         <div class="sm:col-span-2">
            <label for="fecha" class="text-sm/6 font-semibold text-gray-900 dark:text-white">Guardavidas que intervinieron</label>
            <div class="mt-2">
            <select id="guardavidas" name="guardavidas[]" multiple>
                @foreach($guardavidas as $g)
                    <option value="{{ $g->id }}"
                        @if(collect(old('guardavidas', $intervencion?->guardavidas->pluck('id') ?? []))->contains($g->id))
                            selected
                        @endif>
                        {{ $g->nombre }} {{ $g->apellido }}
                    </option>
                @endforeach
            </select>
            </div>
        </div>

        <!-- Detalles -->
        <div class="col-span-full">
            <label class="text-sm/6 font-semibold text-gray-900 dark:text-white">Detalles</label>
            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Describa la intervención.</p>
            <div class="mt-2">
                <textarea id="detalles" name="detalles" rows="4"
                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-800 shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                {{ old('detalles', $intervencion->detalles ?? '') }}
                </textarea>
            </div>
        </div>

      </div>
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
{{-- </form> --}}

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

new TomSelect("#guardavidas",{
    plugins: ['remove_button'],
    persist: false,
    create: false,
});
</script>




