@props(['temporadas', 'temporada' => null])

{{-- Elige la temporada de un listado (?temporada=ID | todas). Por defecto se muestra la activa. --}}
<form method="GET" class="mb-3 flex flex-wrap items-center gap-2">
    @foreach (request()->except('temporada', 'page') as $clave => $valor)
        @if (is_scalar($valor))
            <input type="hidden" name="{{ $clave }}" value="{{ $valor }}">
        @endif
    @endforeach
    <label for="selector-temporada" class="text-sm font-medium text-gray-700 dark:text-gray-300">Temporada</label>
    <select id="selector-temporada" name="temporada" onchange="this.form.submit()"
        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm py-1">
        @foreach ($temporadas as $t)
            <option value="{{ $t->id }}" @selected($temporada?->id === $t->id)>{{ $t->nombre }}</option>
        @endforeach
        <option value="todas" @selected(! $temporada)>Todas</option>
    </select>
</form>
