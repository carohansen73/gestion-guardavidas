@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')

<section class="sm:px-4 sm:py-10">
    <x-form-tarjeta action="{{ route('intervencion.update', $intervencion->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('intervenciones.fields')
    </x-form-tarjeta>

 </section>

@endsection
