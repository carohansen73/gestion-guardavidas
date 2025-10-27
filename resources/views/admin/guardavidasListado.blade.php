<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Guardavidas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/listaGuardavidas.css') }}">

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
                <span>Balneario:</span>
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
        <h2 class="titulo">Guardavidas</h2>

        {{-- LISTA DE GUARDAVIDAS --}}
        <ul class="lista-guardavidas">
            @foreach ($guardavidas as $g)
                <li data-balneario="{{ strtolower($g->balnearios->nombre_playa ?? '') }}"
                    data-puesto="{{ strtolower($g->puestos->nombre_puesto ?? '') }}"
                    data-turno="{{ strtolower($g->turnos->nombre_turno ?? '') }}">

                    <div class="info">
                        <h4>{{ $g->nombre ?? 'Sin nombre' }}</h4>
                        <p>
                            {{ $g->updated_at ? $g->updated_at->diffForHumans() : '' }}
                            · puesto {{ $g->puesto->nombre_puesto ?? 'sin asignar' }}
                        </p>
                    </div>
                    <div class="acciones">
                        <i class="fa fa-user" onclick="event.stopPropagation(); verPerfil({{ $g->id }})">ver
                            perfil</i>

                    </div>
                </li>
            @endforeach
        </ul>

        @auth
            @if (Auth::user()->hasRole('jefe_guardavidas'))
                {{-- BOTÓN AGREGAR --}}
                <div class="agregar">
                    <a href="{{ route('guardavidas.create') }}"><i class="fa fa-plus"></i> agregar
                        guardavidas</a>
                </div>

                {{-- BOTÓN Modificar guardavidas --}}
                <form action="{{ route('guardavidas.update', $guardavidas->id) }}" method="POST"
                    onsubmit="return confirm('¿Quiere modificar al guardavidas?')">
                    @csrf
                    @method('UPDATE')
                    <button type="submit">Actualizar</button>
                </form>
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

            <a class="btn-volver" href="{{ url('/login') }}">Volver</a>
        </div>

    </div>

    <script src="{{ asset('js/scriptFiltradoGuardavidas.js') }}" defer></script>

</body>

</html>
