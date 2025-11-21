<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEvolutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo los doctores pueden registrar evoluciones
        return Auth::check() && Auth::user()->role === 'doctor';
    }

    public function rules(): array
    {
        return [
            'medical_history_id' => 'required|exists:medical_histories,id',
            'diagnosis' => 'required|string|min:5',
            'treatment' => 'nullable|string',
            'observations' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'medical_history_id.required' => 'El campo historial médico es obligatorio.',
            'medical_history_id.exists' => 'El historial médico seleccionado no existe.',
            'diagnosis.required' => 'Debe ingresar un diagnóstico.',
            'diagnosis.min' => 'El diagnóstico debe tener al menos 5 caracteres.',
        ];
    }
}
