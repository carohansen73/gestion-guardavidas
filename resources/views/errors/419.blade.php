<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0d47a1">

    <title>Sesión expirada - {{ config('app.name', 'Gestión Guardavidas') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- CSS -->
    @vite(['resources/css/app.css'])

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
<body class="font-sans antialiased">
    <div class="relative min-h-screen flex flex-col justify-center items-center overflow-hidden bg-white dark:bg-gray-900 px-6">

        <!-- Imagen de fondo, mismo recurso y ubicación que en layouts/guest.blade.php -->
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-no-repeat bg-cover opacity-70 dark:opacity-20"
            style="background-image: url('{{ asset('img/lifebuoy.png') }}');
            background-size: 180%;
            background-position: top right;">
        </div>

        <div class="relative z-10 w-full sm:max-w-md text-center bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 py-10">

            <div class="flex justify-center">
                <a href="/">
                    <x-application-logo class="w-16 h-16" />
                </a>
            </div>

            <p class="mt-6 text-sm font-semibold tracking-widest text-sky-600 uppercase">Error 419</p>

            <h1 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white">
                Tu sesión expiró
            </h1>

            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                Por seguridad, la sesión se cierra después de un rato de inactividad. Volvé a iniciar sesión
                para continuar.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('login') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-100 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Volver atrás
                </a>

                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Iniciar sesión de nuevo
                </a>
            </div>
        </div>
    </div>
</body>
</html>
