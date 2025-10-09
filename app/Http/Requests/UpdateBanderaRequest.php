<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBanderaRequest extends StoreBanderaRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return true;
            $bandera = $this->route('bandera');
            return auth()->user()->can('update', $bandera);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // return parent::rules();
        return [
            'fecha'             => 'sometimes|date',
            'turno'             => 'sometimes|in:mañana,tarde',
            'bandera_id'        => 'sometimes|exists:bandera_tipos,id',
            'playa_id'          => 'sometimes|exists:playas,id',
            'viento_intensidad' => 'nullable|numeric|min:0',
            'viento_direccion'  => 'nullable|string',
            'temperatura'       => 'nullable|string',
            'detalles'          => 'nullable|string',
        ];
    }
}
