<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/style.css'])
        {{-- estilo del template --}}
        {{-- <link rel="stylesheet" href="{{ asset('resources/css/style.css') }}"> --}}
</head>

<body>
        <div>
                <video id="video" autoplay></video>
        </div>

        <div id="resultadoQR"></div>


        <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
        <script src="{{ asset('js/script.js') }}"></script>
        <script src="{{ asset('js/qrAsistencia.js') }}"></script>
</body>

</html>