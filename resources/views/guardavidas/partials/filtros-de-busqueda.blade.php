    {{-- @role('admin|encargado') --}}
    <div class="flex flex-col-reverse md:flex-row justify-between align-center ">
         {{-- <div class="">
            <input
                type="text"
                id="searchInput"
                placeholder="Fecha"
                class="w-full px-3 py-2 border rounded"
                oninput="applyFilters()">
        </div> --}}
        <div class="flex flex-wrap gap-2 align-content-center">
            <button
                class="playa-tag px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200"
                onclick="filterByPlaya('all')">
                Todas
            </button>
            @foreach($playas as $playa)
                <button
                    class="playa-tag px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200"
                    onclick="filterByPlaya('{{ $playa->id }}')">
                    {{ $playa->nombre }}
                </button>
            @endforeach
            @if(request()->is('guardavida'))
            <a href="{{ route('guardavidas.disabled')}}"
            class="playa-tag px-3 py-1 bg-orange-600 text-gray-100 rounded hover:bg-orange-400 hover:shadow-lg dark:bg-orange-600 dark:hover:bg-orange-500 dark:text-gray-200">
               Bloqueados
            </a>
            @else
             <a href="{{ route('guardavida.index')}}"
            class="@if (request()->routeIs('guardavidas.disabled'))  bg-gray-600 text-white  @endif playa-tag px-3 py-1 text-gray-100 rounded hover:bg-gray-600 hover:shadow-lg dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200">
               Habilitados
            </a>
            @endif

            <a href="{{ route('guardavidas.export') }}" class="px-3 py-1 bg-emerald-600 text-gray-100 rounded hover:bg-emerald-500 hover:shadow-lg dark:bg-emerald-700 dark:hover:bg-teal-500 dark:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </a>

        </div>
        {{-- Busqueda --}}
        <div class="relative w-full md:w-auto my-3 sm:!my-0">
            <input
                type="text"
                id="searchInput"
                placeholder='Buscar... '
                class="w-full px-3 py-2 border rounded"
                oninput="applyFilters()">

                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="w-5 h-5 absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"
                    id="searchIcon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
        </div>
    </div>




    {{-- @endrole --}}
