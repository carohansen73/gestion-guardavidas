@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Listado de Asistencia del Personal</h5>
            </div>

            <div class="card-body">

                {{-- FILTROS RÁPIDOS --}}
                <div class="filtros-rapidos mb-4">
                    <div class="grupo-filtro">
                        <span>Playa:</span>
                        <button class="btn-filtro activo" data-tipo="balneario" data-valor="todos">Todos</button>
                        <button class="btn-filtro" data-tipo="balneario" data-valor="claromeco">Claromecó</button>
                        <button class="btn-filtro" data-tipo="balneario" data-valor="reta">Reta</button>
                        <button class="btn-filtro" data-tipo="balneario" data-valor="orense">Orense</button>
                    </div>

                    <div class="grupo-filtro">
                        <span>Puesto:</span>
                        <button class="btn-filtro activo" data-tipo="puesto" data-valor="todos">Todos</button>
                        @for ($i = 1; $i <= 6; $i++)
                            <button class="btn-filtro" data-tipo="puesto"
                                data-valor="{{ $i }}">{{ $i }}</button>
                        @endfor
                    </div>

                    <div class="grupo-filtro">
                        <span>Turno:</span>
                        <button class="btn-filtro activo" data-tipo="turno" data-valor="todos">Todos</button>
                        <button class="btn-filtro" data-tipo="turno" data-valor="mañana">Mañana</button>
                        <button class="btn-filtro" data-tipo="turno" data-valor="tarde">Tarde</button>
                    </div>
                </div>

                {{-- LISTADO DE GUARDAVIDAS --}}
                <ul class="lista-guardavidas">
                    @foreach ($guardavidas as $g)
                        <li data-balneario="{{ strtolower($g->puesto->playa->nombre ?? '') }}"
                            data-puesto="{{ strtolower($g->puesto->nombre ?? '') }}">
                            <div class="info">
                                <h4>{{ $g->nombre ?? 'Sin nombre' }}</h4>
                                <p>
                                    {{ $g->updated_at ? $g->updated_at->diffForHumans() : '' }}
                                    · Puesto {{ $g->puestos->nombre ?? 'sin asignar' }}
                                </p>
                            </div>
                            <div class="acciones">
                                <a href="{{ route('asistencias.guardavida', $g->id) }}" class="fa fa-user">
                                    Ver Historial
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- DESCARGAR EXCEL --}}
                <div class="mt-4 text-end">
                    <a href="{{ route('guardavidas.excel') }}" class="btn btn-success">
                        <i class="fa fa-file-excel"></i> Descargar Asistencias
                    </a>
                </div>

                {{-- PAGINACIÓN --}}
                @if (method_exists($guardavidas, 'links'))
                    <div class="paginacion mt-4">
                        {{ $guardavidas->links() }}
                    </div>
                @endif

                {{-- VOLVER --}}
                <div class="text-center mt-4">
                    <a class="btn btn-secondary" href="{{ url('dashboard') }}">Volver</a>
                </div>

            </div>
        </div>
    </div>
@endsection
