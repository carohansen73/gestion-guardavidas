{{-- bg-yellow-500/50 border-l-4 border-yellow-500 text-yellow-900 --}}


@auth
    @php
        $rol = Auth::user()->getRoleNames()->first();
        $playaModel = Auth::user()->guardavida->playa ?? null;
        $playaUsuario = $playaModel->nombre ?? null;
    @endphp
@endauth


{{-- Guardavidas/encargado ve bandera en su playa --}}
@if($rol !== 'admin' && $playaUsuario)

    @include('components.card-bandera', ['bandera' => $bandera, 'playa' => $playaModel])


@else
{{-- Admin ve carrusel de banderas por playa --}}

    @if($bandera && count($bandera) > 0)

        <div x-data="carousel({ total: {{ count($bandera) }} })" class="relative w-full overflow-hidden col-span-2 lg:row-span-2">

            <!-- Slides -->
            <div class="flex transition-transform duration-500"
                :style="`transform: translateX(-${current * 100}%);`">

                @foreach($bandera as $b)
                    <div class="w-full flex-shrink-0">
                        @include('components.card-bandera', ['bandera' => $b['bandera'], 'playa' => $b['playa']])
                    </div>
                @endforeach

            </div>

            <!-- Botón izquierda -->
            <button @click="prev"
                class="absolute left-6 top-1/2 z-10 -translate-y-1/2 flex h-9 w-9 items-center justify-center rounded-full bg-black/30 text-white backdrop-blur-sm shadow-md hover:bg-black/50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Botón derecha -->
            <button @click="next"
                class="absolute right-6 top-1/2 z-10 -translate-y-1/2 flex h-9 w-9 items-center justify-center rounded-full bg-black/30 text-white backdrop-blur-sm shadow-md hover:bg-black/50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Indicadores -->
            <div class="flex justify-center mt-2 space-x-2">
                <template x-for="i in total">
                    <div
                        class="w-2 h-2 rounded-full transition"
                        :class="current === i - 1
                            ? 'bg-sky-500'
                            : 'bg-gray-400 dark:bg-gray-600'">
                    </div>
                </template>
            </div>

        </div>
    @endif


@endif



<script>
document.addEventListener('alpine:init', () => {

    Alpine.data('carousel', ({ total }) => ({
        current: 0,
        total,

        next() {
            this.current = (this.current + 1) % this.total;
        },

        prev() {
            this.current = (this.current - 1 + this.total) % this.total;
        },

        autoplayInterval: null,

        init() {
            this.autoplayInterval = setInterval(() => this.next(), 5000);
        }
    }));
});
</script>

