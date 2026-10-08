@props(['tag' => 'form'])

{{-- Tarjeta de un formulario: la ÚNICA definición del look y de la separación entre secciones (en todos los
     tamaños de pantalla). Estructura: <x-form-encabezado> primero y después una o más <x-form-seccion>.
     Pasa a <form> cualquier atributo (action, method, enctype, onsubmit…); con tag="div" se usa como contenedor
     sin <form>. Mantiene el espacio chico entre el encabezado y la primera sección y grande entre secciones
     (el encabezado va sin margen superior aunque haya un @csrf antes). --}}
<{{ $tag }} {{ $attributes->class(['glass rounded-xl p-6 sm:p-8 space-y-14 sm:space-y-16 text-gray-700 dark:text-gray-200 [&>header]:!mt-0 [&>header+*]:!mt-8']) }}>
    {{ $slot }}
</{{ $tag }}>
