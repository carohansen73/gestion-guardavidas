{{-- Barra de pasos del formulario de inscripción. Recibe $paso (1-4). --}}
@php
    $etapas = [1 => 'Datos personales', 2 => 'Inscripción', 3 => 'Documentos', 4 => 'Revisar y enviar'];
@endphp
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

@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
