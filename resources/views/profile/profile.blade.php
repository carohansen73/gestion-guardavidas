<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Guardavidas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/style.css', 'resources/css/perfilGuardavidas.css'])

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    @include('layouts.navigation')
     @include('layouts.sidebar')


    <div class="lg:ml-64 min-h-screen bg-gray-100 dark:bg-gray-800 ">
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
                <div class="profile-header bg-sky-600 py-4
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
                            <h3 class="section-title text-gray-900 dark:text-white">
                                <i class="fas fa-id-card"></i>
                                Información Personal
                            </h3>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800">Nombre</label>
                            @if ($puedeEditar)
                                <div class="info-value text-gray-700 text-gray-700 font-light editable">
                                    <i class="fas fa-user"></i>
                                    <input type="text" name="nombre" class="form-control p-1"
                                        value="{{ $guardavida->nombre }}" required>
                                </div>
                            @else
                                <div class="info-value text-gray-700 text-gray-700 font-light">
                                    <i class="fas fa-user"></i>
                                    {{ $guardavida->nombre }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800">Apellido</label>
                            @if ($puedeEditar)
                                <div class="info-value text-gray-700 text-gray-700 editable">
                                    <i class="fas fa-user"></i>
                                    <input type="text" name="apellido" class="form-control p-1"
                                        value="{{ $guardavida->apellido }}" required>
                                </div>
                            @else
                                <div class="info-value text-gray-700 text-gray-700">
                                    <i class="fas fa-user"></i>
                                    {{ $guardavida->apellido }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-8">
                            <label class="info-label text-gray-800">DNI</label>
                            @if ($puedeEditar)
                                <div class="info-value text-gray-700 text-gray-700 editable">
                                    <i class="fas fa-id-badge"></i>
                                    <input type="text" name="dni" class="form-control p-1"
                                        value="{{ $guardavida->dni }}" required>
                                </div>
                            @else
                                <div class="info-value text-gray-700 text-gray-700">
                                    <i class="fas fa-id-badge"></i>
                                    {{ $guardavida->dni }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800">Teléfono</label>
                            @if ($puedeEditar)
                                <div class="info-value text-gray-700 text-gray-700 editable">
                                    <i class="fas fa-phone"></i>
                                    <input type="tel" name="telefono" class="form-control p-1"
                                        value="{{ $guardavida->telefono ?? 'No especificado' }}">
                                </div>
                            @else
                                <div class="info-value text-gray-700 text-gray-700">
                                    <i class="fas fa-phone"></i>
                                    {{ $guardavida->telefono ?? 'No especificado' }}
                                </div>
                            @endif
                        </div>

                    <div class="sm:col-span-4">
                            <label class="info-label text-gray-800">Email</label>
                            @if ($puedeEditar)
                                <div class="info-value text-gray-700 text-gray-700 editable">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" name="email" class="form-control p-1"
                                        value="{{ $guardavida->user->email ?? 'No especificado' }}">
                                </div>
                            @else
                                <div class="info-value text-gray-700 text-gray-700">
                                    <i class="fas fa-envelope"></i>
                                    {{ $guardavida->user->email ?? 'No especificado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800">Dirección</label>
                            @if ($puedeEditar)
                                <div class="info-value text-gray-700 text-gray-700 editable">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <input type="text" name="direccion" class="form-control p-1"
                                        value="{{ $guardavida->direccion ?? 'No especificado' }}">
                                </div>
                            @else
                                <div class="info-value text-gray-700 text-gray-700">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $guardavida->direccion ?? 'No especificado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-2">
                            <label class="info-label text-gray-800">Número</label>
                            @if ($puedeEditar)
                                <div class="info-value text-gray-700 text-gray-700 editable">
                                    <i class="fas fa-hashtag"></i>
                                    <input type="text" name="numero" class="form-control p-1"
                                        value="{{ $guardavida->numero ?? 'No especificado' }}">
                                </div>
                            @else
                                <div class="info-value text-gray-700 text-gray-700">
                                    <i class="fas fa-hashtag"></i>
                                    {{ $guardavida->numero ?? 'No especificado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-2">
                            <label class="info-label text-gray-800">Piso/Dpto</label>
                            @if ($puedeEditar)
                                <div class="info-value text-gray-700 text-gray-700 editable">
                                    <i class="fas fa-building"></i>
                                    <input type="text" name="piso_dpto" class="form-control p-1"
                                        value="{{ $guardavida->piso_dpto ?? 'No especificado' }}">
                                </div>
                            @else
                                <div class="info-value text-gray-700 text-gray-700">
                                    <i class="fas fa-building"></i>
                                    {{ $guardavida->piso_dpto ?? 'No especificado' }}
                                </div>
                            @endif
                        </div>

                        <!-- Work Information -->

                        <div class="sm:col-span-8 pt-4 mt-3 border-t border-gray-200 dark:border-gray-700 ">
                            <h3 class="section-title text-gray-900 dark:text-white">
                                <i class="fas fa-briefcase"></i>
                                Información Laboral
                            </h3>
                        </div>

                        <div class="sm:col-span-4">

                            <label class="info-label text-gray-800">Playa</label>
                            @if ($esAdmin && $playas)
                                <div class="info-value text-gray-700 editable">
                                    <i class="fas fa-umbrella-beach"></i>
                                    <select id="selectPlaya" name="playa_id" class="form-control p-1">
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
                                <div class="info-value text-gray-700">
                                    <i class="fas fa-umbrella-beach"></i>
                                    {{ $guardavida->playa ? $guardavida->playa->nombre : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800">Puesto</label>
                            @if ($esAdmin && $puestos)
                                 <div class="info-value text-gray-700 editable">
                                    <i class="fas fa-flag"></i>
                                   <select id="selectPuesto" name="puesto_id" class="form-control p-1">
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
                                <div class="info-value text-gray-700">
                                    <i class="fas fa-flag"></i>
                                    {{ $guardavida->puesto ? $guardavida->puesto->nombre : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800">Turno actual</label>
                            @if ($esAdmin)
                                <div class="info-value text-gray-700 editable">
                                    <i class="fas fa-clock"></i>

                                    @foreach ($turnos as $turno)
                                        {{ $guardavida->turno ?? 'No asignado' }}
                                    @endforeach

                                </div>
                            @else
                                <div class="info-value text-gray-700">
                                    <i class="fas fa-clock"></i>
                                    {{ $guardavida->turno ? $guardavida->turno->nombre_turno : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="sm:col-span-4">
                            <label class="info-label text-gray-800">Función</label>
                            <div class="info-value text-gray-700">
                                <i class="fas fa-tasks"></i>
                                @if ($guardavida->funciones && $guardavida->funciones->isNotEmpty())
                                    {{ $guardavida->funciones->first()->nombre_funcion }}
                                @else
                                    Guardavidas
                                @endif
                            </div>
                        </div>


                        <!-- Action Buttons -->

                    </div>
                      @if ($esAdmin)
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
                    @endif
                </div>

                <!-- Statistics -->
                <div class="bg-white rounded-lg shadow-md sm:px-10 md:px-10 pb-12 py-10 px-4">
                    <h3 class="section-title  text-gray-900 dark:text-white">
                        <i class="fas fa-chart-line"></i>
                        Estadísticas
                    </h3>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="stat-value">{{ $guardavida->asistencias_count ?? 0 }}</div>
                            <div class="stat-label">Asistencias</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-life-ring"></i></div>
                            <div class="stat-value">{{ $guardavida->intervenciones_count ?? 0 }}</div>
                            <div class="stat-label">Intervenciones</div>
                        </div>

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
 </div>
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
</body>

</html>
