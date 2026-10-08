@extends('layouts.app')

@section('content')
<section class="sm:px-4 sm:py-10">

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 dark:bg-red-900/40 dark:text-red-300">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 dark:bg-green-900/40 dark:text-green-200 dark:border-green-800">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
            <li class="me-2" role="presentation">
                <button class="inline-block px-4 py-3 border-b-2 rounded-t-lg"  type="button" role="tab"
                    id="dashboard-tab" data-tabs-target="#dashboard" aria-controls="dashboard" aria-selected="false">
                    Datos
                </button>
            </li>
            <li class="me-2" role="presentation">
                <button class="inline-block px-4 py-3 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                    id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">
                    Perfil
                </button>
            </li>
            {{-- @can('admin') --}}
            <li class="me-2" role="presentation">
                <button class="inline-block px-4 py-3 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                    id="settings-tab" data-tabs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">
                    Roles y permisos
                </button>
            </li>
            {{-- @endcan --}}
        </ul>
    </div>
    {{-- DATOS GUARDAVIDA --}}
    <div id="default-tab-content">
        <div class="hidden pt-6" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
            @include('guardavidas.partials.edit-datos')
        </div>
        {{-- termina datos guardavida  --}}

        {{-- PERFIL --}}
        <div class="hidden pt-6" id="profile" role="tabpanel" aria-labelledby="profile-tab">
           @include('guardavidas.partials.edit-perfil')
        </div>
        {{-- termina perfil  --}}
        {{-- ROL Y PERMISOS --}}
        {{-- @can('admin') --}}
        <div class="hidden pt-6" id="settings" role="tabpanel" aria-labelledby="settings-tab">
            @include('guardavidas.partials.edit-rol')
        </div>
        {{-- @endcan --}}
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get("tab");

    if (tab) {
        // Simular click en el botón correspondiente
        const button = document.querySelector(`[data-tabs-target="#${tab}"]`);
        if (button) button.click();
    } else {
        // Si no hay ?tab=..., mostrar el primero
        document.querySelector('[data-tabs-target]:first-child')?.click();
    }
});
</script>

@vite(['resources/js/filterPuestoByPlaya.js'])
@endsection
