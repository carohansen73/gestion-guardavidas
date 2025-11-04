<?php

namespace App\Http\Requests;

use App\Models\Bandera;
use Illuminate\Foundation\Http\FormRequest;

class StoreBanderaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //Llamo al policy para que chequee si el usuario esta autorizado
        return auth()->user()->can('agregar_bandera', Bandera::class);
        // return Auth::user()->can('create', Bandera::class);
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
            'turno'             => 'nullable|in:M,T',
            'bandera_id'        => 'required|exists:bandera_tipos,id',
            'playa_id'          => 'required|exists:playas,id',
            'viento_intensidad' => 'nullable|numeric|min:0',   // velocidad en km/h o m/s
            'viento_direccion'  => 'nullable|string',
            'temperatura'       => 'nullable|string',
            'detalles'          => 'nullable|string',

        ];
    }
}

/**
 * TODO Hacer pantallas de error:
 * -> 403 Forbidden (si noe stá autorizado)
 *  -> 422 si no valida alguna regla
*/
