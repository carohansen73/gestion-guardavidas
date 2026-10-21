{{-- Marco de página completa para el detalle de un registro. Es el respaldo del modal: se ve si alguien abre el link
     en otra pestaña o sin JavaScript. El contenido es el mismo partial `detalle` que muestra el modal. --}}
<x-volver />

<div class="mx-auto my-4 max-w-2xl overflow-hidden rounded-2xl bg-white shadow-md dark:bg-gray-800">
    {{ $slot }}
</div>
