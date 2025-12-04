@props(['active'])

@php
//  <a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 transition duration-150 ease-in-out']) }}>{{ $slot }}</a>

$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-sky-400 dark:border-gray-400 text-start text-base font-medium text-sky-700 bg-sky-50 dark:text-gray-300 dark:bg-gray-600 focus:outline-none focus:text-sky-800 focus:bg-sky-100 dark:focus:bg-gray-600 focus:border-sky-700 dark:focus:border-gray-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-200 hover:text-gray-800 dark:hover:text-gray-50 hover:bg-gray-50 dark:hover:bg-gray-600 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
