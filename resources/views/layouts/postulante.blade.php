<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Postulación - {{ config('app.name', 'Gestión Guardavidas') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/darkMode.js'])

    <style>[x-cloak] { display: none !important; }</style>

    <script>
        // Evita el FOUC (Flash Of Unstyled Content) - mismo criterio que layouts/app.blade.php
        (function() {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (theme === 'dark' || (!theme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 min-h-screen">

    {{-- Layout minimal a propósito: un postulante no tiene que ver el
         sidebar ni la navegación del sistema principal (intervenciones,
         banderas, etc.) — solo su postulación y cómo salir. --}}
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-application-logo class="h-8 w-auto fill-current text-gray-800 dark:text-gray-200" />
                <span class="font-semibold text-gray-800 dark:text-gray-100">Postulación</span>
            </div>

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        Cerrar sesión
                    </button>
                </form>
            @endauth
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-8">
        <x-session-alerts />
        @yield('content')
    </main>

</body>
</html>
