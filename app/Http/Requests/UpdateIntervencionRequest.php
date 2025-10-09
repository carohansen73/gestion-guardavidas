<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIntervencionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fecha'             => 'required|date',
            'tipo_intervencion' => 'required|string|max:255',
            'victimas'          => 'required|integer|digits_between:1,3',
            'codigo'            => 'required|integer|between:1,6',
            'bandera_id'        => 'required|exists:bandera_tipos,id',
            'traslado'          => 'required|boolean',
            'playa_id'          => 'required|exists:playas,id',
            'puesto_id'         => 'required|exists:puestos,id',
            'detalles'          => 'nullable|string',
            'fuerzas'           => 'array',
            'fuerzas.*'         => 'integer|exists:fuerzas,id',
            'guardavidas'       => 'array',
            'guardavidas.*'     => 'integer|exists:guardavidas,id',
        ];
    }
}
