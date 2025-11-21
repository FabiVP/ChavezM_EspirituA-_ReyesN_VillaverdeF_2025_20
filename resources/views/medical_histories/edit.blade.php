@extends('layouts.panel')

@section('content')
    <div class="card shadow">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Editar Historia Clínica</h3>
                </div>
                <div class="col text-right">
                    <a href="{{ route('medical_histories.index') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-chevron-left"></i>
                        Regresar
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Por favor!!</strong> {{ $error }}
                    </div>
                @endforeach
            @endif

            <form action="{{ route('medical_histories.update', $medicalHistory->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Información del Paciente (solo lectura) -->
                <div class="form-group">
                    <label for="patient" class="form-control-label">Paciente</label>
                    <input type="text" class="form-control bg-light" 
                           value="{{ $medicalHistory->appointment->patient->name }}" 
                           disabled>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Información del paciente (solo lectura)
                    </small>
                </div>

                <!-- Información de la Cita -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Fecha de la cita</label>
                            <input type="text" class="form-control bg-light" 
                                   value="{{ \Carbon\Carbon::parse($medicalHistory->appointment->scheduled_date)->format('d/m/Y') }}" 
                                   disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Hora de la cita</label>
                            <input type="text" class="form-control bg-light" 
                                   value="{{ \Carbon\Carbon::parse($medicalHistory->appointment->scheduled_time)->format('h:i A') }}" 
                                   disabled>
                        </div>
                    </div>
                </div>

                <!-- Diagnóstico -->
                <div class="form-group">
                    <label for="diagnosis" class="form-control-label">Diagnóstico</label>
                    <textarea name="diagnosis" id="diagnosis" rows="4" 
                              class="form-control @error('diagnosis') is-invalid @enderror"
                              placeholder="Ingrese el diagnóstico del paciente..."
                              required>{{ old('diagnosis', $medicalHistory->diagnosis) }}</textarea>
                    @error('diagnosis')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-stethoscope"></i> Diagnóstico principal del paciente
                    </small>
                </div>

                <!-- Antecedentes Médicos -->
                <div class="form-group">
                    <label for="history" class="form-control-label">Antecedentes Médicos</label>
                    <textarea name="history" id="history" rows="5" 
                              class="form-control @error('history') is-invalid @enderror"
                              placeholder="Describa los antecedentes médicos relevantes...">{{ old('history', $medicalHistory->history) }}</textarea>
                    @error('history')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-clipboard-list"></i> Historia clínica y antecedentes relevantes
                    </small>
                </div>

                <!-- Botones de acción -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Actualizar Historia Clínica
                    </button>
                    <a href="{{ route('medical_histories.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Opcional: Agregar algún efecto o validación adicional
        $('#diagnosis, #history').on('focus', function() {
            $(this).addClass('border-primary');
        }).on('blur', function() {
            $(this).removeClass('border-primary');
        });
    });
</script>
@endsection