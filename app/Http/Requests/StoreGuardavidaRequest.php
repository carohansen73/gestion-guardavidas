<?php

namespace App\Http\Requests;

use App\Models\Guardavida;
use Illuminate\Foundation\Http\FormRequest;

class StoreGuardavidaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('agregar_guardavida', Guardavida::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $esGuardavidaOEncargado = in_array($this->input('rol'), ['guardavida', 'encargado']);

        // verifico datos primero para usuario, luego, si es guardavida para guardavidas.
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            // DNI: obligatorio para cualquier rol (admin incluido) — vive en
            // users.dni (Fase 3b), único ahí siempre. Además único en
            // guardavidas.dni cuando corresponde crear ese registro (esa
            // columna quedó en desuso pero todavía tiene datos históricos,
            // se valida igual como resguardo mientras no se borre del todo).
            'dni' => array_filter([
                'required',
                'digits_between:7,8',
                $esGuardavidaOEncargado ? 'unique:guardavidas,dni' : null,
                'unique:users,dni',
            ]),
            'email' => 'required|email|unique:users,email',
            'rol' => 'required|string|in:guardavida,encargado,admin',
        ];
        if ($esGuardavidaOEncargado) {
            $rules = array_merge($rules, [
                'telefono' => 'required|string|max:20',
                'direccion' => 'nullable|string|max:255',
                'numero' => 'nullable|string|max:10',
                'piso_dpto' => 'nullable|string|max:10',
                'playa_id' => 'required|exists:playas,id',
                'puesto_id' => 'required|exists:puestos,id',
                'funcion' => 'required|string|in:Timonel,Encargado,Guardavida,Jefe_de_playa',
                'turno' => 'required|in:M,T',
                'fecha_alta' => 'nullable|date',

                'dias_franco' => 'nullable|array',
                'dias_franco.*' => 'integer|between:0,6',
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        $yaExiste = 'Ya existe una persona con %s. No la des de alta de nuevo: si se postuló, seleccionala desde Postulaciones; si ya fue guardavida, volvela a dar de alta desde "Dados de baja".';

        return [
            'dni.unique' => sprintf($yaExiste, 'ese DNI'),
            'email.unique' => sprintf($yaExiste, 'ese email'),
        ];
    }
}
