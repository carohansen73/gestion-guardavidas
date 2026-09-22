@use('App\Enums\RolUsuario')

@props(['rol', 'label' => true, 'size' => 'w-4 h-4'])

@php
    // Acepta tanto un case de RolUsuario como el string crudo del rol de Spatie.
    $rol = $rol instanceof RolUsuario ? $rol : (RolUsuario::tryFrom($rol) ?? RolUsuario::Guardavida);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full {$rol->fondo()} {$rol->color()} " . ($label ? 'px-2 py-0.5' : 'p-1')]) }}
    title="Rol: {{ $rol->texto() }}">
    @switch($rol->icono())
        @case('sparkles')
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $size }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
            </svg>
            @break

        @case('shield-check')
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $size }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
            </svg>
            @break

        @case('star')
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $size }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.563.563 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.563.563 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
            </svg>
            @break

        @default
            {{-- guardavida: salvavidas (lifebuoy), a falta de un icono estándar de Heroicons para esto --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $size }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="8.25" />
                <circle cx="12" cy="12" r="3" />
                <path stroke-linecap="round" d="M12 3.75v3M12 16.5v3M3.75 12h3M16.5 12h3M6.34 6.34l2.12 2.12M15.54 15.54l2.12 2.12M17.66 6.34l-2.12 2.12M8.46 15.54l-2.12 2.12" />
            </svg>
    @endswitch

    @if ($label)
        <span class="text-xs font-semibold">{{ $rol->texto() }}</span>
    @endif
</span>
