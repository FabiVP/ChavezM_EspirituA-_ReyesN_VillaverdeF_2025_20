<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpecialtyRequest extends FormRequest
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
        // El ID de la especialidad se obtiene automáticamente gracias al Route Model Binding
        $specialtyId = $this->route('specialty')->id;

        return [
            // Regla: Requerido, mínimo 3 caracteres. ÚNICO, IGNORANDO EL ID ACTUAL
            'name' => [
                'required',
                'min:3',
                Rule::unique('specialties')->ignore($specialtyId),
            ],
            'description' => 'nullable',
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