@extends('layouts.panel')

@section('content')
<div class="container">

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4>Antecedentes Médicos</h4>
        </div>

        <div class="card-body">
            {{-- Información de la cita --}}
            <h5 class="mb-3">📅 Información de la cita</h5>
            <ul class="list-group mb-4">
                <li class="list-group-item"><strong>Paciente:</strong> {{ $medicalHistory->appointment->patient->name }}</li>
                <li class="list-group-item"><strong>Médico:</strong> {{ $medicalHistory->appointment->doctor->name }}</li>
                <li class="list-group-item"><strong>Fecha:</strong> {{ $medicalHistory->appointment->scheduled_date }}</li>
                <li class="list-group-item"><strong>Hora:</strong> {{ $medicalHistory->appointment->scheduled_time }}</li>
                <li class="list-group-item"><strong>Estado cita:</strong> {{ $medicalHistory->appointment->status }}</li>
            </ul>

            {{-- Diagnóstico --}}
            <h5 class="mb-3">🩺 Diagnóstico</h5>
            <div class="alert alert-info">
                {{ $medicalHistory->diagnosis }}
            </div>

            {{-- Antecedentes Médicos --}}
            <h5 class="mb-3">📖 Antecedentes médicos</h5>
            <div class="alert alert-secondary mb-4">
                {{ $medicalHistory->history ?? 'No se registraron antecedentes adicionales.' }}
            </div>

            {{-- BOTONES SEGÚN ROL --}}
            <div class="mt-4 d-flex gap-2">

                {{-- SOLO DOCTOR --}}
                @if(Auth::user()->role === 'doctor')

                    {{-- Editar historial --}}
                    <a href="{{ route('medical_histories.edit', $medicalHistory->id) }}" 
                       class="btn btn-warning mr-2">
                        ✏️ Editar Antecedentes
                    </a>

                    {{-- Registrar evolución médica --}}
                    @if($medicalHistory->appointment->status === 'Atendida')
                        <a href="{{ route('evolutions.create', $medicalHistory->id) }}" 
                           class="btn btn-primary mr-2">
                            🩺 Registrar evolución médica
                        </a>
                    @else
                        <button class="btn btn-secondary mr-2" disabled>
                            🩺 Registrar evolución médica (Cita no atendida)
                        </button>
                    @endif

                @endif

                {{-- Botón Ver evoluciones (para todos los roles autenticados) --}}
                <a href="{{ route('evolutions.index', $medicalHistory->id) }}" class="btn btn-info">
                    📂 Ver evoluciones
                </a>

            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('medical_histories.index') }}" class="btn btn-secondary">
            ⬅️ Volver al listado
        </a>
    </div>

    <a href="{{ route('medical-exams.create') }}" class="btn btn-primary">
        Subir Examen Médico
    </a>
    

</div>
@endsection
