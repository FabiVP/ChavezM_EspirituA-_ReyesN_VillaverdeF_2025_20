<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ClinicalRecordController extends Controller
{
    /**
     * 📌 Muestra la lista de pacientes disponibles según el rol del usuario.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // El admin puede ver todos los pacientes
            $patients = User::patients()->get();
        } 
        elseif ($user->role === 'doctor') {
            // El doctor solo ve sus pacientes atendidos
            $patients = User::whereHas('asPatientAppointments', function ($query) use ($user) {
                $query->where('doctor_id', $user->id);
            })
            ->patients()
            ->get();
        } 
        else {
            // Si es paciente, lo redirigimos a su propio expediente
            return redirect()->route('clinical.record.show', $user->id);
        }

        return view('clinical_record.index', compact('patients'));
    }

    /**
     * 📌 Muestra el expediente clínico completo de un paciente.
     */
    public function show(User $patient)
    {
        $user = Auth::user();

        // ✅ Validaciones de acceso
        if ($user->role === 'paciente' && $user->id !== $patient->id) {
            abort(403, 'No puedes acceder al expediente de otro paciente.');
        }

        if ($user->role === 'doctor') {
            $isDoctorPatient = $patient->asPatientAppointments()
                ->where('doctor_id', $user->id)
                ->exists();

            if (! $isDoctorPatient) {
                abort(403, 'Este paciente no ha sido atendido por usted.');
            }
        }

        // ✅ Obtener relaciones
        $appointments = $patient->asPatientAppointments()->with('doctor')->get();

        // Antecedentes médicos
        $histories = $patient->medicalHistories()
            ->orderBy('created_at', 'desc')
            ->get();

        // Evoluciones (relacionadas a las historias)
        $evolutions = $patient->evolutions()
            ->with('doctor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clinical_record.show', compact(
            'patient',
            'appointments',
            'histories',
            'evolutions'
        ));
    }
}
