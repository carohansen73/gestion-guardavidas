<?php

namespace App\Http\Requests;

use App\Models\CambioDeTurno;
use Illuminate\Foundation\Http\FormRequest;

class StoreCambioDeTurnoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       return auth()->user()->can('agregar_cambio_turno', CambioDeTurno::class);
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
            'guardavida_id'     => 'required|exists:guardavidas,id',
            'turno_nuevo'       => 'nullable',
            'playa_id'          => 'nullable|exists:playas,id',
            'puesto_id'         => 'nullable|exists:puestos,id',
            'funcion'           => 'nullable',
            'detalles'          => 'nullable|string',
        ];
    }
}
