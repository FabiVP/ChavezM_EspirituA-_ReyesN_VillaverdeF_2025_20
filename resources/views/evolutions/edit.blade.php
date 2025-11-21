@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header border-0 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Editar evolución médica</h3>
        <a href="{{ route('evolutions.index', $evolution->medical_history_id) }}" class="btn btn-sm btn-success">
            <i class="fas fa-chevron-left"></i> Regresar
        </a>
    </div>

    <div class="card-body">
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> {{ $error }}
                </div>
            @endforeach
        @endif

        <form action="{{ route('evolutions.update', $evolution->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="diagnosis">Diagnóstico</label>
                <textarea name="diagnosis" id="diagnosis" rows="3" class="form-control" required>{{ old('diagnosis', $evolution->diagnosis) }}</textarea>
            </div>

            <div class="form-group">
                <label for="treatment">Tratamiento</label>
                <textarea name="treatment" id="treatment" rows="3" class="form-control" required>{{ old('treatment', $evolution->treatment) }}</textarea>
            </div>

            <div class="form-group">
                <label for="observations">Observaciones adicionales</label>
                <textarea name="observations" id="observations" rows="3" class="form-control">{{ old('observations', $evolution->observations) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save"></i> Actualizar evolución
            </button>
        </form>
    </div>
</div>
@endsection
