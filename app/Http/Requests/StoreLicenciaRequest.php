<?php

namespace App\Http\Requests;

use App\Models\Licencia;
use Illuminate\Foundation\Http\FormRequest;

class StoreLicenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
          return auth()->user()->can('agregar_licencia', Licencia::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fecha_inicio'      => 'required|date',
            'fecha_fin'         => 'required|date|after_or_equal:fecha_inicio',
            'tipo_licencia' => [
                'required',
                'string',
                'in:Capacitación,Enfermedad,Evento deportivo,Exámen,Fallecimiento familiar,Feriado trabajado compensado,Lesión,Licencia médica,Permiso especial,Otro',
            ],
            'en_tiempo'         => 'required|boolean',
            'turno'             => 'nullable',
            'guardavida_id'     => 'required|exists:guardavidas,id',
            'playa_id'          => 'nullable|exists:playas,id',
            'puesto_id'         => 'nullable|exists:puestos,id',
            'detalle'          => 'nullable|string',
            'archivo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ];
    }

    //  public function messages()
    // {
    //     return [
    //         'guardavida_id.required' => 'Debe seleccionar un guardavida.',
    //         'tipo_licencia.required' => 'Debe elegir un tipo de licencia.',
    //         'fecha_inicio.required' => 'Debe ingresar la fecha de inicio.',
    //         'fecha_fin.required' => 'Debe ingresar la fecha de finalización.',
    //         'fecha_fin.after_or_equal' => 'La fecha final debe ser posterior o igual a la fecha de inicio.',
    //         'en_tiempo.required' => 'Debe indicar si avisó en tiempo.',
    //         'playa_id.required' => 'No se pudo asignar la playa del guardavida.',
    //         'puesto_id.required' => 'No se pudo asignar el puesto del guardavida.',
    //         'turno.required' => 'No se pudo asignar el turno del guardavida.',
    //     ];
    // }
}
