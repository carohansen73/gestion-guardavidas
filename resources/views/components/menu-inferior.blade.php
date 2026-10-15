{{-- Menú inferior (bottom sheet) de acciones para los listados del celular. Va UNA vez por listado, dentro del
     x-data de la página, que debe tener `menu: null`. Lo abre el botón de tres puntos de <x-tarjeta-listado>
     con `menu = { titulo, acciones: [...] }`. Cada acción es un arreglo con:
       texto, icono (clase de Font Awesome, ej. "fa-pen"),
       href (+ blank)  -> link
       form + confirmar -> envía un DELETE a esa URL (pide confirmación si hay `confirmar`)
       evento + datos   -> dispara el evento `menu-accion` en window (lo escucha la página: ej. el modal de baja)
       peligro          -> la pinta en rojo --}}
<div x-show="menu" x-cloak class="fixed inset-0 z-[60] sm:hidden" role="dialog" aria-modal="true" aria-label="Acciones"
    @keydown.escape.window="menu = null">
    <div class="absolute inset-0 bg-black/50"
        x-show="menu" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="menu = null"></div>

    <div class="absolute inset-x-0 bottom-0 rounded-t-2xl bg-white pb-6 shadow-xl dark:bg-gray-800"
        x-show="menu" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">

        <div class="mx-auto mt-2 h-1 w-10 rounded-full bg-gray-300 dark:bg-gray-600"></div>
        <p class="truncate px-5 pt-3 pb-2 text-sm font-semibold text-gray-900 dark:text-white" x-text="menu?.titulo"></p>

        <div class="divide-y divide-gray-100 border-y border-gray-100 dark:divide-gray-700 dark:border-gray-700">
            <template x-for="(a, i) in (menu?.acciones ?? [])" :key="i">
                <div>
                    <template x-if="a.href">
                        <a :href="a.href" :target="a.blank ? '_blank' : null" :rel="a.blank ? 'noopener' : null"
                            class="flex items-center gap-3 px-5 py-3.5 text-gray-800 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-gray-700">
                            <i class="fas w-5 text-center text-sky-600 dark:text-sky-400" :class="a.icono" aria-hidden="true"></i>
                            <span x-text="a.texto"></span>
                        </a>
                    </template>
                    <template x-if="!a.href">
                        <button type="button"
                            @click="
                                if (a.form) {
                                    if (!a.confirmar || confirm(a.confirmar)) { $refs.formMenu.action = a.form; $refs.formMenu.submit(); }
                                } else {
                                    $dispatch('menu-accion', a); menu = null;
                                }"
                            class="flex w-full items-center gap-3 px-5 py-3.5 text-left"
                            :class="a.peligro ? 'text-red-700 hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-900/40' : 'text-gray-800 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-gray-700'">
                            <i class="fas w-5 text-center" :class="[a.icono, a.peligro ? '' : 'text-sky-600 dark:text-sky-400']" aria-hidden="true"></i>
                            <span x-text="a.texto"></span>
                        </button>
                    </template>
                </div>
            </template>
        </div>

        <button type="button" @click="menu = null" class="mt-3 w-full px-5 py-2 text-center text-sm font-semibold text-gray-600 dark:text-gray-300">Cancelar</button>
    </div>

    <form x-ref="formMenu" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="_method" value="DELETE">
    </form>
</div>
