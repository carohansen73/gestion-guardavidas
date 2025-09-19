<x-guest-layout>

<section class="text-gray-600 body-font text-center ">
     logo sistema gestion de guardavidas

    <h1 class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-800"> Bienvenido </h1>


    <img src="{{ asset('img/lifeguards.jpg') }}" alt="Logo" class="w-106 h-106 mt-4">

    <a class="block mx-auto mt-16 text-white bg-sky-600 border-0 py-2 px-8 focus:outline-none
    hover:bg-sky-700 rounded text-lg text-center"
        href="{{ route('login') }}">
        {{ __('Iniciar Sesión') }}
    </a>

    logo municipalidad de tres arroyos
</section>

</x-guest-layout>
