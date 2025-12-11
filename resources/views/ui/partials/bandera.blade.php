@auth
    @php
        $rol = Auth::user()->getRoleNames()->first();
        $playaUsuario = Auth::user()->guardavida->playa->nombre ?? null;
    @endphp
    {{-- si es guardavida --}}
    @if($rol !== 'admin' && $playaUsuario)

        @if($bandera)
            @include('components.card-bandera', ['bandera' => $bandera])
        @else
        {{-- si no hay bandera --}}
            <div class="px-4 py-4">
                <div class="bg-yellow-500/50 border-l-4 border-yellow-500 text-yellow-900 dark:bg-gray-600 dark:text-gray-300 rounded-2xl p-6 transform transition focus:scale-105 duration-300">
                        <!-- Contenido principal: playa izquierda, bandera derecha -->
                    <div class="flex justify-content-between align-center">
                        <div>
                            <h2 class="text-2xl md:text-3xl font-bold">Bandera del día</h2>
                            <p>⚠️ No se ha cargado la bandera para hoy.</p>
                        </div>
                        <a href="{{ route('bandera.create') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-8 h-8 py-2 px-2 bg-gray-800/60 text-white rounded-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif

    @else  {{-- si es admin --}}
        @if($bandera)

        <div
    x-data="carousel({ total: {{ count($bandera) }} })"
    class="relative w-full overflow-hidden"
>

    <!-- Slides -->
    <div class="flex transition-transform duration-500"
         :style="`transform: translateX(-${current * 100}%);`">

        @foreach($bandera as $b)
            <div class="w-full flex-shrink-0 px-2">
                {{-- ACA VA TU CARD --}}
                @include('components.card-bandera', ['bandera' => $b])
            </div>
        @endforeach

    </div>

    <!-- Controles -->
    <button @click="prev"
            class="absolute left-2 top-1/2 -translate-y-1/2 bg-gray-700/40 text-white p-2 rounded-full">
        ‹
    </button>

    <button @click="next"
            class="absolute right-2 top-1/2 -translate-y-1/2 bg-gray-700/40 text-white p-2 rounded-full">
        ›
    </button>

    <!-- Indicadores -->
    <div class="flex justify-center mt-2 space-x-2">
        <template x-for="i in total">
            <div
                class="w-2 h-2 rounded-full transition"
                :class="current === i - 1
                    ? 'bg-sky-500'
                    : 'bg-gray-400 dark:bg-gray-600'"
            ></div>
        </template>
    </div>

</div>


        @endif
    @endif
@endauth


<script>
document.addEventListener('alpine:init', () => {

    Alpine.data('carousel', ({ total }) => ({
        current: 0,
        total: total,

        next() {
            this.current = (this.current + 1) % this.total;
        },

        prev() {
            this.current = (this.current - 1 + this.total) % this.total;
        },

        // Auto-slide opcional (desactivalo si no lo querés)
        autoplayInterval: null,

        init() {
            this.autoplayInterval = setInterval(() => this.next(), 5000);
        }
    }));
});
</script>
