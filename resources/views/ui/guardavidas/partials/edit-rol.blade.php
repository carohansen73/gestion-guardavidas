 {{-- <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Settings tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p> --}}

<form action="{{ route('rol.update', $guardavida->user) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-8 bg-white text-gray-600 rounded shadow-md pb-12 px-6 py-6">

        <h3 class="sm:col-span-6 text-gray-900 dark:text-white text-lg font-medium ">
            Rol
        </h3>
        <div class="sm:col-span-3">
            <label for="Rol" class="block text-sm font-medium text-gray-900 dark:text-white">Rol</label>
            <div class="mt-2 relative overflow-hidden">
                <select name="role" id="rol-select"
                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                required>
                    <option class="w-auto" value="">Seleccione</option>
                    <option value="guardavida" {{ old('rol', $rol ) == 'guardavida' ? 'selected' : '' }}>Guardavida</option>
                    <option value="encargado" {{ old('rol', $rol ) == 'encargado' ? 'selected' : '' }}>Encargado</option>
                    <option value="admin" {{ old('rol', $rol ) == 'admin' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>
        </div>

        <!-- Botones -->
        <div class="sm:col-span-8">
            <div class="m-6 mb-6 flex items-center justify-end gap-x-6">
                <button type="button" class="text-sm font-semibold text-gray-900 dark:text-white">Cancelar</button>
                <button type="submit"
                class="rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500">
                Guardar
                </button>
            </div>
        </div>

    </div>
</form>
