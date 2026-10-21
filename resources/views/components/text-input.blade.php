@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm']) }}>
