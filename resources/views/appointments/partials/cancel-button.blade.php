
@php
    $userRole = auth()->user()->role;
    $isAdmin  = $userRole === 'admin';
    $isDoctor = $userRole === 'doctor';
    $isPatient = in_array($userRole, ['paciente','patient']);

    $appointmentDateTime = \Carbon\Carbon::parse($appointment->scheduled_date . ' ' . $appointment->scheduled_time);
    $hoursToAppointment = \Carbon\Carbon::now()->diffInHours($appointmentDateTime, false);
    $patientCanCancel = $hoursToAppointment >= 24;
@endphp

@if($isAdmin || $isDoctor || ($isPatient && $patientCanCancel))
    <!-- Botón cancelar -->
    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#cancelModal{{ $appointment->id }}">
        Cancelar cita
    </button>

    <!-- Modal -->
    <div class="modal fade" id="cancelModal{{ $appointment->id }}" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel{{ $appointment->id }}" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content border-danger">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="cancelModalLabel{{ $appointment->id }}">Cancelar cita</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ url('/miscitas/'.$appointment->id.'/cancel') }}" method="POST">
            @csrf
            <div class="modal-body">
                <p class="text-center text-danger font-weight-bold mb-3">
                    ⚠️ ¿Seguro que deseas cancelar esta cita?
                </p>

                <p><strong>Descripción:</strong> {{ $appointment->description ?? '-' }}</p>
                <p><strong>Fecha:</strong> {{ $appointment->scheduled_date }} <strong>- Hora:</strong> {{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A') }}</p>

                @if($isDoctor)
                    <div class="form-group mt-3">
                        <label for="justification_{{ $appointment->id }}">Motivo (obligatorio)</label>
                        <textarea name="justification" id="justification_{{ $appointment->id }}" class="form-control" rows="4" required></textarea>
                    </div>
                @else
                    <div class="form-group mt-3">
                        <label for="justification_{{ $appointment->id }}">Motivo (opcional)</label>
                        <textarea name="justification" id="justification_{{ $appointment->id }}" class="form-control" rows="3" placeholder="(opcional)"></textarea>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Volver</button>
                <button type="submit" class="btn btn-danger">Sí, cancelar cita</button>
            </div>
          </form>
        </div>
      </div>
    </div>
@elseif($isPatient && !$patientCanCancel)
    <small class="text-muted">Solo se puede cancelar con 24h de anticipación</small>
@endif
