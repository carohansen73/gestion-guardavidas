<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuardavidaRequest extends FormRequest
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
        //verifico datos primero para usuario, luego, si es guardavida para guardavidas.
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'rol' => 'required|string|in:guardavida,encargado,admin',
        ];
        if (in_array($this->input('rol'), ['guardavida', 'encargado'])) {
            $rules = array_merge($rules, [
                'dni' => 'required|digits_between:7,8|unique:guardavidas,dni',
                'telefono' => 'required|string|max:20',
                'direccion' => 'required|string|max:255',
                'numero' => 'required|string|max:10',
                'piso_dpto' => 'nullable|string|max:10',
                'playa_id' => 'required|exists:playas,id',
                'puesto_id' => 'required|exists:puestos,id',
                'funcion' => 'required|string|in:Timonel,Encargado,Guardavida,Jefe_de_playa',
            ]);
        }
        return $rules;
    }
}
