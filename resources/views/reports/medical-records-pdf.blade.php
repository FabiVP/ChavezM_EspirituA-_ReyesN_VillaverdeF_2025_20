<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .title { font-size: 16px; font-weight: bold; color: #2c3e50; }
        .subtitle { font-size: 12px; color: #7f8c8d; }
        .stats { margin: 10px 0; padding: 8px; background: #f8f9fa; border-radius: 4px; font-size: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 10px; }
        .table th { background: #34495e; color: white; padding: 6px; text-align: left; }
        .table td { padding: 5px; border: 1px solid #ddd; vertical-align: top; }
        .evolution-item { margin: 3px 0; padding: 4px; border: 1px solid #eee; border-radius: 3px; font-size: 9px; }
        .evolution-date { color: #7f8c8d; font-weight: bold; }
        .evolution-status { padding: 1px 4px; border-radius: 8px; font-size: 8px; color: white; }
        .status-completa { background: #27ae60; }
        .status-parcial { background: #f39c12; }
        .status-basica { background: #95a5a6; }
        .footer { margin-top: 15px; text-align: center; font-size: 9px; color: #95a5a6; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $title }}</div>
        <div class="subtitle">Generado el: {{ $exportDate }}</div>
    </div>

    <div class="stats">
        <strong>Estadísticas:</strong> 
        Historiales: {{ $totalHistories }} | 
        Pacientes: {{ $totalPatients }} | 
        Doctores: {{ $totalDoctors }} | 
        Evoluciones: {{ $totalEvolutions }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Fecha Cita</th>
                <th>Diagnóstico</th>
                <th>Antecedentes</th>
                <th>Evoluciones</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicalHistories as $history)
            @php
                $historyEvolutions = $evolutions->where('medical_history_id', $history->id);
            @endphp
            <tr>
                <td><strong>{{ $history->patient_name ?? 'N/A' }}</strong></td>
                <td>{{ $history->doctor_name ?? 'N/A' }}</td>
                <td>{{ $history->scheduled_date ?? 'N/A' }}</td>
                <td>{{ Str::limit($history->diagnosis ?? 'Sin diagnóstico', 40) }}</td>
                <td>{{ Str::limit($history->history ?? 'Sin antecedentes', 40) }}</td>
                <td>
                    @if($historyEvolutions->count() > 0)
                        @foreach($historyEvolutions as $evolution)
                            <div class="evolution-item">
                                <div class="evolution-date">
                                    {{ \Carbon\Carbon::parse($evolution->created_at)->format('d/m/Y') }}
                                </div>
                                <span class="evolution-status status-{{ strtolower($evolution->evolution_status) }}">
                                    {{ $evolution->evolution_status }}
                                </span>
                                <div><strong>Dx:</strong> {{ Str::limit($evolution->diagnosis, 50) }}</div>
                                @if($evolution->treatment)
                                    <div><strong>Tx:</strong> {{ Str::limit($evolution->treatment, 40) }}</div>
                                @endif
                            </div>
                        @endforeach
                        <div style="text-align: center; margin-top: 3px; font-style: italic;">
                            Total: {{ $historyEvolutions->count() }} evolución(es)
                        </div>
                    @else
                        <div style="text-align: center; color: #95a5a6; font-style: italic;">
                            Sin evoluciones
                        </div>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema de Gestión Médica - Reporte generado automáticamente
    </div>
</body>
</html>