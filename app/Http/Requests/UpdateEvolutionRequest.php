<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateEvolutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo el doctor que la creó puede actualizarla
        $evolution = $this->route('evolution');
        return Auth::check() && Auth::user()->role === 'doctor' && Auth::id() === $evolution->doctor_id;
    }

    public function rules(): array
    {
        return [
            'diagnosis' => 'required|string|min:5',
            'treatment' => 'nullable|string',
            'observations' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'diagnosis.required' => 'Debe ingresar un diagnóstico.',
            'diagnosis.min' => 'El diagnóstico debe tener al menos 5 caracteres.',
        ];
    }
}
