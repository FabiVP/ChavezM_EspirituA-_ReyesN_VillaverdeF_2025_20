@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h3>Seleccionar Paciente</h3>
    </div>

    <div class="card-body">

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Correo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                <tr>
                    <td>{{ $patient->name }}</td>
                    <td>{{ $patient->cedula ?? '---' }}</td>
                    <td>{{ $patient->email }}</td>
                    <td>
                        <a href="{{ route('clinical.record.show', $patient->id) }}" class="btn btn-primary btn-sm">
                            Ver Expediente
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection
