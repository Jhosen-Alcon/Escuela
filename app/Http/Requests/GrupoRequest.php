<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrupoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'nivel_id' => ['required'],
            'grado' => ['required'],
            'seccion' => ['required'],
            'empleado_id' => ['required'],
            'anio' => ['required'],
        ];
    }
}
