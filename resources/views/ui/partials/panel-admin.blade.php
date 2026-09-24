{{--
    Panel de estadísticas, solo para admin. Antes era la vista /dashboard
    separada (ui/dashboard.blade.php); ahora se incluye acá, dentro de
    home-desktop/home-mobile, para que el admin tenga todo en un solo lugar
    en vez de dos pantallas de "inicio" distintas. Requiere $esAdmin,
    $playas, $totales, $intervencionesPorPlaya, $novedadesMaterialesPorPlaya,
    $guardavidasPorPlaya, $asistenciasHoy, $fueraDeRango30d,
    $licenciasActivasHoy y $novedades (esta última ya se calculaba en
    HomeController::index() para todos, solo que no se usaba en ningún lado).
--}}
@if ($esAdmin)
<div class="mt-4 px-6 pb-8">
    <div class="flex items-center gap-3 mb-1">
        <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
            Panel de administración
        </h2>
        <span class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></span>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
        Mostrando datos de <span class="tituloPlayaSeleccionada font-medium text-sky-600 dark:text-sky-400">todas las playas</span>
    </p>

    <!-- Filtro playas: va acá, antes de las cards, para que se entienda que las
         cards/gráfico de abajo se pueden filtrar por playa (en mobile, la
         columna del filtro quedaba después de las cards y no se entendía). -->
    <div class="mb-5 flex flex-wrap gap-2 items-center">
        <button
            class="btn-filtro bg-sky-600 text-white px-3 py-1 rounded-full text-xs font-medium transition"
            data-playa=""
            data-nombre="Todas">
            Todas
        </button>
        <!-- Botones por cada playa -->
        @foreach($playas as $playa)
            <button
                class="btn-filtro bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 px-3 py-1 rounded-full text-xs font-medium transition"
                data-playa="{{ $playa->id }}"
                data-nombre="{{ $playa->nombre }}">
                {{ $playa->nombre }}
            </button>
        @endforeach
    </div>

    <div class="flex flex-col md:flex-row gap-6">

        <!-- 🟦 Columna principal (2/3 del ancho) -->
        <main class="w-full md:w-2/3 space-y-6">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 md:gap-6">

                <!-- Metric Intervenciones Start  -->
                <div class="rounded-2xl border border-gray-200 bg-white p-4 py-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <div class="flex justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/40">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="text-orange-600 dark:text-orange-400 p-2 inline-block">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288" />
                                </svg>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                                Intervenciones
                            </div>
                            <span id="card-intervenciones" class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                {{ $totales['intervenciones'] }}
                            </span>
                        </div>

                        <div class="flex flex-col justify-end items-end text-end" id="porcentajeIntervencionesPorPlaya">
                        </div>
                    </div>
                </div>
                <!-- Metric Intervenciones End -->

                <!-- Metric Novedades de materiales Start  -->
                <div class="rounded-2xl border border-gray-200 bg-white p-4 py-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <div class="flex justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-100 dark:bg-teal-900/40">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="text-teal-600 dark:text-teal-400 p-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                                Novedades materiales
                            </div>
                            <span id="card-novedades" class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                {{ $totales['novedades'] }}
                            </span>
                        </div>
                        <div class="flex flex-col justify-end items-end text-end"  id="porcentajeNovedadesPorPlaya">
                        </div>
                    </div>
                </div>
                <!-- Metric Novedades de materiales End -->

                <!-- Metric Guardavidas activos Start -->
                <div class="rounded-2xl border border-gray-200 bg-white p-4 py-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <div class="flex justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-900/40">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="text-sky-600 dark:text-sky-400 p-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                </svg>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                                Guardavidas activos
                            </div>
                            <span id="card-guardavidas-activos" class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                {{ $totales['guardavidas'] }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Metric Guardavidas activos End -->

                <!-- Metric Asistencias hoy Start -->
                <div class="rounded-2xl border border-gray-200 bg-white p-4 py-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <div class="flex justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/40">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="text-emerald-600 dark:text-emerald-400 p-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                                Asistencias de hoy
                            </div>
                            <span id="card-asistencias-hoy" class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                {{ $asistenciasHoy }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Metric Asistencias hoy End -->

                <!-- Metric Fuera de rango Start -->
                <div class="rounded-2xl border border-gray-200 bg-white p-4 py-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <div class="flex justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/40">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="text-amber-600 dark:text-amber-400 p-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-4" title="Fichajes a más de 200m del puesto según GPS">
                                Fuera de rango (30 días)
                            </div>
                            <span id="card-fuera-de-rango" class="text-2xl font-bold {{ $fueraDeRango30d > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-800 dark:text-white/90' }}">
                                {{ $fueraDeRango30d }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Metric Fuera de rango End -->

                <!-- Metric Licencias activas Start -->
                <div class="rounded-2xl border border-gray-200 bg-white p-4 py-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <div class="flex justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-900/40">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="text-violet-600 dark:text-violet-400 p-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                                Licencias activas hoy
                            </div>
                            <span id="card-licencias-activas" class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                {{ $licenciasActivasHoy }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Metric Licencias activas End -->
            </div>

            <div class="rounded-2xl border border-gray-100 dark:border-gray-700/60 bg-white dark:bg-gray-800 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Banderas más izadas</h3>
                <canvas id="graficoBanderas" height="120"></canvas>
            </div>
        </main>
        <!-- End Columna principal (2/3 del ancho) -->

        <!-- 🟨 Aside lateral (1/3 del ancho) -->
        <aside class="w-full md:w-1/3">

            <!-- Guardavidas por playa -->
            <div class="rounded-2xl border border-gray-100 dark:border-gray-700/60 bg-white dark:bg-gray-800 shadow-sm p-5 mb-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">
                    Guardavidas por playa
                </h3>
                <ul id="listaGuardavidasPorPlaya" class="space-y-2">
                    @forelse ($guardavidasPorPlaya as $item)
                        <li class="flex items-center justify-between text-sm text-gray-700 dark:text-gray-200">
                            <span>{{ $item->playa->nombre ?? 'Sin playa' }}</span>
                            <span class="font-semibold">{{ $item->total }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500 dark:text-gray-400">No hay guardavidas activos.</li>
                    @endforelse
                </ul>
            </div>

            <div class="rounded-2xl border border-gray-100 dark:border-gray-700/60 bg-white dark:bg-gray-800 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">
                    Últimas novedades
                </h3>

                <ol class="relative border-s border-gray-200 dark:border-gray-700 mx-2 my-2">
                    @foreach ($novedades as $index => $novedad)
                        {{--
                            $novedad->color trae valores heterogéneos según qué la generó
                            (BanderaObserver guarda el código de BanderaTipo, ej.
                            "bandera-bueno"; IntervencionObserver/NovedadMaterialObserver
                            guardan un nombre de color suelto como "orange"/"teal") — ninguno
                            de esos es una clase de Tailwind válida por sí sola, así que se
                            mapean acá a bg+texto reales. Para bandera, esto es lo que hace
                            que el color del ícono refleje el estado de la bandera del día.
                        --}}
                        @php
                            $coloresNovedad = [
                                'bandera-bueno' => 'text-sky-500',
                                'bandera-dudoso' => 'text-yellow-500',
                                'bandera-peligroso' => 'text-red-700 dark:text-red-500',
                                'bandera-rayos' => 'text-gray-900 dark:text-gray-100',
                                'bandera-prohibido' => 'text-red-700 dark:text-red-500',
                                'bandera-perdido' => 'text-gray-400 dark:text-gray-300',
                                'orange' => 'text-orange-500',
                                'teal' => 'text-teal-500',
                            ];
                            $colorClase = $coloresNovedad[$novedad->color] ?? 'text-gray-400';
                        @endphp

                        <li class="mb-5 ms-6">
                            <span class="{{ $colorClase }}
                                absolute flex items-center justify-center w-6 h-6 text-lg -start-3">
                                {!! $novedad->icono !!}
                            </span>
                            <p class="flex items-center gap-2 mb-0.5 text-sm font-semibold text-gray-900 dark:text-white">
                                {{$novedad->titulo}}
                                @if ($loop->first)
                                <span class="bg-sky-100 text-sky-700 text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full dark:bg-sky-900/50 dark:text-sky-300">
                                    Nuevo
                                </span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                {{$novedad->playa->nombre}} · {{ $novedad->fecha }}
                            </p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </aside>
    </div>
</div>

@vite(['resources/js/dashboard-charts.js'])
@endif
