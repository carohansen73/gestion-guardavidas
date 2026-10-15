@extends('layouts.app')

{{-- Ficha de solo lectura de un guardavidas (admin / encargado). La edición propia vive en
     guardavidas/perfil.blade.php (/profile) y la de otros en guardavida.edit. --}}
@php
    $perfilUser = $guardavida->user;
    $fotoPerfil = $guardavida->fotoPersonal();
@endphp

@section('content')

<button type="button" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-sky-100 dark:hover:text-gray-300" onclick="window.history.back()">
    <i class="fas fa-arrow-left"></i>
    <span class=" hover:underline">Volver</span>
</button>

    <main class="overflow-x-hidden">

        <!-- HEADER -->
        <div class="glass relative rounded-3xl bg-white my-4 py-5 px-5 flex flex-col items-center text-center
                md:flex-row md:items-center md:text-left md:justify-start">

            <!-- Avatar -->
            <div class="w-32 h-32 flex items-center justify-center shrink-0 rounded-full bg-sky-600 text-white text-6xl overflow-hidden">
                @if ($fotoPerfil)
                    <img src="{{ route('guardavida.foto', $guardavida) }}?v={{ $fotoPerfil->id }}"
                        alt="Foto de {{ $guardavida->nombre }} {{ $guardavida->apellido }}"
                        class="w-full h-full object-cover">
                @else
                    <i class="fas fa-user"></i>
                @endif
            </div>

            <!-- Info del perfil -->
            <div class="flex flex-col items-center md:items-start mt-3 md:mt-0 md:w-3/4 md:pl-4">

                <div class="flex flex-wrap items-center justify-center gap-2 md:justify-start">
                    <x-role-badge :rol="\App\Enums\RolUsuario::principal($perfilUser)" size="w-3.5 h-3.5" />

                    <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-semibold text-white {{ $perfilUser->enabled ? 'bg-sky-400 dark:bg-sky-500' : 'bg-red-500 dark:bg-red-600' }}">
                        <i class="fas {{ $perfilUser->enabled ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                        {{ $perfilUser->enabled ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <h2 class="text-gray-700 dark:text-gray-200 text-4xl font-semibold mt-2">
                    {{ $guardavida->nombre }} {{ $guardavida->apellido }}
                </h2>

                <p class="text-base font-medium text-gray-600 dark:text-gray-300 flex items-center gap-1.5 mt-1">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ $guardavida->playa?->nombre ?? 'Sin playa asignada' }}
                    @if ($guardavida->puesto)
                        · {{ $guardavida->puesto->nombre }}
                    @endif
                </p>

                <p class="text-xs text-gray-600/80 dark:text-gray-300/80 mt-2 flex flex-wrap items-center justify-center gap-1.5">
                    <span><i class="fa-regular fa-envelope"></i> {{ $perfilUser->email }}</span>
                    ·
                    <span><i class="fa-solid fa-phone"></i>  {{ $guardavida->telefono ?? 'No especificado' }}</span>
                </p>

            </div>

            {{-- Editar --}}
            @can('editar_guardavida')
                <a href="{{ route('guardavida.edit', $guardavida) }}"
                    class="mt-4 inline-flex items-center justify-center gap-2 rounded-md bg-sky-600 px-5 py-2 text-sm font-medium text-white shadow hover:bg-sky-500 dark:bg-sky-700 dark:text-white dark:hover:bg-sky-600 md:absolute md:right-5 md:top-5 md:mt-0">
                    <i class="fas fa-pen"></i>
                    Editar
                </a>
            @endcan
        </div>


        <!-- Grid layout -->
        <div class="grid items-start gap-5 lg:grid-cols-3">

            <div class="lg:col-span-2">

                <!-- Card Info Personal -->
                <section class="glass bg-white rounded-3xl grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-8 text-gray-600 dark:text-gray-300 px-5 py-6 my-4">

                    <div class="sm:col-span-8">
                        <h2 class="text-lg text-gray-700 dark:text-gray-50">
                            <i class="fas fa-id-card text-sky-600"></i>
                            Información Personal
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Es importante mantener esta información actualizada para una correcta identificación y comunicación.
                        </p>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-user me-1 text-sky-600"></i> Nombre</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $guardavida->nombre }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-user me-1 text-sky-600"></i> Apellido</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $guardavida->apellido }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-id-badge me-1 text-sky-600"></i> DNI</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $guardavida->dni }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-id-badge me-1 text-sky-600"></i> Edad</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">
                            {{ $perfil?->fecha_nacimiento ? $perfil->fecha_nacimiento->age.' años' : 'No especificada' }}
                        </div>
                    </div>

                    {{-- Email y teléfono también están en el encabezado; se repiten acá como ficha completa. --}}
                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-id-badge me-1 text-sky-600"></i> Email</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $perfilUser->email }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-phone me-1 text-sky-600"></i> Teléfono</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $guardavida->telefono ?? 'No especificado' }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-id-badge me-1 text-sky-600"></i> Género</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $perfil?->genero ?: 'No especificado' }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-map-marker-alt me-1 text-sky-600"></i> Dirección</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $guardavida->direccion ?: 'No especificado' }} {{ $guardavida->numero ?: '' }} {{ $guardavida->piso_dpto ?: '' }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-id-badge me-1 text-sky-600"></i> Libreta de guardavidas</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $perfil?->numero_libreta ?: '-' }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-map-marker-alt me-1 text-sky-600"></i> Grupo sanguíneo</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $perfil?->grupo_sanguineo ?: 'No especificado' }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-id-badge me-1 text-sky-600"></i> Obra social</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $perfil?->obra_social_nombre ?: '-' }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-map-marker-alt me-1 text-sky-600"></i> Nº de afiliado</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $perfil?->obra_social_numero_afiliado ?: '-' }}</div>
                    </div>

                </section>


                {{-- Card Info Laboral --}}
                <section class="glass bg-white rounded-3xl grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-8 text-gray-600 dark:text-gray-300 px-5 py-6 my-4">

                    <div class="sm:col-span-8">
                        <h2 class="text-lg text-gray-700 dark:text-gray-50">
                            <i class="fas fa-briefcase text-sky-600"></i>
                            Información Laboral
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            La información laboral es administrada exclusivamente por el personal autorizado (Jefe de Playa, Encargado o Administrador).
                        </p>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-umbrella-beach me-1 text-sky-600"></i> Playa</label>
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $guardavida->playa?->nombre ?? 'No asignado' }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-flag me-1 text-sky-600"></i> Puesto</label>
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $guardavida->puesto?->nombre ?? 'No asignado' }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-clock me-1 text-sky-600"></i> Turno actual</label>
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $guardavida->turno ?: 'No asignado' }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-tasks me-1 text-sky-600"></i> Función</label>
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ str_replace('_', ' ', $guardavida->funcion) }}</div>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="text-xs text-gray-800 dark:text-gray-100"><i class="fas fa-map-marker-alt me-1 text-sky-600"></i> Día/s de franco</label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $francoTexto ?? 'Sin franco configurado' }}</div>
                    </div>

                </section>


                {{-- Card Estadísticas --}}
                <section class="glass bg-white rounded-3xl text-gray-600 dark:text-gray-300 px-5 py-6 my-4">
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
                </section>
            </div>{{-- /columna izquierda --}}


            {{-- Layout aside --}}
            <div class="lg:col-span-1">

                {{-- Card Función y rol --}}
                <aside class="glass bg-white rounded-3xl text-gray-600 dark:text-gray-300 px-5 py-6 my-4">
                    <h2 class="text-lg text-gray-700 dark:text-gray-50">
                        <i class="fas fa-briefcase text-sky-600"></i>
                        Función y rol
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        La función indica el puesto real del guardavidas y el rol define sus permisos en el sistema, independientemente de su función.
                    </p>

                    <div class="pt-6">
                        <label class="text-xs text-gray-800 dark:text-gray-100">
                            <i class="fas fa-user me-1 text-sky-600"></i> Función
                        </label>
                        <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ str_replace('_', ' ', $guardavida->funcion) }}</div>
                    </div>
                    <div class="pt-6">
                        <label class="text-xs text-gray-800 dark:text-gray-100">
                            <i class="fas fa-user me-1 text-sky-600"></i> Rol
                        </label>
                        <div class="mt-1">
                            <x-role-badge :rol="\App\Enums\RolUsuario::principal($perfilUser)" size="w-3.5 h-3.5" />
                        </div>
                    </div>
                </aside>


                {{-- Postulación --}}
                <aside class="glass bg-white rounded-3xl grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-8 text-gray-600 dark:text-gray-300 px-5 py-6 my-4">
                    <div class="sm:col-span-8">
                        <h2 class="text-lg text-gray-700 dark:text-gray-50">
                            <i class="fas fa-briefcase text-sky-600"></i>
                            Postulación
                        </h2>
                    </div>

                    @if (! $ultimaPostulacion)
                        <p class="sm:col-span-8 text-sm text-gray-500 dark:text-gray-400">No tiene postulaciones cargadas.</p>
                    @else
                        <div class="sm:col-span-4">
                            <label class="text-xs text-gray-800 dark:text-gray-100">
                                <i class="fas fa-calendar me-1 text-sky-600"></i> Temporada
                            </label>
                            <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">
                                {{ $ultimaPostulacion->temporada?->nombre ?: '-' }}
                            </div>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="text-xs text-gray-800 dark:text-gray-100">
                                <i class="fas fa-clipboard-check me-1 text-sky-600"></i> Estado
                            </label>
                            <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">
                                {{ ucfirst($ultimaPostulacion->estado) }}
                                @if ($ultimaPostulacion->seleccionado)
                                    · <span class="text-green-600 dark:text-green-400">Seleccionado</span>
                                @endif
                            </div>
                        </div>

                        <div class="sm:col-span-8">
                            <label class="text-xs text-gray-800 dark:text-gray-100">
                                <i class="fas fa-calendar-days me-1 text-sky-600"></i> Disponibilidad
                            </label>
                            <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">
                                @if ($ultimaPostulacion->disponible_desde && $ultimaPostulacion->disponible_hasta)
                                    Del {{ $ultimaPostulacion->disponible_desde->format('d/m/Y') }}
                                    al {{ $ultimaPostulacion->disponible_hasta->format('d/m/Y') }}
                                @elseif ($ultimaPostulacion->disponible_desde)
                                    Desde el {{ $ultimaPostulacion->disponible_desde->format('d/m/Y') }}
                                @elseif ($ultimaPostulacion->disponible_hasta)
                                    Hasta el {{ $ultimaPostulacion->disponible_hasta->format('d/m/Y') }}
                                @else
                                    No especificada
                                @endif
                            </div>
                        </div>

                        @if ($ultimaPostulacion->enviada_at)
                            <div class="sm:col-span-4">
                                <label class="text-xs text-gray-800 dark:text-gray-100">
                                    <i class="fas fa-paper-plane me-1 text-sky-600"></i> Enviada
                                </label>
                                <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">
                                    {{ $ultimaPostulacion->enviada_at->format('d/m/Y') }}
                                </div>
                            </div>
                        @endif

                        @if ($ultimaPostulacion->fecha_revision)
                            <div class="sm:col-span-4">
                                <label class="text-xs text-gray-800 dark:text-gray-100">
                                    <i class="fas fa-user-check me-1 text-sky-600"></i> Revisada
                                </label>
                                <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">
                                    {{ \Illuminate\Support\Carbon::parse($ultimaPostulacion->fecha_revision)->format('d/m/Y') }}
                                </div>
                            </div>
                        @endif

                        @if (filled($ultimaPostulacion->observaciones))
                            <div class="sm:col-span-8">
                                <label class="text-xs text-gray-800 dark:text-gray-100">
                                    <i class="fas fa-comment-dots me-1 text-sky-600"></i> Observaciones
                                </label>
                                <div class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line">{{ $ultimaPostulacion->observaciones }}</div>
                            </div>
                        @endif
                    @endif
                </aside>


                {{-- Talles --}}
                <aside class="glass bg-white rounded-3xl grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-8 text-gray-600 dark:text-gray-300 px-5 py-6 my-4">
                    <div class="sm:col-span-8">
                        <h2 class="text-lg text-gray-700 dark:text-gray-50">
                            <i class="fas fa-shirt text-sky-600"></i>
                            Talles de ropa
                        </h2>
                    </div>

                    @foreach ([
                        'Remera' => $perfil?->talle_remera,
                        'Pantalón' => $perfil?->talle_pantalon,
                        'Campera' => $perfil?->talle_campera,
                        'Traje de baño' => $perfil?->talle_traje_bano,
                    ] as $prenda => $talle)
                        <div class="sm:col-span-4">
                            <label class="text-xs text-gray-800 dark:text-gray-100">
                                <i class="fas fa-tag me-1 text-sky-600"></i> {{ $prenda }}
                            </label>
                            <div class="text-sm text-gray-700 font-semibold dark:text-gray-200">{{ $talle ?: '-' }}</div>
                        </div>
                    @endforeach
                </aside>


                {{-- Documentación (se abre desde la postulación; requiere el permiso ver_postulacion) --}}
                @if ($puedeVerDocumentos)
                    <aside class="glass bg-white rounded-3xl text-gray-600 dark:text-gray-300 px-5 py-6 my-4">
                        <h2 class="text-lg text-gray-700 dark:text-gray-50">
                            <i class="fas fa-folder-open text-sky-600"></i>
                            Documentación
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            @if ($postulacionDocs?->temporada)
                                Postulación {{ $postulacionDocs->temporada->nombre }}.
                            @else
                                Último archivo cargado de cada tipo.
                            @endif
                        </p>

                        @forelse ($documentos as $documento)
                            <a href="{{ route('postulaciones.documento', [$documento->postulacion_id, $documento->tipo]) }}"
                                target="_blank" rel="noopener"
                                class="flex items-center justify-between gap-3 mt-4 pb-3 border-b border-gray-200 dark:border-gray-700 hover:text-sky-600">
                                <span class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    <i class="fas {{ str_contains((string) $documento->mime, 'pdf') ? 'fa-file-pdf' : 'fa-file-image' }} text-sky-600"></i>
                                    {{ $documento->label }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    @if ($documento->postulacion_id !== $ultimaPostulacionDocs)
                                        {{ $documento->postulacion?->temporada?->nombre }}
                                    @endif
                                    <i class="fas fa-up-right-from-square ml-1"></i>
                                </span>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">No cargó documentación.</p>
                        @endforelse
                    </aside>
                @endif

            </div>{{-- /aside --}}
        </div>{{-- /grid --}}

    </main>
@endsection
