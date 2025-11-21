<?php

namespace App\StoreProcedures;

class SpecialtyUserSP extends BaseSP
{
    public static function getByDoctor($doctorId)
    {
        return (new static)->executeProcedure('sp_list_specialties_by_doctor', [$doctorId]);
    }

    public static function assignSpecialty($doctorId, $specialtyId)
    {
        return (new static)->executeProcedure('sp_assign_specialty', [
            $doctorId,
            $specialtyId
        ]);
    }

    public static function removeSpecialty($doctorId, $specialtyId)
    {
        return (new static)->executeProcedure('sp_remove_specialty', [
            $doctorId,
            $specialtyId
        ]);
    }

    public static function syncSpecialties($doctorId, array $specialtyIds)
    {
        $csv = implode(',', $specialtyIds);

        return (new static)->executeProcedure('sp_sync_specialties', [
            $doctorId,
            $csv
        ]);
    }
}
