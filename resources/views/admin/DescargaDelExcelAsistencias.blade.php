@extends('layouts.dashboard')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Exportar Asistencias por Rango de Fechas</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('asistencias.exportDia') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3">
                            <label for="fecha_inicio" class="form-label">Desde</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="fecha_fin" class="form-label">Hasta</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-file-earmark-excel"></i> Descargar Excel
                            </button>
                        </div>
                    </div>
                </form>

                @if (session('error'))
                    <div class="alert alert-danger mt-3">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
