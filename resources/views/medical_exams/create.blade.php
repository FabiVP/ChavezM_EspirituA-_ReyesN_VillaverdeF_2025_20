@extends('layouts.panel')

@section('content')

<div class="card shadow">
    <div class="card-header border-0">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Subir examen médico</h3>
            </div>
            <div class="col text-right">
                <a href="{{ url('/medical-exams') }}" class="btn btn-sm btn-success">
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

        <form action="{{ url('/medical-exams') }}" method="POST" enctype="multipart/form-data">
            @csrf

         
            <!-- Nombre del examen -->
            <div class="form-group">
                <label for="exam_name">Nombre del examen</label>
                <input type="text" name="exam_name" class="form-control"
                       placeholder="Ej: Hemograma completo" value="{{ old('exam_name') }}" required>
            </div>

            <!-- Subir archivo -->
            <div class="form-group">
                <label for="file">Archivo del examen (PDF o imagen)</label>
                <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
            </div>

            <button type="submit" class="btn btn-sm btn-primary">Guardar examen</button>
        </form>

    </div>
</div>

@endsection
