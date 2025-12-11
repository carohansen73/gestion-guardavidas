<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://use.typekit.net/wjn2blc.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/style.css',  'resources/js/darkMode.js'])
    {{-- estilo del template --}}
    {{-- <link rel="stylesheet" href="{{ asset('resources/css/style.css') }}"> --}}

    <script>
    // Evita el FOUC (Flash Of Unstyled Content)
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

<body class="font-sans antialiased pb-0 " x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">


    @include('layouts.navigation')

    @include('layouts.sidebar')


    <!-- Contenido principal -->

    <!-- Page Heading -->
    @isset($header)
        <header>
            {!! $header !!}
        </header>
    @endisset

    <!-- Page Content -->
    <div class="desktop-ml-64 min-h-screen bg-gray-100 dark:bg-gray-900 transition-all pb-14">
        <main class="overflow-x-hidden">
            @yield('content')
            {{-- {{ $slot }} --}}
        </main>
    </div>

    @include('layouts.bottom-navigation')

    <!---<script src="{{ asset('js/script.js') }}"></script>-->
    <script type="module" src="{{ asset('app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/mensajesSW.js') }}"></script>
    <script src="{{ asset('resources/js/script.js') }}"></script>

    {{-- <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script> --}}
</body>

</html>
