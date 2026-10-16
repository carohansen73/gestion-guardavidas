@extends('layouts.app')

{{-- Respaldo de página completa: el listado muestra este mismo contenido en un modal (ver <x-modal-detalle>). --}}
@section('content')
    <x-detalle-pagina>
        @include('licencias.partials.detalle')
    </x-detalle-pagina>
@endsection
