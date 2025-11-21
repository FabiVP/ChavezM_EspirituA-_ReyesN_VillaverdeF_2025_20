@extends('layouts.panel')

@section('content')
<div class="container">

    <div class="card shadow-lg">
        <div class="card-header bg-success text-white">
            <h4>📂 Expediente Clínico - {{ $patient->name }}</h4>
        </div>

        <div class="card-body">
            
            <!-- Navegación por pestañas -->
            <ul class="nav nav-tabs mb-4" id="clinicalRecordTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#info">🧑‍⚕️ Datos del Paciente</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#history">📖 Antecedentes Médicos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#evolutions">🩺 Evoluciones Médicas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#appointments">📅 Historial de Citas</a>
                </li>
            </ul>

            <!-- Contenido de pestañas -->
            <div class="tab-content">

                <!-- Datos del paciente -->
                <div class="tab-pane fade show active" id="info">
                    <h5>Información del paciente</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Nombre:</strong> {{ $patient->name }}</li>
                        <li class="list-group-item"><strong>Email:</strong> {{ $patient->email }}</li>
                        <li class="list-group-item"><strong>DNI:</strong> {{ $patient->cedula ?? 'No registrado' }}</li>
                        <li class="list-group-item"><strong>Teléfono:</strong> {{ $patient->phone ?? 'No registrado' }}</li>
                        <li class="list-group-item"><strong>Dirección:</strong> {{ $patient->address ?? 'No registrada' }}</li>
                    </ul>
                </div>

                <!-- Antecedentes médicos -->
                <div class="tab-pane fade" id="history">
                    <h5>Antecedentes Médicos</h5>
                    @forelse($histories as $history)
                        <div class="alert alert-secondary mb-2">
                            <strong>Diagnóstico:</strong> {{ $history->diagnosis }} <br>
                            <strong>Antecedentes:</strong> {{ $history->history ?? 'N/A' }} <br>
                            <small class="text-muted">📅 {{ $history->created_at->format('d/m/Y') }}</small>
                        </div>
                    @empty
                        <p class="text-muted">Sin antecedentes registrados.</p>
                    @endforelse
                </div>

                <!-- Evoluciones médicas -->
                <div class="tab-pane fade" id="evolutions">
                    <h5>Evoluciones Médicas</h5>
                    @forelse($evolutions as $evolution)
                        <div class="alert alert-info mb-2">
                            <strong>Descripción:</strong> {{ $evolution->description }} <br>
                            <strong>Tratamiento:</strong> {{ $evolution->treatment ?? 'N/A' }} <br>
                            <small class="text-muted">🕒 {{ $evolution->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    @empty
                        <p class="text-muted">Sin evoluciones registradas.</p>
                    @endforelse
                </div>

                <!-- Citas -->
                <div class="tab-pane fade" id="appointments">
                    <h5>Historial de Citas</h5>
                    @forelse($appointments as $appointment)
                        <div class="list-group mb-2">
                            <div class="list-group-item">
                                <strong>Fecha:</strong> {{ $appointment->scheduled_date }} <br>
                                <strong>Hora:</strong> {{ $appointment->scheduled_time }} <br>
                                <strong>Médico:</strong> {{ $appointment->doctor->name }} <br>
                                <strong>Estado:</strong> {{ $appointment->status }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No hay citas registradas.</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">⬅️ Volver</a>
    </div>

</div>
@endsection
