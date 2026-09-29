<?php

namespace App\Http\Requests;

class UpdateTemporadaRequest extends StoreTemporadaRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('editar_temporada', $this->route('temporada'));
    }
}
