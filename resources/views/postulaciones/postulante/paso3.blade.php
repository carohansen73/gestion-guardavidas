@extends('layouts.postulante')
@section('content')

@include('postulaciones.postulante._pasos', ['paso' => 3])

<form method="POST" action="{{ route('postulacion.guardar', 3) }}" enctype="multipart/form-data"
    class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 space-y-6">
    @csrf

    <div>
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Documentación</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Podés subir solo lo que tengas hoy y completar el resto más adelante. Formatos: PDF o imagen (JPG/PNG), hasta {{ $maxKb / 1024 }} MB por archivo.
            Si volvés a subir un documento, reemplaza al anterior.
        </p>
        @if ($modeloDeclaracion)
            <a href="{{ $modeloDeclaracion }}" target="_blank" class="inline-block mt-2 text-sm text-sky-600 dark:text-sky-400 hover:underline">
                Descargar modelo de declaración jurada (PDF)
            </a>
        @endif
    </div>

    <div class="space-y-4">
        @foreach ($tipos as $tipo => $config)
            @php $subido = $postulacion->documento($tipo); @endphp
            <div class="rounded-md border border-gray-200 dark:border-gray-700 p-3">
                <div class="flex items-center justify-between gap-2">
                    <label for="doc_{{ $tipo }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $config['label'] }}
                        @unless ($config['obligatorio'])
                            <span class="text-gray-400">(opcional — solo si tenés)</span>
                        @endunless
                    </label>
                    @if ($subido)
                        <span class="text-xs text-emerald-600 dark:text-emerald-400">✓ Subido</span>
                    @elseif ($config['obligatorio'])
                        <span class="text-xs text-amber-600 dark:text-amber-400">Falta</span>
                    @endif
                </div>

                @if ($subido)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <a href="{{ route('postulacion.documento', [$postulacion, $tipo]) }}" target="_blank" class="text-sky-600 dark:text-sky-400 hover:underline">
                            {{ $subido->nombre_original }}
                        </a>
                    </p>
                @endif

                <input type="file" name="doc_{{ $tipo }}" id="doc_{{ $tipo }}"
                    accept="{{ collect($config['mimes'])->map(fn ($m) => '.'.$m)->implode(',') }}"
                    class="mt-2 block w-full text-sm text-gray-700 dark:text-gray-200">
            </div>
        @endforeach
    </div>

    <div class="flex justify-between items-center">
        <a href="{{ route('postulacion.paso', 2) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Anterior</a>
        <button type="submit" class="bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar y continuar</button>
    </div>
</form>

@endsection
