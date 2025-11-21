<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\MedicalHistory;
use App\StoreProcedures\MedicalHistorySP;

class MedicalHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listado de antecedentes médicos (según rol)
     * 👉 Se mantiene con Eloquent (tal como recomendaste)
     */
    public function index()
    {
        $role = auth()->user()->role;

        if ($role == 'doctor') {
            $appointments = Appointment::with(['patient', 'medicalHistory'])
                ->where('doctor_id', auth()->id())
                ->where('status', 'Atendida')
                ->orderBy('scheduled_date', 'desc')
                ->get();

        } elseif ($role == 'paciente') {
            $appointments = Appointment::with(['doctor', 'medicalHistory'])
                ->where('patient_id', auth()->id())
                ->where('status', 'Atendida')
                ->orderBy('scheduled_date', 'desc')
                ->get();

        } else { // admin
            $appointments = Appointment::with(['patient', 'doctor', 'medicalHistory'])
                ->where('status', 'Atendida')
                ->orderBy('scheduled_date', 'desc')
                ->get();
        }

        return view('medical_histories.index', compact('appointments', 'role'));
    }

    /**
     * Formulario de creación (solo doctor)
     */
    public function create(Appointment $appointment = null)
    {
        if (auth()->user()->role !== 'doctor') {
            abort(403, 'Acción no autorizada');
        }

        // Todas las citas atendidas del doctor
        $appointments = Appointment::where('doctor_id', auth()->id())
                        ->where('status', 'Atendida')
                        ->with('patient')
                        ->get();

        return view('medical_histories.create',
            compact('appointments', 'appointment')
        );
    }

    /**
     * Guardar nuevo historial – CAPA 4
     */
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required',
            'diagnosis' => 'required',
            'history' => 'nullable',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);

        // 👉 Se reemplaza Eloquent por Stored Procedure
        MedicalHistorySP::createHistory(
            $appointment->id,
            auth()->id(),
            $appointment->patient_id,
            $request->diagnosis,
            $request->history
        );

        return redirect()
            ->route('medical_histories.index')
            ->with('success', 'Antecedente médico registrado correctamente.');
    }

   /**
 * Mostrar un historial específico – CAPA 4
 */
public function show($id)
{
    // Obtener datos desde el Stored Procedure
    $medicalHistory = MedicalHistorySP::show($id);

    // Verificar si el resultado es null o vacío
    if (!$medicalHistory) {
        abort(404);
    }

    // Construimos manualmente el objeto appointment para que las vistas funcionen igual que antes
    $medicalHistory->appointment = (object) [
        'scheduled_date' => $medicalHistory->scheduled_date,
        'scheduled_time' => $medicalHistory->scheduled_time,
        'status' => $medicalHistory->appointment_status,
        'patient' => (object)[
            'name' => $medicalHistory->patient_name
        ],
        'doctor' => (object)[
            'name' => $medicalHistory->doctor_name
        ]
    ];

    // Seguridad equivalente a la versión original usando IDs devueltos por el SP
    $role = auth()->user()->role;

    if ($role === 'paciente' && $medicalHistory->patient_id != auth()->id()) {
        abort(403, 'Acceso denegado');
    }

    if ($role === 'doctor' && $medicalHistory->doctor_id != auth()->id()) {
        abort(403, 'Acceso denegado');
    }

    // Vista
    return view('medical_histories.show', compact('medicalHistory', 'role'));
}


    /**
     * Editar un historial – CAPA 4
     */
    public function edit($id)
    {
        $medicalHistory = MedicalHistorySP::show($id);
        
        if (!$medicalHistory) {
            abort(404);
        }
    
        if (auth()->user()->role !== 'doctor' ||
            $medicalHistory->doctor_id != auth()->id()) {
            abort(403, 'Acción no autorizada');
        }
    
        // 🔥 AGREGAR: Construir el objeto appointment igual que en show()
        $medicalHistory->appointment = (object) [
            'scheduled_date' => $medicalHistory->scheduled_date,
            'scheduled_time' => $medicalHistory->scheduled_time,
            'status' => $medicalHistory->appointment_status,
            'patient' => (object)[
                'name' => $medicalHistory->patient_name
            ],
            'doctor' => (object)[
                'name' => $medicalHistory->doctor_name
            ]
        ];
    
        return view('medical_histories.edit', compact('medicalHistory'));
    }

    /**
     * Actualizar un historial – CAPA 4
     */
    public function update(Request $request, $id)
    {
        $medicalHistory = MedicalHistorySP::show($id);
        if (!$medicalHistory) {
            abort(404);
        }

        if (auth()->user()->role !== 'doctor' ||
            $medicalHistory->doctor_id != auth()->id()) {
            abort(403, 'Acción no autorizada');
        }

        $request->validate([
            'diagnosis' => 'required|string|max:1000',
            'history' => 'nullable|string|max:2000',
        ]);

        MedicalHistorySP::updateHistory(
            $id,
            $request->diagnosis,
            $request->history
        );

        return redirect()
            ->route('medical_histories.index')
            ->with('notification', 'Antecedente médico actualizado correctamente.');
    }

    /**
     * Eliminar un historial – CAPA 4
     */
    public function destroy($id)
{
    $medicalHistory = MedicalHistorySP::show($id);
    
    if (!$medicalHistory) {
        abort(404);
    }

    $role = auth()->user()->role;

    if ($role === 'doctor' && $medicalHistory->doctor_id != auth()->id()) {
        abort(403, 'Acción no autorizada');
    }

    MedicalHistorySP::deleteHistory($id);

    return redirect()
        ->route('medical_histories.index')
        ->with('notification', 'Antecedente médico eliminado correctamente.');
}
}
