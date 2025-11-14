<!-- resources/views/layouts/bottom-navbar.blade.php -->

{{-- <nav class="fixed bottom-0 left-0 right-0 bg-blue-500 text-white lg:hidden">
    <div class="flex justify-around items-center p-2">
        <a href="#" class="flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14" />
            </svg>
            <span class="text-xs">Inicio</span>
        </a>
        <a href="#" class="flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14" />
            </svg>
            <span class="text-xs">Buscar</span>
        </a>
        <a href="#" class="flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14" />
            </svg>
            <span class="text-xs">Perfil</span>
        </a>
    </div>
</nav> --}}

<nav>

    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white lg:hidden buttom-navigation border-t border-gray-200 shadow rounded-t-lg">
        <div class="flex justify-around items-center  px-2">
            <!-- Íconos de la navbar -->
            @can('agregar_intervencion')
            <a aria-current="page" class="flex flex-1 flex-col items-center justify-center text-xs font-medium py-1 px-4 text-gray-500 flex-grow text-center"
                href="{{ route('intervencion.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="w-9 h-9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{-- <span class="sr-only">Home</span> --}}
                <p> Intervención </p>
            </a>
            @else
                <a aria-current="page" class="flex flex-1 flex-col items-center justify-center text-xs font-medium py-1 px-4 text-gray-500 flex-grow text-center"
                href="{{ route('guardavida.myProfile') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                {{-- <span class="sr-only">Home</span> --}}
                <p> Perfil </p>
            </a>

            @endcan

            <!-- Botón flotante en el centro -->
            <a href="{{route('home')}}" class="relative flex flex-1 flex-col items-center justify-center text-xs font-medium  text-gray-700 py-2 px-4 flex-grow text-center">
                <div class="absolute bottom-0 p-2 rounded-full border-4 border-sky-200 bg-gray-100 shadow-lg">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="w-8 h-8  text-sky-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>

                </div>
                <span class="sr-only">Home</span>
            </a>


    <a class="flex flex-1 flex-col items-center justify-center text-xs font-medium text-gray-500 py-1 px-4 flex-grow text-center"  href=" {{ route('activeCamera') }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        class="w-7 h-7">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
        </svg>


        {{-- <span class="sr-only">Profile</span> --}}
        <p>Fichar</p>
    </a>
    </div>
</nav>
