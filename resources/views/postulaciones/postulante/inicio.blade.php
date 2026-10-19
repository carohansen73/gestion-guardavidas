@extends('layouts.app')

{{-- Inicio del postulante: la card de su inscripción vigente (estado, comentario del equipo y acceso al
     formulario por pasos) o, si no hay, cuándo abre la próxima. No se muestra el historial de temporadas. --}}
@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <x-session-alerts />

    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">¡Hola, {{ Auth::user()->name }}!</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Acá vas a ver cuándo abre la inscripción de cada temporada y cómo va la tuya.</p>
    </div>

    @if (! empty($postularme))
        @include('dashboard.partials.postulacion')
    @elseif ($proxima)
        <section class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
            <div class="flex items-start gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">
                    <i class="fas fa-calendar-days text-xl" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Próxima inscripción · {{ $proxima->nombre }}</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                        Abre el {{ $proxima->fecha_inicio_postulacion->format('d/m/Y') }}
                    </p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        Podés inscribirte hasta el {{ $proxima->fecha_fin_postulacion->format('d/m/Y') }}.
                        La temporada empieza el {{ $proxima->fecha_inicio->format('d/m/Y') }}.
                    </p>
                </div>
            </div>
        </section>
    @else
        <section class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
            <p class="text-gray-700 dark:text-gray-200">
                Por ahora no hay una inscripción abierta ni una fecha de apertura definida. Cuando se habilite la próxima temporada vas a verlo acá.
            </p>
        </section>
    @endif

</div>
@endsection
