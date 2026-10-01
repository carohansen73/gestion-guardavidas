@props(['registros', 'movil' => false, 'paginar' => false])

{{-- Tabla estándar de los listados: es la única definición del estilo (tarjeta
     blanca, esquinas redondeadas, letra chica). Para que todas las tablas se
     vean igual hay que usar este componente en vez de armar un <table> propio.

     - movil:   por defecto la tabla se oculta en celular, porque cada listado
                tiene su propia versión de tarjetas (index-mobile). Pasar
                `movil` cuando NO hay versión de tarjetas y la tabla tiene que
                verse (con scroll horizontal) también en pantallas chicas.
     - paginar: muestra los links de paginación debajo (los $registros tienen
                que ser un paginador).

     El contenido (<thead>/<tbody>) va en el slot. Cabecera sugerida:
     <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
     con <th class="px-4 py-2 text-left"> y filas con <td class="px-4 py-2">. --}}
<div class="{{ $movil ? '' : 'hidden sm:block' }} sm:px-4 pt-4 sm:pb-12">
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="min-w-full text-sm text-gray-600 dark:text-gray-100">
            {{ $slot }}
        </table>
    </div>

    @if ($paginar)
        <div class="mt-4">
            {{ $registros->links() }}
        </div>
    @endif
</div>
