@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
        <h3 class="mb-0">Detalle de evolución médica</h3>
    </div>

    <div class="card-body">
        <ul class="list-group mb-4">
            {{-- ✅ CORREGIDO: Usar $evolutionDetail en lugar de $evolution --}}
            <li class="list-group-item"><strong>Paciente:</strong> {{ $evolutionDetail->patient_name ?? 'N/A' }}</li>
            <li class="list-group-item"><strong>Médico:</strong> {{ $evolutionDetail->doctor_name ?? 'N/A' }}</li>
            <li class="list-group-item">
                <strong>Fecha de registro:</strong> 
                {{-- ✅ CORREGIDO: Parsear created_at como string --}}
                {{ \Carbon\Carbon::parse($evolutionDetail->created_at)->format('d/m/Y H:i') }}
            </li>
        </ul>

        <div class="mb-3">
            <h5>🩺 Diagnóstico</h5>
            <div class="alert alert-info">
                {{ $evolutionDetail->diagnosis }}
            </div>
        </div>

        <div class="mb-3">
            <h5>💊 Tratamiento</h5>
            <div class="alert alert-success">
                {{ $evolutionDetail->treatment ?? 'No se especificó tratamiento' }}
            </div>
        </div>

        <div class="mb-3">
            <h5>🗒️ Observaciones</h5>
            <div class="alert alert-secondary">
                {{ $evolutionDetail->observations ?? 'Sin observaciones adicionales.' }}
            </div>
        </div>

        <div class="text-right">
            {{-- ✅ CORREGIDO: Usar medical_history_id de $evolutionDetail --}}
            <a href="{{ route('evolutions.index', $evolutionDetail->medical_history_id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-chevron-left"></i> Volver
            </a>

            {{-- ✅ CORREGIDO: Validar con doctor_id de $evolutionDetail --}}
            @if(Auth::user()->role === 'doctor' && Auth::id() === $evolutionDetail->doctor_id)
                <a href="{{ route('evolutions.edit', $evolutionDetail->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endif
        </div>
    </div>
</div>
@endsection