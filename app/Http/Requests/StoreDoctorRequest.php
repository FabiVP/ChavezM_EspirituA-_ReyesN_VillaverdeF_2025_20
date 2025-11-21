<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Solo permitir a usuarios autenticados crear doctores (asumimos que solo el admin lo hace).
     * @return bool
     */
    public function authorize()
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3',
            // DNI (8 DÍGITOS) y ÚNICO
            'cedula' => 'required|digits:8|unique:users', 
            // TELÉFONO (9 DÍGITOS)
            'phone' => 'required|digits:9',
            // EMAIL (@GMAIL.COM) y ÚNICO
            'email' => 'required|email|regex:/^.+@gmail\.com$/i|unique:users',
            'address' => 'nullable|min:6',
            'specialties' => 'required|array', // Se debe seleccionar al menos una especialidad
            'password' => 'required|min:8', // La contraseña es obligatoria al crear
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'El nombre del médico es obligatorio',
            'name.min' => 'El nombre del médico debe tener más de 3 caracteres',
            
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Ingresa una dirección de correo electrónico válido',
            'email.unique' => 'El Correo Electrónico ya se encuentra registrado.',
            'email.regex' => 'El Correo Electrónico solo debe usar el dominio @gmail.com',
            
            'cedula.required' => 'El DNI es obligatorio!',
            'cedula.digits' => 'El DNI debe tener 8 dígitos!',
            'cedula.unique' => 'El DNI ya se encuentra registrado!',
            
            'address.min' => 'La dirección debe tener al menos 6 caracteres',
            
            'phone.required' => 'El número de teléfono es obligatorio',
            'phone.digits' => 'El número de teléfono debe tener 9 dígitos.',
            
            'specialties.required' => 'Debe seleccionar al menos una especialidad.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}