<div id="setupOverlay"
     class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white p-6 rounded shadow-xl max-w-md w-full relative">
        <div class="d-flex justify-center mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-14 h-14 text-sky-600">
            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
            </svg>

        </div>

        <h2 class="text-xl font-bold mb-2 text-center dark:text-white">
            Configurar tu puesto y turno
        </h2>

        <p class="text-gray-600 dark:text-gray-100 mb-4 text-center">
            Antes de continuar, seleccioná el <b>puesto</b> y el <b>turno</b> donde vas a trabajar.
        </p>
        <p class="text-gray-700 dark:text-gray-200 mb-4 text-sm text-center">
            <b class="dark:text-gray-100">Importante:</b> Seleccioná correctamente tu puesto, ya que será el lugar donde vas a registrar tu asistencia.
        </p>

        <form action="{{ route('guardavida.setup.store') }}" method="POST">
            @csrf

            {{-- Puesto --}}
            <div class="mb-4">
                <label class="font-semibold">Puesto</label>
                <select name="puesto_id" required class="w-full border p-2 rounded  bg-white text-gray-900 border-gray-300
                    dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600
                    focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <option value="">Seleccionar...</option>

                    @foreach(Auth::user()->guardavida->playa->puestos as $puesto)
                        <option value="{{ $puesto->id }}">{{ $puesto->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Turno --}}
            <div class="mb-4">
                <label class="font-semibold">Turno</label>
                <select name="turno" required class="w-full border p-2 rounded bg-white text-gray-900 border-gray-300
                    dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600
                    focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <option value="">Seleccionar...</option>
                    <option value="M">Mañana</option>
                    <option value="T">Tarde</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-sky-600 text-white p-2 rounded hover:bg-sky-700 focus:ring-2 focus:ring-blue-500 dark:hover:bg-sky-500">
                Guardar
            </button>
        </form>

    </div>
</div>
