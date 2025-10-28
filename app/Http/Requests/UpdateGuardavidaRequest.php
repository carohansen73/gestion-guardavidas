<?php

namespace App\Http\Requests;

use App\Models\Guardavida;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGuardavidaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('editar_guardavida', Guardavida::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $rules = [
            // 'nombre' => 'required|string|max:255',
            // 'apellido' => 'required|string|max:255',
            'dni' => ['required', 'digits_between:7,8', Rule::unique('guardavidas', 'dni')->ignore($this->guardavida->id)],
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'piso_dpto' => 'nullable|string|max:10',
            'playa_id' => 'required|exists:playas,id',
            'puesto_id' => 'required|exists:puestos,id',
            'funcion' => 'required|string|in:Timonel,Encargado,Guardavida,Jefe_de_playa',
        ];

        return $rules;

    }
}
