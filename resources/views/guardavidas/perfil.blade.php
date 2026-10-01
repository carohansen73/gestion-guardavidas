@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}
@push('styles')
    @vite(['resources/css/perfilGuardavidas.css'])
@endpush

@php
    // Admin/encargado sin ficha de guardavida asociada: $guardavida viene en
    // null (ver GuardavidaController::myProfile) y esta vista se limita a
    // mostrarle la card de "Datos de Usuario" para que pueda cambiar su
    // email/contraseña.
    $perfilUser = $guardavida?->user ?? auth()->user();
@endphp

@section('content')

    {{-- <div class="lg:ml-64 min-h-screen bg-gray-100 dark:bg-gray-800 "> --}}
        <main class="overflow-x-hidden pb-5 mx-4">



            <!-- Alert Messages -->
            <div id="alertContainer"></div>

            <!-- Profile Card -->
            <div class="profile-card bg-white rounded-lg shadow-md my-4">

                    <!-- Profile Header -->
                <div class="profile-header bg-sky-600 dark:bg-sky-800 py-4
                    flex flex-col items-center text-center
                    md:flex-row md:items-center md:text-left md:justify-start rounded-t-lg">

                    <!-- Avatar -->
                    <div class="profile-avatar text-6xl md:w-1/4 md:flex md:justify-center">
                        <i class="fas fa-user"></i>
                    </div>

                    <!-- Info -->
                    <div class="flex flex-col items-center md:items-start mt-3 md:mt-0 md:w-3/4 md:pl-4 md:text-left">

                        <x-role-badge :rol="\App\Enums\RolUsuario::principal($perfilUser)" size="w-3.5 h-3.5" />

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

                        @unless ($esPropietario)
                            <span class="badge {{ $perfilUser->enabled ? 'badge-active' : 'badge-inactive' }} flex items-center gap-1 mt-2">
                                <i class="fas {{ $perfilUser->enabled ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $perfilUser->enabled ? 'Activo' : 'Inactivo' }}
                            </span>

                            <p class="text-xs text-gray-200/80 mt-2 flex items-center gap-1.5">
                                <i class="fas fa-eye"></i>
                                Viendo en modo lectura, como administrador
                            </p>
                        @endunless
                        @if ($esPropietario)


                            <p class="text-xs text-gray-200/80 mt-2 flex items-center gap-1.5">
                                <i class="fas fa-pencil"></i>
                                Editar mi perfil
                            </p>
                        @endif
                    </div>
                </div>


                @if ($guardavida)
                <!-- Profile Body -->
                <form action="{{ route('guardavida.updateProfile', $guardavida->id) }}" method="POST" class="profile-body"
                    data-guardavida-id="{{ $guardavida->id }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-8 text-gray-600 rounded px-4 sm:px-10 py-6">

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
                            @if ($esPropietario)
                                <input id="nombre" type="text" name="nombre" placeholder="Nombre"
                                class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                                value="{{ old('nombre', $guardavida->nombre ?? '') }}" required/>
                                @error('nombre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">{{ $guardavida->nombre }}</div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-user me-1 text-sky-600"></i>Apellido</label>
                            @if ($esPropietario)
                                <input  type="text" name="apellido" placeholder="Apellido"
                                    class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                                    value="{{ old('apellido', $guardavida->apellido ?? '') }}" required/>
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">{{ $guardavida->apellido }}</div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-id-badge me-1  text-sky-600"></i>DNI</label>
                            @if ($esPropietario)
                                <input  type="number" name="dni" placeholder="DNI"
                                class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                                value="{{ old('dni', $guardavida->dni ?? '') }}" required/>
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">{{ $guardavida->dni }}</div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-phone me-1 text-sky-600"></i>Teléfono</label>
                            @if ($esPropietario)
                                <input type="tel" name="telefono" placeholder="Teléfono"
                                class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                                value="{{ old('telefono', $guardavida->telefono ?? 'No especificado') }}" required/>
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">{{ $guardavida->telefono ?? 'No especificado' }}</div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-map-marker-alt me-1  text-sky-600"></i>Dirección</label>
                            @if ($esPropietario)
                                <input type="text" name="direccion" placeholder="Dirección"
                                class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                                value="{{ old('direccion', $guardavida->direccion ?? 'No especificado') }}" required/>
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">{{ $guardavida->direccion ?? 'No especificado' }}</div>
                            @endif
                        </div>

                        <div class="sm:col-span-2">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-hashtag me-1 text-sky-600"></i>Número</label>
                            @if ($esPropietario)
                                <input type="number" name="numero" placeholder="Número"
                                class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                                value="{{ old('numero', $guardavida->numero ?? 'No especificado') }}" required/>
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">{{ $guardavida->numero ?? 'No especificado' }}</div>
                            @endif
                        </div>

                        <div class="sm:col-span-2">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-building me-1  text-sky-600"></i>Piso/Dpto</label>
                            @if ($esPropietario)
                                <input type="text" name="piso_dpto" placeholder="Piso/Dpto"
                                class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                                value="{{ old('piso_dpto', $guardavida->piso_dpto ?? '') }}" />
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">{{ $guardavida->piso_dpto ?: '—' }}</div>
                            @endif
                        </div>

                        <!-- Work Information -->

                        <div class="sm:col-span-8 pt-4 mt-3 border-t border-gray-200 dark:border-gray-700 ">
                            <h2 class="text-lg text-gray-700 dark:text-gray-50">
                                <i class="fas fa-briefcase  text-sky-600"></i>
                                Información Laboral
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                La información laboral es administrada exclusivamente por el personal autorizado (Jefe de Playa, Encargado o Administrador).
                            </p>
                        </div>

                        <div class="sm:col-span-4">






                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-umbrella-beach me-1 text-sky-600"></i>Playa</label>
                            @if ($esAdminOEncargado && $esPropietario && $playas)
                                <div class="text-gray-700 dark:text-gray-200">
                                    <select id="selectPlaya" name="playa_id"
                                    class="block w-full rounded-md border bg-gray-100 px-3 py-1.5 text-gray-600 shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-1">
                                        <option value="">Seleccionar balneario</option>
                                        @foreach ($playas as $balneario)
                                            <option value="{{ $balneario->id }}"
                                                {{ $guardavida->playa_id == $balneario->id ? 'selected' : '' }}>
                                                {{ ucfirst($balneario->nombre) }}

                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="info-value text-gray-700  dark:text-gray-200 ">

                                    {{ $guardavida->playa ? $guardavida->playa->nombre : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-flag me-1 text-sky-600"></i>Puesto</label>
                            @if ($esAdminOEncargado && $esPropietario && $puestos)
                                 <div class="text-gray-700 dark:text-gray-200">

                                   <select id="selectPuesto" name="puesto_id"
                                    class="block w-full rounded-md border bg-gray-100 px-3 py-1.5 text-gray-600 shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-1">
                                        <option value="">Seleccionar puesto</option>
                                        @foreach ($puestos as $puesto)
                                            @if ($puesto->playa_id == $guardavida->playa_id)
                                                <option value="{{ $puesto->id }}"
                                                    {{ $guardavida->puesto_id == $puesto->id ? 'selected' : '' }}>
                                                    {{$puesto->nombre }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">

                                    {{ $guardavida->puesto ? $guardavida->puesto->nombre : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-clock me-1 me-1 text-sky-600"></i>Turno actual</label>
                            @if ($esAdminOEncargado && $esPropietario)
                                <div class="text-gray-700 dark:text-gray-200">

                                    <div class="mt-2 flex gap-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="turno" value="M"
                                                {{ old('turno', $guardavida->turno ?? '') == 'M' ? 'checked' : '' }}
                                                class="text-sky-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2">Mañana</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="turno" value="T"
                                                {{ old('turno', $guardavida->turno ?? '') == 'T' ? 'checked' : '' }}
                                                class="text-sky-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2">Tarde</span>
                                        </label>
                                    </div>

                                </div>
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">

                                    {{ $guardavida->turno ? $guardavida->turno : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-tasks me-1 text-sky-600"></i>Función</label>
                            @if ($esPropietario)
                                <div class="text-gray-700 dark:text-gray-200">
                                    <select id="funcion" name="funcion"
                                     class="block w-full rounded-md border bg-gray-100 px-3 py-1.5 text-gray-600 shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-1">
                                        <option value="Guardavida" {{ old('funcion', $guardavida->funcion ) == 'Guardavida' ? 'selected' : '' }}>Guardavida</option>
                                        <option value="Timonel" {{ old('funcion', $guardavida->funcion ) == 'Timonel' ? 'selected' : '' }}>Timonel</option>
                                        <option value="Encargado" {{ old('funcion', $guardavida->funcion ) == 'Encargado' ? 'selected' : '' }}>Encargado</option>
                                        <option value="Jefe_de_playa" {{ old('funcion', $guardavida->funcion ) == 'Jefe_de_playa' ? 'selected' : '' }}>Jefe de playa</option>
                                    </select>
                                </div>
                            @else
                                <div class="info-value text-gray-700 dark:text-gray-200">

                                    {{ str_replace('_', ' ', $guardavida->funcion) }}
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        @if ($esPropietario)
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
                        @else
                            <div class="sm:col-span-8 flex flex-wrap items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                    <i class="fas fa-arrow-left"></i>
                                    Volver
                                </button>
                                @can('editar_guardavida')
                                    <a href="{{ route('guardavida.edit', $guardavida) }}" class="btn btn-primary">
                                        <i class="fas fa-pen me-1"></i>
                                        Editar
                                    </a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>


                @if ($esPropietario)
                  <!-- Franco -->
            <div class="profile-card bg-white rounded-lg shadow-md my-4 px-4 sm:px-10 py-6">
                <h2 class="text-lg text-gray-700 dark:text-gray-50 mb-2">
                    <i class="fas fa-bed"></i>
                    Franco
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                    Elegí tu(s) día(s) libre(s) fijo(s) de todas las semanas. Si una semana puntual necesitás cambiarlo,
                    <a href="{{ route('franco-intercambio.index') }}" class="text-sky-600 hover:underline">pedíselo a un compañero</a> desde "Cambios de Franco".
                </p>

                {{--
                    OJO: esto NO puede ser un <form> acá — ya estamos dentro del
                    <form> grande de "Profile Body" (abre en la línea ~65), y los
                    navegadores no soportan <form> anidados: si esto fuera un
                    <form> propio, el navegador ignora su action/method y termina
                    mandando todo por el formulario externo (bug real que pasó:
                    terminaba haciendo PATCH a la ruta de "mi-profile", que solo
                    acepta PUT). Se resuelve con el atributo form="..." de HTML5,
                    que asocia estos inputs/el botón a un <form> declarado aparte
                    (ver el final del archivo, después de cerrar el form grande),
                    sin importar dónde estén anidados en el HTML.
                --}}
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex flex-wrap gap-4">
                        @foreach (['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $i => $nombreDia)
                            <label class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-200">
                                <input type="checkbox" name="dias_franco[]" value="{{ $i }}" form="francoForm"
                                    {{ in_array($i, old('dias_franco', $guardavida->diasFrancoActuales())) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
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
                @endif

            </form>
            @endif

            @if ($esPropietario)
            <!-- Datos de Usuario (email y contraseña) -->
            <div class="profile-card bg-white rounded-lg shadow-md my-4 px-4 sm:px-10 py-6">
                <h2 class="text-lg text-gray-700 dark:text-gray-50 mb-1">
                    <i class="fas fa-user-lock  text-sky-600"></i>
                    Datos de Usuario
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
Tu email es tu usuario. Verificá que sea correcto para poder iniciar sesión o recuperar tu cuenta.                </p>

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
            @endif

            @if ($guardavida)
                <!-- Statistics -->
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6 bg-white rounded-lg shadow-md my-4 px-4 sm:px-10 py-10">
                    <h2 class="sm:col-span-6 text-lg text-gray-700 dark:text-gray-50">
                        <i class="fas fa-chart-line  text-sky-600"></i>
                        Estadísticas
                    </h2>

                    <div class="sm:col-span-3 stat-card rounded border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-200 p-4">
                        <div class="stat-icon text-end"><i class="fas fa-calendar-check"></i></div>
                        <div class="stat-value">{{ $guardavida->asistencias_count ?? 0 }}</div>
                        <div class="stat-label">Asistencias</div>
                    </div>

                    <div class="sm:col-span-3 stat-card rounded border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-200 p-4">
                        <div class="stat-icon text-end"><i class="fas fa-life-ring"></i></div>
                        <div class="stat-value">{{ $guardavida->intervenciones_count ?? 0 }}</div>
                        <div class="stat-label">Intervenciones</div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="sm:col-span-6 flex flex-wrap items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            <i class="fas fa-arrow-left"></i>
                            Volver
                        </button>
                    </div>
                </div>

                {{-- Form "invisible" del franco (ver comentario más arriba, cerca de "Guardar franco") --}}
                <form id="francoForm" action="{{ route('guardavida.actualizarDiaFranco') }}" method="POST" class="hidden">
                    @csrf
                    @method('PATCH')
                </form>
            @else
                <div class="flex justify-end px-4 sm:px-10 my-4">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                        <i class="fas fa-arrow-left"></i>
                        Volver
                    </button>
                </div>
            @endif



        </main>
    </div>
 {{-- </div> --}}
 <script>
    window.esAdminOEncargado = @json($esAdminOEncargado);
    window.puedeEditar = @json($puedeEditar);
</script>
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
