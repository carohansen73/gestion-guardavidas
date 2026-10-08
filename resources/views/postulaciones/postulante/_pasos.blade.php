{{-- Barra de pasos del formulario de inscripción. Recibe $paso (1-4). --}}
@php
    $etapas = [1 => 'Datos personales', 2 => 'Inscripción', 3 => 'Documentos', 4 => 'Revisar y enviar'];
@endphp

<header class="glass overflow-hidden rounded-xl p-5 mb-6">

    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-sky-600 dark:text-gray-400">Convocatoria de temporada </p>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1"> Postulación a guardavidas </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Completá los cuatro pasos para enviar tu inscripción.</p>
        </div>

        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold bg-amber-500/20 text-amber-500">
        Borrador
        </span>
    </div>

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
