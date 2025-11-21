<?php

namespace App\StoreProcedures;

class DoctorSP extends BaseSP
{
    public static function listDoctors()
    {
        return (new static)->executeProcedure('sp_list_doctors');
    }

    public static function showDoctor($id)
    {
        return (new static)->callProcedureSingle('sp_show_doctor', [$id]);
    }

    public static function createDoctor($data)
    {
        return (new static)->executeProcedure('sp_create_doctor', [
            $data['name'],
            $data['email'],
            $data['password'],
            $data['cedula'],
            $data['address'],
            $data['phone']
        ]);
    }

    public static function updateDoctor($id, $data)
    {
        return (new static)->executeProcedure('sp_update_doctor', [
            $id,
            $data['name'],
            $data['email'],
            $data['password'] ?? null,
            $data['cedula'],
            $data['address'],
            $data['phone']
        ]);
    }

    public static function deleteDoctor($id)
    {
        return (new static)->executeProcedure('sp_delete_doctor', [$id]);
    }
}
