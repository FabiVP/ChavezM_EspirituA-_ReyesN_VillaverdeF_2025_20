<?php

namespace App\StoreProcedures;

class HorarioSP extends BaseSP
{
    public static function list($doctorId)
    {
        return (new self)->executeProcedure(
            'sp_list_horarios_by_doctor',
            [$doctorId]
        );
    }

    public static function save($doctorId, $day, $active, $mStart, $mEnd, $aStart, $aEnd)
{
    return (new self)->executeProcedure(
        'sp_save_horarios',
        [
            $doctorId,
            $day,
            $active,
            $mStart,
            $mEnd, 
            $aStart,
            $aEnd
        ]
    );
}
}
