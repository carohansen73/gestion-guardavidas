@extends('layouts.app')

{{-- Respaldo de página completa: el listado muestra este mismo contenido en un modal (ver <x-modal-detalle>). --}}
@section('content')
    <x-detalle-pagina>
        @include('cambios-de-turno.partials.detalle')
    </x-detalle-pagina>
@endsection
