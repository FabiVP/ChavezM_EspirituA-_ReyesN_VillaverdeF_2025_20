@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Notificar Resultados Disponibles</h3>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Mensaje de confirmación --}}
        @if (session('notification'))
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert" style="max-width: 600px; margin: 20px auto;">
                <strong>{{ session('notification') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true" class="text-danger" style="font-size: 1.5rem;">&times;</span>
                </button>
            </div>
        @endif

        @if (empty($appointments) || count($appointments) === 0)
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                No tienes citas atendidas para notificar resultados.
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered align-items-center">
                <thead class="thead-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Especialidad</th>
                        <th>Descripción</th>
                        <th>Recordatorio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->scheduled_date }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A') }}</td>
                        <td>{{ $appointment->patient_name }}</td>
                        <td>{{ $appointment->specialty_name }}</td>
                        <td>{{ $appointment->description }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    data-toggle="modal" 
                                    data-target="#resultsModal"
                                    data-patient-name="{{ $appointment->patient_name }}"
                                    data-appointment-date="{{ $appointment->scheduled_date }}"
                                    data-appointment-id="{{ $appointment->id }}">
                                📋 Notificar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL PARA NOTIFICAR RESULTADOS --}}
{{-- ========================================================= --}}
<div class="modal fade" id="resultsModal" tabindex="-1" aria-labelledby="resultsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="resultsModalLabel">
                    <i class="fas fa-file-medical me-2"></i>Notificar Resultados
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-danger" style="font-size: 1.5rem;">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-file-pdf fa-3x text-primary mb-3"></i>
                </div>
                <h5 class="mb-3" id="resultsMessage"></h5>
                <p class="text-muted">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Los resultados serán enviados al paciente
                    </small>
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <form id="confirmResultsForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane me-2"></i>Enviar Resultados
                    </button>
                </form>
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resultsModal = document.getElementById('resultsModal');
        const resultsMessage = document.getElementById('resultsMessage');
        const confirmResultsForm = document.getElementById('confirmResultsForm');
        
        // Cuando se abre el modal
        $('#resultsModal').on('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const patientName = button.getAttribute('data-patient-name');
            const appointmentDate = button.getAttribute('data-appointment-date');
            const appointmentId = button.getAttribute('data-appointment-id');
            
            // Construir mensaje
            const message = `¿Enviar resultados a <strong>${patientName}</strong> para la cita del <strong>${appointmentDate}</strong>?`;
            resultsMessage.innerHTML = message;
            
            // Actualizar acción del formulario
            confirmResultsForm.action = `/appointments/${appointmentId}/send-results`;
        });
    });
</script>
@endsection