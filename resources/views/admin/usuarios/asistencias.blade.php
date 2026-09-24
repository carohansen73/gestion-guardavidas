@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Listado de Asistencia del Personal</h5>
            </div>

            <div class="card-body">

                {{-- Filtro por playa: links reales por GET (antes eran
                     botones que filtraban del lado del cliente solo entre
                     las filas ya cargadas en pantalla). --}}
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('asistencias.index', ['playa_id' => 'all'] + request()->except(['page', 'playa_id'])) }}"
                            class="{{ !request('playa_id') || request('playa_id') == 'all' ? 'bg-sky-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200' }} px-3 py-1 rounded hover:opacity-90">
                            Todas
                        </a>
                        @foreach ($playas as $playa)
                            <a href="{{ route('asistencias.index', ['playa_id' => $playa->id] + request()->except(['page', 'playa_id'])) }}"
                                class="{{ request('playa_id') == $playa->id ? 'bg-sky-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200' }} px-3 py-1 rounded hover:opacity-90">
                                {{ $playa->nombre }}
                            </a>
                        @endforeach
                    </div>

                    <x-boton-exportar-asistencia-general />
                </div>

                <form method="GET" class="flex gap-3 items-end flex-wrap mb-3">
                    <input type="hidden" name="playa_id" value="{{ request('playa_id') }}">

                    <div>
                        <label for="search" class="text-sm text-gray-700 dark:text-gray-200">Buscar:</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Nombre o apellido"
                            class="border rounded p-1">
                    </div>

                    <div>
                        <label for="inicio" class="text-sm text-gray-700 dark:text-gray-200">Desde:</label>
                        <input type="date" name="inicio" id="inicio" value="{{ $inicio->toDateString() }}" class="border rounded p-1">
                    </div>

                    <div>
                        <label for="fin" class="text-sm text-gray-700 dark:text-gray-200">Hasta:</label>
                        <input type="date" name="fin" id="fin" value="{{ $fin->toDateString() }}" class="border rounded p-1">
                    </div>

                    <button class="bg-sky-600 hover:bg-sky-500 text-white px-4 py-1 rounded">
                        Filtrar
                    </button>

                    <a href="{{ route('asistencias.index') }}" class="bg-gray-400 hover:bg-gray-300 text-white px-4 py-1 rounded">
                        Mes actual
                    </a>
                </form>

                {{-- /LISTADO DE GUARDAVIDAS --}}
                <div  class="bg-white dark:bg-gray-600 my-2">
                    @foreach ($guardavidas as $g)
                        <ul class="registro-item-lista rounded lista-guardavidas list-group" data-playa="{{ $g->playa->id ?? '' }}">
                            <li class="list-group-item d-flex justify-content-between align-items-center mb-2 shadow-sm rounded registro-item-lista">
                                <div class="info">
                                    <h5 class="mb-1 fw-semibold text-primary">{{ $g->nombre ?? 'Sin nombre' }}</h4>
                                    <p class="mb-0 text-muted small">
                                        {{ $g->updated_at ? $g->updated_at->diffForHumans() : '' }}
                                        · Puesto {{ $g->puesto->nombre ?? 'sin asignar' }}
                                    </p>
                                </div>
                                <div class="acciones">
                                    <a href="{{ route('asistencias.guardavida', $g->id) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        Historial
                                    </a>
                                </div>
                            </li>
                        </ul>
                    @endforeach
                </div>

                {{-- PAGINACIÓN --}}
                @if (method_exists($guardavidas, 'links'))
                    <div class="paginacion mt-4">
                        {{ $guardavidas->links() }}
                    </div>
                @endif

                {{-- VOLVER --}}
                <div class="text-center mt-4">
                    <a class="btn btn-secondary" onclick="window.history.back()">Volver</a>
                </div>

            </div>
        </div>
    </div>
@endsection
