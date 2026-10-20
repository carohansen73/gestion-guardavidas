@extends('layouts.app')

{{-- Vista del celular. La de escritorio es index-desktop.blade.php (el controlador elige por dispositivo). --}}
@section('content')
    @php
        $chip = fn (bool $activo) => ($activo ? 'bg-sky-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200').' shrink-0 whitespace-nowrap rounded-full px-3 py-1 text-sm';
        $periodo = $inicio->format('d/m').' – '.$fin->format('d/m/Y');
    @endphp

    <div class="text-gray-600 dark:text-gray-100 body-font sm:px-4">
        <div class="mb-4 flex items-center justify-between sm:mt-4">
            <h2 class="text-gray-700 dark:text-white text-2xl font-bold tracking-tight"> Asistencias </h2>
            <x-boton-exportar-asistencia-general />
        </div>

        {{-- Playas: una sola fila que se desliza, para no ocupar alto. --}}
        <div class="-mx-4 mb-3 flex gap-2 overflow-x-auto px-4 pb-1">
            <a href="{{ route('asistencias.index', ['playa_id' => 'all'] + request()->except(['page', 'playa_id'])) }}"
                class="{{ $chip(! request('playa_id') || request('playa_id') == 'all') }}">Todas</a>
            @foreach ($playas as $playa)
                <a href="{{ route('asistencias.index', ['playa_id' => $playa->id] + request()->except(['page', 'playa_id'])) }}"
                    class="{{ $chip(request('playa_id') == $playa->id) }}">{{ $playa->nombre }}</a>
            @endforeach
        </div>

        <form method="GET" id="filtros-asistencias" x-data="{ abierto: false }" class="mb-4 space-y-2">
            <input type="hidden" name="playa_id" value="{{ request('playa_id') }}">

            {{-- Búsqueda en vivo (ver el script al final) --}}
            <div class="relative">
                <input type="search" name="search" id="search" value="{{ request('search') }}" placeholder="Buscar por nombre o apellido"
                    autocomplete="off"
                    class="block w-full rounded-md border-gray-300 pr-10 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="pointer-events-none absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>

            <div class="flex items-center gap-2">
                {{-- Mes: flechas para pasar de un mes al otro (el presentismo se mira por mes). Tocar el nombre abre el
                     período a mano / la temporada. Con fechas a mano o temporada no hay flechas: se ve el período. --}}
                <div class="flex min-w-0 flex-1 items-center rounded-md border border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-700">
                    @if ($mesNav)
                        <a href="{{ route('asistencias.index', ['mes' => $mesNav['anterior']] + request()->only(['playa_id', 'search', 'orden'])) }}"
                            aria-label="Mes anterior"
                            class="flex h-10 w-10 shrink-0 items-center justify-center text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                        </a>
                    @endif

                    <button type="button" @click="abierto = !abierto"
                        class="flex h-10 min-w-0 flex-1 items-center justify-center gap-1.5 px-2 text-sm font-medium text-gray-800 dark:text-gray-100">
                        <span class="truncate">{{ $mesNav['nombre'] ?? (($temporadaElegida ? $temporadaElegida->nombre.': ' : '').$periodo) }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            class="h-4 w-4 shrink-0 transition" :class="abierto ? 'rotate-180' : ''" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    @if ($mesNav)
                        @if ($mesNav['siguiente'])
                            <a href="{{ route('asistencias.index', ['mes' => $mesNav['siguiente']] + request()->only(['playa_id', 'search', 'orden'])) }}"
                                aria-label="Mes siguiente"
                                class="flex h-10 w-10 shrink-0 items-center justify-center text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                            </a>
                        @else
                            {{-- Mes en curso: no hay un mes siguiente al que ir. --}}
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center text-gray-300 dark:text-gray-500" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                            </span>
                        @endif
                    @endif
                </div>

                <select name="orden" id="orden" aria-label="Ordenar"
                    class="rounded-md border-gray-300 py-2 text-sm text-gray-700 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <option value="alfabetico" @selected(request('orden', 'alfabetico') !== 'faltas')>A-Z</option>
                    <option value="faltas" @selected(request('orden') === 'faltas')>Más faltas</option>
                </select>
            </div>

            {{-- Período: temporada o fechas a mano (elegir una borra la otra, igual que en escritorio). --}}
            <div x-show="abierto" x-cloak
                class="space-y-3 rounded-md border border-gray-200 bg-gray-50 p-3 dark:border-gray-600 dark:bg-gray-700">
                <div>
                    <label for="temporada" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Temporada</label>
                    <select name="temporada" id="temporada"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        onchange="document.getElementById('inicio').value = ''; document.getElementById('fin').value = ''; this.form.submit()">
                        <option value="">Elegir…</option>
                        @foreach (($temporadas ?? []) as $t)
                            <option value="{{ $t->id }}" @selected(($temporadaElegida?->id) === $t->id)>{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Desde</label>
                        <input type="date" name="inicio" id="inicio" value="{{ $inicio->toDateString() }}"
                            oninput="document.getElementById('temporada').value = ''"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hasta</label>
                        <input type="date" name="fin" id="fin" value="{{ $fin->toDateString() }}"
                            oninput="document.getElementById('temporada').value = ''"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('asistencias.index') }}" class="text-sm text-gray-600 hover:underline dark:text-gray-300">Mes actual</a>
                    <button class="rounded-full bg-sky-500 px-5 py-2 text-sm text-white shadow hover:bg-sky-400">Filtrar</button>
                </div>
            </div>
        </form>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded my-2 dark:bg-green-900/40 dark:text-green-300">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded my-2 dark:bg-red-900/40 dark:text-red-300">{{ session('error') }}</div>
        @endif

        <div id="lista-asistencias">
            @include('asistencias.partials.lista-mobile')
        </div>
    </div>

    {{-- Búsqueda y orden en vivo: pide al servidor la misma página con los filtros actuales (así respeta playa,
         período y paginado, que del lado del cliente no se verían: solo hay 10 personas cargadas) y reemplaza
         únicamente la lista. Sin JS, el formulario sigue funcionando como un GET normal (Enter). --}}
    <script>
        (function () {
            const form = document.getElementById('filtros-asistencias');
            const lista = document.getElementById('lista-asistencias');
            const buscador = document.getElementById('search');
            const orden = document.getElementById('orden');
            let espera = null;
            let pedido = null;

            function url() {
                const params = new URLSearchParams();
                new FormData(form).forEach((valor, clave) => { if (valor !== '') params.set(clave, valor); });
                return window.location.pathname + (params.toString() ? '?' + params : '');
            }

            function actualizar() {
                const destino = url();
                if (pedido) pedido.abort();
                pedido = new AbortController();
                lista.classList.add('opacity-60');

                fetch(destino, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: pedido.signal })
                    .then(r => { if (!r.ok) throw new Error(r.status); return r.text(); })
                    .then(html => {
                        lista.innerHTML = html;
                        lista.classList.remove('opacity-60');
                        history.replaceState(null, '', destino);
                    })
                    .catch(e => { if (e.name !== 'AbortError') window.location.href = destino; });
            }

            buscador.addEventListener('input', () => { clearTimeout(espera); espera = setTimeout(actualizar, 300); });
            orden.addEventListener('change', actualizar);
            // Enter en el buscador: que no recargue la página, ya se actualiza solo.
            form.addEventListener('submit', e => { if (document.activeElement === buscador) { e.preventDefault(); clearTimeout(espera); actualizar(); } });
        })();
    </script>
@endsection
