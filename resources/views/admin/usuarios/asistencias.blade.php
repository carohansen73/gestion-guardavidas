@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Listado de Asistencia del Personal</h5>
            </div>

            <div class="card-body">

                {{-- REUTILIZO FILTROS --}}
                <x-filtros-de-busqueda :playas="$playas" tipo="asistencia-general" />

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
                    <a class="btn btn-secondary" href="{{ url('dashboard') }}">Volver</a>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('js/table-intervenciones.js') }}"></script>
@endsection
