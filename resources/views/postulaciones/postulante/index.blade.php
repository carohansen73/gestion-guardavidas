@extends('layouts.postulante')
@section('content')

@php
    $colores = [
        'borrador' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
        'pendiente' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
        'aceptada' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
        'rechazada' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
        'incompleta' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
    ];
    $etiquetas = [
        'borrador' => 'Borrador (sin enviar)',
        'pendiente' => 'Enviada, pendiente de revisión',
        'aceptada' => 'Aceptada',
        'rechazada' => 'Rechazada',
        'incompleta' => 'Incompleta',
    ];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">¡Hola, {{ Auth::user()->name }}!</h1>

    @if (! $temporada)
        <p class="text-gray-600 dark:text-gray-300 mt-2">
            Por ahora no hay una inscripción abierta. Cuando se habilite la próxima temporada vas a poder completar el formulario desde acá.
        </p>
    @else
        <p class="text-gray-600 dark:text-gray-300 mt-2">
            Inscripción abierta: <strong>{{ $temporada->nombre }}</strong>
            (hasta el {{ $temporada->fecha_fin_postulacion->format('d/m/Y') }}).
        </p>

        @if (! $postulacion)
            <p class="text-gray-600 dark:text-gray-300 mt-2">
                Podés completarla de a pasos: guardá lo que tengas hoy y subí el resto otro día. Recién la ve el equipo cuando la enviás.
            </p>
            <a href="{{ route('postulacion.paso', 1) }}"
                class="inline-block mt-4 bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">
                Comenzar inscripción
            </a>
        @else
            <div class="mt-4 flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Estado:</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colores[$postulacion->estado] ?? '' }}">
                    {{ $etiquetas[$postulacion->estado] ?? $postulacion->estado }}
                </span>
            </div>

            @if ($postulacion->observaciones)
                <div class="mt-3 rounded-md bg-gray-50 dark:bg-gray-700 p-3 text-sm text-gray-700 dark:text-gray-200">
                    <p class="font-semibold mb-1">Comentario del equipo</p>
                    <p class="whitespace-pre-line">{{ $postulacion->observaciones }}</p>
                </div>
            @endif

            {{-- Aceptada ≠ seleccionada: ser aceptada no garantiza trabajar esa temporada. --}}
            @if ($postulacion->seleccionado)
                <div class="mt-3 rounded-md bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 p-3 text-sm text-emerald-800 dark:text-emerald-200">
                    Fuiste <strong>seleccionado/a</strong> para trabajar esta temporada:
                    {{ $postulacion->playaAsignada?->nombre }}
                    @if ($postulacion->puestoAsignado) — {{ $postulacion->puestoAsignado->nombre }} @endif.
                </div>
            @elseif ($postulacion->estado === 'aceptada')
                <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                    Tu inscripción fue aceptada. La selección de quienes trabajan la temporada es un paso posterior: te avisamos por acá.
                </p>
            @endif

            @if ($postulacion->editablePorPostulante())
                <a href="{{ route('postulacion.paso', 1) }}"
                    class="inline-block mt-4 bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">
                    {{ $postulacion->estado === 'borrador' ? 'Continuar inscripción' : 'Revisar / corregir mi inscripción' }}
                </a>
            @endif
        @endif
    @endif
</div>

@if ($anteriores->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Inscripciones anteriores</h2>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            @foreach ($anteriores as $anterior)
                <li class="py-2 flex items-center justify-between gap-3">
                    <span class="text-gray-700 dark:text-gray-200">{{ $anterior->temporada->nombre }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colores[$anterior->estado] ?? '' }}">
                        {{ $etiquetas[$anterior->estado] ?? $anterior->estado }}@if ($anterior->seleccionado) · seleccionado/a @endif
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@endsection
