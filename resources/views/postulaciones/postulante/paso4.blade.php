@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-3xl">
    <x-session-alerts />


@include('postulaciones.postulante._pasos', ['paso' => 4])

@php
    // Acá solo se llega en borrador, ya enviada (pendiente) o devuelta por el equipo (incompleta).
    $yaEnviada = $postulacion->estado === 'pendiente';
    $devuelta = $postulacion->estado === 'incompleta';
@endphp

<x-form-tarjeta tag="div">

    <x-form-encabezado :paso="4" :titulo="$yaEnviada ? 'Revisar tu inscripción' : 'Revisar y enviar'">
        @if ($yaEnviada)
            Tu inscripción ya fue enviada y el equipo la está revisando. Podés seguir corrigiéndola mientras tanto: cada paso se guarda al avanzar y el equipo ve que la modificaste.
        @elseif ($devuelta)
            El equipo te pidió correcciones. Cuando termines, enviala de nuevo desde acá.
        @else
            Mientras no la envíes, el equipo no podrá ver tu inscripción. Una vez enviada, podrás seguir corrigiéndola mientras esté en revisión.
        @endif
    </x-form-encabezado>

    <x-form-seccion titulo="Estado de tu inscripción" icono="shield-check">
        @if ($faltantes)
            <div class="rounded-md bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 p-3 text-sm text-amber-800 dark:text-amber-200">
                <p class="font-semibold mb-1">Todavía falta completar:</p>
                <ul class="list-disc pl-5">
                    @foreach ($faltantes as $faltante)
                        <li>{{ $faltante }}</li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="rounded-md bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 p-3 text-sm text-emerald-800 dark:text-emerald-200">
                @if ($yaEnviada)
                    Tu inscripción está completa y fue enviada{{ $postulacion->enviada_at ? ' el '.$postulacion->enviada_at->format('d/m/Y \a \l\a\s H:i') : '' }}. El equipo la está revisando.
                @elseif ($devuelta)
                    Los datos obligatorios están completos. Revisá lo que te pidió el equipo, corregí lo que haga falta y enviala de nuevo.
                @else
                    ¡Está todo completo! Ya podés enviar tu inscripción.
                @endif
            </div>
        @endif
    </x-form-seccion>

    <x-form-seccion titulo="Resumen" icono="clipboard-check" :descripcion="($yaEnviada ? 'Lo que enviaste.' : 'Lo que vas a enviar.').' Podés volver a cualquier paso para corregirlo.'">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
            <div><dt class="text-gray-500 dark:text-gray-400">Disponibilidad</dt>
                <dd class="text-gray-800 dark:text-gray-100">
                    {{ $postulacion->disponible_desde?->format('d/m/Y') ?? '—' }} al {{ $postulacion->disponible_hasta?->format('d/m/Y') ?? '—' }}
                </dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">Playas preferidas</dt>
                <dd class="text-gray-800 dark:text-gray-100">
                    @forelse ($postulacion->playas as $playa)
                        {{ $playa->pivot->prioridad }}. {{ $playa->nombre }}@unless ($loop->last), @endunless
                    @empty
                        Sin preferencia
                    @endforelse
                </dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">Documentos subidos</dt>
                <dd class="text-gray-800 dark:text-gray-100">
                    {{ $postulacion->documentos->count() }} de {{ count($tipos) }}
                </dd></div>
        </dl>
    </x-form-seccion>

    <div class="flex justify-between items-center">
        <a href="{{ route('postulacion.paso', 3) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Anterior</a>
        @if ($yaEnviada)
            {{-- Ya enviada: no hay nada más que enviar, los cambios se guardaron al pasar por cada paso. --}}
            <a href="{{ route('home') }}" class="bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Volver al inicio</a>
        @else
            <form method="POST" action="{{ route('postulacion.enviar') }}">
                @csrf
                <button type="submit" @disabled($faltantes)
                    class="bg-sky-500 hover:bg-sky-400 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-full px-5 py-2 shadow">
                    Enviar inscripción
                </button>
            </form>
        @endif
    </div>
</x-form-tarjeta>

</div>
@endsection
