@props(['seleccionados' => []])

<div class="flex flex-wrap gap-4">
    @foreach (['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $i => $nombreDia)
        <label class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-200">
            <input type="checkbox" name="dias_franco[]" value="{{ $i }}"
                {{ in_array($i, $seleccionados) ? 'checked' : '' }}
                class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
            {{ $nombreDia }}
        </label>
    @endforeach
</div>
