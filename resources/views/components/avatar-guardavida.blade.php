{{-- Avatar circular de un guardavidas para los listados. Usa `foto_personal_id`, que el controlador carga
     para toda la página en una sola consulta (ver Guardavida::fotosPersonalesDe). Sin foto, muestra el ícono. --}}
@props(['guardavida', 'size' => 'w-10 h-10', 'icono' => 'text-lg'])

<div {{ $attributes->merge(['class' => "{$size} shrink-0 flex items-center justify-center overflow-hidden rounded-full bg-sky-600 dark:bg-sky-700 text-white"]) }}>
    @if ($guardavida->foto_personal_id)
        <img src="{{ route('guardavida.foto', $guardavida) }}?v={{ $guardavida->foto_personal_id }}"
            alt="Foto de {{ $guardavida->nombre }} {{ $guardavida->apellido }}"
            loading="lazy" decoding="async"
            class="w-full h-full object-cover">
    @else
        <i class="fas fa-user {{ $icono }}" aria-hidden="true"></i>
    @endif
</div>
