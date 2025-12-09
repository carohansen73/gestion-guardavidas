@extends('layouts.app')

@section('content')

<div class="text-gray-600 dark:text-gray-100 body-font px-4 ">
    <div class="flex justify-between align-center mb-sm-4">
        <h2 class="text-gray-700 text-xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl">
            Historial de Asistencias de
            <span class="text-lg text-sky-600">
            {!! $guardavida->nombre !!}   {!! $guardavida->apellido !!}
            </span>
        </span>
        </h2>
    </div>

    @if (session('success'))
         <div class="bg-green-100 text-green-700 p-3 rounded my-2">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded my-2">
            {{ session('error') }}
        </div>
    @endif

</div>

<div x-data="{ selectedId: null }">

    <div  class="flex justify-between my-2 mx-4 px-4 py-2 bg-gray-50 border border-gray-200 dark:border-gray-700  shadow-sm">
        <form method="GET" class="flex gap-4">
            <div>
                <label for="inicio">Desde:</label>
                <input type="date" name="inicio" id="inicio"
                    value="{{ request('inicio') }}"
                    class="border rounded p-1">
            </div>

            <div>
                <label for="fin">Hasta:</label>
                <input type="date" name="fin" id="fin"
                    value="{{ request('fin') }}"
                    class="border rounded p-1">
            </div>

            <button class="bg-sky-600 hover:bg-sky-500 text-white px-4 py-1 rounded">
                Filtrar
            </button>

            @if(request()->filled('inicio') || request()->filled('fin'))
                <a href="{{ route('asistencias.guardavida', $guardavida->id) }}"
                class="bg-gray-400 hover:bg-gray-300 text-white px-4 py-1 rounded">
                    Limpiar
                </a>
            @endif
        </form>

        @if(request()->filled('inicio') || request()->filled('fin'))
        {{-- ⭐ Ya hay filtro aplicado → NO abrir modal, exportar directo --}}
        <a href="{{ route('export.playas', [
            'guardavida_id' => $guardavida->id,
            'tipo' => 'asistencia-guardavida',
            'inicio' => request('inicio'),
            'fin' => request('fin')
        ]) }}"
        class="px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
        </a>
        @else
            <button class="px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-500"
                onclick="openAsistenciaGuardavidaModal({{ $guardavida->id }})">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </button>
        @endif
    </div>

     {{-- Lista para Mobile --}}
     <section class="text-gray-600 dark:text-gray-100 body-font px-4 py-4 sm:hidden">
        <div id="accordion-collapse" data-accordion="collapse" class="bg-white dark:bg-gray-600">
            @foreach ($historial as $h)
                <div class="registro-item-lista rounded ">
                    <h2 id="accordion-collapse-heading-{{$h['fecha']}}">
                        <button type="button" class="flex items-center justify-between w-full px-4 py-2 font-medium text-left text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3"
                            data-accordion-target="#accordion-collapse-body-{{$h['fecha']}}" aria-expanded="true" aria-controls="accordion-collapse-body-1">
                            {{-- <a href="{{ route('intervencion.show', $intervencion) }}"> --}}
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                  {{ \Carbon\Carbon::parse($h['fecha'])->format('d/m/Y') }}
                                </h3>
                                <span class="text-sm text-gray-500 dark:text-gray-300">
                                Estado: <strong>{{ $h['estado'] }}</strong>
                                </span>
                            </div>
                            {{-- </a> --}}
                            <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                            </svg>
                        </button>
                    </h2>

                    <div id="accordion-collapse-body-{{$h['fecha']}}" class="hidden" aria-labelledby="accordion-collapse-heading-{{$h['fecha']}}">
                        <div class="px-4 py-2 border border-b-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900">

                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Ingreso:</strong> {{ $h['ingreso'] ? \Carbon\Carbon::parse($h['ingreso'])->format('H:i') : '-' }}
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Egreso:</strong> {{ $h['egreso'] ? \Carbon\Carbon::parse($h['egreso'])->format('H:i') : '-' }}
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Puesto:</strong> {{ $h['puesto'] }}
                            </p>

                            <p class="text-sm text-gray-800 dark:text-gray-400 font-medium mt-1 line-clamp-2">
                                Detalles:
                                <span class="text-sm text-gray-500 dark:text-gray-300">
                                    detalleeeesss
                                </span>
                            </p>

                        </div><!--px-4-->
                    </div><!--accordion-->

                </div>
            @endforeach
        </div>
    </section>






    {{-- Tabla para Desktop --}}
   <x-index-table :registros="$historial">
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
            <tr>
                {{-- <th class="px-4 py-2 text-left cursor-pointer" onclick="toggleSort()">Fecha</th> --}}
                <th class="px-4 py-2 text-left">Fecha</th>
                <th class="px-4 py-2 text-left">Estado</th>
                <th class="px-4 py-2 text-left">Ingreso</th>
                <th class="px-4 py-2 text-left">Egreso</th>
                <th class="px-4 py-2 text-left">Puesto</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
           @foreach ($historial as $h)
                <tr class="registro-item-tabla hover:bg-gray-50 dark:hover:bg-gray-800"
                    >
                    {{-- <td class="px-4 py-2">{{ $intervencion->fecha->format('d/m/Y') }}</td> --}}
                    <td class="px-4 py-2">{{ $h['fecha'] }}</td>
                    <td class="px-4 py-2">{{ $h['estado'] }}</td>
                    <td class="px-4 py-2">{{ $h['ingreso'] }}</td>
                    <td class="px-4 py-2">{{ $h['egreso'] }}</td>
                     <td class="px-4 py-2">{{ $h['puesto'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </x-index-table>
{{-- End tabla --}}

                {{-- @if (method_exists($guardavidas, 'links'))
                    <div class="paginacion mt-4">
                        {{ $guardavidas->links() }}
                    </div>
                @endif --}}

                {{-- VOLVER --}}
                <div class="text-center mt-4">
                    <a class="btn btn-secondary" onclick="window.history.back()">Volver</a>
                </div>


</div> <!-- selectedId -->






<div id="asistenciaGuardavidaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
   <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-96 p-6 animate-fade-in">

        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
            Exportar asistencia
        </h2>


        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Seleccioná el rango de fechas para generar el Excel.
        </p>

        <div class="mb-3">
            <label class="text-gray-700 dark:text-gray-300 text-sm">Desde:</label>
            <input type="date" id="inicioGuardavida" class="border p-1 w-full rounded">
        </div>

        <div class="mb-3">
            <label class="text-gray-700 dark:text-gray-300 text-sm">Hasta:</label>
            <input type="date" id="finGuardavida" class="border p-1 w-full rounded">
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <button class="px-3 py-1 bg-gray-400 text-white rounded"
                onclick="closeAsistenciaGuardavidaModal()">
                Cancelar
            </button>

            <button class="px-3 py-1 bg-emerald-600 text-white rounded"
                onclick="submitAsistenciaGuardavidaExcel({{ $guardavida->id }})">
                Exportar
            </button>
        </div>

    </div>
</div>


<style>
    .animate-fade-in {
        animation: fadeIn .25s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>




<script>
    // ⬅️ JS ya existente (NO SE TOCA)
    function openAsistenciaModal(tipo) {
        if (tipo === 'asistencia-general') {
            document.getElementById('asistenciaModal').classList.remove('hidden');
        }
    }

    function closeAsistenciaModal() {
        document.getElementById('asistenciaModal').classList.add('hidden');
    }

    function submitAsistenciaExcel() {
        const inicio = document.getElementById('inicioGeneral').value;
        const fin = document.getElementById('finGeneral').value;

        if (!inicio && !fin) {
            if (!confirm("No seleccionaste fechas. ¿Generar los últimos 30 días?")) {
                return;
            }
        }

        const url = `/export/playas?tipo=asistencia-general&inicio=${inicio}&fin=${fin}`;

        window.location.href = url;
        closeAsistenciaModal()
    }


    // ⭐ NUEVO JS PARA ASISTENCIA POR GUARDAVIDA ⭐

    function openAsistenciaGuardavidaModal(id) {
        document.getElementById('asistenciaGuardavidaModal').classList.remove('hidden');
    }

    function closeAsistenciaGuardavidaModal() {
        document.getElementById('asistenciaGuardavidaModal').classList.add('hidden');
    }

    function submitAsistenciaGuardavidaExcel(id) {
        const inicio = document.getElementById('inicioGuardavida').value;
        const fin = document.getElementById('finGuardavida').value;

        const inicioParam = inicio ? `&inicio=${inicio}` : '';
        const finParam = fin ? `&fin=${fin}` : '';

        const url = `/export/playas?tipo=asistencia-guardavida&inicio=${inicioParam}&fin=${finParam}&guardavida_id=${id}`;

        window.location.href = url;

        closeAsistenciaGuardavidaModal();
    }
</script>






<script src="{{ asset('js/table-intervenciones.js') }}"></script>
@endsection
