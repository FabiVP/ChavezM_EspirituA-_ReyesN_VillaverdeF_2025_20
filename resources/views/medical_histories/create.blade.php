@extends('layouts.panel')

@section('content')
<div class="card shadow border-0">
    <div class="card-header bg-gradient-primary text-white">
        <h3 class="mb-0">
            <i class="ni ni-folder-17"></i> Registrar Antecedentes Médicos
        </h3>
    </div>

    <div class="card-body">
        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('medical_histories.store') }}" method="POST">
            @csrf

            @if($appointment)
              <div class="alert alert-info">
                 <h5>📋 Información de la Cita</h5>
                    <strong>Paciente:</strong> {{ $appointment->patient->name }}<br>
                   <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($appointment->scheduled_date)->format('d/m/Y') }}<br>
                  <strong>Hora:</strong> {{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('H:i') }}
             </div>

                     <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
            @else
                 <div class="alert alert-warning">
                 No se ha seleccionado una cita.  
                 <br>
         <strong>Seleccione una cita atendida para registrar antecedentes.</strong>
                    </div>
            @endif


            <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

            {{-- Diagnóstico --}}
            <div class="form-group">
                <label for="diagnosis" class="form-control-label">Diagnóstico *</label>
                <textarea class="form-control @error('diagnosis') is-invalid @enderror" 
                          id="diagnosis" name="diagnosis" rows="4" 
                          placeholder="Ingrese el diagnóstico del paciente..." required>{{ old('diagnosis') }}</textarea>
                @error('diagnosis')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Antecedentes Médicos --}}
            <div class="form-group">
                <label for="history" class="form-control-label">Antecedentes Médicos</label>
                <textarea class="form-control @error('history') is-invalid @enderror" 
                          id="history" name="history" rows="4" 
                          placeholder="Ingrese los antecedentes médicos del paciente...">{{ old('history') }}</textarea>
                @error('history')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Botones --}}
            <div class="d-flex justify-content-end">
                <a href="{{ route('medical_histories.index') }}" class="btn btn-secondary mr-3">
                    <i class="ni ni-fat-remove"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ni ni-check-bold"></i> Guardar Antecedentes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection