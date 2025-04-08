<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'], // Nombre
            'apellido_paterno' => ['required', 'string', 'max:255'], // Apellido paterno
            'apellido_materno' => ['required', 'string', 'max:255'], // Apellido materno
            'numero' => ['required', 'string', 'max:20'], // Número telefónico
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->id)], // Email, ignorando el actual
        ];
    }
    public function attributes(): array
    {
        return [
            'name' => 'Nombre',
            'apellido_paterno' => 'Apellido Paterno',
            'apellido_materno' => 'Apellido Materno',
            'numero' => 'Número Telefónico',
            'email' => 'Correo Electrónico',
        ];
    }

}
