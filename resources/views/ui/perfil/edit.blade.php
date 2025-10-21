<form method="POST" action="{{ route('perfil.update') }}" class="space-y-6">
    @csrf
    @method('PUT')

    <!-- Nombre -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre</label>
        <input type="text" name="name" id="name"
            value="{{ old('name', auth()->user()->name) }}"
            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm" required>
        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Email -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
        <input type="email" name="email" id="email"
            value="{{ old('email', auth()->user()->email) }}"
            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm" required>
        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Contraseña actual -->
    <div>
        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Contraseña actual</label>
        <input type="password" name="current_password" id="current_password"
            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
        @error('current_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Nueva contraseña -->
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nueva contraseña</label>
        <input type="password" name="password" id="password"
            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
        @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Confirmar nueva contraseña -->
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Confirmar nueva contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation"
            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
    </div>

    <button type="submit"
        class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
        Guardar cambios
    </button>
</form>
