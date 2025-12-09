@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')

<section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10">
    <h2 class="mb-3 text-gray-700 dark:text-white text-2xl font-bold tracking-tight text-heading md:text-3xl lg:text-4xl section-title">Editar Intervención</h2>

    <form action="{{ route('intervencion.update', $intervencion->id) }}" method="POST" class="">
        @csrf
        @method('PUT')

        <div class="container bg-white rounded shadow-md px-4 py-6 mx-auto">
            @include('ui.intervenciones.fields')
        </div>

    </form>

 </section>

@endsection
