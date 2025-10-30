<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado asistencias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/asistenciasGuardavidas.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <div class="phone">

        {{-- ENCABEZADO --}}
        <header class="header">
            <img src="{{ asset('assets/img/iconos/logo-muni.png') }}" alt="Logo Municipalidad" class="logo">
        </header>

        {{-- FILTROS RÁPIDOS --}}
        <div class="filtros-rapidos">
            <div class="grupo-filtro">
                <span>Playa:</span>
                <button class="btn-filtro activo" data-tipo="balneario" data-valor="todos">Todos</button>
                <button class="btn-filtro" data-tipo="balneario" data-valor="balneario1">Claromeco</button>
                <button class="btn-filtro" data-tipo="balneario" data-valor="balneario2">Reta</button>
                <button class="btn-filtro" data-tipo="balneario" data-valor="balneario2">Orense</button>



            </div>

            <div class="grupo-filtro">
                <span>Puesto:</span>
                <button class="btn-filtro activo" data-tipo="puesto" data-valor="todos">Todos</button>
                <button class="btn-filtro" data-tipo="puesto" data-valor="1">1</button>
                <button class="btn-filtro" data-tipo="puesto" data-valor="2">2</button>
                <button class="btn-filtro" data-tipo="puesto" data-valor="3">3</button>
                <button class="btn-filtro" data-tipo="puesto" data-valor="4">4</button>
                <button class="btn-filtro" data-tipo="puesto" data-valor="5">5</button>
                <button class="btn-filtro" data-tipo="puesto" data-valor="6">6</button>

            </div>

            <div class="grupo-filtro">
                <span>Turno:</span>
                <button class="btn-filtro activo" data-tipo="turno" data-valor="todos">Todos</button>
                <button class="btn-filtro" data-tipo="turno" data-valor="mañana">Mañana</button>
                <button class="btn-filtro" data-tipo="turno" data-valor="tarde">Tarde</button>
            </div>
        </div>


        {{-- TÍTULO --}}
        <h2 class="titulo">Listado asistencia del personal</h2>

        {{-- LISTA DE GUARDAVIDAS  modificar esto bien con como trae la info desde el controller --}}


        <ul class="lista-guardavidas">
            @foreach ($guardavidas as $g)
                <li data-balneario="{{ strtolower($g->puesto->playa->nombre ?? '') }}"
                    data-puesto="{{ strtolower($g->puesto->nombre ?? '') }}" {{--   data-turno="{{ strtolower($g->turnos->nombre_turno ?? '') }}"> --}} <div class="info">
                    <h4>{{ $g->nombre ?? 'Sin nombre' }}</h4>
                    <p>
                        {{ $g->updated_at ? $g->updated_at->diffForHumans() : '' }}
                        · puesto {{ $g->puestos->nombre ?? 'sin asignar' }}
                    </p>
    </div>
    <div class="acciones">
        <a href="{{ route('asistencias.guardavida', $g->id) }}" class="fa fa-user">
            ver Historial
        </a>


    </div>
    </li>
    @endforeach
    </ul>

    @auth
        @if (Auth::user()->hasRole('admin' || 'encargado'))
            {{-- BOTÓN DESCARGAR EXCEL DE ASISTENCIAS ULTIMO MES  --}}
            <div class="excel">
                <a href="{{ route('guardavidas.excel') }}"><i class="fa fa-plus"></i>
                    Descargar asistencias</a>
            </div>
        @endif
    @endauth

    {{-- PAGINACIÓN --}}
    @if (method_exists($guardavidas, 'links'))
        <div class="paginacion">
            {{ $guardavidas->links() }}
        </div>
    @endif

    {{-- VOLVER --}}
    <div class="div-btn-volver">

        <a class="btn-volver" href="{{ url('dashboard') }}">Volver</a>
    </div>

    </div>

    <script src="{{ asset('js/scriptFiltradoGuardavidas.js') }}" defer></script>

</body>

</html>
