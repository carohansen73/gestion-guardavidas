@extends('layouts.app')

@section('content')

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
        Tu día franco fijo es <strong>{{ $guardavida->dia_franco_nombre ?? 'sin configurar (lo elegís desde tu perfil)' }}</strong>.
        Acá podés pedirle a un compañero cambiar el franco por única vez, para una fecha puntual — no cambia tu día franco fijo de todas las semanas.
    </p>

    <!-- Formulario de nueva solicitud -->
    <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm rounded p-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">Pedir un cambio</h3>

        @if ($companeros->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No hay otros guardavidas en tu playa para pedirles un cambio.</p>
        @else
            <form action="{{ route('franco-intercambio.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf

                <div class="sm:col-span-2">
                    <label class="text-sm text-gray-700 dark:text-gray-200">Compañero:</label>
                    <select name="guardavida_destinatario_id" required class="border rounded p-1.5 w-full">
                        <option value="">Seleccionar...</option>
                        @foreach ($companeros as $c)
                            <option value="{{ $c->id }}">{{ $c->apellido }} {{ $c->nombre }} — franco {{ $c->dia_franco_nombre ?? 'sin configurar' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm text-gray-700 dark:text-gray-200">Tu día que cedés:</label>
                    <input type="date" name="fecha_propia" required class="border rounded p-1.5 w-full">
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
        @endif
    </div>

    <!-- Solicitudes recibidas -->
    <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm rounded p-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">Pedidos que te hicieron</h3>

        @if ($recibidas->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No tenés pedidos.</p>
        @else
            <ul class="divide-y divide-gray-200 dark:divide-gray-600">
                @foreach ($recibidas as $r)
                    <li class="py-2 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <div class="text-sm">
                            <strong>{{ $r->solicitante->apellido }} {{ $r->solicitante->nombre }}</strong>
                            te pide cambiar el <strong>{{ $r->fecha_propia->format('d/m/Y') }}</strong> (que sería franco de {{ $r->solicitante->nombre }})
                            por el <strong>{{ $r->fecha_deseada->format('d/m/Y') }}</strong> (que pasaría a ser tu franco ese día).
                            @if ($r->mensaje)
                                <br><span class="text-gray-500 dark:text-gray-400">"{{ $r->mensaje }}"</span>
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

@endsection
