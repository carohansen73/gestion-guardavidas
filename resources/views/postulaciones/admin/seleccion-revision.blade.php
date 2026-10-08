@extends('layouts.app')
@section('content')

@php
    $input = 'rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm';
@endphp

<div class="text-gray-600 dark:text-gray-100 body-font sm:px-4">
    <div class="flex flex-wrap justify-between items-center gap-2 mb-2 sm:mt-4">
        <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl">Revisar selección</h2>
        <a href="{{ route('postulaciones.seleccion', ['temporada' => $temporada->id]) }}" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Volver a la lista</a>
    </div>
    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
        {{ $temporada->nombre }} · {{ $postulaciones->count() }} persona(s). Elegí la playa de cada una (obligatorio). El puesto y el turno son opcionales: si quedan sin definir, el propio guardavida los elige la primera vez que ingresa.
        Marcá "Encargado" solo donde corresponda; el resto queda con rol guardavida. Todavía no se guardó nada.
    </p>

    <x-session-alerts />

    @if ($descartadas > 0)
        <div class="mb-4 p-3 rounded-md bg-amber-100 text-amber-800 text-sm dark:bg-amber-900/40 dark:text-amber-300">
            {{ $descartadas }} postulación(es) de las que tildaste ya no se pueden seleccionar (no están aceptadas o ya fueron seleccionadas) y se quitaron de la lista.
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-md bg-red-100 text-red-800 text-sm dark:bg-red-900/40 dark:text-red-300">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('postulaciones.seleccion.confirmar') }}" id="form-revision">
        @csrf
        <input type="hidden" name="temporada" value="{{ $temporada->id }}">

        <div class="mb-4 max-w-xs">
            <label for="desde" class="block text-xs text-gray-500 dark:text-gray-400">Fecha de inicio en el plantel (para el presentismo)</label>
            <input type="date" id="desde" name="desde" value="{{ old('desde', now()->toDateString()) }}" required class="{{ $input }}">
        </div>

        {{-- Atajo: la misma playa y puesto para todos los que todavía no tienen puesto (ej. una tanda de una sola playa) --}}
        <div class="flex flex-wrap items-end gap-3 mb-4">
            <div>
                <label for="playa-masiva" class="block text-xs text-gray-500 dark:text-gray-400">Playa</label>
                <select id="playa-masiva" class="{{ $input }}">
                    <option value="">Elegir playa…</option>
                    @foreach ($playasConPuestos as $playa)
                        <option value="{{ $playa->id }}">{{ $playa->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="puesto-masivo" class="block text-xs text-gray-500 dark:text-gray-400">Puesto (opcional)</label>
                <select id="puesto-masivo" class="{{ $input }}"><option value="">Sin definir</option></select>
            </div>
            <button type="button" id="aplicar-masivo" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Asignar a los que no tienen</button>
        </div>
    </form>
</div>

<x-index-table :registros="$postulaciones" movil>
    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
        <tr>
            <th class="px-4 py-2 text-left">Apellido y nombre</th>
            <th class="px-4 py-2 text-left">Pidió</th>
            <th class="px-4 py-2 text-left">Hoy</th>
            <th class="px-4 py-2 text-left">Playa</th>
            <th class="px-4 py-2 text-left">Puesto</th>
            <th class="px-4 py-2 text-left">Turno</th>
            <th class="px-4 py-2 text-left">Encargado</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
        @foreach ($postulaciones as $postulacion)
            @php
                $id = $postulacion->id;
                $guardavida = $postulacion->user->guardavida;
                $preferidas = $postulacion->playas->pluck('id')->all();
                // Las playas que pidió aparecen primero en el selector de puestos.
                $ordenadas = $playasConPuestos->sortBy(fn ($pl) => in_array($pl->id, $preferidas, true) ? array_search($pl->id, $preferidas, true) : 99)->values();
                $puestoActual = old("filas.$id.puesto_id", $guardavida?->puesto_id);
                $faltaPuesto = $errors->has("filas.$id.puesto_id");
            @endphp
            <tr class="{{ $faltaPuesto ? 'bg-red-50 dark:bg-red-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <td class="px-4 py-2">
                    {{ $postulacion->user->lastname }}, {{ $postulacion->user->name }}
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $postulacion->user->dni }}</div>
                </td>
                <td class="px-4 py-2">{{ $postulacion->playas->pluck('nombre')->implode(' / ') ?: '—' }}</td>
                <td class="px-4 py-2">
                    @if ($guardavida)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300">Se actualiza</span>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $guardavida->playa?->nombre }} · {{ $guardavida->puesto?->nombre }}</div>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Se crea</span>
                    @endif
                </td>
                @php
                    $playaActual = old("filas.$id.playa_id", $guardavida?->playa_id ?? ($preferidas[0] ?? null));
                @endphp
                <td class="px-4 py-2">
                    <select name="filas[{{ $id }}][playa_id]" form="form-revision" required class="{{ $input }} playa-fila" data-fila="{{ $id }}">
                        <option value="">Elegir playa…</option>
                        @foreach ($ordenadas as $playa)
                            <option value="{{ $playa->id }}" @selected((int) $playaActual === $playa->id)>{{ $playa->nombre }}{{ in_array($playa->id, $preferidas, true) ? ' ★' : '' }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-4 py-2">
                    <select name="filas[{{ $id }}][puesto_id]" form="form-revision" class="{{ $input }} puesto-fila" data-fila="{{ $id }}" data-inicial="{{ $puestoActual }}">
                        <option value="">Sin definir (lo elige el guardavida)</option>
                    </select>
                </td>
                <td class="px-4 py-2">
                    @php $turnoActual = old("filas.$id.turno", $guardavida?->turno); @endphp
                    <select name="filas[{{ $id }}][turno]" form="form-revision" class="{{ $input }}">
                        <option value="">Sin definir</option>
                        <option value="M" @selected($turnoActual === 'M')>Mañana</option>
                        <option value="T" @selected($turnoActual === 'T')>Tarde</option>
                    </select>
                </td>
                <td class="px-4 py-2">
                    <input type="hidden" name="filas[{{ $id }}][encargado]" value="0" form="form-revision">
                    <input type="checkbox" name="filas[{{ $id }}][encargado]" value="1" form="form-revision"
                        @checked(old("filas.$id.encargado", $postulacion->user->hasRole('encargado')))
                        class="rounded border-gray-300 text-sky-600 focus:ring-sky-500 dark:border-gray-600">
                </td>
            </tr>
        @endforeach
    </tbody>
</x-index-table>

<div class="sm:px-4 pb-8 -mt-6 sm:-mt-8">
    <div class="flex flex-wrap items-center justify-end gap-3">
        <span class="text-sm text-gray-500 dark:text-gray-400">Se van a crear o actualizar {{ $postulaciones->count() }} guardavida(s).</span>
        <button type="submit" form="form-revision"
            class="px-4 py-2 rounded-md bg-sky-500 hover:bg-sky-400 text-white text-sm font-medium"
            onclick="return confirm('¿Confirmar la selección de {{ $postulaciones->count() }} persona(s)? Pasan a ser guardavidas de la temporada.')">
            Confirmar selección
        </button>
    </div>
</div>

<script>
    // Puestos agrupados por playa: el selector de puesto de cada fila muestra solo los de la playa elegida.
    var PUESTOS = @js($playasConPuestos->mapWithKeys(fn ($pl) => [$pl->id => $pl->puestos->map(fn ($pu) => ['id' => $pu->id, 'nombre' => $pu->nombre])->values()]));

    function llenarPuestos(sel, playaId, puestoId) {
        sel.innerHTML = '<option value="">Sin definir (lo elige el guardavida)</option>';
        (PUESTOS[playaId] || []).forEach(function (p) {
            var o = document.createElement('option');
            o.value = p.id; o.textContent = p.nombre;
            sel.appendChild(o);
        });
        sel.value = puestoId && (PUESTOS[playaId] || []).some(function (p) { return String(p.id) === String(puestoId); }) ? puestoId : '';
    }

    document.querySelectorAll('select.playa-fila').forEach(function (playaSel) {
        var puestoSel = document.querySelector('select.puesto-fila[data-fila="' + playaSel.dataset.fila + '"]');
        llenarPuestos(puestoSel, playaSel.value, puestoSel.dataset.inicial);
        playaSel.addEventListener('change', function () { llenarPuestos(puestoSel, playaSel.value, ''); });
    });

    // Atajo masivo: completa playa y puesto SOLO en las filas que todavía no tienen puesto.
    var playaMasiva = document.getElementById('playa-masiva');
    var puestoMasivo = document.getElementById('puesto-masivo');
    playaMasiva.addEventListener('change', function () { llenarPuestos(puestoMasivo, playaMasiva.value, ''); });
    document.getElementById('aplicar-masivo').addEventListener('click', function () {
        if (!playaMasiva.value) { return; }
        document.querySelectorAll('select.puesto-fila').forEach(function (puestoSel) {
            if (puestoSel.value) { return; }
            var playaSel = document.querySelector('select.playa-fila[data-fila="' + puestoSel.dataset.fila + '"]');
            playaSel.value = playaMasiva.value;
            llenarPuestos(puestoSel, playaMasiva.value, puestoMasivo.value);
        });
    });
</script>

@endsection
