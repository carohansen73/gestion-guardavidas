<x-guest-layout>

<section class="text-gray-600 body-font text-center">


    <h1 class="text-3xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl"> Bienvenido </h1>


    <img src="{{ asset('img/lifeguards.jpg') }}" alt="Logo" class="w-106 h-106 mt-4">

    <a class="block mx-auto mt-16 text-white bg-sky-600 border-0 py-2 px-8 focus:outline-none
    hover:bg-sky-700 rounded text-lg text-center"
        href="{{ route('login') }}">
        {{ __('Iniciar Sesión') }}
    </a>
    <div class="d-flex justify-center">
   <img src="{{ asset('img/muni-tsas.png') }}" alt="Logo" class="w-40 mt-4">
    </div>

</section>

</x-guest-layout>
