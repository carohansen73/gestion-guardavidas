{{-- Barra de pasos del formulario de inscripción. Recibe $paso (1-4) y $postulacion (null si todavía no la empezó). --}}
@php
    $etapas = [1 => 'Datos personales', 2 => 'Inscripción', 3 => 'Documentos', 4 => 'Revisar y enviar'];

    // Al formulario solo se entra en borrador, enviada (pendiente) o devuelta por el equipo (incompleta).
    $estadoActual = $postulacion?->estado ?? 'borrador';
    [$etiquetaEstado, $claseEstado, $bajada] = [
        'pendiente' => ['Enviada, en revisión', 'bg-sky-500/20 text-sky-600 dark:text-sky-400', 'Ya la enviaste. Podés corregir lo que necesites y volver a confirmar en el último paso.'],
        'incompleta' => ['Incompleta', 'bg-orange-500/20 text-orange-600 dark:text-orange-400', 'El equipo te pidió correcciones. Revisá el comentario, corregí y volvé a enviarla en el último paso.'],
    ][$estadoActual] ?? ['Borrador', 'bg-amber-500/20 text-amber-500', 'Completá los cuatro pasos para enviar tu inscripción.'];
    $pedidoDelEquipo = $estadoActual === 'incompleta' ? trim((string) $postulacion->observaciones) : '';
@endphp

<header class="glass overflow-hidden rounded-xl p-5 mb-6">

    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-sky-600 dark:text-gray-400">Convocatoria de temporada </p>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1"> Postulación a guardavidas </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $bajada }}</p>
        </div>

        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $claseEstado }}">
            {{ $etiquetaEstado }}
        </span>
    </div>

    @if ($pedidoDelEquipo !== '')
        <div class="mb-4 rounded-md border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800 dark:border-orange-800 dark:bg-orange-900/30 dark:text-orange-200">
            <p class="font-semibold">Comentario del equipo</p>
            <p class="mt-1 whitespace-pre-line">{{ $pedidoDelEquipo }}</p>
        </div>
    @endif

    <ol class="flex flex-wrap gap-2 mb-6 text-sm">
        @foreach ($etapas as $n => $titulo)
            <li>
                {{-- Desde el paso 2 en adelante hace falta que la inscripción ya exista (se crea al guardar el paso 1). --}}
                <a href="{{ route('postulacion.paso', $n) }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border
                        {{ $paso === $n
                            ? 'bg-sky-500 text-white border-sky-500'
                            : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <span class="font-semibold">{{ $n }}</span> {{ $titulo }}
                </a>
            </li>
        @endforeach
    </ol>

</header>

@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4 dark:bg-red-900/40 dark:text-red-300">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
