@extends('layouts.app')
@section('content')

@php
    $perfil = $postulacion->perfil;
    $colores = [
        'pendiente' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
        'aceptada' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
        'rechazada' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
        'incompleta' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
    ];
    $input = 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm';
    $dato = fn ($valor) => filled($valor) ? $valor : '—';
@endphp

<div class="text-gray-600 dark:text-gray-100 sm:px-4 sm:py-6 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <a href="{{ route('postulaciones.index', ['temporada' => $postulacion->temporada_id]) }}" class="text-sm text-sky-500 hover:underline">← Volver al listado</a>
            <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight md:text-3xl mt-1">
                {{ $postulacion->user->lastname }}, {{ $postulacion->user->name }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $postulacion->temporada->nombre }}</p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $colores[$postulacion->estado] ?? '' }}">
            {{ ucfirst($postulacion->estado) }}
        </span>
    </div>

    <x-session-alerts />

    {{-- Datos personales --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Datos personales</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
            <div><dt class="text-gray-500 dark:text-gray-400">DNI</dt><dd>{{ $dato($postulacion->user->dni) }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">Email</dt><dd>{{ $dato($postulacion->user->email) }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">Teléfono</dt><dd>{{ $dato($perfil?->telefono) }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">Fecha de nacimiento</dt><dd>{{ $dato($perfil?->fecha_nacimiento?->format('d/m/Y')) }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">Género</dt><dd>{{ $dato($perfil?->genero) }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">Grupo sanguíneo</dt><dd>{{ $dato($perfil?->grupo_sanguineo) }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500 dark:text-gray-400">Domicilio</dt>
                <dd>{{ $dato(trim(($perfil?->direccion ?? '').' '.($perfil?->numero ?? '').' '.($perfil?->piso_dpto ?? ''))) }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">N° de libreta</dt><dd>{{ $dato($perfil?->numero_libreta) }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">Obra social</dt>
                <dd>{{ $perfil?->tieneObraSocial() ? $perfil->obra_social_nombre.' (afiliado '.$dato($perfil->obra_social_numero_afiliado).')' : 'No tiene' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500 dark:text-gray-400">Talles</dt>
                <dd>Remera {{ $dato($perfil?->talle_remera) }} · Pantalón {{ $dato($perfil?->talle_pantalon) }} · Campera {{ $dato($perfil?->talle_campera) }} · Traje de baño {{ $dato($perfil?->talle_traje_bano) }}</dd></div>
        </dl>
    </div>

    {{-- Inscripción --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Inscripción</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
            <div><dt class="text-gray-500 dark:text-gray-400">Disponibilidad</dt>
                <dd>{{ $dato($postulacion->disponible_desde?->format('d/m/Y')) }} al {{ $dato($postulacion->disponible_hasta?->format('d/m/Y')) }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">Playas preferidas</dt>
                <dd>
                    @forelse ($postulacion->playas as $playa)
                        {{ $playa->pivot->prioridad }}. {{ $playa->nombre }}@unless ($loop->last) · @endunless
                    @empty
                        Sin preferencia
                    @endforelse
                </dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">Enviada</dt><dd>{{ $dato($postulacion->enviada_at?->format('d/m/Y H:i')) }}</dd></div>
            @if ($postulacion->seleccionado)
                <div><dt class="text-gray-500 dark:text-gray-400">Asignación</dt>
                    <dd>{{ $postulacion->playaAsignada?->nombre }} @if ($postulacion->puestoAsignado) — {{ $postulacion->puestoAsignado->nombre }} @endif</dd></div>
            @endif
        </dl>
    </div>

    {{-- Documentos --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Documentos</h3>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            @foreach ($tipos as $tipo => $config)
                @php $doc = $postulacion->documento($tipo); @endphp
                <li class="py-2 flex items-center justify-between gap-3">
                    <span>{{ $config['label'] }}</span>
                    @if ($doc)
                        <a href="{{ route('postulaciones.documento', [$postulacion, $tipo]) }}" target="_blank" class="text-sky-500 hover:text-sky-400 dark:text-sky-400">
                            Ver ({{ $doc->nombre_original }})
                        </a>
                    @elseif ($postulacion->documentoRequerido($tipo))
                        <span class="text-amber-600 dark:text-amber-400">No subido</span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Revisión --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Revisión</h3>

        @if ($postulacion->revisadoPor)
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                Última revisión: {{ $postulacion->revisadoPor->name }} {{ $postulacion->revisadoPor->lastname }},
                {{ $postulacion->fecha_revision?->format('d/m/Y H:i') }}
            </p>
        @endif

        @can('revisar', $postulacion)
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-3">
                    <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('postulaciones.revisar', $postulacion) }}" class="space-y-3">
                @csrf
                @method('PATCH')
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observaciones <span class="text-gray-400">(las ve el postulante)</span></label>
                    <textarea name="observaciones" id="observaciones" rows="3" class="{{ $input }}">{{ old('observaciones', $postulacion->observaciones) }}</textarea>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" name="estado" value="aceptada" class="px-4 py-2 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white text-sm">Aceptar</button>
                    <button type="submit" name="estado" value="rechazada" class="px-4 py-2 rounded-full bg-red-600 hover:bg-red-500 text-white text-sm">Rechazar</button>
                    <button type="submit" name="estado" value="pendiente" class="px-4 py-2 rounded-full bg-gray-500 hover:bg-gray-400 text-white text-sm">Dejar pendiente</button>
                </div>
            </form>
        @else
            <p class="text-sm">{{ $dato($postulacion->observaciones) }}</p>
        @endcan
    </div>
</div>

@endsection
