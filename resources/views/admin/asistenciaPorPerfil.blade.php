<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Guardavidas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('perfilGuardavidas.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="container">
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
        <div class="profile-card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h2 class="profile-name">{{ $guardavida->nombre }} {{ $guardavida->apellido }}</h2>
                <p class="profile-role">Guardavidas Profesional</p>
                @if ($esAdmin)
                    <span class="badge badge-admin">
                        <i class="fas fa-crown"></i> Vista Administrativa
                    </span>
                @endif
                <span class="badge badge-active">
                    <i class="fas fa-check-circle"></i> Activo
                </span>
            </div>

            <!-- Profile Body -->
            <form id="profileForm" class="profile-body">
                @csrf
                @method('PUT')

                <!-- Personal Information -->
                <h3 class="section-title">
                    <i class="fas fa-id-card"></i>
                    Registro de asistencias
                </h3>

                <div class="info-grid">
                    <div class="info-item">
                        <label class="info-label">Puesto asignado</label>

                        <div class="info-value">
                            <i class="fas fa-user"></i>
                            {{ $guardavida->asistencia->puesto_id }}
                        </div>

                    </div>



                    <div class="info-item">
                        <label class="info-label">Fecha de Fichaje</label>

                        <div class="info-value">
                            <i class="fas fa-id-badge"></i>
                            {{ $guardavida->asistencia->fecha }}
                        </div>

                    </div>

                    <div class="info-item">
                        <label class="info-label">Hora de entrada</label>

                        <div class="info-value">
                            <i class="fas fa-phone"></i>
                            {{ $guardavida->asistencia->hora_entrada ?? 'No especificado' }}
                        </div>

                    </div>

                    <div class="info-item">
                        <label class="info-label">Hora de salida</label>

                        <div class="info-value">
                            <i class="fas fa-phone"></i>
                            {{ $guardavida->asistencia->hora_salida ?? 'No especificado' }}
                        </div>

                    </div>






                    <!-- Work Information -->
                    <h3 class="section-title">
                        <i class="fas fa-briefcase"></i>
                        Información Laboral
                    </h3>

                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">Balneario</label>
                            @if ($esAdmin && $balnearios)
                                <div class="info-value editable">
                                    <i class="fas fa-umbrella-beach"></i>
                                    <select name="balneario_id" class="form-control">
                                        <option value="">Seleccionar balneario</option>
                                        @foreach ($balnearios as $balneario)
                                            <option value="{{ $balneario->id }}"
                                                {{ $guardavida->balneario_id == $balneario->id ? 'selected' : '' }}>
                                                {{ ucfirst($balneario->nombre_playa) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="info-value">
                                    <i class="fas fa-umbrella-beach"></i>
                                    {{ $guardavida->balnearios ? ucfirst($guardavida->balnearios->nombre_playa) : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="info-item">
                            <label class="info-label">Puesto</label>
                            @if ($esAdmin && $puestos)
                                <div class="info-value editable">
                                    <i class="fas fa-flag"></i>
                                    <select name="puesto_id" class="form-control">
                                        <option value="">Seleccionar puesto</option>
                                        @foreach ($puestos as $puesto)
                                            <option value="{{ $puesto->id }}"
                                                {{ $guardavida->puesto_id == $puesto->id ? 'selected' : '' }}>
                                                {{ $puesto->nombre_puesto }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="info-value">
                                    <i class="fas fa-flag"></i>
                                    {{ $guardavida->puesto ? $guardavida->puesto->nombre_puesto : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="info-item">
                            <label class="info-label">Turno</label>
                            <div class="info-value">
                                <i class="fas fa-clock"></i>
                                aca va el turno asignado
                                {{--     @if ($guardavida->turnos && $guardavida->turnos->isNotEmpty()) --}}
                                {{--      {{ $guardavida->turnos->first()->nombre_turno }}   --}}
                                {{--   @else
                                    No asignado
                                @endif --}}
                            </div>
                        </div>

                        <div class="info-item">
                            <label class="info-label">Función</label>
                            <div class="info-value">
                                <i class="fas fa-tasks"></i>
                                @if ($guardavida->funcion->isNotEmpty())
                                    {{ $guardavida->first()->funcion }}
                                @else
                                    Guardavidas
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <h3 class="section-title">
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


            </form>
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

    <script src="{{ asset('js/asistenciaDeGuardavida.js') }}" defer></script>
</body>

</html>
