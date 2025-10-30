<?php

namespace App\Http\Requests;

use App\Models\CambioDeTurno;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCambioDeTurnoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         return auth()->user()->can('editar_cambio_turno', CambioDeTurno::class);
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
            'turno_nuevo'       => 'required',
            'playa_id'          => 'required|exists:playas,id',
            'puesto_id'         => 'required|exists:puestos,id',
            'funcion'           => 'required',
            'detalles'          => 'nullable|string',
        ];
    }
}
