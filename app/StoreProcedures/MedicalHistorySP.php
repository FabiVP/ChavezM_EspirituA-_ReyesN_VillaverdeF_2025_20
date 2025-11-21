<?php

namespace App\StoreProcedures;

class MedicalHistorySP extends BaseSP
{
    public static function listByDoctor($doctorId)
    {
        return (new self)->executeProcedure(
            'sp_list_medical_history_by_doctor',
            [$doctorId]
        );
    }

    public static function listByPatient($patientId)
    {
        return (new self)->executeProcedure(
            'sp_list_medical_history_by_patient',
            [$patientId]
        );
    }

    public static function listAll()
    {
        return (new self)->executeProcedure(
            'sp_list_medical_history_all'
        );
    }

    public static function createHistory($appointmentId, $doctorId, $patientId, $diagnosis, $history)
    {
        return (new self)->executeProcedure(
            'sp_create_medical_history',
            [
                $appointmentId,
                $doctorId,
                $patientId,
                $diagnosis,
                $history
            ]
        );
    }

    public static function updateHistory($id, $diagnosis, $history)
    {
        return (new self)->executeProcedure(
            'sp_update_medical_history',
            [
                $id,
                $diagnosis,
                $history
            ]
        );
    }

    public static function deleteHistory($id)
    {
        return (new self)->executeProcedure(
            'sp_delete_medical_history',
            [$id]
        );
    }

    public static function show($id)
    {
        return (new self)->callProcedureSingle(
            'sp_show_medical_history',
            [$id]
        );
    }
}
