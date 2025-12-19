<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/css/qr.css', 'resources/js/app.js'])
</head>

<body>
    @include('layouts.navigation')

    {{-- ANIMACIÓN DE CARGA --}}
    <div id="contenedorCarga">
        <div class="position">
            <div id="carga"></div>
            <p class="text-xl font-medium text-gray-800 py-2 px-4 flex-grow text-center">
                Guardando Asistencia...
            </p>
        </div>
    </div>


    {{-- CONTENEDOR DEL ESCÁNER --}}
    <div class="contenedorQR">
        <div class="flex justify-center items-center alturaQR">
            <div id="qr-reader" class="w-[300px] h-[450px]">
            </div>

        </div>
    </div>


    <div
        class="fixed bottom-0 left-0 right-0 z-40 bg-gray-100 lg:hidden buttom-navigation border-t border-gray-200 shadow rounded-t-lg">
        <div class="flex justify-around items-center px-2 border-t-[3px] border-[#4d4d4d] rounded-[10px]">
            <p class="text-xl font-medium text-gray-700 py-2 px-4 flex-grow text-center">
                Escanea el código QR para guardar la asistencia
            </p>
        </div>
    </div>

    <!-- Librerías externas -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    {{-- MÓDULOS --}}
    <script type="module" src="{{ asset('js/baseDeDatosNavegador.js') }}"></script>
    <script type="module" src="{{ asset('js/qrAsistencia.js') }}"></script>

</body>

</html>
