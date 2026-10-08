@use('App\Enums\RolUsuario')

@php
    $playa = Auth::user()->guardavida?->playa?->nombre;
    $rol = Auth::user()->getRoleNames()->first();
    $rolPrincipal = RolUsuario::principal(Auth::user());
    $notificacionesFrancoSinLeer = Auth::user()->unreadNotifications->count();
    // Esta misma barra se reutiliza en layouts/postulante.blade.php (sin sidebar): ahí se pasa $conSidebar = false.
    $conSidebar = $conSidebar ?? true;
    // Un postulante solo puede ir a su postulación (ver RedirectPostulante): no se le muestran los enlaces del sistema.
    $esPostulante = Auth::user()->hasRole('postulante');
@endphp

{{-- Barra superior. Escritorio (sm+): modo oscuro + desplegable de usuario a la derecha.
     Celular: logo/playa a la izquierda y botón hamburguesa que abre un panel con los enlaces del
     sistema, los de la cuenta, el modo oscuro y cerrar sesión. --}}
<nav x-data="{ open: false }" @keydown.escape.window="open = false" @resize.window="if (window.matchMedia('(min-width: 640px) and (hover: hover) and (pointer: fine)').matches) open = false"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
    class="relative z-50 bg-white border-b border-gray-200 shadow-lg {{ $conSidebar ? 'desktop-ml-64' : '' }} dark:border-gray-700">

    <!-- Celular y tablet: fondo oscurecido y borroso detrás del menú abierto (al tocarlo, se cierra) -->
    <div x-show="open" style="display: none;" @click="open = false" aria-hidden="true"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="solo-celular-o-tablet fixed inset-0 z-0 bg-gray-900/40 backdrop-blur-sm"></div>

    <div class="relative z-10 bg-white max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo / playa (con sidebar, en escritorio lo muestra el sidebar) -->
            <div class="shrink-0 flex items-center {{ $conSidebar ? 'solo-celular-o-tablet' : '' }}">
                <a href="{{ route('home') }}" class="flex items-center">
                    @if ($rol !== 'admin' && $playa)
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-7 h-7 animate-bounce md:animate-none text-orange-600 dark:text-orange-500 mx-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        <p class="text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-2xl dark:text-white">{{ $playa }}</p>
                    @else
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-100" />
                    @endif
                </a>
            </div>

            <!-- Escritorio (mouse): modo oscuro + desplegable de usuario -->
            <div class="solo-escritorio flex items-center ms-auto">
                <x-darkmode-toggle />

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button"
                            class="relative inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-white bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            @if ($notificacionesFrancoSinLeer > 0)
                                <span class="absolute top-0.5 right-0.5 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-800"
                                    title="Tenés notificaciones sin leer"></span>
                            @endif

                            <x-role-badge :rol="$rolPrincipal" :label="false" size="w-5 h-5" />
                            <div class="ms-1.5">{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                            <div class="font-medium text-sm text-gray-800 dark:text-gray-50 truncate">{{ Auth::user()->email }}</div>
                            <x-role-badge :rol="$rolPrincipal" size="w-3.5 h-3.5" class="mt-1" />
                        </div>

                        @if ($esPostulante)
                            <x-dropdown-link :href="route('postulacion.paso', 1)">
                                {{ __('Mis datos') }}
                            </x-dropdown-link>
                        @else
                            @unless ($conSidebar)
                                <x-dropdown-link :href="route('home')">
                                    {{ __('Inicio') }}
                                </x-dropdown-link>
                            @endunless

                            <x-dropdown-link :href="route('guardavida.myProfile')">
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('guardavida.misAsistencias')">
                                {{ __('Mis Asistencias') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('franco-intercambio.index')">
                                {{ __('Cambios de Franco') }}
                                @if ($notificacionesFrancoSinLeer > 0)
                                    <span class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-bold bg-red-500 text-white">
                                        {{ $notificacionesFrancoSinLeer }}
                                    </span>
                                @endif
                            </x-dropdown-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Celular y tablet: botón hamburguesa -->
            <div class="solo-celular-o-tablet flex items-center">
                <button type="button" @click="open = ! open" :aria-expanded="open.toString()" aria-label="Abrir menú"
                    class="relative inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-200 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition duration-150 ease-in-out">
                    @if ($notificacionesFrancoSinLeer > 0)
                        <span class="absolute top-1 right-1 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-800"
                            title="Tenés notificaciones sin leer"></span>
                    @endif
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" style="display: none;" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Celular y tablet: panel del menú (flota sobre el contenido, no lo empuja hacia abajo) -->
    <div x-show="open" style="display: none;"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="solo-celular-o-tablet absolute inset-x-0 top-full z-10 bg-white border-b border-gray-200 dark:border-gray-700 shadow-xl max-h-[calc(100vh-4rem)] overflow-y-auto">

        @unless ($esPostulante)
            <div class="py-2 space-y-1">
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    {{ __('Inicio') }}
                </x-responsive-nav-link>
            </div>
            @canany(['ver_bandera', 'ver_intervencion', 'ver_novedad_material'])
                <div class="py-2 border-t border-gray-200 dark:border-gray-700">
                    <p class="px-4 pt-1 pb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Operación</p>
                    <div class="space-y-1">
                        @can('ver_bandera')
                            <x-responsive-nav-link :href="route('bandera.index')" :active="request()->routeIs('bandera.*')">
                                {{ __('Banderas') }}
                            </x-responsive-nav-link>
                        @endcan
                        @can('ver_intervencion')
                            <x-responsive-nav-link :href="route('intervencion.index')" :active="request()->routeIs('intervencion.*')">
                                {{ __('Intervenciones') }}
                            </x-responsive-nav-link>
                        @endcan
                        @can('ver_novedad_material')
                            <x-responsive-nav-link :href="route('novedad-de-material.index')" :active="request()->routeIs('novedad-de-material.*')">
                                {{ __('Novedades materiales') }}
                            </x-responsive-nav-link>
                        @endcan
                    </div>
                </div>
            @endcanany
            @canany(['ver_guardavida', 'ver_asistencia', 'ver_licencia', 'ver_cambio_turno'])
                <div class="py-2 border-t border-gray-200 dark:border-gray-700">
                    <p class="px-4 pt-1 pb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Personal</p>
                    <div class="space-y-1">
                        @can('ver_guardavida')
                            <x-responsive-nav-link :href="route('guardavida.index')" :active="request()->routeIs('guardavida.*')">
                                {{ __('Guardavidas') }}
                            </x-responsive-nav-link>
                        @endcan
                        @can('ver_asistencia')
                            <x-responsive-nav-link :href="route('asistencias.index')" :active="request()->routeIs('asistencias.*')">
                                {{ __('Asistencias') }}
                            </x-responsive-nav-link>
                        @endcan
                        @can('ver_licencia')
                            <x-responsive-nav-link :href="route('licencia.index')" :active="request()->routeIs('licencia.*')">
                                {{ __('Licencias') }}
                            </x-responsive-nav-link>
                        @endcan
                        @can('ver_cambio_turno')
                            <x-responsive-nav-link :href="route('cambio-de-turno.index')" :active="request()->routeIs('cambio-de-turno.*')">
                                {{ __('Cambios de turno') }}
                            </x-responsive-nav-link>
                        @endcan
                    </div>
                </div>
            @endcanany
            @canany(['ver_temporada', 'ver_postulacion'])
                <div class="py-2 border-t border-gray-200 dark:border-gray-700">
                    <p class="px-4 pt-1 pb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Temporada</p>
                    <div class="space-y-1">
                        @can('ver_temporada')
                            <x-responsive-nav-link :href="route('temporada.index')" :active="request()->routeIs('temporada.*')">
                                {{ __('Temporadas') }}
                            </x-responsive-nav-link>
                        @endcan
                        @can('ver_postulacion')
                            <x-responsive-nav-link :href="route('postulaciones.index')" :active="request()->routeIs('postulaciones.*')">
                                {{ __('Postulaciones') }}
                            </x-responsive-nav-link>
                        @endcan
                    </div>
                </div>
            @endcanany
            @canany(['abm_roles_y_permisos'])
                <div class="py-2 border-t border-gray-200 dark:border-gray-700">
                    <p class="px-4 pt-1 pb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Administración</p>
                    <div class="space-y-1">
                        @can('abm_roles_y_permisos')
                            <x-responsive-nav-link :href="route('permisos.index')" :active="request()->routeIs('permisos.*')">
                                {{ __('Permisos') }}
                            </x-responsive-nav-link>
                        @endcan
                    </div>
                </div>
            @endcanany
        @endunless

        <!-- Cuenta -->
        <div class="py-3 border-t border-gray-200 dark:border-gray-700">
            <div class="px-4 pb-2">
                <div class="font-medium text-base text-gray-800 dark:text-gray-50">{{ Auth::user()->name }} {{ Auth::user()->lastname }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-100 truncate">{{ Auth::user()->email }}</div>
                <x-role-badge :rol="$rolPrincipal" size="w-3.5 h-3.5" class="mt-2" />
            </div>

            <div class="mt-1 space-y-1">
                @if ($esPostulante)
                    <x-responsive-nav-link :href="route('postulacion.paso', 1)">
                        {{ __('Mis datos') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('guardavida.myProfile')">
                        {{ __('Perfil') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('guardavida.misAsistencias')">
                        {{ __('Mis Asistencias') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('franco-intercambio.index')">
                        {{ __('Cambios de Franco') }}
                        @if ($notificacionesFrancoSinLeer > 0)
                            <span class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-bold bg-red-500 text-white">
                                {{ $notificacionesFrancoSinLeer }}
                            </span>
                        @endif
                    </x-responsive-nav-link>
                @endif

                <!-- Modo oscuro (en celular vive acá adentro; en escritorio es el botón de la barra) -->
                <button type="button" data-theme-toggle
                    class="flex w-full items-center gap-2 ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-200 hover:text-gray-800 dark:hover:text-gray-50 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none transition duration-150 ease-in-out">
                    <svg data-theme-show="light" class="hidden h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 0 1 .162.819A8.97 8.97 0 0 0 9 6a9 9 0 0 0 9 9 8.97 8.97 0 0 0 3.463-.69.75.75 0 0 1 .981.98 10.503 10.503 0 0 1-9.694 6.46c-5.799 0-10.5-4.7-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 0 1 .818.162Z" clip-rule="evenodd" />
                    </svg>
                    <svg data-theme-show="dark" class="hidden h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" />
                    </svg>
                    <span data-theme-show="light" class="hidden">Modo oscuro</span>
                    <span data-theme-show="dark" class="hidden">Modo claro</span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
