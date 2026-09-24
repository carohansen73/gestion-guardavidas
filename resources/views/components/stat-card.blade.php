{{--
    Card de estadística reutilizable: ícono con fondo de color arriba, label
    y número abajo (apilado, NO al costado — con el ícono al lado el texto
    no tenía ancho suficiente y se salía de la card). Mismo layout que ya
    usaban las cards del panel de admin.

    Props: color (sky|orange|teal|emerald|amber|violet|purple), label, value,
    href (opcional, hace la card clickeable), valueClass (opcional, para
    forzar un color puntual en el número, ej. una alerta en ámbar).
    Slot: el <svg> del ícono.
--}}
@props(['color' => 'sky', 'label', 'value', 'href' => null, 'valueClass' => null])

@php
    $colors = [
        'sky' => 'bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400',
        'orange' => 'bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400',
        'teal' => 'bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400',
        'emerald' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400',
        'amber' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400',
        'violet' => 'bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400',
        'purple' => 'bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400',
    ];
    $iconClasses = $colors[$color] ?? $colors['sky'];
    $cardClasses = 'block rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03] p-4 md:p-6 shadow-sm transition hover:shadow-md' . ($href ? ' hover:-translate-y-0.5' : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $cardClasses]) }}>
@else
    <div {{ $attributes->merge(['class' => $cardClasses]) }}>
@endif
        <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $iconClasses }}">
            {{ $slot }}
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400 mt-4">{{ $label }}</div>
        <span class="text-2xl font-bold text-gray-800 dark:text-white/90 {{ $valueClass }}">{{ $value }}</span>
@if ($href)
    </a>
@else
    </div>
@endif
