<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $id = $this->input('id');
        return [
            'rol_id' => ['required'],
            'numero_documento' => ['required'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'genero' => ['required'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->id)],
            'password' => ['confirmed', Rules\Password::defaults()],
            'estado' => ['required'],
        ];
    }
}
