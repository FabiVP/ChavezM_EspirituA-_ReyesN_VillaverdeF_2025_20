@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">
                    <i class="fas fa-calendar-alt"></i> Reprogramar Cita
                </h3>
            </div>
            <div class="col text-right">
                <a href="{{ url('/miscitas') }}" class="btn btn-sm btn-default">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Información de la cita original --}}
        <div class="alert alert-info">
            <h5>📋 Cita Original a Reprogramar</h5>
            <strong>Paciente:</strong> {{ $appointment->patient->name }}<br>
            <strong>Doctor:</strong> {{ $appointment->doctor->name }}<br>
            <strong>Especialidad:</strong> {{ $appointment->specialty->name }}<br>
            <strong>Fecha original:</strong> {{ $appointment->scheduled_date }}<br>
            <strong>Hora original:</strong> {{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A') }}<br>
            <strong>Tipo:</strong> {{ $appointment->type }}<br>
            <strong>Descripción:</strong> {{ $appointment->description }}
        </div>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Por favor!!</strong> {{ $error }}
                </div>
            @endforeach
        @endif

        <form action="{{ route('appointments.reprogram.store', $appointment) }}" method="POST">
            @csrf

            {{-- Motivo de reprogramación --}}
            <div class="form-group">
                <label for="reprogramming_reason" class="form-control-label">Motivo de la reprogramación *</label>
                <textarea name="reprogramming_reason" id="reprogramming_reason" rows="3" 
                          class="form-control @error('reprogramming_reason') is-invalid @enderror"
                          placeholder="Explique por qué necesita reprogramar esta cita..." required>{{ old('reprogramming_reason') }}</textarea>
                @error('reprogramming_reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                {{-- Especialidad --}}
                <div class="form-group col-md-6">
                    <label for="specialty_id">Especialidad *</label>
                    <select name="specialty_id" id="specialty_id" class="form-control @error('specialty_id') is-invalid @enderror" required>
                        <option value="">Seleccionar especialidad</option>
                        @foreach($specialties as $specialty)
                            <option value="{{ $specialty->id }}" 
                                {{ old('specialty_id', $appointment->specialty_id) == $specialty->id ? 'selected' : '' }}>
                                {{ $specialty->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('specialty_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Médicos --}}
                <div class="form-group col-md-6">
                    <label for="doctor_id">Médico *</label>
                    <select name="doctor_id" id="doctor_id" class="form-control @error('doctor_id') is-invalid @enderror" required>
                        <option value="">Seleccione un médico</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" 
                                {{ old('doctor_id', $appointment->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Fecha --}}
            <div class="form-group">
                <label for="scheduled_date">Fecha *</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                    </div>
                    <input class="form-control datepicker @error('scheduled_date') is-invalid @enderror"
                    id="scheduled_date" name="scheduled_date"
                    placeholder="Seleccionar fecha" 
                    type="date" value="{{ old('scheduled_date') }}" 
                    data-date-format="yyyy-mm-dd"
                    data-date-start-date="{{ date('Y-m-d') }}" 
                    data-date-end-date="+30d" required>
                </div>
                @error('scheduled_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Horas disponibles --}}
            <div class="form-group">
                <label for="hours">Hora de atención *</label>
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <h4 class="m-3" id="titleMorning">En la mañana</h4>
                            <div id="hoursMorning">
                                <div class="custom-control custom-radio mb-3">
                                    <input type="radio" name="scheduled_time" value="" class="custom-control-input" disabled>
                                    <label class="custom-control-label text-muted">Seleccione médico y fecha</label>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="m-3" id="titleAfternoon">En la tarde</h4>
                            <div id="hoursAfternoon">
                                <div class="custom-control custom-radio mb-3">
                                    <input type="radio" name="scheduled_time" value="" class="custom-control-input" disabled>
                                    <label class="custom-control-label text-muted">Seleccione médico y fecha</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @error('scheduled_time')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Botones --}}
            <div class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar-check"></i> Confirmar Reprogramación
                </button>
                <a href="{{ url('/miscitas') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}} "></script>
<script>
$(document).ready(function() {
    // Cargar médicos cuando cambie especialidad
    $('#specialty_id').change(function() {
        var specialtyId = $(this).val();
        if (specialtyId) {
            $.get('/especialidades/' + specialtyId + '/medicos', function(data) {
                $('#doctor_id').empty().append('<option value="">Seleccione un médico</option>');
                $.each(data, function(key, doctor) {
                    $('#doctor_id').append('<option value="' + doctor.id + '">' + doctor.name + '</option>');
                });
            });
        } else {
            $('#doctor_id').empty().append('<option value="">Seleccione un médico</option>');
        }
        // Limpiar horas al cambiar especialidad
        clearHours();
    });

    // Cargar horas cuando cambie médico o fecha
    $('#doctor_id, #scheduled_date').change(function() {
        loadAvailableHours();
    });

    // Cargar horas automáticamente si ya hay datos
    if ($('#doctor_id').val() && $('#scheduled_date').val()) {
        setTimeout(function() {
            loadAvailableHours();
        }, 1000);
    }

    function clearHours() {
        $('#hoursMorning').html('<div class="custom-control custom-radio mb-3"><input type="radio" name="scheduled_time" value="" class="custom-control-input" disabled><label class="custom-control-label text-muted">Seleccione médico y fecha</label></div>');
        $('#hoursAfternoon').html('<div class="custom-control custom-radio mb-3"><input type="radio" name="scheduled_time" value="" class="custom-control-input" disabled><label class="custom-control-label text-muted">Seleccione médico y fecha</label></div>');
    }

    function loadAvailableHours() {
        var doctorId = $('#doctor_id').val();
        var scheduledDate = $('#scheduled_date').val();
        
        if (!doctorId || !scheduledDate) {
            clearHours();
            return;
        }

        // Mostrar loading
        $('#hoursMorning').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando horas...</p></div>');
        $('#hoursAfternoon').html('');

        $.get('/horario/horas', {
            doctor_id: doctorId,
            date: scheduledDate
        }, function(data) {
            console.log('Horas recibidas:', data);
            
            // Limpiar contenedores
            $('#hoursMorning').empty();
            $('#hoursAfternoon').empty();

            // Procesar horas de la mañana
            if (data.morning && data.morning.length > 0) {
                $('#titleMorning').text('En la mañana');
                data.morning.forEach(function(interval, index) {
                    var radioId = 'intervalMorning' + index;
                    $('#hoursMorning').append(
                        '<div class="custom-control custom-radio mb-3">' +
                        '<input type="radio" id="' + radioId + '" name="scheduled_time" value="' + interval.start + '" class="custom-control-input" required>' +
                        '<label class="custom-control-label" for="' + radioId + '">' + interval.start + ' - ' + interval.end + '</label>' +
                        '</div>'
                    );
                });
            } else {
                $('#hoursMorning').html('<div class="text-muted"><small>No hay horas disponibles en la mañana</small></div>');
            }

            // Procesar horas de la tarde
            if (data.afternoon && data.afternoon.length > 0) {
                $('#titleAfternoon').text('En la tarde');
                data.afternoon.forEach(function(interval, index) {
                    var radioId = 'intervalAfternoon' + index;
                    $('#hoursAfternoon').append(
                        '<div class="custom-control custom-radio mb-3">' +
                        '<input type="radio" id="' + radioId + '" name="scheduled_time" value="' + interval.start + '" class="custom-control-input" required>' +
                        '<label class="custom-control-label" for="' + radioId + '">' + interval.start + ' - ' + interval.end + '</label>' +
                        '</div>'
                    );
                });
            } else {
                $('#hoursAfternoon').html('<div class="text-muted"><small>No hay horas disponibles en la tarde</small></div>');
            }

            // Si no hay horas en ningún turno
            if ((!data.morning || data.morning.length === 0) && (!data.afternoon || data.afternoon.length === 0)) {
                $('#hoursMorning').html('<div class="alert alert-warning text-center"><small>No hay horas disponibles para esta fecha</small></div>');
                $('#titleAfternoon').hide();
                $('#hoursAfternoon').hide();
            } else {
                $('#titleAfternoon').show();
                $('#hoursAfternoon').show();
            }

        }).fail(function(error) {
            console.log('Error cargando horas:', error);
            $('#hoursMorning').html('<div class="alert alert-danger text-center"><small>Error al cargar las horas</small></div>');
            $('#hoursAfternoon').html('');
        });
    }
});
</script>
@endsection