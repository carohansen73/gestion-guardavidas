@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')


<button @click="darkMode = !darkMode"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded">
  Cambiar modo
</button>


 <section class="text-gray-600 dark:text-gray-100 body-font px-4 py-10">
      <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">Editar Intervención</h2>

    <form action="{{ route('intervencion.update', $intervencion->id) }}" method="POST" class="">
        @csrf
        @method('PUT')

        <div class="container bg-white rounded shadow-md px-4 py-6 mx-auto">
            @include('ui.intervenciones.fields')
        </div>

    </form>

 </section>

@endsection
