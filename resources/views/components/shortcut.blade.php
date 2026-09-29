@props(['label', 'value' => null, 'href' => null, 'valueClass' => null])

 <a href="{{ $href }}" class="group flex min-w-0 flex-col justify-between gap-3 rounded-2xl bg-card/70 p-4 ring-1 ring-border transition hover:-translate-y-0.5">
      <div class="flex items-center justify-between">
        {{ $slot }}
        @if(! is_null($value))
            <span class="rounded-full bg-sky-400 px-2 py-0.5 text-[10px] font-bold text-slate-50 {{ $valueClass }}">
                {{ $value }}
            </span>
        @endif
      </div>
      <p class="flex items-center gap-1 truncate text-sm font-semibold">
        {{ $label }}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round"
            class="h-3.5 w-3.5 text-sky-600 transition group-hover:text-sky-600">
            <path d="M7 7h10v10" />
            <path d="M7 17 17 7" />
        </svg>
      </p>
    </a>
