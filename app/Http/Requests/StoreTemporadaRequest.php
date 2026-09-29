<?php

namespace App\Http\Requests;

use App\Models\Temporada;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTemporadaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('agregar_temporada', Temporada::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'fecha_inicio_postulacion' => 'required|date',
            'fecha_fin_postulacion' => 'required|date|after_or_equal:fecha_inicio_postulacion',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ];
    }

    /**
     * Que no se solape la ventana operativa con la de otra temporada, ni la
     * de postulación con la de otra temporada — cada ventana se valida por
     * separado (una ventana de postulación SÍ puede solaparse con la
     * ventana operativa de la temporada anterior, eso es normal).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $temporadaId = $this->route('temporada')?->id;

            if ($this->solapaConOtraTemporada('fecha_inicio', 'fecha_fin', $temporadaId)) {
                $validator->errors()->add('fecha_inicio', 'El rango operativo se superpone con el de otra temporada.');
            }

            if ($this->solapaConOtraTemporada('fecha_inicio_postulacion', 'fecha_fin_postulacion', $temporadaId)) {
                $validator->errors()->add('fecha_inicio_postulacion', 'El rango de postulación se superpone con el de otra temporada.');
            }
        });
    }

    private function solapaConOtraTemporada(string $campoInicio, string $campoFin, ?int $ignorarId): bool
    {
        return Temporada::query()
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->where($campoInicio, '<=', $this->input($campoFin))
            ->where($campoFin, '>=', $this->input($campoInicio))
            ->exists();
    }
}
