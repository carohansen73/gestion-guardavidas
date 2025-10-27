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
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/css/qr.css', 'resources/js/app.js'])
    {{-- estilo del template --}}
    {{-- <link rel="stylesheet" href="{{ asset('resources/css/style.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('resources/css/qr.css') }}"> --}}
</head>

<body>
    @include('layouts.navigation')

    <div id="contenedorCarga">
        <div class="position">
            <div id="carga"></div>
            <p class="text-xl font-medium text-gray-800 py-2 px-4 flex-grow text-center">Guardando Asistencia...</p>
        </div>
    </div>

    <div class="contenedorQR">
        <div class="qr-frame">
            <span></span>
            <video id="video" autoplay></video>
        </div>
    </div>

    <div
        class="fixed bottom-0 left-0 right-0 z-40 bg-gray-100 lg:hidden buttom-navigation border-t border-gray-200 shadow rounded-t-lg">
        <div class="flex justify-around items-center  px-2 border-t-[3px] border-[#4d4d4d] rounded-[10px]">
            <p class="text-xl font-medium text-gray-00 py-2 px-4 flex-grow text-center">Escanea el codigo QR para
                guardar la asistencia</p>
        </div>
    </div>




    <!-- Librerías externas -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script type="module" src="{{ asset('js/baseDeDatosNavegador.js') }}"></script>
    <script type="module" src="{{ asset('js/qrAsistencia.js') }}"></script>
</body>

</html>
