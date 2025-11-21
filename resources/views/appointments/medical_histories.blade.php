@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
        <h3 class="mb-0">Antecedentes Médicos - Citas Realizadas</h3>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Estado</th>
                    <th>Diagnóstico</th>
                    <th>Antecedentes Médicos</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->scheduled_date }}</td>
                        <td>{{ $appointment->scheduled_time_12 }}</td>
                        <td>{{ $appointment->patient->name }}</td>
                        <td><span class="badge badge-success">Realizada</span></td>

                        {{-- Diagnóstico --}}
                        <td>
                            @if($appointment->medicalHistory && $appointment->medicalHistory->diagnosis)
                                ✅ {{ $appointment->medicalHistory->diagnosis }}
                            @else
                                <a href="{{ route('diagnosis.create', $appointment->id) }}" class="btn btn-sm btn-outline-primary">📝 Registrar</a>
                            @endif
                        </td>

                        {{-- Antecedentes --}}
                        <td>
                            @if($appointment->medicalHistory && $appointment->medicalHistory->history)
                                ✅ {{ $appointment->medicalHistory->history }}
                            @else
                                <a href="{{ route('history.create', $appointment->id) }}" class="btn btn-sm btn-outline-info">📋 Registrar</a>
                            @endif
                        </td>

                        {{-- Opciones --}}
                        <td>
                            <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-sm btn-info">🔍 Ver</a>
                            <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-sm btn-warning">✏️ Editar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
