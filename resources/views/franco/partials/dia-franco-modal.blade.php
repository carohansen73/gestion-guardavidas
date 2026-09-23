<div id="diaFrancoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-96 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
            Configurar mi franco
        </h2>

        <form action="{{ route('guardavida.actualizarDiaFranco') }}" method="POST">
            @csrf
            @method('PATCH')

            <label class="text-sm text-gray-700 dark:text-gray-300">Día(s) franco fijo(s) de todas las semanas:</label>
            <div class="mt-2">
                <x-dias-franco-checkboxes />
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
