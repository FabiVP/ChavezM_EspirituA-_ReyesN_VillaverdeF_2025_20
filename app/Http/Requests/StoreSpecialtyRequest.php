<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecialtyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
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
            // Regla: Requerido, mínimo 3 caracteres Y DEBE SER ÚNICO en la tabla 'specialties'
            'name' => 'required|min:3|unique:specialties',
            'description' => 'nullable', // Asumimos que la descripción es opcional.
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
            'name.required' => 'El nombre de la especialidad es obligatorio.',
            'name.min' => 'El nombre de la especialidad debe tener más de 3 caracteres.',
            'name.unique' => 'Ya existe una especialidad con este nombre.',
        ];
    }
}