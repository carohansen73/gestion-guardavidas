@props(['registros'])

{{-- Igual que <x-index-table> pero con los links de paginación debajo. --}}
<x-index-table :registros="$registros" paginar>
    {{ $slot }}
</x-index-table>
