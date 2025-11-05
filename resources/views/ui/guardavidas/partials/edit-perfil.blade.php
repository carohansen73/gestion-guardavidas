{{-- <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Dashboard tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p> --}}
<form action="{{ route('user.update', $guardavida->user) }}" method="POST">
        @csrf
        @method('PUT')

    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-8 bg-white text-gray-600 rounded shadow-md pb-12 px-6 py-6">

        <h3 class="sm:col-span-6 text-gray-900 dark:text-white text-lg font-medium ">
            Perfil
        </h3>
        <!-- Nombre -->
        <div class="sm:col-span-4">
            <label for="nombre" class="block text-sm font-medium dark:text-white">Nombre</label>
            <div class="mt-2">
                <input id="nombre_user" type="text" name="nombre" placeholder="Nombre"
                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                value="{{ old('nombre', $guardavida->nombre ?? '') }}" required/>
                @error('nombre')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Apellido -->
        <div class="sm:col-span-4">
            <label for="apellido" class="block text-sm font-medium dark:text-white">Apellido</label>
            <div class="mt-2">
                <input id="apellido_user" type="text" name="apellido" placeholder="Apellido"
                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                value="{{ old('apellido', $guardavida->apellido ?? '') }}" required/>
                @error('apellido')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Email -->
        <div class="sm:col-span-4">
            <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white">Email</label>
            <div class="mt-2">
                <input id="email" type="email" name="email" placeholder="usuario@gmail.com"
                class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                value="{{ old('email', $guardavida->user->email ?? '') }}" required/>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

         <!-- Nueva contraseña -->
        <div class="sm:col-span-4">
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nueva contraseña</label>
            <input type="password" name="password" id="password"
                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
            @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Confirmar nueva contraseña -->
         <div class="sm:col-span-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Confirmar nueva contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
        </div>

        <!-- Botones -->
        <div class="sm:col-span-8">
            <div class="m-6 mb-6 flex items-center justify-end gap-x-6">
                <button type="button" class="text-sm font-semibold text-gray-900 dark:text-white" onclick="window.history.back()">Cancelar</button>
                <button type="submit"
                class="rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500">
                Guardar
                </button>
            </div>
        </div>

    </div>
</form>
