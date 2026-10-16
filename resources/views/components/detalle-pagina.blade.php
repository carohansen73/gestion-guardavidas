{{-- Marco de página completa para el detalle de un registro. Es el respaldo del modal: se ve si alguien abre el link
     en otra pestaña o sin JavaScript. El contenido es el mismo partial `detalle` que muestra el modal. --}}
<button type="button" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-sky-100" onclick="window.history.back()">
    <i class="fas fa-arrow-left" aria-hidden="true"></i>
    <span class="hover:underline">Volver</span>
</button>

<div class="mx-auto my-4 max-w-2xl overflow-hidden rounded-2xl bg-white shadow-md dark:bg-gray-800">
    {{ $slot }}
</div>
