<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/style.css'])
        {{-- estilo del template --}}
        {{-- <link rel="stylesheet" href="{{ asset('resources/css/style.css') }}"> --}}
    </head>
    <body class="font-sans antialiased  md:pb-0 pb-14" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">

            @include('layouts.navigation')


            @include('layouts.sidebar')



            <!-- Contenido principal -->


                <!-- Page Heading -->
                @isset($header)
                    <header >

                            {!! $header !!}

                    </header>
                @endisset

                <!-- Page Content -->
                <div class="lg:ml-64 min-h-screen bg-gray-50 dark:bg-gray-800">
                    <main class="overflow-x-hidden mb-4">
                        @yield('content')
                        {{-- {{ $slot }} --}}
                    </main>
                 </div>


        

        @include('layouts.bottom-navigation')

        <!---<script src="{{ asset('js/script.js') }}"></script>-->
        <script type="module" src="{{ asset('app.js') }}"></script>
         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
         <script src="{{ asset('js/mensajesSW.js') }}"></script>
         <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>
    </body>
</html>
