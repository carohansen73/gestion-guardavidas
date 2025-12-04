 @if (session('success'))
        <div class="flex items-start sm:items-center p-3 my-2 bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200 text-sm rounded border border-success-subtle" role="alert">
            <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <p> {{ session('success') }} </p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-start sm:items-center p-3 my-2 bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200 text-sm rounded border border-success-subtle" role="alert">
            <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <p> {{ session('error') }} </p>
        </div>
    @endif
