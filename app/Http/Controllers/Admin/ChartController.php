<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// ✅ Importar los Store Procedures
use App\StoreProcedures\AppointmentSP;
use App\StoreProcedures\MedicalHistorySP;
use App\StoreProcedures\EvolutionSP;


// Agregar estos métodos al ChartController
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AppointmentsExport;
use App\Exports\MedicalRecordsExport;


class ChartController extends Controller
{
    public function appointments(){
        
        $monthCounts = Appointment::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(1) as count'))
                ->groupBy('month')
                ->get()
                ->toArray();
        $counts = array_fill(0, 12, 0);
        foreach($monthCounts as $monthCount){
            $index = $monthCount['month']-1;
            $counts[$index] = $monthCount['count'];
        }

        // 🆕 NUEVO: Datos para las pestañas adicionales
        $appointmentSP = new AppointmentSP();
        $allAppointments = $appointmentSP->listarCitas();
        
        $medicalHistorySP = new MedicalHistorySP();
        $medicalHistories = $medicalHistorySP->listAll();

          // 🆕 NUEVO: Obtener evoluciones médicas (método estático)
         $evolutions = EvolutionSP::listAllWithPatient();
            
           

        return view('charts.appointments', compact(
            'counts', 
            'allAppointments', 
            'medicalHistories',
            'evolutions' // 🆕 Nuevo dato
        ));
    }

    public function doctors(){
        $now = Carbon::now();
        $end = $now->format('Y-m-d');
        $start = $now->subYear()->format('Y-m-d');

        return view('charts.doctors', compact('end', 'start'));
    }

    public function doctorsJson(Request $request){

        // AÑADIMOS VALIDACIÓN SIMPLE AQUÍ
        $rules = [
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d|after_or_equal:start',
        ];
        $messages = [
            'start.required' => 'La fecha de inicio es obligatoria.',
            'end.required' => 'La fecha de fin es obligatoria.',
            'end.after_or_equal' => 'La fecha de fin no puede ser anterior a la de inicio.',
        ];
        $this->validate($request, $rules, $messages);

        $start = $request->input('start');
        $end = $request->input('end');

        $doctors = User::doctors()
            ->select('name')
            ->withCount(['attendedAppointments' => function($query) use ($start, $end){
                $query->whereBetween('scheduled_date', [$start, $end]);
            },
            'cancellAppointments'=> function($query) use ($start, $end){
                $query->whereBetween('scheduled_date', [$start, $end]);
            }
            ])
            ->orderBy('attended_appointments_count', 'desc')
            ->take(5)
            ->get();
        
        $data = [];
        $data['categories'] = $doctors->pluck('name'); 

        $series = [];
        $series1['name'] = 'Citas atendidas';
        $series1['data'] = $doctors->pluck('attended_appointments_count');
        $series2['name'] = 'Citas canceladas';
        $series2['data'] = $doctors->pluck('cancell_appointments_count');

        $series[] = $series1;
        $series[] = $series2;
        $data['series'] = $series;

        return $data;
    }



        // En ChartController.php - agregar estos métodos
public function downloadReport(Request $request)
{
    $type = $request->input('type');
    $format = $request->input('format', 'pdf');
    
    switch($type) {
        case 'appointments':
            return $this->downloadAppointmentsReport($format);
        case 'medical_records':
            return $this->downloadMedicalRecordsReport($format);
        default:
            return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }
}

private function downloadAppointmentsReport($format)
{
    $appointmentSP = new AppointmentSP();
    $allAppointments = $appointmentSP->listarCitas();
    
    $data = [
        'title' => 'Reporte de Historial Completo de Citas',
        'appointments' => $allAppointments,
        'total' => $allAppointments->count(),
        'confirmed' => $allAppointments->where('status', 'Confirmada')->count(),
        'attended' => $allAppointments->where('status', 'Atendida')->count(),
        'cancelled' => $allAppointments->where('status', 'Cancelada')->count(),
        'reprogrammed' => $allAppointments->where('status', 'Reprogramada')->count(),
        'exportDate' => now()->format('d/m/Y H:i'),
    ];
    
    if ($format === 'excel') {
        return $this->exportAppointmentsToExcel($data);
    }
    
    return $this->exportAppointmentsToPDF($data);
}

private function downloadMedicalRecordsReport($format)
{
    $medicalHistorySP = new MedicalHistorySP();
    $medicalHistories = $medicalHistorySP->listAll();
    
    $evolutionSP = new EvolutionSP();
    $evolutions = $evolutionSP->listAllWithPatient();
    
    $data = [
        'title' => 'Reporte de Expedientes Clínicos',
        'medicalHistories' => $medicalHistories,
        'evolutions' => $evolutions,
        'totalHistories' => $medicalHistories->count(),
        'totalPatients' => $medicalHistories->unique('patient_id')->count(),
        'totalDoctors' => $medicalHistories->unique('doctor_id')->count(),
        'totalEvolutions' => $evolutions->count(),
        'exportDate' => now()->format('d/m/Y H:i'),
    ];
    
    if ($format === 'excel') {
        return $this->exportMedicalRecordsToExcel($data);
    }
    
    return $this->exportMedicalRecordsToPDF($data);
}


private function exportAppointmentsToPDF($data)
{
    $pdf = PDF::loadView('reports.appointments-pdf', $data);
    return $pdf->download('reporte-citas-' . now()->format('Y-m-d') . '.pdf');
}

private function exportAppointmentsToExcel($data)
{
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\AppointmentsExport($data), 
        'reporte-citas-' . now()->format('Y-m-d') . '.xlsx'
    );
}


private function exportMedicalRecordsToPDF($data)
{
    $pdf = PDF::loadView('reports.medical-records-pdf', $data);
    return $pdf->download('reporte-expedientes-' . now()->format('Y-m-d') . '.pdf');
}

private function exportMedicalRecordsToExcel($data)
{
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\MedicalRecordsExport($data), 
        'reporte-expedientes-' . now()->format('Y-m-d') . '.xlsx'
    );
}



}