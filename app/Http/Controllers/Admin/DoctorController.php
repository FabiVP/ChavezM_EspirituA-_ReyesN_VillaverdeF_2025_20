<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Specialty;

// Form Requests
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;

// Store Procedures
use App\StoreProcedures\DoctorSP;
use App\StoreProcedures\SpecialtyUserSP;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = DoctorSP::listDoctors();
        return view('doctors.index', compact('doctors'));
    }

    public function create()
    {
        $specialties = Specialty::all();
        return view('doctors.create', compact('specialties'));
    }

    public function store(StoreDoctorRequest $request)
{
    $data = $request->all();
    $data['password'] = bcrypt($request->password);

    // Crear doctor y obtener el ID real desde el SP
    $doctorResult = DoctorSP::createDoctor($data);
    $doctorId = $doctorResult[0]->new_id;

    // Sincronizar especialidades correctamente
    SpecialtyUserSP::syncSpecialties($doctorId, $request->specialties);

    return redirect('/medicos')->with([
        'notification' => 'El médico se ha registrado correctamente.'
    ]);
}


    public function edit($id)
    {
        // Obtener doctor por SP
        $doctor = DoctorSP::showDoctor($id);
        if (!$doctor) abort(404);

        $specialties = Specialty::all();

        // Obtener especialidades asignadas al doctor por SP
        $specialty_ids = SpecialtyUserSP::getByDoctor($id)
            ->pluck('id')
            ->toArray();

        return view('doctors.edit', compact('doctor', 'specialties', 'specialty_ids'));
    }

    public function update(UpdateDoctorRequest $request, $id)
    {
        $data = $request->only('name','email','cedula','address','phone');

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Actualizar doctor por SP
        DoctorSP::updateDoctor($id, $data);

        // Actualizar especialidades por SP
        SpecialtyUserSP::syncSpecialties($id, $request->specialties);

        return redirect('/medicos')->with([
            'notification' => 'La información del médico se actualizó correctamente.'
        ]);
    }

    public function destroy($id)
    {
        // Protección
        if ($id == 1 || $id == 2) {
            return back()->with([
                'notification' => '🚫 No se puede eliminar al administrador o médico fijo.'
            ]);
        }

        DoctorSP::deleteDoctor($id);

        return redirect('/medicos')->with([
            'notification' => 'El médico se eliminó correctamente.'
        ]);
    }
}
