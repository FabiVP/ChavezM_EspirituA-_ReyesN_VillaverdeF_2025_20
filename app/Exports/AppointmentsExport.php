<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AppointmentsExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['appointments']->map(function($appointment) {
            return [
                'Fecha Cita' => $appointment->scheduled_date,
                'Hora' => \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A'),
                'Paciente' => $appointment->patient_name,
                'Médico' => $appointment->doctor_name,
                'Especialidad' => $appointment->specialty_name,
                'Estado' => $appointment->status,
                'Tipo de Consulta' => $appointment->type
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha Cita',
            'Hora',
            'Paciente',
            'Médico',
            'Especialidad',
            'Estado',
            'Tipo de Consulta'
        ];
    }
}