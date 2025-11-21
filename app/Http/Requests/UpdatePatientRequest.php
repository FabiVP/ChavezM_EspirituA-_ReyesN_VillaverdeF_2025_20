<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Laravel Resource usa {paciente}, no {medico}
        $userId = $this->route('paciente') ?? $this->route('id');

        return [
            'name' => 'required|min:3',
            'address' => 'nullable|min:6',

            // DNI (8 dígitos) único, ignorando el usuario actual
            'cedula' => [
                'required',
                'digits:8',
                Rule::unique('users', 'cedula')->ignore($userId),
            ],

            // Teléfono (9 dígitos)
            'phone' => [
                'required',
                'digits:9',
            ],

            // Email único y con dominio Gmail
            'email' => [
                'required',
                'email',
                'regex:/^.+@gmail\.com$/i',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            // Contraseña opcional
            'password' => 'nullable|min:8',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del paciente es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.unique' => 'El correo electrónico ya se encuentra registrado.',
            'email.regex' => 'El correo electrónico debe usar el dominio @gmail.com.',

            'cedula.required' => 'El DNI es obligatorio.',
            'cedula.digits' => 'El DNI debe tener 8 dígitos (formato peruano).',
            'cedula.unique' => 'El DNI ya se encuentra registrado.',

            'address.min' => 'La dirección debe tener al menos 6 caracteres.',

            'phone.required' => 'El número de teléfono es obligatorio.',
            'phone.digits' => 'El número de teléfono debe tener 9 dígitos.',

            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
