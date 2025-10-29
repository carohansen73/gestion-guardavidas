<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Guardavidas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/perfilGuardavidas.css') }}">
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
                {{ $puedeEditar ? 'Perfil de Guardavidas' : 'Mi Perfil' }}
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
            <form action="{{ route('guardavida.updateProfile', $guardavida) }}" method="POST" class="profile-body">

                <!-- Personal Information -->
                <h3 class="section-title">
                    <i class="fas fa-id-card"></i>
                    Información Personal
                </h3>

                <div class="info-grid">
                    <div class="info-item">
                        <label class="info-label">Nombre</label>
                        @if ($puedeEditar)
                            <div class="info-value editable">
                                <i class="fas fa-user"></i>
                                <input type="text" name="nombre" class="form-control"
                                    value="{{ $guardavida->nombre }}" required>
                            </div>
                        @else
                            <div class="info-value">
                                <i class="fas fa-user"></i>
                                {{ $guardavida->nombre }}
                            </div>
                        @endif
                    </div>

                    <div class="info-item">
                        <label class="info-label">Apellido</label>
                        @if ($puedeEditar)
                            <div class="info-value editable">
                                <i class="fas fa-user"></i>
                                <input type="text" name="apellido" class="form-control"
                                    value="{{ $guardavida->apellido }}" required>
                            </div>
                        @else
                            <div class="info-value">
                                <i class="fas fa-user"></i>
                                {{ $guardavida->apellido }}
                            </div>
                        @endif
                    </div>

                    <div class="info-item">
                        <label class="info-label">DNI</label>
                        @if ($puedeEditar)
                            <div class="info-value editable">
                                <i class="fas fa-id-badge"></i>
                                <input type="text" name="dni" class="form-control"
                                    value="{{ $guardavida->dni }}" required>
                            </div>
                        @else
                            <div class="info-value">
                                <i class="fas fa-id-badge"></i>
                                {{ $guardavida->dni }}
                            </div>
                        @endif
                    </div>

                    <div class="info-item">
                        <label class="info-label">Teléfono</label>
                        @if ($puedeEditar)
                            <div class="info-value editable">
                                <i class="fas fa-phone"></i>
                                <input type="tel" name="telefono" class="form-control"
                                    value="{{ $guardavida->telefono ?? 'No especificado' }}">
                            </div>
                        @else
                            <div class="info-value">
                                <i class="fas fa-phone"></i>
                                {{ $guardavida->telefono ?? 'No especificado' }}
                            </div>
                        @endif
                    </div>

                    <div class="info-item">
                        <label class="info-label">Email</label>
                        @if ($puedeEditar)
                            <div class="info-value editable">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" class="form-control"
                                    value="{{ $guardavida->user->email ?? 'No especificado' }}">
                            </div>
                        @else
                            <div class="info-value">
                                <i class="fas fa-envelope"></i>
                                {{ $guardavida->user->email ?? 'No especificado' }}
                            </div>
                        @endif
                    </div>


                    <div class="info-item">
                        <label class="info-label">Dirección</label>
                        @if ($puedeEditar)
                            <div class="info-value editable">
                                <i class="fas fa-map-marker-alt"></i>
                                <input type="text" name="direccion" class="form-control"
                                    value="{{ $guardavida->direccion ?? 'No especificado' }}">
                            </div>
                        @else
                            <div class="info-value">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $guardavida->direccion ?? 'No especificado' }}
                            </div>
                        @endif
                    </div>


                    <div class="info-item">
                        <label class="info-label">Número</label>
                        @if ($puedeEditar)
                            <div class="info-value editable">
                                <i class="fas fa-hashtag"></i>
                                <input type="text" name="numero" class="form-control"
                                    value="{{ $guardavida->numero ?? 'No especificado' }}">
                            </div>
                        @else
                            <div class="info-value">
                                <i class="fas fa-hashtag"></i>
                                {{ $guardavida->numero ?? 'No especificado' }}
                            </div>
                        @endif
                    </div>

                    <div class="info-item">
                        <label class="info-label">Piso/Dpto</label>
                        @if ($puedeEditar)
                            <div class="info-value editable">
                                <i class="fas fa-building"></i>
                                <input type="text" name="piso_dpto" class="form-control"
                                    value="{{ $guardavida->piso_dpto ?? 'No especificado' }}">
                            </div>
                        @else
                            <div class="info-value">
                                <i class="fas fa-building"></i>
                                {{ $guardavida->piso_dpto ?? 'No especificado' }}
                            </div>
                        @endif
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
                                    {{ $guardavida->playa ? $guardavida->playa->nombre : 'No asignado' }}
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
                                    {{ $guardavida->puesto ? $guardavida->puesto->nombre : 'No asignado' }}
                                </div>
                            @endif
                        </div>

                        <div class="info-item">
                            <label class="info-label">Turno</label>
                            <div class="info-value">
                                <i class="fas fa-clock"></i>
                                @if ($guardavida->turnos && $guardavida->turnos->isNotEmpty())
                                    {{ $guardavida->turnos->first()->nombre_turno }}
                                @else
                                    No asignado
                                @endif
                            </div>
                        </div>

                        <div class="info-item">
                            <label class="info-label">Función</label>
                            <div class="info-value">
                                <i class="fas fa-tasks"></i>
                                @if ($guardavida->funciones && $guardavida->funciones->isNotEmpty())
                                    {{ $guardavida->funciones->first()->nombre_funcion }}
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

                    <!-- Action Buttons -->
                    @if ($puedeEditar)
                        <div class="action-buttons">
                            <button type="submit" class="btn btn-primary" id="btnGuardar">
                                <i class="fas fa-save"></i>
                                Guardar Cambios
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </button>
                        </div>
                    @else
                        <div class="action-buttons">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i>
                                Volver
                            </button>
                        </div>
                    @endif
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

    <script src="{{ asset('js/perfilGuardavidas.js') }}" defer></script>
</body>

</html>
