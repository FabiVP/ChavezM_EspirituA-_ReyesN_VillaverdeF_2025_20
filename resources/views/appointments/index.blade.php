@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header border-0 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Mis citas médicas</h3>
        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'paciente')
            <a href="{{ url('/reservarcitas/create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Nueva cita
            </a>
        @endif
    </div>

    <div class="card-body">
        {{-- ✅ Notificación de éxito --}}
        @if (session('notification'))
            <div class="alert alert-success" role="alert">
                {{ session('notification') }}
            </div>
        @endif

        @php
            $userRole = $role ?? (auth()->check() ? auth()->user()->role : null);
            $isAdmin  = $userRole === 'admin';
            $isDoctor = $userRole === 'doctor';
            $isPatient = in_array($userRole, ['paciente','patient']);
        @endphp

        {{-- === Citas pendientes === --}}
        <h4>Citas pendientes</h4>
        @if (empty($pendingAppointments) || count($pendingAppointments) === 0)
            <p>No tienes citas pendientes.</p>
        @else
        <div class="table-responsive">
            <table class="table table-bordered align-items-center">
                <thead class="thead-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acción</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pendingAppointments as $appointment)
                        @php
                            $rowColor = match($appointment->status) {
                                'Reprogramada' => 'table-success',
                                'Cancelada' => 'table-danger',
                                'Confirmada' => 'table-info',
                                default => ''
                            };
                        @endphp

                        <tr class="{{ $rowColor }}">
                            <td>{{ $appointment->scheduled_date }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A') }}</td>
                            <td class="fs-5 fw-bold">
                                @switch($appointment->status)
                                    @case('Reprogramada')
                                        <span class="badge bg-success">✅ REPROGRAMADA</span>
                                        @break
                                    @case('Cancelada')
                                        <span class="badge bg-danger">❌ CANCELADA</span>
                                        @break
                                    @case('Confirmada')
                                        <span class="badge bg-primary">📅 CONFIRMADA</span>
                                        @break
                                    @default
                                        <span class="badge bg-warning text-dark">RESERVADA</span>
                                @endswitch
                            </td>

                            <td>
                                <a href="{{ url('/miscitas/'.$appointment->id) }}" class="btn btn-sm btn-info">
                                    Ver
                                </a>
                            </td>

                            <td>
                                {{-- ✅ Verifica existencia del método antes de usarlo (capa 4 compatibilidad) --}}
                                @if(property_exists($appointment, 'status') && $appointment->status == 'Cancelada')
                                    @if(method_exists($appointment, 'canBeReprogrammed') && $appointment->canBeReprogrammed())
                                        <a href="{{ route('appointments.reprogram.form', $appointment->id) }}" 
                                           class="btn btn-sm btn-warning mb-1" title="Reprogramar cita">
                                           🔄 Reprogramar
                                        </a>
                                        @include('appointments.partials.cancel-button', ['appointment' => $appointment])
                                    @endif
                                @elseif($appointment->status == 'Reprogramada')
                                    <button class="btn btn-sm btn-success mb-1" disabled>
                                        ✅ Reprogramada
                                    </button>
                                @elseif($appointment->status == 'Confirmada')
                                    <button class="btn btn-sm btn-primary mb-1" disabled>
                                        📅 Programada
                                    </button>
                                @else
                                    @include('appointments.partials.cancel-button', ['appointment' => $appointment])
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif



{{-- ========================================================= --}}{{-- ========================================================= --}}{{-- ========================================================= --}}
   {{-- === Citas confirmadas ========================================================= --}}
{{-- === Citas confirmadas ========================================================= --}}
<h4 class="mt-4">Citas confirmadas</h4>

@if (empty($confirmedAppointments) || count($confirmedAppointments) === 0)
    <p>No tienes citas confirmadas.</p>
@else
<div class="table-responsive">
    <table class="table table-bordered align-items-center">
        <thead class="thead-light">
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Notificaciones</th>
                <th>Recordatorio</th>
                <th>Acción</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($confirmedAppointments as $appointment)
            <tr>
                <td>{{ $appointment->scheduled_date }}</td>
                <td>{{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A') }}</td>
                <td class="fs-5 fw-bold">
                    <span class="badge bg-primary">📅 CONFIRMADA</span>
                </td>
                
                {{-- COLUMNA DE NOTIFICACIONES AUTOMÁTICAS --}}
                <td>
                    <span class="badge {{ App\Http\Controllers\AppointmentController::getNotificationClass($appointment) }}">
                        {{ App\Http\Controllers\AppointmentController::getNotificationText($appointment) }}
                    </span>
                </td>
                
                {{-- COLUMNA: BOTÓN DE NOTIFICACIÓN MANUAL --}}
              {{-- COLUMNA: BOTÓN DE NOTIFICACIÓN MANUAL --}}
                <td>
                    <button type="button" class="btn btn-sm btn-outline-success" 
                    data-toggle="modal" 
                    data-target="#notificationModal"
                    data-patient-name="{{ $appointment->patient->name ?? 'Paciente' }}"
                    data-appointment-date="{{ $appointment->scheduled_date }}"
                     data-appointment-time="{{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A') }}"
            data-appointment-id="{{ $appointment->id }}">
        📧 Notificar
                    </button>
                </td>
                
                <td>
                    <a href="{{ url('/miscitas/'.$appointment->id) }}" class="btn btn-sm btn-info">Ver</a>
                </td>
                
                <td>
                    @if(App\Http\Controllers\AppointmentController::canCancelAppointment($appointment))
                        @include('appointments.partials.cancel-button', ['appointment' => $appointment])
                    @else
                        <span class="text-danger">
                            ❌ No se puede cancelar
                        </span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ========================================================= --}}
{{-- MODAL DE NOTIFICACIÓN CENTRADO MEJORADO --}}
{{-- ========================================================= --}}
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="notificationModalLabel">
                    <i class="fas fa-bell me-2"></i>Enviar Recordatorio
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-danger" style="font-size: 1.5rem;">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-paper-plane fa-3x text-success mb-3"></i>
                </div>
                <h5 class="mb-3" id="notificationMessage"></h5>
                
                {{-- ========================================================= --}}
                {{-- OPCIONES DE NOTIFICACIÓN --}}
                {{-- ========================================================= --}}
                <div class="row mt-4">
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-success w-100 notification-option" data-channel="whatsapp">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-primary w-100 notification-option" data-channel="email">
                            <i class="fas fa-envelope me-2"></i>Email
                        </button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-info w-100 notification-option" data-channel="sms">
                            <i class="fas fa-sms me-2"></i>SMS
                        </button>
                    </div>
                </div>
                
                <p class="text-muted mt-3">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Selecciona el canal de notificación
                    </small>
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <form id="confirmNotificationForm" method="POST">
                    @csrf
                    <input type="hidden" name="channel" id="selectedChannel" value="">
                    <button type="submit" class="btn btn-success px-4" id="sendNotificationBtn" disabled>
                        <i class="fas fa-paper-plane me-2"></i>Enviar
                    </button>
                </form>
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
{{-- ========================================================= --}}{{-- ========================================================= --}}{{-- ========================================================= --}}
        {{-- === Historial === --}}
        <h4 class="mt-4">Historial de citas</h4>
        @if (empty($oldAppointments) || count($oldAppointments) === 0)
            <p>No tienes historial de citas.</p>
        @else
        <div class="table-responsive">
            <table class="table table-bordered align-items-center">
                <thead class="thead-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acción</th>
                        <th>Reprogramar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($oldAppointments as $appointment)
                        @php
                            $rowColor = match($appointment->status) {
                                'Reprogramada' => 'table-success',
                                'Cancelada' => 'table-danger',
                                'Atendida' => 'table-secondary',
                                default => ''
                            };
                        @endphp
                        
                            <td>{{ $appointment->scheduled_date }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A') }}</td>

                            <td class="fs-4 fw-bold text-uppercase">
                                @if($appointment->status == 'Reprogramada')
                                    <span class="text-success">✅ REPROGRAMADA</span>
                                @elseif($appointment->status == 'Cancelada')
                                    <span class="text-danger">❌ CANCELADA</span>
                                @elseif($appointment->status == 'Atendida')
                                    <span class="text-primary">🩺 ATENDIDA</span>
                                @else
                                    <span class="text-dark">{{ strtoupper($appointment->status) }}</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ url('/miscitas/'.$appointment->id) }}" class="btn btn-sm btn-info">Ver</a>
                            </td>

                            <td>
                                @if(property_exists($appointment, 'status') && $appointment->status == 'Cancelada')
                                    @if(method_exists($appointment, 'canBeReprogrammed') && $appointment->canBeReprogrammed())
                                        <a href="{{ route('appointments.reprogram.form', $appointment->id) }}" 
                                           class="btn btn-sm btn-warning" title="Reprogramar cita">
                                           🔄 Reprogramar
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @elseif($appointment->status == 'Reprogramada')
                                    <button class="btn btn-sm btn-success" disabled title="Ya reprogramada">
                                        ✅ Reprogramada
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection



{{-- ========================================================= --}}
{{-- JAVASCRIPT PARA EL MODAL --}}
{{-- ========================================================= --}}
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationModal = document.getElementById('notificationModal');
        const notificationMessage = document.getElementById('notificationMessage');
        const confirmNotificationForm = document.getElementById('confirmNotificationForm');
        const selectedChannel = document.getElementById('selectedChannel');
        const sendNotificationBtn = document.getElementById('sendNotificationBtn');
        
        // Cuando se abre el modal
        $('#notificationModal').on('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const patientName = button.getAttribute('data-patient-name');
            const appointmentDate = button.getAttribute('data-appointment-date');
            const appointmentTime = button.getAttribute('data-appointment-time');
            const appointmentId = button.getAttribute('data-appointment-id');
            
            // Construir mensaje
            const message = `Enviar recordatorio a <strong>${patientName}</strong><br>para la cita del <strong>${appointmentDate}</strong><br>a las <strong>${appointmentTime}</strong>`;
            notificationMessage.innerHTML = message;
            
            // Actualizar acción del formulario
            confirmNotificationForm.action = `/appointments/${appointmentId}/send-reminder`;
            
            // Resetear selección
            resetChannelSelection();
        });
        
        // Seleccionar canal de notificación
        document.querySelectorAll('.notification-option').forEach(button => {
            button.addEventListener('click', function() {
                // Remover selección anterior
                document.querySelectorAll('.notification-option').forEach(btn => {
                    btn.classList.remove('btn-success', 'btn-primary', 'btn-info');
                    btn.classList.add('btn-outline-success', 'btn-outline-primary', 'btn-outline-info');
                });
                
                // Agregar selección actual
                const channel = this.getAttribute('data-channel');
                this.classList.remove('btn-outline-success', 'btn-outline-primary', 'btn-outline-info');
                
                // Colores según el canal
                if (channel === 'whatsapp') {
                    this.classList.add('btn-success');
                } else if (channel === 'email') {
                    this.classList.add('btn-primary');
                } else if (channel === 'sms') {
                    this.classList.add('btn-info');
                }
                
                // Habilitar botón de enviar
                selectedChannel.value = channel;
                sendNotificationBtn.disabled = false;
                sendNotificationBtn.innerHTML = `<i class="fas fa-paper-plane me-2"></i>Enviar por ${channel.toUpperCase()}`;
            });
        });
        
        // Resetear selección
        function resetChannelSelection() {
            document.querySelectorAll('.notification-option').forEach(btn => {
                btn.classList.remove('btn-success', 'btn-primary', 'btn-info');
                btn.classList.add('btn-outline-success', 'btn-outline-primary', 'btn-outline-info');
            });
            selectedChannel.value = '';
            sendNotificationBtn.disabled = true;
            sendNotificationBtn.innerHTML = `<i class="fas fa-paper-plane me-2"></i>Enviar`;
        }
        
        // Cerrar modal con Escape o click fuera
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('#notificationModal').modal('hide');
            }
        });
    });
</script>
@endsection