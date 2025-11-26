<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Guardavidas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/style.css', 'resources/css/perfilGuardavidas.css'])

    <script src="{{ asset('js/historialLicencias.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
     @include('layouts.navigation')
     @include('layouts.sidebar')


    <div class="lg:ml-64 min-h-screen bg-gray-100 dark:bg-gray-800 ">
        <main class="overflow-x-hidden pb-5 mx-4">

        <!-- Header -->
        <div class="header">
            <button onclick="window.history.back()" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Volver
            </button>
            <h1 class="header-title">
                <i class="fas fa-user-shield"></i>
                {{ $esAdmin ? 'Perfil asistencia del Personal' : 'Mis asistencias' }}
            </h1>
        </div>

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
                <!-- Profile Header end-->






            <!-- Profile Body -->

            <form id="profileForm" class="profile-body">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6 text-gray-600 rounded sm:px-10 md:px-10 pb-12 py-10">




                    <!-- Personal Information -->
                    <div class="sm:col-span-6">
                        <h3 class="section-title">
                            <i class="fas fa-id-card"></i>
                            Registro de asistencias
                        </h3>
                    </div>

                    <!--  Listado de asistencias -->
                    @if ($guardavida->asistencias->isNotEmpty())
                        <div class="info-grid">
                            @foreach ($guardavida->asistencias as $asistencia)
                                <div class="info-item">
                                    <label class="info-label">Puesto asignado</label>
                                    <div class="info-value">
                                        <i class="fas fa-flag"></i>
                                        {{ $asistencia->puesto->puesto_id ?? 'No asignado' }}
                                    </div>
                                </div>


                                <div class="info-item">
                                    <label class="info-label">Playa</label>
                                    <div class="info-value">
                                        <i class="fas fa-umbrella-beach"></i>
                                        {{ $asistencia->puesto->playa->nombre ?? 'No especificado' }}
                                    </div>
                                </div>

                                <div class="info-item">
                                    <label class="info-label">Fecha</label>
                                    <div class="info-value">
                                        <i class="fas fa-calendar"></i>
                                        {{ $asistencia->fecha ?? 'No se registra asistencia en esta fecha' }}
                                    </div>
                                </div>

                                <div class="info-item">
                                    <label class="info-label">Hora de entrada</label>
                                    <div class="info-value">
                                        <i class="fas fa-clock"></i>
                                        {{ $asistencia->hora_entrada ?? 'No especificado' }}
                                    </div>
                                </div>

                                <div class="info-item">
                                    <label class="info-label">Hora de salida</label>
                                    <div class="info-value">
                                        <i class="fas fa-clock"></i>
                                        {{ $asistencia->hora_salida ?? 'No especificado' }}
                                    </div>
                                </div>

                                <hr class="separator">
                            @endforeach
                        </div>
                    @else
                        <p class="no-data">No hay asistencias registradas.</p>
                    @endif





                    <!-- Work Information -->
                    <div class="sm:col-span-6">
                        <h3 class="section-title">
                            <i class="fas fa-briefcase"></i>
                            Información Laboral
                        </h3>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="info-label">Playa</label>
                        <div class="info-value">
                            <i class="fas fa-umbrella-beach me-1"></i>
                            {{ $guardavida->playa->nombre ?? 'No asignado' }}
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="info-label">Puesto</label>
                        <div class="info-value">
                            <i class="fas fa-flag"></i>
                            {{ $guardavida->puesto->nombre ?? 'No asignado' }}
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="info-label">Función</label>
                        <div class="info-value">
                            <i class="fas fa-tasks"></i>
                            {{ $guardavida->funcion ?? 'Guardavidas' }}
                        </div>
                    </div>


                <div class="sm:col-span-6">
                    <h3 class="section-title">
                        <i class="fas fa-chart-line"></i> Estadísticas
                    </h3>
                </div>

                <div class="sm:col-span-2">
                    <div class="stat-card ">
                        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                        <div class="stat-value">{{ $guardavida->asistencias_count }}</div>
                        <div class="stat-label">Asistencias</div>
                    </div>
                </div>
                    <div class="stat-card sm:col-span-2">
                        <div class="stat-icon"><i class="fas fa-life-ring"></i></div>
                        <div class="stat-value">{{ $guardavida->intervenciones_count }}</div>
                        <div class="stat-label">Intervenciones</div>
                    </div>

                     <div class="stat-card sm:col-span-2">
                        <div class="stat-icon"><i class="fas fa-life-ring"></i></div>
                        <div class="stat-value">{{ $guardavida->licencias_count }}</div>
                        <div class="stat-label">Licencias


                            <a href="#" id="verLicenciasBtn"> ver historial</a>
                        </div>
                    </div>


                    </div>
                </div>
            </form>
        </div>
    </div>

    <!--popap para ver historial de licencias -->
    <!-- Esto: Carga todas las licencias del guardavida.
               El script de paginación las divide en páginas de 4 ítems (itemsPorPagina = 4).
               Mantiene los links a los archivos si existen.
-->
    <div id="modal-licencias" class="modal-licencias">
        <div class="modal-content">
            <h3>Historial de licencias</h3>

            <!-- Contenedor scrollable -->
            <div class="lista-container">
                <ul class="lista-licencias" id="lista-licencias">
                    <!-- aca traigo el listado de las licencias existentes  -->
                    @forelse ($guardavida->licencias as $licencia)
                        <li>
                            <div class="licencia-info">
                                <strong>{{ $licencia->tipo_licencia }}</strong><br>
                                <span>
                                    <i class="fas fa-calendar"></i>
                                    {{ $licencia->fecha_inicio->format('d/m/Y') }}
                                    - {{ $licencia->fecha_fin->format('d/m/Y') }}
                                </span><br>
                                <small>
                                    {{ $licencia->detalle ?? 'Sin detalles adicionales.' }}
                                </small>
                                @if ($licencia->archivo)
                                    <br><a href="{{ $licencia->archivo_url }}" target="_blank">
                                        <i class="fas fa-file-download"></i> Ver archivo
                                    </a>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="no-data">No hay licencias registradas.</li>
                    @endforelse

                </ul>
            </div>

            <!-- Paginación -->
            <div class="paginacion">
                <button class="pag-btn" id="prev-page">&lt;</button>
                <span id="page-num">1</span>
                <button class="pag-btn" id="next-page">&gt;</button>
            </div>

            <button id="cerrar-modal">Cerrar</button>
        </div>
    </div>




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


</body>

</html>
