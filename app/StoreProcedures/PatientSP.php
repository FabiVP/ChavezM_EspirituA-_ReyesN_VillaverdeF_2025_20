<?php

namespace App\StoreProcedures;

class PatientSP extends BaseSP
{
    public static function listPatients()
    {
        return (new static)->executeProcedure('sp_list_patients');
    }

    public static function showPatient($id)
    {
        // corregido: sp_show_patient → sp_get_patient
        return (new static)->callProcedureSingle('sp_get_patient', [$id]);
    }

    public static function createPatient($data)
    {
        return (new static)->executeProcedure('sp_create_patient', [
            $data['name'],
            $data['email'],
            $data['password'],  // ← YA VIENE HASHEADO DEL CONTROLADOR
            $data['cedula'],
            $data['address'],
            $data['phone']
        ]);
    }


public static function updatePatient($id, $data)
{
    // Si no viene password, lo enviamos como cadena vacía
    $password = isset($data['password']) && $data['password'] != ''
        ? bcrypt($data['password'])
        : '';

    return (new static)->executeProcedure('sp_update_patient', [
        $id,
        $data['name'],
        $data['email'],
        $password,  // <-- ahora sí enviamos password
        $data['cedula'],
        $data['address'],
        $data['phone']
    ]);
}


    public static function deletePatient($id)
    {
        return (new static)->executeProcedure('sp_delete_patient', [$id]);
    }
}
