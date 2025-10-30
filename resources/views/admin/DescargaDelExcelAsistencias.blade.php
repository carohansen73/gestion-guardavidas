{{-- resources/views/admin/DescargaDelExcelAsistencias.blade.php --}}
@extends('layouts.dashboard')

@section('content')
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">📊 Exportar Asistencias por Día</h2>

        <form action="{{ route('asistencias.exportDia') }}" method="GET"
            class="bg-white shadow-md rounded-lg p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Fecha --}}
            <div>
                <label for="fecha" class="block font-semibold mb-2 text-gray-700">Fecha</label>
                <input type="date" name="fecha" id="fecha" required
                    class="w-full border rounded-md p-2 focus:ring focus:ring-blue-200">
            </div>

            {{-- Balneario --}}
            <div>
                <label for="balneario" class="block font-semibold mb-2 text-gray-700">Balneario</label>
                <select name="balneario" id="balneario" class="w-full border rounded-md p-2">
                    <option value="">Todos</option>
                    <option value="Claromecó">Claromecó</option>
                    <option value="Reta">Reta</option>
                    <option value="Orense">Orense</option>
                </select>
            </div>

            {{-- Puesto --}}
            <div>
                <label for="puesto" class="block font-semibold mb-2 text-gray-700">Puesto</label>
                <select name="puesto" id="puesto" class="w-full border rounded-md p-2">
                    <option value="">Todos</option>
                    @for ($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            {{-- Turno --}}
            <div>
                <label for="turno" class="block font-semibold mb-2 text-gray-700">Turno</label>
                <select name="turno" id="turno" class="w-full border rounded-md p-2">
                    <option value="">Todos</option>
                    <option value="mañana">Mañana</option>
                    <option value="tarde">Tarde</option>
                </select>
            </div>

            {{-- Botón --}}
            <div class="md:col-span-2 flex justify-end mt-4">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition">
                    <i class="fa fa-file-excel mr-2"></i>Descargar Excel
                </button>
            </div>
        </form>

        {{-- Volver --}}
        <div class="mt-6">
            <a href="{{ route('guardavidas.index') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                <i class="fa fa-arrow-left"></i> Volver al listado de guardavidas
            </a>
        </div>
    </div>
@endsection
