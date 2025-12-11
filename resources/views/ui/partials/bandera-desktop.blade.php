{{-- bg-yellow-500/50 border-l-4 border-yellow-500 text-yellow-900 --}}


@auth
    @php
        $rol = Auth::user()->getRoleNames()->first();
        $playaUsuario = Auth::user()->guardavida->playa->nombre ?? null;
    @endphp
@endauth



@if($rol !== 'admin' && $playaUsuario)
    @if($bandera)

        @include('components.card-bandera-desktop', ['bandera' => $bandera])

    @else
    {{-- aun no se cargo la bandera del dia --}}

        <div class="p-2">
            <div class="bg-yellow-500/50 border-l-4 border-yellow-500 text-yellow-900 dark:from-blue-700 dark:to-teal-700 text-black rounded shadow-sm p-6 transform transition hover:scale-105 duration-300">
                <div class="flex justify-content-between align-center">
                    <div class="mb-2">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Bandera del día</h2>
                        <p class="text-sm text-gray-800 dark:text-gray-300">⚠️ No se ha cargado la bandera para hoy.</p>
                    </div>

                    @can('agregar_bandera')
                        <a href="{{ route('bandera.create') }}" class="">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-8 h-8 py-2 px-2 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 text-yellow-900 dark:text-white p-1 rounded-full">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </a>
                    @endcan
                </div>

                @can('ver_bandera')
                    <a href="{{ route('bandera.index') }}"
                        class="inline-flex items-center text-sm justify-center px-3 py-2 bg-gray-800/80 text-white rounded-full hover:bg-gray-700 transition mt-2">
                        Historial de banderas
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="3"
                            stroke="currentColor"
                            class="w-5 h-5 ms-3 bg-white p-1 text-gray-900 dark:text-white rounded-xl">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                @endcan
            </div>
        </div>
    @endif

{{-- Es admin --}}
@else

    @if($bandera && count($bandera) > 0)

        <div x-data="carousel({ total: {{ count($bandera) }} })" class="relative w-full overflow-hidden">

            <!-- Slides -->
            <div class="flex transition-transform duration-500"
                :style="`transform: translateX(-${current * 100}%);`">

                @foreach($bandera as $b)
                    <div class="w-full flex-shrink-0 px-2">
                        @include('components.card-bandera-desktop', ['bandera' => $b])
                    </div>
                @endforeach

            </div>

            <!-- Botón izquierda -->
            <button @click="prev"
                class="absolute left-2 top-1/2 -translate-y-1/2 bg-gray-700/40 text-white p-2 rounded-full">
                ‹
            </button>

            <!-- Botón derecha -->
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

