@extends('layouts.postulante')
@section('content')

@include('postulaciones.postulante._pasos', ['paso' => 3])

@php
    // Los documentos se agrupan por tema. Si se suma un tipo nuevo en PostulacionDocumento::TIPOS y no
    // está en ningún grupo, aparece solo en un grupo "Otros documentos" (no se pierde).
    $grupos = [
        ['titulo' => 'Identidad', 'icono' => 'user', 'descripcion' => 'Tu foto y el DNI, frente y dorso.',
            'tipos' => ['foto_personal', 'dni_frente', 'dni_dorso']],
        ['titulo' => 'Formación y experiencia', 'icono' => 'file-text', 'descripcion' => 'Currículum, libreta de guardavidas y, si tenés, licencia motonáutica.',
            'tipos' => ['curriculum', 'libreta', 'licencia_motonautica']],
        ['titulo' => 'Antecedentes y declaración jurada', 'icono' => 'shield-check', 'descripcion' => 'Documentos que se renuevan cada temporada.',
            'tipos' => ['antecedentes_penales', 'declaracion_jurada']],
    ];
    $agrupados = collect($grupos)->pluck('tipos')->flatten()->all();
    $otros = array_values(array_diff(array_keys($tipos), $agrupados));
    if ($otros) {
        $grupos[] = ['titulo' => 'Otros documentos', 'icono' => 'file-text', 'descripcion' => null, 'tipos' => $otros];
    }
@endphp

<x-form-tarjeta method="POST" action="{{ route('postulacion.guardar', 3) }}" enctype="multipart/form-data">
    @csrf

    <x-form-encabezado :paso="3" titulo="Documentación">
        Podés subir solo lo que tengas hoy y completar el resto más adelante. Formatos: PDF o imagen (JPG/PNG), hasta {{ $maxKb / 1024 }} MB por archivo.
        Si volvés a subir un documento, reemplaza al anterior.
    </x-form-encabezado>

    @foreach ($grupos as $grupo)
        <x-form-seccion :titulo="$grupo['titulo']" :icono="$grupo['icono']" :descripcion="$grupo['descripcion']">

            {{-- El modelo de declaración jurada va junto a donde se sube la firmada. --}}
            @if ($modeloDeclaracion && in_array('declaracion_jurada', $grupo['tipos'], true))
                <a href="{{ $modeloDeclaracion }}" target="_blank" class="inline-block text-sm text-sky-600 dark:text-sky-400 hover:underline">
                    Descargar modelo de declaración jurada (PDF)
                </a>
            @endif

            <div class="space-y-4">
                @foreach ($grupo['tipos'] as $tipo)
                    @continue(! isset($tipos[$tipo]))
                    @php
                        $config = $tipos[$tipo];
                        $subido = $postulacion->documento($tipo);
                    @endphp
                    {{-- Declaración jurada: si la temporada todavía no tiene modelo no se muestra
                         (ni se exige), salvo que la persona ya haya subido una. --}}
                    @continue($tipo === 'declaracion_jurada' && ! $modeloDeclaracion && ! $subido)
                    <div class="rounded-md border border-gray-200 dark:border-gray-700 p-3">
                        <div class="flex items-center justify-between gap-2">
                            <label for="doc_{{ $tipo }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $config['label'] }}
                                @unless ($postulacion->documentoRequerido($tipo))
                                    <span class="text-gray-400">(opcional{{ $tipo === 'licencia_motonautica' ? ' — solo si tenés' : '' }})</span>
                                @endunless
                            </label>
                            @if ($subido)
                                <span class="text-xs text-emerald-600 dark:text-emerald-400">✓ Subido</span>
                            @elseif ($postulacion->documentoRequerido($tipo))
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
        </x-form-seccion>
    @endforeach

    <div class="flex justify-between items-center">
        <a href="{{ route('postulacion.paso', 2) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Anterior</a>
        <button type="submit" class="bg-sky-500 hover:bg-sky-400 text-white rounded-full px-5 py-2 shadow">Guardar y continuar</button>
    </div>
</x-form-tarjeta>

@endsection
