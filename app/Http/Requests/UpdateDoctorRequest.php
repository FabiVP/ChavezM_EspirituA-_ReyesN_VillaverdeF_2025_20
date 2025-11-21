<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Captura el ID del médico desde la ruta resource
        $doctor = $this->route('medico');
        $userId = is_object($doctor) ? $doctor->id : $doctor;

        return [
            'name' => 'required|min:3',
            'address' => 'nullable|min:6',

            'cedula' => [
                'required',
                'digits:8',
                Rule::unique('users', 'cedula')->ignore($userId, 'id'),
            ],

            'phone' => 'required|digits:9',

            'email' => [
                'required',
                'email',
                'regex:/^.+@gmail\.com$/i',
                Rule::unique('users', 'email')->ignore($userId, 'id'),
            ],

            'password' => 'nullable|min:8',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del médico es obligatorio.',
            'name.min' => 'El nombre del médico debe tener más de 3 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa una dirección de correo electrónico válida.',
            'email.unique' => 'El correo electrónico ya se encuentra registrado.',
            'email.regex' => 'El correo electrónico solo debe usar el dominio @gmail.com.',

            'cedula.required' => 'El DNI es obligatorio.',
            'cedula.digits' => 'El DNI debe tener 8 dígitos.',
            'cedula.unique' => 'El DNI ya se encuentra registrado.',

            'address.min' => 'La dirección debe tener al menos 6 caracteres.',
            'phone.required' => 'El número de teléfono es obligatorio.',
            'phone.digits' => 'El número de teléfono debe tener 9 dígitos.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
