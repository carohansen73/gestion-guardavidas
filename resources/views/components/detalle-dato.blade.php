{{-- Un dato (etiqueta + valor) del detalle de un registro. Se usa dentro de una grilla. --}}
@props(['label'])

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $label }}</dt>
    <dd class="mt-0.5 break-words text-sm text-gray-900 dark:text-gray-100">{{ $slot }}</dd>
</div>
