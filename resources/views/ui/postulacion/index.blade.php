@extends('layouts.postulante')
@section('content')

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
        ¡Hola, {{ Auth::user()->name }}!
    </h1>
    <p class="text-gray-600 dark:text-gray-300">
        Tu cuenta de postulante ya está lista. El formulario de inscripción todavía se está preparando —
        pronto vas a poder completarlo desde acá.
    </p>
</div>

@endsection
