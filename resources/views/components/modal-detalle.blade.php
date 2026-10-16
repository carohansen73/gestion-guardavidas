{{-- Modal del detalle (solo lectura) de un registro, para escritorio y celular (en el celular sube como hoja desde abajo).
     Va UNA vez por listado. Se abre con cualquier link que tenga el atributo `data-detalle` (click normal; con Ctrl/Cmd/Shift
     o botón del medio el navegador lo abre en otra pestaña, como página completa) o disparando `abrir-detalle` en window
     con la URL. Pide esa URL por AJAX: los controladores `show` devuelven solo el contenido (`partials/detalle`) cuando
     la petición es AJAX, y la página completa si no. --}}
<div x-data="modalDetalle()" x-cloak x-show="abierto" @abrir-detalle.window="abrir($event.detail)" @keydown.escape.window="cerrar()"
    class="fixed inset-0 z-[70] flex items-end justify-center sm:items-center sm:p-6" role="dialog" aria-modal="true" aria-label="Detalle">

    <div class="absolute inset-0 bg-black/50" x-show="abierto"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="cerrar()"></div>

    <div class="relative flex max-h-[90vh] w-full flex-col overflow-hidden rounded-t-2xl bg-white shadow-xl dark:bg-gray-800 sm:max-w-2xl sm:rounded-2xl"
        x-show="abierto"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-8 opacity-0 sm:translate-y-0 sm:scale-95" x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <button type="button" @click="cerrar()" aria-label="Cerrar"
            class="absolute right-3 top-3 z-10 flex h-9 w-9 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
            <i class="fas fa-xmark text-lg" aria-hidden="true"></i>
        </button>

        <div class="overflow-y-auto overscroll-contain">
            <div x-show="cargando" class="flex items-center justify-center gap-3 px-6 py-16 text-gray-500 dark:text-gray-300">
                <i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Cargando…
            </div>
            <p x-show="error" x-text="error" class="px-6 py-16 text-center text-sm text-red-700 dark:text-red-300"></p>
            <div x-show="!cargando && !error" x-html="html"></div>
        </div>
    </div>
</div>

<script>
    function modalDetalle() {
        return {
            abierto: false, cargando: false, error: '', html: '', pedido: 0,

            async abrir(url) {
                const pedido = ++this.pedido;
                this.abierto = true; this.cargando = true; this.error = ''; this.html = '';
                document.body.classList.add('overflow-hidden');
                try {
                    const r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, credentials: 'same-origin' });
                    if (pedido !== this.pedido) return;
                    if (r.status === 403) throw new Error('No tenés permiso para ver este registro.');
                    if (!r.ok) throw new Error('No se pudo cargar el detalle. Probá de nuevo.');
                    this.html = await r.text();
                } catch (e) {
                    if (pedido === this.pedido) this.error = e.message || 'No se pudo cargar el detalle.';
                } finally {
                    if (pedido === this.pedido) this.cargando = false;
                }
            },

            cerrar() {
                if (!this.abierto) return;
                this.abierto = false; this.pedido++;
                document.body.classList.remove('overflow-hidden');
            },
        };
    }

    // Un solo listener para todos los links con data-detalle (tablas, tarjetas y el menú inferior del celular).
    if (!window.__detalleListo) {
        window.__detalleListo = true;
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[data-detalle]');
            if (!link || e.defaultPrevented || e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('abrir-detalle', { detail: link.href }));
        });
    }
</script>
