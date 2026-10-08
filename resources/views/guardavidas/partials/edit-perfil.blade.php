<x-form-tarjeta action="{{ route('user.update', $guardavida->user) }}" method="POST">
    @csrf
    @method('PUT')

    <x-form-encabezado titulo="Perfil de la cuenta" icono="shield-check" etiqueta="{{ $guardavida->apellido }}, {{ $guardavida->nombre }}">
        Nombre, email y contraseña con los que ingresa al sistema.
    </x-form-encabezado>

    <x-form-seccion titulo="Datos de la cuenta" icono="user">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-8">
                <!-- Nombre -->
                <div class="sm:col-span-4">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                    <div>
                        <input id="nombre_user" type="text" name="nombre" placeholder="Nombre"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                        value="{{ old('nombre', $guardavida->nombre ?? '') }}" required/>
                        @error('nombre')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Apellido -->
                <div class="sm:col-span-4">
                    <label for="apellido" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</label>
                    <div>
                        <input id="apellido_user" type="text" name="apellido" placeholder="Apellido"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                        value="{{ old('apellido', $guardavida->apellido ?? '') }}" required/>
                        @error('apellido')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email -->
                <div class="sm:col-span-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <div>
                        <input id="email" type="email" name="email" placeholder="usuario@gmail.com"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                        value="{{ old('email', $guardavida->user->email ?? '') }}" required/>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
        </div>
    </x-form-seccion>

    <x-form-seccion titulo="Contraseña" icono="key-round" descripcion="Dejala vacía para no cambiarla.">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-8">
                 <!-- Nueva contraseña -->
                <div class="sm:col-span-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nueva contraseña</label>
                    <input type="password" name="password" id="password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Confirmar nueva contraseña -->
                 <div class="sm:col-span-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>
        </div>
    </x-form-seccion>

        <!-- Botones -->
        <div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-end sm:gap-x-6">
            <button type="button" class="text-sm text-gray-500 dark:text-gray-400 hover:underline" onclick="window.history.back()">Cancelar</button>
            <button type="submit" class="w-full sm:w-auto bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar</button>
        </div>
</x-form-tarjeta>
