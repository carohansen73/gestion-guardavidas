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
            class="playa-tag px-3 py-1 bg-orange-500 text-gray-100 rounded hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200">
               Bloqueados
            </a>
            @else
             <a href="{{ route('guardavida.index')}}"
            class="playa-tag px-3 py-1 bg-sky-500 text-gray-100 rounded hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200">
               Habilitados
            </a>
            @endif
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
