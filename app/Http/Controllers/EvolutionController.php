<?php

namespace App\Http\Controllers;

use App\Models\Evolution;
use App\Models\MedicalHistory;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreEvolutionRequest;
use App\Http\Requests\UpdateEvolutionRequest;
use App\StoreProcedures\EvolutionSP;

class EvolutionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($medical_history_id)
    {
        $medicalHistory = MedicalHistory::with('evolutions.doctor')->findOrFail($medical_history_id);

        if (Auth::user()->role === 'paciente' && Auth::id() !== $medicalHistory->appointment->patient_id) {
            abort(403, 'No tienes permiso para ver este expediente.');
        }

        if (Auth::user()->role === 'doctor' && Auth::id() !== $medicalHistory->appointment->doctor_id) {
            abort(403, 'No puedes ver evoluciones de otro médico.');
        }

        // ✅ CORREGIDO: Usar SP para obtener evoluciones (mejor performance)
        $evolutions = EvolutionSP::listByMedicalHistory($medical_history_id);

        return view('evolutions.index', compact('medicalHistory', 'evolutions'));
    }

    public function create(MedicalHistory $medical_history)
    {
        if (Auth::user()->role !== 'doctor' || Auth::id() !== $medical_history->appointment->doctor_id) {
            abort(403, 'Solo el doctor responsable puede registrar evoluciones.');
        }

        // Renombramos para que la vista use $medicalHistory
        $medicalHistory = $medical_history;

        return view('evolutions.create', compact('medicalHistory'));
    }

    public function store(StoreEvolutionRequest $request)
    {
        if (Auth::user()->role !== 'doctor') abort(403);

        $history = MedicalHistory::findOrFail($request->medical_history_id);
        if ($history->appointment->doctor_id !== Auth::id()) {
            abort(403, 'No puedes registrar evolución para un paciente que no es tuyo.');
        }

        // ✅ CORREGIDO: Usar SP para crear evolución
        $evolutionData = [
            'medical_history_id' => $request->medical_history_id,
            'doctor_id' => Auth::id(),
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'observations' => $request->observations,
        ];

        EvolutionSP::create($evolutionData);

        return redirect()
            ->route('medical_histories.show', $request->medical_history_id)
            ->with('success', '✅ Evolución médica registrada correctamente.');
    }

    public function edit(Evolution $evolution)
    {
        if (Auth::user()->role !== 'doctor' || Auth::id() !== $evolution->doctor_id) {
            abort(403, 'No puedes editar esta evolución.');
        }

        return view('evolutions.edit', compact('evolution'));
    }

    public function update(UpdateEvolutionRequest $request, Evolution $evolution)
    {
        if (Auth::user()->role !== 'doctor' || Auth::id() !== $evolution->doctor_id) {
            abort(403);
        }

        // ✅ CORREGIDO: Usar SP para actualizar evolución
        $evolutionData = [
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'observations' => $request->observations,
        ];

        EvolutionSP::update($evolution->id, $evolutionData);

        return redirect()
            ->route('medical_histories.show', $evolution->medical_history_id)
            ->with('success', '✅ Evolución médica actualizada correctamente.');
    }

    public function show(Evolution $evolution)
    {
        $history = $evolution->medicalHistory;

        if (Auth::user()->role === 'paciente' && Auth::id() !== $history->appointment->patient_id) {
            abort(403, 'No autorizado.');
        }

        if (Auth::user()->role === 'doctor' && Auth::id() !== $history->appointment->doctor_id) {
            abort(403, 'No autorizado.');
        }

        // ✅ CORREGIDO: Usar SP para obtener datos enriquecidos de la evolución
        $evolutionDetail = EvolutionSP::getById($evolution->id);

        return view('evolutions.show', compact('evolutionDetail'));
    }
}