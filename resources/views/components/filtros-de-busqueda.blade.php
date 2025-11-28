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

            @props(['tipo'])

            @if($tipo === 'asistencia-general')
                {{-- Excel de asistencias → abre modal para seleccioanr fechas --}}
                <button type="button"
                    onclick="openAsistenciaModal('{{ $tipo }}')"
                    class="px-3 py-1 bg-emerald-600 text-gray-100 rounded hover:bg-emerald-500 hover:shadow-lg dark:bg-emerald-700 dark:hover:bg-teal-500 dark:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                </button>
            @else
                {{-- En otras vistas → genera excel directamente --}}
                <a href="{{ route('export.playas', ['tipo' => $tipo]) }}" class="px-3 py-1 bg-emerald-600 text-gray-100 rounded hover:bg-emerald-500 hover:shadow-lg dark:bg-emerald-700 dark:hover:bg-teal-500 dark:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
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


{{--  Modal para seleccionar fechas (solo asistencia-general) --}}
<div id="asistenciaModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-96 p-6 animate-fade-in">

        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
            Exportar asistencia general
        </h2>

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Seleccioná el rango de fechas para generar el Excel.
        </p>

        <form id="asistenciaForm" class="space-y-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Desde
                </label>
                <input type="date" id="inicioGeneral"
                       class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Hasta
                </label>
                <input type="date" id="finGeneral"
                       class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button"
                    onclick="closeAsistenciaModal()"
                    class="px-3 py-1 bg-gray-300 dark:bg-gray-600 rounded hover:bg-gray-400 dark:hover:bg-gray-500">
                    Cancelar
                </button>

                <button type="button"
                    onclick="submitAsistenciaExcel()"
                    class="px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-500 shadow">
                    Generar Excel
                </button>
            </div>

        </form>
    </div>
</div>

{{--  Animación para modal --}}
<style>
    .animate-fade-in {
        animation: fadeIn .25s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>


<script>
    function openAsistenciaModal(tipo) {
        if (tipo === 'asistencia-general') {
            document.getElementById('asistenciaModal').classList.remove('hidden');
        }
    }

    function closeAsistenciaModal() {
        document.getElementById('asistenciaModal').classList.add('hidden');
    }

    function submitAsistenciaExcel() {
        const inicio = document.getElementById('inicioGeneral').value;
        const fin = document.getElementById('finGeneral').value;

        // Validación opcional
        if (!inicio && !fin) {
            if (!confirm("No seleccionaste fechas. ¿Generar los últimos 30 días?")) {
                return;
            }
        }

        const url = `/export/playas?tipo=asistencia-general&inicio=${inicio}&fin=${fin}`;

        window.location.href = url;
        closeAsistenciaModal()
    }
</script>
