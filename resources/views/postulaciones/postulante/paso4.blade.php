@extends('layouts.postulante')
@section('content')

@include('postulaciones.postulante._pasos', ['paso' => 4])

<div class="glass rounded-xl p-6 pt-10 space-y-10">

    <x-postulacion-encabezado :paso="4" titulo="Revisar y enviar">
        Mientras no la envíes, el equipo no podrá ver tu inscripción. Una vez enviada, podrás seguir corrigiéndola hasta que sea aceptada/rechazada.
    </x-postulacion-encabezado>

    <x-postulacion-seccion titulo="Estado de tu inscripción" icono="shield-check">
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
                ¡Está todo completo! Ya podés enviar tu inscripción.
            </div>
        @endif
    </x-postulacion-seccion>

    <x-postulacion-seccion titulo="Resumen" icono="clipboard-check" descripcion="Lo que vas a enviar. Podés volver a cualquier paso para corregirlo.">
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
    </x-postulacion-seccion>

    <div class="flex justify-between items-center">
        <a href="{{ route('postulacion.paso', 3) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Anterior</a>
        <form method="POST" action="{{ route('postulacion.enviar') }}">
            @csrf
            <button type="submit" @disabled($faltantes)
                class="bg-sky-500 hover:bg-sky-400 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-full px-5 py-2 shadow">
                {{ $postulacion->estado === 'borrador' || $postulacion->estado === 'incompleta' ? 'Enviar inscripción' : 'Confirmar cambios' }}
            </button>
        </form>
    </div>
</div>

@endsection
