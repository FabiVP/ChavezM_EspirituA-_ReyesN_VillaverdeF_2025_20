<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MedicalRecordsExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['medicalHistories']->map(function($history) {
            $evolutionsCount = $this->data['evolutions']
                ->where('medical_history_id', $history->id)
                ->count();

            return [
                'Paciente' => $history->patient_name ?? 'N/A',
                'Médico' => $history->doctor_name ?? 'N/A',
                'Fecha Cita' => $history->scheduled_date ?? 'N/A',
                'Diagnóstico' => $history->diagnosis ?? 'Sin diagnóstico',
                'Antecedentes' => $history->history ?? 'Sin antecedentes',
                'Total Evoluciones' => $evolutionsCount,
                'Fecha Registro' => \Carbon\Carbon::parse($history->created_at)->format('d/m/Y')
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Paciente',
            'Médico',
            'Fecha Cita',
            'Diagnóstico',
            'Antecedentes',
            'Total Evoluciones',
            'Fecha Registro'
        ];
    }
}