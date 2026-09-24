<div class="flex flex-wrap gap-3">
    @can('agregar_bandera')
        <a href="{{ route('bandera.create') }}"
            class="group flex items-center gap-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 pl-3 pr-5 py-2.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </span>
            <span class="text-left leading-tight">
                <span class="block text-xs text-gray-400">Agregar</span>
                <span class="block text-sm font-semibold text-gray-800 dark:text-white">Bandera</span>
            </span>
        </a>
    @endcan

    @can('agregar_intervencion')
        <a href="{{ route('intervencion.create') }}"
            class="group flex items-center gap-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 pl-3 pr-5 py-2.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </span>
            <span class="text-left leading-tight">
                <span class="block text-xs text-gray-400">Agregar</span>
                <span class="block text-sm font-semibold text-gray-800 dark:text-white">Intervención</span>
            </span>
        </a>
    @endcan

    @can('agregar_novedad_material')
        <a href="{{ route('novedad-de-material.create') }}"
            class="group flex items-center gap-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 pl-3 pr-5 py-2.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </span>
            <span class="text-left leading-tight">
                <span class="block text-xs text-gray-400">Agregar</span>
                <span class="block text-sm font-semibold text-gray-800 dark:text-white">Novedad de material</span>
            </span>
        </a>
    @endcan
</div>
