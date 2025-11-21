@extends('layouts.panel')

@section('content')

<div class="card shadow">
    <div class="card-header border-0">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Registrar evolución médica</h3>
            </div>
            <div class="col text-right">
                <a href="{{ route('medical_histories.show', $medicalHistory->id) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-chevron-left"></i> Regresar
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">

        {{-- ✅ Mostrar errores de validación --}}
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Por favor:</strong> {{ $error }}
                </div>
            @endforeach
        @endif

        {{-- ✅ Formulario para registrar evolución médica --}}
        <form action="{{ route('evolutions.store') }}" method="POST">
            @csrf

            {{-- ID oculto del historial médico --}}
            <input type="hidden" name="medical_history_id" value="{{ $medicalHistory->id }}">

            <div class="form-group">
                <label for="diagnosis" class="font-weight-bold">Diagnóstico</label>
                <textarea name="diagnosis" id="diagnosis" rows="3" 
                    class="form-control @error('diagnosis') is-invalid @enderror" 
                    placeholder="Escriba el diagnóstico del paciente..." required>{{ old('diagnosis') }}</textarea>
                @error('diagnosis')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="treatment" class="font-weight-bold">Tratamiento</label>
                <textarea name="treatment" id="treatment" rows="3" 
                    class="form-control @error('treatment') is-invalid @enderror" 
                    placeholder="Describa el tratamiento indicado..." required>{{ old('treatment') }}</textarea>
                @error('treatment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="observations" class="font-weight-bold">Observaciones adicionales</label>
                <textarea name="observations" id="observations" rows="3" 
                    class="form-control @error('observations') is-invalid @enderror" 
                    placeholder="Notas adicionales sobre la evolución (opcional)">{{ old('observations') }}</textarea>
                @error('observations')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-save"></i> Guardar evolución
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
