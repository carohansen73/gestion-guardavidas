@extends('layouts.app')
@section('content')

@php
    $input = 'rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm';
    $idsPagina = $postulaciones->getCollection()
        ->filter(fn ($p) => $p->estado === 'aceptada' && ! $p->seleccionado)->pluck('id')->values();
@endphp

{{-- La selección vive en el navegador (sessionStorage) para que no se pierda al cambiar de página o de filtro. --}}
<script>
    function seleccionPostulantes(o) {
        return {
            ids: [],
            init() {
                try { this.ids = JSON.parse(sessionStorage.getItem(o.clave) || '[]'); } catch (e) { this.ids = []; }
                this.$watch('ids', () => { try { sessionStorage.setItem(o.clave, JSON.stringify(this.ids)); } catch (e) {} });
            },
            tiene(id) { return this.ids.includes(id); },
            alternar(id) { this.tiene(id) ? this.ids = this.ids.filter(i => i !== id) : this.ids.push(id); },
            get paginaCompleta() { return o.pagina.length > 0 && o.pagina.every(i => this.tiene(i)); },
            alternarPagina() {
                this.paginaCompleta
                    ? this.ids = this.ids.filter(i => !o.pagina.includes(i))
                    : this.ids = [...new Set([...this.ids, ...o.pagina])];
            },
            agregarTodos() { this.ids = [...new Set([...this.ids, ...o.todos])]; },
            limpiar() { this.ids = []; },
            get cantidad() { return this.ids.length; },
            get urlRevisar() { return o.urlRevisar + '?temporada=' + o.temporada + '&ids=' + this.ids.join(','); },
        };
    }
    @if (session('limpiar_seleccion'))
        try { sessionStorage.removeItem('seleccion-temporada-{{ session('limpiar_seleccion') }}'); } catch (e) {}
    @endif
</script>

<div x-data="seleccionPostulantes({
        clave: 'seleccion-temporada-{{ $temporadaId }}',
        temporada: {{ $temporadaId }},
        pagina: @js($idsPagina),
        todos: @js($idsSeleccionables),
        urlRevisar: @js(route('postulaciones.seleccion.revisar')),
    })">

<div class="text-gray-600 dark:text-gray-100 body-font sm:px-4">

    <div class="flex flex-wrap justify-between items-center gap-2 mb-4 sm:mt-4">
        <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl">Selección de postulantes</h2>
        <div class="flex gap-2">
            <a href="{{ route('postulaciones.index', ['temporada' => $temporadaId]) }}" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Volver a postulaciones</a>
            <a href="{{ route('postulaciones.seleccion.cierre', ['temporada' => $temporadaId]) }}" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Cerrar selección</a>
        </div>
    </div>

    <x-session-alerts />

    {{-- Resumen de la temporada --}}
    <div class="flex flex-wrap gap-2 mb-4 text-sm">
        <span class="px-3 py-1.5 rounded-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
            Aceptadas <span class="font-semibold">{{ $resumen['aceptadas'] }}</span>
        </span>
        <span class="px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
            Seleccionadas <span class="font-semibold">{{ $resumen['seleccionadas'] }}</span>
        </span>
        @foreach ($playas as $playa)
            @if (($resumen['porPlaya'][$playa->id] ?? 0) > 0)
                <span class="px-3 py-1.5 rounded-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
                    {{ $playa->nombre }} <span class="font-semibold">{{ $resumen['porPlaya'][$playa->id] }}</span>
                </span>
            @endif
        @endforeach
    </div>

    <form method="GET" action="{{ route('postulaciones.seleccion') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div>
            <label for="temporada" class="block text-xs text-gray-500 dark:text-gray-400">Temporada</label>
            <select name="temporada" id="temporada" class="{{ $input }}" onchange="this.form.submit()">
                @foreach ($temporadas as $temporada)
                    <option value="{{ $temporada->id }}" @selected($temporada->id === $temporadaId)>{{ $temporada->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="playa" class="block text-xs text-gray-500 dark:text-gray-400">Playa preferida</label>
            <select name="playa" id="playa" class="{{ $input }}" onchange="this.form.submit()">
                <option value="">Todas</option>
                @foreach ($playas as $playa)
                    <option value="{{ $playa->id }}" @selected((int) $playaId === $playa->id)>{{ $playa->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="ex" class="block text-xs text-gray-500 dark:text-gray-400">¿Ya fue guardavida?</label>
            <select name="ex" id="ex" class="{{ $input }}" onchange="this.form.submit()">
                <option value="todos" @selected($fueGuardavida === 'todos')>Todos</option>
                <option value="si" @selected($fueGuardavida === 'si')>Sí, ya fueron guardavidas</option>
                <option value="no" @selected($fueGuardavida === 'no')>No, son nuevos</option>
            </select>
        </div>
        <div>
            <label for="estado" class="block text-xs text-gray-500 dark:text-gray-400">Estado</label>
            <select name="estado" id="estado" class="{{ $input }}" onchange="this.form.submit()">
                <option value="aceptada" @selected($estado === 'aceptada')>Aceptadas</option>
                <option value="pendiente" @selected($estado === 'pendiente')>Pendientes</option>
                <option value="rechazada" @selected($estado === 'rechazada')>Rechazadas</option>
                <option value="todas" @selected($estado === 'todas')>Todas</option>
            </select>
        </div>
        <div>
            <label for="ya" class="block text-xs text-gray-500 dark:text-gray-400">Ya seleccionados</label>
            <select name="ya" id="ya" class="{{ $input }}" onchange="this.form.submit()">
                <option value="ocultar" @selected($ya === 'ocultar')>Ocultar</option>
                <option value="mostrar" @selected($ya === 'mostrar')>Mostrar</option>
                <option value="solo" @selected($ya === 'solo')>Ver solo esos</option>
            </select>
        </div>
        <div>
            <label for="buscar" class="block text-xs text-gray-500 dark:text-gray-400">Buscar</label>
            <input type="text" name="buscar" id="buscar" value="{{ $buscar }}" placeholder="Nombre, apellido o DNI" class="{{ $input }}">
        </div>
        <button type="submit" class="px-3 py-2 rounded-md bg-sky-500 hover:bg-sky-400 text-white text-sm">Filtrar</button>
    </form>

    {{-- Contador siempre visible (arriba): cuántos tildados hay en total, aunque estén en otras páginas o filtros --}}
    <div class="flex flex-wrap items-center gap-3 mb-3 p-3 rounded-md border text-sm"
        :class="cantidad > 0 ? 'bg-sky-50 border-sky-300 text-sky-900 dark:bg-sky-900/30 dark:border-sky-700 dark:text-sky-100' : 'bg-white border-gray-300 dark:bg-gray-800 dark:border-gray-600'">
        <span>Tildados: <strong class="text-base" x-text="cantidad"></strong></span>
        <a x-show="cantidad > 0" x-cloak :href="urlRevisar" class="px-3 py-1.5 rounded-md bg-sky-500 hover:bg-sky-400 text-white font-medium">Revisar y confirmar</a>
        <span x-show="cantidad === 0" class="text-gray-500 dark:text-gray-400">Tildá a las personas y después tocá "Revisar y confirmar".</span>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-2 text-sm">
        <span>{{ $postulaciones->total() }} resultado(s) · {{ count($idsSeleccionables) }} se pueden seleccionar</span>
        @if (count($idsSeleccionables) > 0)
            <button type="button" @click="agregarTodos()" class="text-sky-600 hover:text-sky-500 dark:text-sky-400 underline">
                Tildar los {{ count($idsSeleccionables) }} de este filtro
            </button>
        @endif
        <button type="button" x-show="cantidad > 0" x-cloak @click="limpiar()" class="text-gray-500 hover:text-gray-400 underline">Limpiar selección</button>
    </div>
</div>

<x-index-table :registros="$postulaciones" movil paginar>
    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
        <tr>
            <th class="px-4 py-2 text-left w-10">
                <input type="checkbox" :checked="paginaCompleta" @change="alternarPagina()" title="Tildar esta página"
                    class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
            </th>
            <th class="px-4 py-2 text-left">Apellido y nombre</th>
            <th class="px-4 py-2 text-left">DNI</th>
            <th class="px-4 py-2 text-left">Playas preferidas</th>
            <th class="px-4 py-2 text-left">Hoy</th>
            <th class="px-4 py-2 text-left">Estado</th>
            <th class="px-4 py-2 text-left"></th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
        @forelse ($postulaciones as $postulacion)
            @php
                $seleccionable = $postulacion->estado === 'aceptada' && ! $postulacion->seleccionado;
                $guardavida = $postulacion->user->guardavida;
            @endphp
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-2">
                    @if ($seleccionable)
                        <input type="checkbox" :checked="tiene({{ $postulacion->id }})" @change="alternar({{ $postulacion->id }})"
                            class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    @else
                        <input type="checkbox" disabled class="rounded border-gray-300 opacity-40">
                    @endif
                </td>
                <td class="px-4 py-2">{{ $postulacion->user->lastname }}, {{ $postulacion->user->name }}</td>
                <td class="px-4 py-2">{{ $postulacion->user->dni }}</td>
                <td class="px-4 py-2">{{ $postulacion->playas->pluck('nombre')->implode(' / ') ?: '—' }}</td>
                <td class="px-4 py-2">
                    @if ($postulacion->seleccionado)
                        <span class="text-emerald-700 dark:text-emerald-300">Seleccionado/a: {{ $postulacion->playaAsignada?->nombre }} · {{ $postulacion->puestoAsignado?->nombre }}</span>
                    @elseif ($guardavida)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300">Ya fue guardavida</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $guardavida->playa?->nombre }} · {{ $guardavida->puesto?->nombre }}</span>
                    @else
                        <span class="text-xs text-gray-500 dark:text-gray-400">Nuevo/a</span>
                    @endif
                </td>
                <td class="px-4 py-2">{{ ucfirst($postulacion->estado) }}</td>
                <td class="px-4 py-2 whitespace-nowrap">
                    <a href="{{ route('postulaciones.show', $postulacion) }}" class="text-sky-500 hover:text-sky-400 dark:text-sky-400">Ver</a>
                    @if ($postulacion->seleccionado)
                        <form method="POST" action="{{ route('postulaciones.deseleccionar', $postulacion) }}" class="inline"
                            onsubmit="return confirm('¿Sacar a esta persona de la selección? Vuelve a ser postulante.')">
                            @csrf
                            <button type="submit" class="ms-3 text-red-500 hover:text-red-400">Sacar</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No hay postulaciones para estos filtros.</td>
            </tr>
        @endforelse
    </tbody>
</x-index-table>

{{-- Mismo contador y botón debajo de la lista (sin barra flotante que tape las filas) --}}
<div class="sm:px-4 pb-8 -mt-6 sm:-mt-8">
    <div x-show="cantidad > 0" x-cloak class="flex flex-wrap items-center justify-end gap-3 text-sm">
        <span>Tildados: <strong class="text-base" x-text="cantidad"></strong></span>
        <a :href="urlRevisar" class="px-4 py-2 rounded-md bg-sky-500 hover:bg-sky-400 text-white font-medium">Revisar y confirmar</a>
    </div>
</div>

</div>

@endsection
