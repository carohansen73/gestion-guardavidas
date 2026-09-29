    @php
        $hayBandera = (bool) $bandera;
        $playaActual = $bandera->playa ?? $playa;
        $fecha = $hayBandera ? \Carbon\Carbon::parse($bandera->fecha)->locale('es') : null;
        $puedeCargarBandera = auth()->user()->can('agregar_bandera');
        $hrefCard = $hayBandera || ! $puedeCargarBandera ? route('bandera.index') : route('bandera.create');
    @endphp


        {{-- <div class="p-2"> --}}
            <section class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-sky-500 dark:from-blue-900 dark:to-sky-700 rounded shadow-sm p-6 md:px-10 transform transition hover:scale-105 duration-300 col-span-2 lg:row-span-2">
                <a href="{{ $hrefCard }}">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="pointer-events-none  text-sky-900 absolute -bottom-10 -right-8 h-56 w-56 opacity-20">
                        <path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.6 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>
                        <path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.6 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>
                        <path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.6 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>
                    </svg>


                    <p class="text-xs uppercase tracking-wide text-sky-200 dark:text-gray-200 mb-1">
                        <i class="fas fa-umbrella-beach me-1"></i> {{ $playaActual->nombre }}
                    </p>


                    {{-- Estado bandera --}}
                    <div class="mt-2 flex items-center gap-4">
                        <div class="{{ $hayBandera ? $bandera->bandera->color : 'bg-slate-50/10 ring-1 ring-white/20 backdrop-blur' }} grid h-20 w-20 shrink-0 place-items-center rounded-3xl">
                            {{-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="bandera w-12 h-12 flex-shrink-0 {{ $hayBandera ? 'animate-ondear text-white' : 'text-slate-50/50' }}">
                                <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                            </svg> --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="bandera w-12 h-12 flex-shrink-0 {{ $hayBandera ? 'animate-ondear text-white' : 'text-slate-50/50' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                </svg>

                            {{-- <i class="fas fa-flag "></i> --}}
                        </div>
                        <div class="min-w-0">
                            <p class="font-display text-3xl font-bold leading-tight text-slate-50">{{ $hayBandera ? 'Bandera del día' : 'Bandera pendiente' }}</p>
                            <p class="text-sm opacity-80 text-slate-100">{{ $hayBandera ? $bandera->bandera->codigo : 'Pendiente de evaluación diaria' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="min-w-0 rounded-2xl bg-slate-50/10 p-3 ring-1 ring-white/20 backdrop-blur shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="h-4 w-4 text-slate-50 opacity-80">
                                <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" />
                            </svg>
                            <p class="mt-2 truncate font-display text-lg font-bold text-slate-50">{{ $hayBandera ? $bandera->temperatura.'º' : '--' }}</p>
                            <p class="text-[11px] uppercase tracking-wide text-slate-50 opacity-70">TEMP</p>
                        </div>
                        <div class="min-w-0 rounded-2xl bg-slate-50/10 p-3 ring-1 ring-white/20 backdrop-blur shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="h-4 w-4 text-slate-50 opacity-80">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h12a2 2 0 10-2-2m-10 4h8a2 2 0 11-2 2" />
                            </svg>
                            <p class="mt-2 truncate font-display text-lg font-bold text-slate-50">{{ $hayBandera ? trim($bandera->viento_intensidad.' '.$bandera->viento_direccion) : '--' }}</p>
                            <p class="text-[11px] uppercase tracking-wide text-slate-50 opacity-70">VIENTO</p>
                        </div>
                    </div>

                    @if($hayBandera)
                        <p class="mt-4 flex items-center gap-1.5 text-slate-100 text-xs opacity-70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="h-3.5 w-3.5 me-1">
                                    <path d="M12.75 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM7.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM8.25 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM9.75 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM10.5 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM12.75 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM14.25 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 13.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
                                    <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd" />
                            </svg>
                            {{ $fecha->translatedFormat('l') }}, {{ $fecha->format('H:i')}}
                        </p>
                    @else
                        <p class="mt-4 flex items-center gap-1.5 text-slate-100 text-xs opacity-70">
                            <i class="fas fa-plus me-1"></i>
                            {{ $puedeCargarBandera ? 'Tocá para cargarla' : 'Aún no fue cargada hoy' }}
                        </p>
                    @endif

                </a>
            </section>
        {{-- </div> --}}
