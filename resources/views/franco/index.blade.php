@extends('layouts.app')

@section('content')

@php($diasFranco = $guardavida->diasFrancoActuales())

<div class="text-gray-600 dark:text-gray-100 body-font px-4">
    <div class="flex justify-between align-center my-4">
        <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl">
            Cambios de Franco
        </h2>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded my-2">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded my-2">
            {{ $errors->first() }}
        </div>
    @endif

    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
        Tu franco fijo es <strong>{{ $guardavida->dias_franco_nombres ?? 'sin configurar' }}</strong>.
        Acá podés pedirle a un compañero cambiar el franco por única vez, para una fecha puntual — no cambia tu franco fijo de todas las semanas.
    </p>

    <!-- Formulario de nueva solicitud -->
    <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm rounded p-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">Pedir un cambio</h3>

        @if ($diasFranco === [])
            <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-200 text-sm p-3 rounded mb-3">
                Todavía no configuraste tu franco fijo. Necesitás hacerlo antes de poder pedir un cambio.
            </div>
            <button type="button" class="bg-sky-600 hover:bg-sky-500 text-white px-4 py-1.5 rounded"
                onclick="document.getElementById('diaFrancoModal').classList.remove('hidden')">
                Configurar mi franco
            </button>
        @elseif ($companeros->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No hay otros guardavidas en tu playa para pedirles un cambio.</p>
        @else
            <form action="{{ route('franco-intercambio.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf

                <div class="sm:col-span-2">
                    <label class="text-sm text-gray-700 dark:text-gray-200">Compañero:</label>
                    <select name="guardavida_destinatario_id" required class="border rounded p-1.5 w-full">
                        <option value="">Seleccionar...</option>
                        @foreach ($companeros as $c)
                            <option value="{{ $c->id }}">{{ $c->apellido }} {{ $c->nombre }} — franco {{ $c->dias_franco_nombres ?? 'sin configurar' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm text-gray-700 dark:text-gray-200">
                        Tu día que cedés (tiene que ser tu franco: {{ $guardavida->dias_franco_nombres }}):
                    </label>
                    <input type="date" name="fecha_propia" id="fecha_propia_input" required class="border rounded p-1.5 w-full">
                    <p id="fecha_propia_error" class="hidden text-red-500 text-xs mt-1"></p>
                </div>

                <div>
                    <label class="text-sm text-gray-700 dark:text-gray-200">Día que querés tomar en su lugar:</label>
                    <input type="date" name="fecha_deseada" required class="border rounded p-1.5 w-full">
                </div>

                <div class="sm:col-span-2">
                    <label class="text-sm text-gray-700 dark:text-gray-200">Mensaje (opcional):</label>
                    <input type="text" name="mensaje" maxlength="255" class="border rounded p-1.5 w-full" placeholder="Ej: tengo un trámite ese día">
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="bg-sky-600 hover:bg-sky-500 text-white px-4 py-1.5 rounded">
                        Enviar pedido
                    </button>
                </div>
            </form>

            <script>
                (function () {
                    var diasFranco = @json($diasFranco);
                    var nombresDias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
                    var input = document.getElementById('fecha_propia_input');
                    var error = document.getElementById('fecha_propia_error');

                    input.addEventListener('change', function () {
                        if (!this.value) {
                            error.classList.add('hidden');
                            return;
                        }
                        var partes = this.value.split('-').map(Number);
                        var fecha = new Date(partes[0], partes[1] - 1, partes[2]);
                        if (diasFranco.indexOf(fecha.getDay()) === -1) {
                            var nombresFranco = diasFranco.map(function (d) { return nombresDias[d]; }).join(' o ');
                            error.textContent = 'Esa fecha es ' + nombresDias[fecha.getDay()] + ', pero tu franco es los ' + nombresFranco + '.';
                            error.classList.remove('hidden');
                        } else {
                            error.classList.add('hidden');
                        }
                    });
                })();
            </script>
        @endif
    </div>

    <!-- Solicitudes recibidas -->
    <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm rounded p-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">Pedidos que te hicieron</h3>

        @if ($diasFranco === [] && $recibidas->contains('estado', 'pendiente'))
            <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-200 text-sm p-3 rounded mb-3">
                Todavía no configuraste tu franco fijo — necesitás hacerlo (botón "Configurar mi franco" más arriba) antes de poder aceptar un pedido.
            </div>
        @endif

        @if ($recibidas->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No tenés pedidos.</p>
        @else
            <ul class="divide-y divide-gray-200 dark:divide-gray-600">
                @foreach ($recibidas as $r)
                    @php($noCoincideConMiFranco = $diasFranco !== [] && ! in_array((int) $r->fecha_deseada->dayOfWeek, $diasFranco, true))
                    <li class="py-2 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <div class="text-sm">
                            <strong>{{ $r->solicitante->apellido }} {{ $r->solicitante->nombre }}</strong>
                            te pide cambiar el <strong>{{ $r->fecha_propia->format('d/m/Y') }}</strong> (que sería franco de {{ $r->solicitante->nombre }})
                            por el <strong>{{ $r->fecha_deseada->format('d/m/Y') }}</strong> (que pasaría a ser tu franco ese día).
                            @if ($r->mensaje)
                                <br><span class="text-gray-500 dark:text-gray-400">"{{ $r->mensaje }}"</span>
                            @endif
                            @if ($r->estado === 'pendiente' && $noCoincideConMiFranco)
                                <br><span class="text-red-500 text-xs">
                                    El {{ $r->fecha_deseada->format('d/m/Y') }} no es tu franco (el tuyo es los {{ $guardavida->dias_franco_nombres }}) — no vas a poder aceptarlo así.
                                </span>
                            @endif
                            <br>
                            <span class="text-xs uppercase font-medium
                                {{ $r->estado === 'pendiente' ? 'text-amber-600' : ($r->estado === 'aceptado' ? 'text-emerald-600' : 'text-gray-500') }}">
                                {{ $r->estado }}
                            </span>
                        </div>

                        @if ($r->estado === 'pendiente')
                            <div class="flex gap-2">
                                <form action="{{ route('franco-intercambio.aceptar', $r->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-sm">Aceptar</button>
                                </form>
                                <form action="{{ route('franco-intercambio.rechazar', $r->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-gray-400 hover:bg-gray-300 text-white rounded text-sm">Rechazar</button>
                                </form>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Solicitudes enviadas -->
    <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm rounded p-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">Tus pedidos</h3>

        @if ($enviadas->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No hiciste ningún pedido.</p>
        @else
            <ul class="divide-y divide-gray-200 dark:divide-gray-600">
                @foreach ($enviadas as $e)
                    <li class="py-2 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <div class="text-sm">
                            Le pediste a <strong>{{ $e->destinatario->apellido }} {{ $e->destinatario->nombre }}</strong>
                            cambiar tu <strong>{{ $e->fecha_propia->format('d/m/Y') }}</strong> por el
                            <strong>{{ $e->fecha_deseada->format('d/m/Y') }}</strong>.
                            <br>
                            <span class="text-xs uppercase font-medium
                                {{ $e->estado === 'pendiente' ? 'text-amber-600' : ($e->estado === 'aceptado' ? 'text-emerald-600' : 'text-gray-500') }}">
                                {{ $e->estado }}
                            </span>
                        </div>

                        @if ($e->estado === 'pendiente')
                            <form action="{{ route('franco-intercambio.cancelar', $e->id) }}" method="POST" onsubmit="return confirm('¿Cancelar este pedido?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-gray-400 hover:bg-gray-300 text-white rounded text-sm">Cancelar pedido</button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="text-center mt-4">
        <a class="btn btn-secondary" onclick="window.history.back()">Volver</a>
    </div>
</div>

@if ($diasFranco === [])
    <div id="diaFrancoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-96 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                Configurar mi franco
            </h2>

            <form action="{{ route('guardavida.actualizarDiaFranco') }}" method="POST">
                @csrf
                @method('PATCH')

                <label class="text-sm text-gray-700 dark:text-gray-300">Día(s) franco fijo(s) de todas las semanas:</label>
                <div class="flex flex-col gap-1 mt-2">
                    @foreach (['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $i => $nombreDia)
                        <label class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-200">
                            <input type="checkbox" name="dias_franco[]" value="{{ $i }}" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                            {{ $nombreDia }}
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" class="px-3 py-1 bg-gray-400 text-white rounded"
                        onclick="document.getElementById('diaFrancoModal').classList.add('hidden')">
                        Cancelar
                    </button>
                    <button type="submit" class="px-3 py-1 bg-sky-600 text-white rounded">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

@endsection
