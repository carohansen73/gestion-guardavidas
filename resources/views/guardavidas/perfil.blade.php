@extends('layouts.app')
@push('styles')
    @vite(['resources/css/perfilGuardavidas.css'])
@endpush

@php
    // Siempre es el perfil del usuario logueado (editar a otro se hace por guardavida.edit).
    // Un admin sin ficha de guardavida asociada llega con $guardavida = null (ver
    // GuardavidaController::myProfile) y esta vista se limita a la card de "Datos de Usuario"
    // para que pueda cambiar su email/contraseña.
    $perfilUser = $guardavida?->user ?? auth()->user();
    $fotoPerfil = $guardavida?->fotoPersonal();

    $claseInput = 'block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2';
@endphp

@section('content')

    <main class="overflow-x-hidden sm:pb-5 sm:mx-4">

        <x-volver :href="route('home')" class="mt-1 sm:mt-4" />

        <!-- Alert Messages -->
        <div id="alertContainer"></div>

        <!-- Profile Card -->
        <div class="profile-card bg-white rounded-lg shadow-md my-4">

            <!-- Profile Header -->
            <div class="profile-header bg-sky-600 dark:bg-sky-800 py-4
                flex flex-col items-center text-center
                md:flex-row md:items-center md:text-left md:justify-start rounded-t-lg">

                <!-- Avatar -->
                <div class="profile-avatar text-6xl md:w-1/4 md:flex md:justify-center overflow-hidden">
                    @if ($fotoPerfil)
                        <img src="{{ route('guardavida.foto', $guardavida) }}?v={{ $fotoPerfil->id }}"
                            alt="Foto de {{ $guardavida->nombre }} {{ $guardavida->apellido }}"
                            class="w-full h-full object-cover rounded-full">
                    @else
                        <i class="fas fa-user"></i>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex flex-col items-center md:items-start mt-3 md:mt-0 md:w-3/4 md:pl-4 md:text-left">

                    <x-role-badge :rol="$perfilUser->rolPrincipal()" size="w-3.5 h-3.5" />

                    <h2 class="profile-name text-white text-xl font-semibold mt-2">
                        @if ($guardavida)
                            {{ $guardavida->nombre }} {{ $guardavida->apellido }}
                        @else
                            {{ $perfilUser->name }} {{ $perfilUser->lastname }}
                        @endif
                    </h2>

                    @if ($guardavida)
                        <p class="profile-role text-gray-200 flex items-center gap-1.5 mt-1">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $guardavida->playa?->nombre ?? 'Sin playa asignada' }}
                            @if ($guardavida->puesto)
                                · {{ $guardavida->puesto->nombre }}
                            @endif
                        </p>
                    @endif

                    <p class="text-xs text-gray-200/80 mt-2 flex items-center gap-1.5">
                        <i class="fas fa-pencil"></i>
                        Editar mi perfil
                    </p>
                </div>
            </div>

            @if ($guardavida)
                <!-- Profile Body -->
                <form action="{{ route('guardavida.updateProfile', $guardavida->id) }}" method="POST" class="profile-body">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-8 text-gray-600 rounded px-4 sm:px-10 py-6 dark:text-gray-300">

                        <!-- Personal Information -->
                        <div class="sm:col-span-8">
                            <h2 class="text-lg text-gray-700 dark:text-gray-50">
                                <i class="fas fa-id-card text-sky-600"></i>
                                Información Personal
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                En esta sección podés consultar y actualizar tus datos personales.
                                Es importante mantener esta información actualizada para una correcta identificación y comunicación.
                            </p>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-user me-1 text-sky-600"></i> Nombre</label>
                            <input id="nombre" type="text" name="nombre" placeholder="Nombre" class="{{ $claseInput }}"
                                value="{{ old('nombre', $guardavida->nombre) }}" required />
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-user me-1 text-sky-600"></i> Apellido</label>
                            <input type="text" name="apellido" placeholder="Apellido" class="{{ $claseInput }}"
                                value="{{ old('apellido', $guardavida->apellido) }}" required />
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-id-badge me-1 text-sky-600"></i> DNI</label>
                            <input type="number" name="dni" placeholder="DNI" class="{{ $claseInput }}"
                                value="{{ old('dni', $guardavida->dni) }}" required />
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-phone me-1 text-sky-600"></i> Teléfono</label>
                            <input type="tel" name="telefono" placeholder="Teléfono" class="{{ $claseInput }}"
                                value="{{ old('telefono', $guardavida->telefono) }}" required />
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-map-marker-alt me-1 text-sky-600"></i> Dirección</label>
                            <input type="text" name="direccion" placeholder="Dirección" class="{{ $claseInput }}"
                                value="{{ old('direccion', $guardavida->direccion) }}" />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-hashtag me-1 text-sky-600"></i> Número</label>
                            <input type="number" name="numero" placeholder="Número" class="{{ $claseInput }}"
                                value="{{ old('numero', $guardavida->numero) }}" />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-building me-1 text-sky-600"></i> Piso/Dpto</label>
                            <input type="text" name="piso_dpto" placeholder="Piso/Dpto" class="{{ $claseInput }}"
                                value="{{ old('piso_dpto', $guardavida->piso_dpto) }}" />
                        </div>

                        <!-- Work Information -->
                        <div class="sm:col-span-8 pt-4 mt-3 border-t border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg text-gray-700 dark:text-gray-50">
                                <i class="fas fa-briefcase text-sky-600"></i>
                                Información Laboral
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                @if ($esEncargado)
                                    Como encargado podés cambiar tu puesto y tu turno. El resto lo administra el personal autorizado (Jefe de Playa o Administrador).
                                @else
                                    La información laboral es administrada exclusivamente por el personal autorizado (Jefe de Playa, Encargado o Administrador).
                                @endif
                            </p>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-umbrella-beach me-1 text-sky-600"></i> Playa</label>
                            <div class="info-value text-gray-700 dark:text-gray-200">{{ $guardavida->playa?->nombre ?? 'No asignado' }}</div>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-flag me-1 text-sky-600"></i> Puesto</label>
                            @if ($puestos)
                                <select id="selectPuesto" name="puesto_id" required
                                    class="block w-full rounded-md border bg-gray-100 px-3 py-1.5 text-gray-600 shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-1">
                                    @foreach ($puestos as $puesto)
                                        <option value="{{ $puesto->id }}" @selected(old('puesto_id', $guardavida->puesto_id) == $puesto->id)>
                                            {{ $puesto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">{{ $guardavida->puesto?->nombre ?? 'No asignado' }}</div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-clock me-1 text-sky-600"></i> Turno actual</label>
                            @if ($esEncargado)
                                <div class="mt-2 flex gap-4 text-gray-700 dark:text-gray-200">
                                    @foreach (['M' => 'Mañana', 'T' => 'Tarde'] as $valor => $nombreTurno)
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="turno" value="{{ $valor }}" required
                                                @checked(old('turno', $guardavida->turno) == $valor)
                                                class="text-sky-600 border-gray-300 focus:ring-sky-500 dark:border-gray-600">
                                            <span class="ml-2">{{ $nombreTurno }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">{{ $guardavida->turno ?: 'No asignado' }}</div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-tasks me-1 text-sky-600"></i> Función</label>
                            <div class="info-value text-gray-700 dark:text-gray-200">{{ str_replace('_', ' ', $guardavida->funcion) }}</div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="sm:col-span-8 flex flex-wrap items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" class="btn text-white bg-gray-500 hover:bg-gray-400" onclick="window.history.back()">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" id="btnGuardar">
                                <i class="fas fa-save me-1"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>

        @if ($guardavida)
            <!-- Franco -->
            <div class="profile-card bg-white rounded-lg shadow-md my-4 px-4 sm:px-10 py-6">
                <h2 class="text-lg text-gray-700 dark:text-gray-50 mb-2">
                    <i class="fas fa-bed text-sky-600"></i>
                    Franco
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                    Elegí tu(s) día(s) libre(s) fijo(s) de todas las semanas. Si una semana puntual necesitás cambiarlo,
                    <a href="{{ route('franco-intercambio.index') }}" class="text-sky-600 hover:underline">pedíselo a un compañero</a> desde "Cambios de Franco".
                </p>

                {{-- Los inputs y el botón se asocian al <form id="francoForm"> de abajo con form="...". --}}
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex flex-wrap gap-4">
                        @foreach (['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $i => $nombreDia)
                            <label class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-200">
                                <input type="checkbox" name="dias_franco[]" value="{{ $i }}" form="francoForm"
                                    {{ in_array($i, old('dias_franco', $guardavida->diasFrancoActuales())) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-sky-600 focus:ring-sky-500 dark:border-gray-600">
                                {{ $nombreDia }}
                            </label>
                        @endforeach
                    </div>

                    <button type="submit" form="francoForm" class="btn btn-primary shrink-0 md:ml-auto">
                        <i class="fas fa-save me-1"></i>
                        Guardar franco
                    </button>
                </div>
            </div>

            <form id="francoForm" action="{{ route('guardavida.actualizarDiaFranco') }}" method="POST" class="hidden">
                @csrf
                @method('PATCH')
            </form>
        @endif

        <!-- Datos de Usuario (email y contraseña) -->
        <div class="profile-card bg-white rounded-lg shadow-md my-4 px-4 sm:px-10 py-6">
            <h2 class="text-lg text-gray-700 dark:text-gray-50 mb-1">
                <i class="fas fa-user-lock text-sky-600"></i>
                Datos de Usuario
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Tu email es tu usuario. Verificá que sea correcto para poder iniciar sesión o recuperar tu cuenta.
            </p>

            <div class="flex flex-col gap-8">
                <!-- Email -->
                <div>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-envelope me-1 text-sky-600"></i> Email</label>
                        <div class="flex flex-col justify-between md:flex-row gap-3 mt-2">
                            <input type="email" name="email"
                                class="block w-full max-w-xs bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
                                value="{{ old('email', $perfilUser->email) }}" required autocomplete="username" />
                            <button type="submit" class="btn btn-primary shrink-0">
                                <i class="fas fa-save me-1"></i>
                                Guardar email
                            </button>
                        </div>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @if (session('status') === 'profile-updated')
                            <p class="text-sm text-green-600 mt-1">Guardado.</p>
                        @endif
                    </form>
                </div>

                <!-- Contraseña -->
                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-key me-1 text-sky-600"></i> Contraseña actual</label>
                        <input type="password" name="current_password" autocomplete="current-password"
                            class="block w-full max-w-xs bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2" />
                        @error('current_password', 'updatePassword')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <label class="info-label text-gray-800 dark:text-gray-100 mt-3 block"><i class="fas fa-lock me-1 text-sky-600"></i> Nueva contraseña</label>
                        <input type="password" name="password" autocomplete="new-password"
                            class="block w-full max-w-xs bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2" />
                        @error('password', 'updatePassword')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <label class="info-label text-gray-800 dark:text-gray-100 mt-3 block"><i class="fas fa-lock me-1 text-sky-600"></i> Repetir nueva contraseña</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                            class="block w-full max-w-xs bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2" />

                        <div class="flex items-center justify-end gap-3 mt-4">
                            @if (session('status') === 'password-updated')
                                <span class="text-sm text-green-600">Guardada.</span>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Cambiar contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($guardavida)
            <!-- Estadísticas (mismo contenido que en la ficha) -->
            <div class="profile-card bg-white rounded-lg shadow-md my-4 px-4 sm:px-10 py-6 text-gray-600 dark:text-gray-300">
                <h2 class="text-lg text-gray-700 dark:text-gray-50">
                    <i class="fas fa-chart-line text-sky-600"></i>
                    Estadísticas
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    @if ($temporadaStats)
                        Temporada {{ $temporadaStats->nombre }} y total histórico.
                    @else
                        Total histórico.
                    @endif
                </p>

                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ([
                        'asistencias' => ['Asistencias', 'fa-calendar-check'],
                        'intervenciones' => ['Intervenciones', 'fa-life-ring'],
                        'licencias' => ['Licencias', 'fa-file-medical'],
                    ] as $clave => [$titulo, $icono])
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4">
                            <div class="flex items-center justify-between text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ $titulo }}
                                <i class="fas {{ $icono }} text-sky-600 dark:text-sky-400"></i>
                            </div>
                            <div class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">
                                {{ $estadisticas[$clave]['temporada'] }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $temporadaStats ? 'esta temporada' : 'en total' }}
                            </div>
                            @if ($temporadaStats)
                                <div class="mt-3 border-t border-gray-200 dark:border-gray-700 pt-2 text-xs text-gray-600 dark:text-gray-300">
                                    Total histórico: <span class="font-semibold">{{ $estadisticas[$clave]['total'] }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </main>

    <!-- Mostrar mensajes de sesión si existen -->
    @if (session('success'))
        <script>
            mostrarAlerta('success', '{{ session('success.titulo') }}', '{{ session('success.detalle') }}');
        </script>
    @endif

    @if (session('error'))
        <script>
            mostrarAlerta('error', '{{ session('error.titulo') }}', '{{ session('error.detalle') }}');
        </script>
    @endif

    <script src="{{ asset('js/perfilGuardavidas.js') }}" defer></script>
@endsection
