@extends('layouts.panel')

@section('content')
<div class="card shadow border-0">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
            <i class="ni ni-folder-17"></i> Antecedentes Médicos
        </h3>
    </div>

    <div class="card-body">
        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ni ni-check-bold"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- Tabla --}}
        <div class="table-responsive">
            <table class="table table-hover align-items-center">
                <thead class="thead-light text-center">
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
                <tbody class="text-center">
                    @forelse($appointments as $appointment)
                        <tr>
                            {{-- Fecha y Hora --}}
                            <td>{{ \Carbon\Carbon::parse($appointment->scheduled_date)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('H:i') }}</td>
                            
                            {{-- Paciente --}}
                            <td>
                                <i class="ni ni-single-02 text-primary"></i>
                                {{ $appointment->patient->name ?? '—' }}
                            </td>

                            {{-- Estado --}}
                            <td>
                                @if($appointment->status === 'Atendida')
                                    <span class="badge badge-success">Atendida</span>
                                @elseif($appointment->status === 'Cancelada')
                                    <span class="badge badge-danger">Cancelada</span>
                                @else
                                    <span class="badge badge-warning">{{ $appointment->status }}</span>
                                @endif
                            </td>

                            {{-- Diagnóstico --}}
                            <td>
                                @if($appointment->medicalHistory && $appointment->medicalHistory->diagnosis)
                                    📝 <span class="text-success font-weight-bold">Registrado ✅</span>
                                @else
                                    📝 <span class="text-muted">No registrado ❌</span>
                                @endif
                            </td>

                           
                            {{-- Antecedentes --}} {{-- Antecedentes --}} {{-- Antecedentes --}}
                            <td>
                              @if($appointment->medicalHistory && $appointment->medicalHistory->history)  {{-- Cambiado --}}
                                  📋 <span class="text-info">Registrado ✅</span>
                            @else
                                 📋 <span class="text-muted">Ninguno registrado ❌</span>
                                 @endif
                                </td>

                            {{-- Opciones --}}
                            <td>
                                {{-- Ver --}}
                                @if($appointment->medicalHistory)
                                    <a href="{{ route('medical_histories.show', $appointment->medicalHistory->id) }}" 
                                       class="btn btn-sm btn-info" title="Ver detalle">
                                       🔍 Ver
                                    </a>
                                @endif

                                {{-- Editar  --}}
                                @if(auth()->user()->role == 'doctor')
                                    @if($appointment->medicalHistory)
                                        <a href="{{ route('medical_histories.edit', $appointment->medicalHistory->id) }}" 
                                           class="btn btn-sm btn-warning" title="Editar historial">
                                           ✏️ Editar
                                        </a>
                                    @else
                                        <a href="{{ route('medical_histories.create', $appointment->id) }}" 
                                           class="btn btn-sm btn-success" title="Registrar historial">
                                           ➕ Registrar
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="ni ni-single-copy-04"></i>
                                No hay antecedentes médicos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection