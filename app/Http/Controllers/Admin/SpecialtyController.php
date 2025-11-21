<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Specialty;
use App\Http\Controllers\Controller;
// Importamos los Form Requests
use App\Http\Requests\StoreSpecialtyRequest;
use App\Http\Requests\UpdateSpecialtyRequest;

class SpecialtyController extends Controller
{

    public function index(){
        $specialties = Specialty::all();
        return view('specialties.index', compact('specialties'));
    }

    public function create(){
        return view('specialties.create');
    }

    // Renombramos 'sendData' a 'store' por convención
    public function store(StoreSpecialtyRequest $request){
        // *** SE HA ELIMINADO LA VALIDACIÓN MANUAL (rules y messages) ***

        $specialty = new Specialty();
        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();
        $notification = 'La especialidad se ha creado correctamente.';

        return redirect('/especialidades')->with(compact('notification'));

    }

    public function edit(Specialty $specialty){
        return view('specialties.edit', compact('specialty'));
    }

    public function update(UpdateSpecialtyRequest $request, Specialty $specialty){
        // *** SE HA ELIMINADO LA VALIDACIÓN MANUAL (rules y messages) ***

        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();

        $notification = 'La especialidad se ha actualizado correctamente.';

        return redirect('/especialidades')->with(compact('notification'));

    }

    public function destroy(Specialty $specialty){
        $deleteName = $specialty->name;         
        $specialty->delete();

        $notification = 'La especialidad '. $deleteName .' se ha eliminado correctamente.';

        return redirect('/especialidades')->with(compact('notification'));
    }


}
