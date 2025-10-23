<?php

namespace App\Http\Requests;

use App\Models\Material;
use App\Models\NovedadMaterial;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNovedadMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         return auth()->user()->can('editar_novedad_material', NovedadMaterial::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       return [
            'fecha'                  => 'required|date',
            'playa_id'               => 'required|exists:playas,id',
            'tipo_novedad'           => 'required|string',
            'detalles'               => 'nullable|string',
            'material_id'            => 'required',
            'nuevo_material_nombre'  => 'nullable|string|max:255',
            'nuevo_material_detalle' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            // Si selecciona un material existente
            if (isset($data['material_id']) && $data['material_id'] !== 'otro') {
                if (!Material::where('id', $data['material_id'])->exists()) {
                    $validator->errors()->add('material_id', 'El material seleccionado no existe.');
                }
            }

            // Si selecciona "otro", debe ingresar nombre
            if (isset($data['material_id']) && $data['material_id'] === 'otro') {
                if (empty($data['nuevo_material_nombre'])) {
                    $validator->errors()->add('nuevo_material_nombre', 'Debe ingresar un nombre para el nuevo material.');
                }
            }
        });
    }
}
