<div id="diaFrancoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-96 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
            Configurar mi franco
        </h2>

        <form action="{{ route('guardavida.actualizarDiaFranco') }}" method="POST">
            @csrf
            @method('PATCH')

            <label class="text-sm text-gray-700 dark:text-gray-300">Día(s) franco fijo(s) de todas las semanas:</label>
            <div class="flex flex-col gap-1 mt-2">
                @foreach (['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $i => $nombreDia)
                    <label class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-200">
                        <input type="checkbox" name="dias_franco[]" value="{{ $i }}" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        {{ $nombreDia }}
                    </label>
                @endforeach
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" class="px-3 py-1 bg-gray-400 text-white rounded"
                    onclick="document.getElementById('diaFrancoModal').classList.add('hidden')">
                    Cancelar
                </button>
                <button type="submit" class="px-3 py-1 bg-sky-600 text-white rounded">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
