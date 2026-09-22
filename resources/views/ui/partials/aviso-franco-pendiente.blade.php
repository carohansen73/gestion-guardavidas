<div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-200 text-sm px-4 py-3 rounded mx-4 mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
    <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <span>Todavía no configuraste tu <strong>día de franco</strong>. Hace falta para que el sistema calcule bien tus asistencias.</span>
    </div>
    <button type="button" onclick="document.getElementById('diaFrancoModal').classList.remove('hidden')"
        class="shrink-0 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-3 py-1.5 rounded self-start sm:self-auto">
        Configurar ahora
    </button>
</div>

@include('franco.partials.dia-franco-modal')
