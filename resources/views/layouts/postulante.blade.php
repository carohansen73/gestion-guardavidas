<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Postulación - {{ config('app.name', 'Gestión Guardavidas') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js',  'resources/css/style.css', 'resources/js/darkMode.js'])

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

    {{-- Layout acotado a propósito: sin el sidebar ni la navegación del sistema principal, pero con la
         MISMA barra superior (layouts/navigation) para que se vea como el mismo sistema. Un postulante
         "puro" solo puede ir a su postulación (ver RedirectPostulante), así que la barra le muestra solo
         "Mis datos" y "Cerrar sesión"; un guardavida/encargado que se postula conserva su menú completo. --}}
    @auth
        @include('layouts.navigation', ['conSidebar' => false])
    @endauth

    <main class="max-w-3xl mx-auto px-4 py-8">
        <x-session-alerts />
        @yield('content')
    </main>

</body>
</html>
