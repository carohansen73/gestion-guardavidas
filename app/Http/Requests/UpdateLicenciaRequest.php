<?php

namespace App\Http\Requests;

use App\Models\Licencia;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLicenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('editar_licencia', Licencia::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'tipo_licencia' => [
                'required',
                'string',
                'in:Capacitación,Enfermedad,Evento deportivo,Exámen,Fallecimiento familiar,Feriado trabajado compensado,Lesión,Licencia médica,Permiso especial,Otro',
            ],
        'fecha_inicio'  => ['required', 'date'],
        'fecha_fin'     => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        'en_tiempo'     => ['required', 'boolean'],
        'detalle'       => ['nullable', 'string', 'max:1000'],
        'guardavida_id' => ['required', 'exists:guardavidas,id'],
        'playa_id'      => ['required', 'exists:playas,id'],
        'puesto_id'     => ['nullable', 'exists:puestos,id'],
        'turno'         => ['required', 'in:M,T'],
        'archivo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
    ];
    }
}
