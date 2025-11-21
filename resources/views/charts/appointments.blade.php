@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Reportes de Citas</h3>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Pestañas para los diferentes reportes --}}
        <ul class="nav nav-pills mb-4" id="reportsTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="chart-tab" data-toggle="tab" href="#chart" role="tab" aria-controls="chart" aria-selected="true">
                    <i class="ni ni-chart-pie-35"></i> Gráfico Mensual
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">
                    <i class="ni ni-collection"></i> Historial Completo
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="records-tab" data-toggle="tab" href="#records" role="tab" aria-controls="records" aria-selected="false">
                    <i class="ni ni-folder-17"></i> Expedientes Clínicos
                </a>
            </li>
        </ul>

        <div class="tab-content" id="reportsTabContent">
            {{-- 🟦 PESTAÑA 1: Gráfico Mensual (EXISTENTE) --}}
            <div class="tab-pane fade show active" id="chart" role="tabpanel" aria-labelledby="chart-tab">
                <div id="container"></div>
            </div>

            {{-- 🟩 PESTAÑA 2: Historial Completo de Citas (NUEVO) --}}
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">

                     {{-- 🆕 BOTONES DE EXPORTACIÓN --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="mb-0">Historial Completo de Citas</h4>
        </div>
        <div class="col-md-6 text-right">
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-download"></i> Exportar Reporte
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="exportReport('appointments', 'pdf')">
                        <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                    </a>
                    <a class="dropdown-item" href="#" onclick="exportReport('appointments', 'excel')">
                        <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
 {{-- -------------------------------------------------------------------------- --}}
                @if(isset($allAppointments) && $allAppointments->count() > 0)
                    {{-- Estadísticas --}}
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-gradient-info text-white">
                                <div class="card-body py-3 text-center">
                                    <h6 class="card-title">Total</h6>
                                    <h3>{{ $allAppointments->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-success text-white">
                                <div class="card-body py-3 text-center">
                                    <h6 class="card-title">Confirmadas</h6>
                                    <h3>{{ $allAppointments->where('status', 'Confirmada')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-primary text-white">
                                <div class="card-body py-3 text-center">
                                    <h6 class="card-title">Atendidas</h6>
                                    <h3>{{ $allAppointments->where('status', 'Atendida')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-danger text-white">
                                <div class="card-body py-3 text-center">
                                    <h6 class="card-title">Canceladas</h6>
                                    <h3>{{ $allAppointments->where('status', 'Cancelada')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-warning text-white">
                                <div class="card-body py-3 text-center">
                                    <h6 class="card-title">Reprogramadas</h6>
                                    <h3>{{ $allAppointments->where('status', 'Reprogramada')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de Citas --}}
                    <div class="table-responsive">
                        <table class="table table-bordered align-items-center">
                            <thead class="thead-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Médico</th>
                                    <th>Especialidad</th>
                                    <th>Estado</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->scheduled_date }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A') }}</td>
                                    <td>{{ $appointment->patient_name }}</td>
                                    <td>{{ $appointment->doctor_name }}</td>
                                    <td>{{ $appointment->specialty_name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $appointment->status == 'Confirmada' ? 'success' : ($appointment->status == 'Atendida' ? 'primary' : ($appointment->status == 'Cancelada' ? 'danger' : 'warning')) }}">
                                            {{ $appointment->status }}
                                        </span>
                                    </td>
                                    <td>{{ $appointment->type }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No hay citas registradas en el sistema.
                    </div>
                @endif
            </div>

            {{-- 🟪 PESTAÑA 3: Expedientes Clínicos (MODIFICADA CON EVOLUCIONES) --}}
            <div class="tab-pane fade" id="records" role="tabpanel" aria-labelledby="records-tab">
                  {{-- 🆕 BOTONES DE EXPORTACIÓN --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="mb-0">Expedientes Clínicos</h4>
        </div>
        <div class="col-md-6 text-right">
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-download"></i> Exportar Reporte
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="exportReport('medical_records', 'pdf')">
                        <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                    </a>
                    <a class="dropdown-item" href="#" onclick="exportReport('medical_records', 'excel')">
                        <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

 {{-- -------------------------------------------------------------------------- --}}

                @if(isset($medicalHistories) && $medicalHistories->count() > 0)
                    {{-- Estadísticas ACTUALIZADAS --}}
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-gradient-info text-white">
                                <div class="card-body py-3 text-center">
                                    <h6 class="card-title">Total Historiales</h6>
                                    <h3>{{ $medicalHistories->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-success text-white">
                                <div class="card-body py-3 text-center">
                                    <h6 class="card-title">Pacientes</h6>
                                    <h3>{{ $medicalHistories->unique('patient_id')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-primary text-white">
                                <div class="card-body py-3 text-center">
                                    <h6 class="card-title">Doctores</h6>
                                    <h3>{{ $medicalHistories->unique('doctor_id')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-warning text-white">
                                <div class="card-body py-3 text-center">
                                    <h6 class="card-title">Total Evoluciones</h6>
                                    <h3>{{ $evolutions->count() ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-dark text-white">
                                <div class="card-body py-3 text-center">
                                    <h6 class="card-title">Prom. Evoluciones</h6>
                                    <h3>{{ number_format($evolutions->count() / max($medicalHistories->count(), 1), 1) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de Expedientes MODIFICADA --}}
                    <div class="table-responsive">
                        <table class="table table-bordered align-items-center table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Paciente</th>
                                    <th>Médico</th>
                                    <th>Fecha Cita</th>
                                    <th>Diagnóstico</th>
                                    <th>Antecedentes</th>
                                    <th>Evoluciones Médicas</th> {{-- 🆕 NUEVA COLUMNA --}}
                                    <th>Fecha Registro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medicalHistories as $history)
                                @php
                                    // 🆕 Obtener evoluciones para este historial médico específico
                                    $historyEvolutions = $evolutions->where('medical_history_id', $history->id);
                                @endphp
                                <tr>
                                    <td class="font-weight-bold">{{ $history->patient_name ?? 'N/A' }}</td>
                                    <td>{{ $history->doctor_name ?? 'N/A' }}</td>
                                    <td>{{ $history->scheduled_date ?? 'N/A' }}</td>
                                    <td>
                                        <span class="diagnosis-text" data-toggle="tooltip" title="{{ $history->diagnosis ?? 'Sin diagnóstico' }}">
                                            {{ Str::limit($history->diagnosis ?? 'Sin diagnóstico', 50) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="history-text" data-toggle="tooltip" title="{{ $history->history ?? 'Sin antecedentes' }}">
                                            {{ Str::limit($history->history ?? 'Sin antecedentes', 50) }}
                                        </span>
                                    </td>
                                    <td style="min-width: 300px;">
                                        {{-- 🆕 SECCIÓN DE EVOLUCIONES MÉDICAS --}}
                                        @if($historyEvolutions->count() > 0)
                                            <div class="evolution-section">
                                                @foreach($historyEvolutions->take(2) as $evolution)
                                                    <div class="evolution-item mb-2 p-2 border rounded bg-light">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar-alt"></i>
                                                                {{ \Carbon\Carbon::parse($evolution->created_at)->format('d/m/Y') }}
                                                            </small>
                                                            <span class="badge badge-{{ $evolution->evolution_status == 'Completa' ? 'success' : ($evolution->evolution_status == 'Parcial' ? 'warning' : 'secondary') }} badge-sm">
                                                                {{ $evolution->evolution_status }}
                                                            </span>
                                                        </div>
                                                        <small class="d-block mt-1 text-dark">
                                                            <strong>Dx:</strong> 
                                                            {{ $evolution->diagnosis_short ?? Str::limit($evolution->diagnosis, 60) }}
                                                        </small>
                                                        @if($evolution->treatment)
                                                            <small class="d-block text-info">
                                                                <strong>Tx:</strong> 
                                                                {{ Str::limit($evolution->treatment, 40) }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                
                                                {{-- Mostrar contador si hay más de 2 evoluciones --}}
                                                @if($historyEvolutions->count() > 2)
                                                    <div class="text-center mt-1">
                                                        <small class="text-primary font-weight-bold">
                                                            <i class="fas fa-plus-circle"></i>
                                                            +{{ $historyEvolutions->count() - 2 }} más evoluciones
                                                        </small>
                                                    </div>
                                                @else
                                                    <div class="text-center mt-1">
                                                        <small class="text-success">
                                                            <i class="fas fa-file-medical-alt"></i>
                                                            {{ $historyEvolutions->count() }} evolución(es)
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <span class="text-muted">
                                                    <i class="fas fa-times-circle"></i>
                                                    Sin evoluciones
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No hay expedientes clínicos registrados en el sistema.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        // Gráfico existente
        Highcharts.chart('container', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Citas registradas mensualmente'
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            yAxis: {
                title: {
                    text: 'Cantidad de citas'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                name: 'Citas registradas',
                data: @json($counts)
            }]
        });

        // Script para las pestañas
        $(document).ready(function() {
            $('#reportsTab a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // Activar tooltips para diagnósticos y antecedentes
            $('[data-toggle="tooltip"]').tooltip();
        });

            // 🆕 FUNCIÓN PARA EXPORTAR REPORTES
        //function exportReport(type, format) {
            // Mostrar loading
          //  Swal.fire({
           //     title: 'Generando reporte...',
           //     text: 'Por favor espere un momento',
           //     allowOutsideClick: false,
           //     didOpen: () => {
           //         Swal.showLoading()
          //      }
          //  });

          function exportReport(type, format) {
         // Mostrar mensaje simple de carga
     console.log('Generando reporte: ' + type + ' en formato ' + format);


            // Crear formulario dinámico
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("reports.download") }}';
            
            // Agregar CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Agregar tipo de reporte
            const reportType = document.createElement('input');
            reportType.type = 'hidden';
            reportType.name = 'type';
            reportType.value = type;
            form.appendChild(reportType);
            
            // Agregar formato
            const reportFormat = document.createElement('input');
            reportFormat.type = 'hidden';
            reportFormat.name = 'format';
            reportFormat.value = format;
            form.appendChild(reportFormat);
            
            // Enviar formulario
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

    </script>

    <style>
        .evolution-item {
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .evolution-item:hover {
            background-color: #f8f9fa !important;
            transform: translateX(2px);
        }

        .diagnosis-text, .history-text {
            cursor: help;
        }

        .badge-sm {
            font-size: 0.7rem;
            padding: 0.25em 0.4em;
        }

        .evolution-section {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
@endsection