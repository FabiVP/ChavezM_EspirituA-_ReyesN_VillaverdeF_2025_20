<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; color: #2c3e50; }
        .subtitle { font-size: 14px; color: #7f8c8d; }
        .stats { margin: 15px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { background: #34495e; color: white; padding: 8px; text-align: left; }
        .table td { padding: 6px; border: 1px solid #ddd; }
        .table tr:nth-child(even) { background: #f2f2f2; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 10px; color: white; }
        .badge-success { background: #27ae60; }
        .badge-primary { background: #3498db; }
        .badge-danger { background: #e74c3c; }
        .badge-warning { background: #f39c12; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #95a5a6; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $title }}</div>
        <div class="subtitle">Generado el: {{ $exportDate }}</div>
    </div>

    <div class="stats">
        <strong>Estadísticas:</strong> 
        Total: {{ $total }} | 
        Confirmadas: {{ $confirmed }} | 
        Atendidas: {{ $attended }} | 
        Canceladas: {{ $cancelled }} | 
        Reprogramadas: {{ $reprogrammed }}
    </div>

    <table class="table">
        <thead>
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
            @foreach($appointments as $appointment)
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

    <div class="footer">
        Sistema de Gestión Médica - Reporte generado automáticamente
    </div>
</body>
</html>