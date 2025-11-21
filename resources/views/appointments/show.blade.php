@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Cita #{{ $appointment->id }}</h3>
            </div>
            <div class="col text-right">
                <a href="{{ url('/miscitas') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-chevron-left"></i>
                    Regresar
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- INFORMACIÓN DE REPROGRAMACIÓN --}}
        @if($appointment->wasReprogrammed())
            <div class="alert alert-success">
                <h5><i class="fas fa-sync-alt"></i> Información de Reprogramación</h5>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Reprogramado por:</strong> {{ $appointment->reprogrammedBy->name ?? 'N/A' }}<br>
                        <strong>Fecha de reprogramación:</strong> {{ $appointment->created_at->format('d/m/Y H:i') }}<br>
                        <strong>Motivo:</strong> {{ $appointment->reprogramming_reason ?? 'No especificado' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Cita original:</strong> 
                        <a href="{{ url('/miscitas/'.$appointment->originalAppointment->id) }}" class="btn btn-sm btn-outline-info ml-2">
                            Ver cita original
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- SI ESTA CITA FUE REPROGRAMADA (es la original) --}}
        @if($appointment->hasReprogrammedVersions())
            <div class="alert alert-info">
                <h5><i class="fas fa-history"></i> Esta cita fue reprogramada</h5>
                <strong>Nueva cita:</strong> 
                @foreach($appointment->reprogrammedAppointments as $reprogrammed)
                    <a href="{{ url('/miscitas/'.$reprogrammed->id) }}" class="btn btn-sm btn-outline-primary ml-2">
                        Ver cita reprogramada ({{ $reprogrammed->scheduled_date }})
                    </a>
                @endforeach
            </div>
        @endif

        {{-- INFORMACIÓN PRINCIPAL DE LA CITA --}}
        <ul>
            <dd>
                <strong>Fecha:</strong> {{ $appointment->scheduled_date }}
            </dd>
            <dd>
                <strong>Hora de atención:</strong> {{ $appointment->scheduled_time_12 }}
            </dd>

            @if($role == 'paciente' || $role == 'admin')
                <dd>
                    <strong>Doctor:</strong> {{ $appointment->doctor->name }}
                </dd>
            @endif
            
            @if($role == 'doctor' || $role == 'admin')
                <dd>
                    <strong>Paciente:</strong> {{ $appointment->patient->name }}
                </dd>
            @endif
            
            <dd>
                <strong>Especialidad:</strong> {{ $appointment->specialty->name }}
            </dd>
            <dd>
                <strong>Tipo de consulta:</strong> {{ $appointment->type }}
            </dd>
            <dd>
                <strong>Estado:</strong> 
                @if($appointment->status == 'Reprogramada')
                    <span class="badge badge-success">✅ Reprogramada</span>
                @elseif($appointment->status == 'Cancelada')
                    <span class="badge badge-danger">❌ Cancelada</span>
                @elseif($appointment->status == 'Confirmada')
                    <span class="badge badge-success">✅ Confirmada</span>
                @elseif($appointment->status == 'Atendida')
                    <span class="badge badge-info">🏥 Atendida</span>
                @else
                    <span class="badge badge-primary">{{ $appointment->status }}</span>
                @endif
            </dd>
            <dd>
                <strong>Síntomas:</strong> {{ $appointment->description }}
            </dd>
        </ul>

        {{-- BOTÓN REPROGRAMAR --}}
        @if($appointment->status == 'Cancelada' && $appointment->canBeReprogrammed())
            <div class="mt-3">
                <a href="{{ route('appointments.reprogram.form', $appointment->id) }}" 
                   class="btn btn-warning">
                   <i class="fas fa-sync-alt"></i> Reprogramar Cita
                </a>
            </div>
        @endif

        {{-- DETALLES DE CANCELACIÓN --}}
        @if($appointment->status == 'Cancelada')
            <div class="alert bg-light text-primary mt-3">
                <h3>Detalles de la cancelación</h3>
                @if($appointment->cancellation)
                    <ul>
                        <li>
                            <strong>Fecha de cancelación:</strong>
                            {{ $appointment->cancellation->created_at }}
                        </li>
                        <li>
                            <strong>La cita fue cancelada por:</strong>
                            {{ $appointment->cancellation->cancelled_by->name }}
                        </li>
                        <li>
                            <strong>Motivo de la cancelación:</strong>
                            {{ $appointment->cancellation->justification }}
                        </li>
                    </ul>
                @else 
                    <ul>
                        <li>La cita fue cancelada antes de su confirmación.</li>
                    </ul>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection