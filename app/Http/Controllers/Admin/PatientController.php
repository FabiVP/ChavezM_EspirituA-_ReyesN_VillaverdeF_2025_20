<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\StoreProcedures\PatientSP;

class PatientController extends Controller
{
    public function index()
    { 
        $patients = PatientSP::listPatients();
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(StorePatientRequest $request)
    {
        $data = $request->only('name','email','cedula','address','phone');
        $data['password'] = bcrypt($request->password);

        PatientSP::createPatient($data);

        return redirect('/pacientes')->with([
            'notification' => 'El paciente se ha registrado correctamente.'
        ]);
    }

    public function edit($id)
    {
        $patient = PatientSP::showPatient($id);

        if (!$patient) abort(404);

        return view('patients.edit', compact('patient'));
    }

    public function update(UpdatePatientRequest $request, $id)
    {
        $data = $request->only('name','email','cedula','address','phone');

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        PatientSP::updatePatient($id, $data);

        return redirect('/pacientes')->with([
            'notification' => 'La información del paciente se actualizó correctamente.'
        ]);
    }

    public function destroy($id)
    {
        PatientSP::deletePatient($id);

        return redirect('/pacientes')->with([
            'notification' => 'El paciente se eliminó correctamente.'
        ]);
    }
}
