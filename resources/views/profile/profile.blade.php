@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}
@push('styles')
    @vite(['resources/css/perfilGuardavidas.css'])
@endpush

@section('content')

    {{-- <div class="lg:ml-64 min-h-screen bg-gray-100 dark:bg-gray-800 "> --}}
        <main class="overflow-x-hidden pb-5 mx-4">
            <!-- Header -->
            {{-- <div class="header">
                <h1 class="header-title ">
                    <i class="fas fa-user-shield"></i>
                    <span class="puedeEditar">
                        {{ $puedeEditar ? 'Perfil de Guardavidas' : 'Mi Perfil' }}
                    </span>
                </h1>
            </div> --}}

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
                    <div class="flex flex-col mt-3 md:mt-0 md:w-3/4 md:pl-4 md:text-left">

                        <h2 class="profile-name text-white text-xl font-semibold">
                            {{ $guardavida->nombre }} {{ $guardavida->apellido }}
                        </h2>

                        <p class="profile-role text-gray-200">
                            {{ $guardavida->funcion }}
                        </p>

                        <div class="flex gap-2 justify-center md:justify-start mt-2">

                            @if ($esAdmin)
                                <span class="badge badge-admin flex items-center gap-1">
                                    <i class="fas fa-crown"></i> Vista Administrativa
                                </span>
                            @endif

                            <span class="badge badge-active flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> Activo
                            </span>
                        </div>
                    </div>
                </div>


                <!-- Profile Body -->
                <form action="{{ route('guardavida.updateProfile', $guardavida->id) }}" method="POST" class="profile-body"
                    data-guardavida-id="{{ $guardavida->id }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-8 text-gray-600 rounded sm:px-10 md:px-10 pb-12 py-10">

                        <!-- Personal Information -->
                        <div class="sm:col-span-8">
                            <h2 class="text-lg text-gray-700 dark:text-gray-50">
                                <i class="fas fa-id-card"></i>
                                Información Personal
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                En esta sección podés consultar y actualizar tus datos personales.
                                Es importante mantener esta información actualizada para una correcta identificación y comunicación.
                            </p>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-user me-1"></i> Nombre</label>
                            <input id="nombre" type="text" name="nombre" placeholder="Nombre"
                            class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                            value="{{ old('nombre', $guardavida->nombre ?? '') }}" required/>
                            @error('nombre')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-user me-1"></i>Apellido</label>
                            <input  type="text" name="apellido" placeholder="Apellido"
                                class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                                value="{{ old('apellido', $guardavida->apellido ?? '') }}" required/>
                        </div>

                        <div class="sm:col-span-8">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-id-badge me-1"></i>DNI</label>
                           <input  type="number" name="dni" placeholder="DNI"
                            class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                            value="{{ old('dni', $guardavida->dni ?? '') }}" required/>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-phone me-1"></i>Teléfono</label>
                            <input type="tel" name="telefono" placeholder="Teléfono"
                            class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                            value="{{ old('telefono', $guardavida->telefono ?? 'No especificado') }}" required/>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-envelope me-1"></i>Email</label>
                            <input type="email" name="email" placeholder="Email"
                            class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                            value="{{ old('email', $guardavida->user->email ?? 'No especificado') }}" required/>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-map-marker-alt me-1"></i>Dirección</label>
                            <input type="text" name="direccion" placeholder="Dirección"
                            class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                            value="{{ old('direccion', $guardavida->direccion ?? 'No especificado') }}" required/>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-hashtag me-1"></i>Número</label>
                            <input type="number" name="numero" placeholder="Número"
                            class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                            value="{{ old('numero', $guardavida->numero ?? 'No especificado') }}" required/>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-building me-1"></i>Piso/Dpto</label>
                            <input type="text" name="piso_dpto" placeholder="Piso/Dpto"
                            class="block w-full bg-gray-100 rounded-md border px-3 py-1.5 text-gray-600 shadow-sm focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-2"
                            value="{{ old('piso_dpto', $guardavida->piso_dpto ?? '') }}" />
                        </div>

                        <!-- Work Information -->

                        <div class="sm:col-span-8 pt-4 mt-3 border-t border-gray-200 dark:border-gray-700 ">
                            <h2 class="text-lg text-gray-700 dark:text-gray-50">
                                <i class="fas fa-briefcase"></i>
                                Información Laboral
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                La información laboral es administrada exclusivamente por el personal autorizado (Jefe de Playa, Encargado o Administrador).
                            </p>
                        </div>

                        <div class="sm:col-span-4">






                            <label class="info-label text-gray-800 dark:text-gray-100"><i class="fas fa-umbrella-beach me-1 text-sky-600"></i>Playa</label>
                            @if ($esAdmin && $playas)
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
                                    <i class="fas fa-umbrella-beach"></i>
                                    {{ $guardavida->playa ? $guardavida->playa->nombre : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-flag me-1 text-sky-600"></i>Puesto</label>
                            @if ($esAdmin && $puestos)
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
                                    <i class="fas fa-flag"></i>
                                    {{ $guardavida->puesto ? $guardavida->puesto->nombre : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-clock me-1 me-1 text-sky-600"></i>Turno actual</label>
                            @if ($esAdmin)
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
                                    <i class="fas fa-clock"></i>
                                    {{ $guardavida->turno ? $guardavida->turno : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800 dark:text-gray-100"> <i class="fas fa-tasks me-1 text-sky-600"></i>Función</label>
                            <div class="text-gray-700 dark:text-gray-200">
                                <select id="funcion" name="funcion"
                                 class="block w-full rounded-md border bg-gray-100 px-3 py-1.5 text-gray-600 shadow-sm outline outline-1 outline-gray-300 focus:outline-sky-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500 mt-1">
                                    <option value="Guardavida" {{ old('funcion', $guardavida->funcion ) == 'Guardavida' ? 'selected' : '' }}>Guardavida</option>
                                    <option value="Timonel" {{ old('funcion', $guardavida->funcion ) == 'Timonel' ? 'selected' : '' }}>Timonel</option>
                                    <option value="Encargado" {{ old('funcion', $guardavida->funcion ) == 'Encargado' ? 'selected' : '' }}>Encargado</option>
                                    <option value="Jefe_de_playa" {{ old('funcion', $guardavida->funcion ) == 'Jefe_de_playa' ? 'selected' : '' }}>Jefe de playa</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                    </div>
                      {{-- @if ($esAdmin) --}}
                    <div class="action-buttons mb-4">
                        <button type="submit" class="btn btn-primary me-2 mb-2" id="btnGuardar">
                            <i class="fas fa-save me-1"></i>
                            Guardar Cambios
                        </button>
                        <button type="button" class="btn text-white bg-gray-500 hover:bg-gray-400 mb-2" onclick="window.history.back()">
                            <i class="fas fa-times me-1"></i>
                            Cancelar
                        </button>
                    </div>
                    {{-- @endif --}}
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6 bg-white rounded-lg shadow-md sm:px-10 md:px-10 pb-12 py-10 px-4">
                    <h2 class="sm:col-span-6 text-lg text-gray-700 dark:text-gray-50">
                        <i class="fas fa-chart-line"></i>
                        Estadísticas
                    </h2>

                    <div class="sm:col-span-3 stat-card rounded border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-200 p-4 m-2">
                        <div class="stat-icon text-end"><i class="fas fa-calendar-check"></i></div>
                        <div class="stat-value">{{ $guardavida->asistencias_count ?? 0 }}</div>
                        <div class="stat-label">Asistencias</div>
                    </div>

                  <div class="sm:col-span-3 stat-card rounded border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-200 p-4 m-2">
                        <div class="stat-icon text-end"><i class="fas fa-life-ring"></i></div>
                        <div class="stat-value">{{ $guardavida->intervenciones_count ?? 0 }}</div>
                        <div class="stat-label">Intervenciones</div>
                    </div>

                </div>
                <!-- Action Buttons -->

                    <div class="action-buttons">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            <i class="fas fa-arrow-left"></i>
                            Volver
                        </button>
                    </div>

            </form>

        </main>
    </div>
 {{-- </div> --}}
 <script>
    window.esAdmin = @json($esAdmin);
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
