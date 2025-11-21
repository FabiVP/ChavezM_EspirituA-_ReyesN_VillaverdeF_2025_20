<?php

namespace App\StoreProcedures;

// 📌 AppointmentSP hereda de BaseSP, donde está:
//     - executeProcedure('sp_name', [...])
//     - callProcedureSingle()
//     - conexión a DB usando DB::select('CALL ...')
class AppointmentSP extends BaseSP
{
    /* ============================================================
     * 🟦 LISTAR CITAS (GENERAL)
     * Procedimiento: sp_listar_citas
     * ============================================================ */
    public function listarCitas()
    {
        return $this->executeProcedure('sp_listar_citas');
    }

    /* ============================================================
     * 🟦 CITAS PENDIENTES DE UN PACIENTE
     * Procedimiento: sp_get_pending_appointments
     * ============================================================ */
    public function getPending($userId)
    {
        return $this->executeProcedure('sp_get_pending_appointments', [$userId]);
    }

    /* ============================================================
     * 🟦 CITAS CONFIRMADAS DE UN PACIENTE
     * Procedimiento: sp_get_confirmed_appointments
     * ============================================================ */
    public function getConfirmed($userId)
    {
        return $this->executeProcedure('sp_get_confirmed_appointments', [$userId]);
    }

    /* ============================================================
     * 🟦 HISTORIAL DE CITAS DE UN PACIENTE
     * Procedimiento: sp_get_old_appointments
     * ============================================================ */
    public function getOld($userId)
    {
        return $this->executeProcedure('sp_get_old_appointments', [$userId]);
    }

    /* ============================================================
     * 🟦 MOSTRAR UNA CITA POR ID
     * Procedimiento: sp_mostrar_cita
     * ============================================================ */
    public function mostrarCita($appointmentId)
    {
        return $this->callProcedureSingle('sp_mostrar_cita', [$appointmentId]);
    }

    /* ============================================================
     * 🟦 REGISTRAR UNA NUEVA CITA
     * Procedimiento: sp_registrar_cita
     * ------------------------------------------------------------
     * Parámetros esperados:
     *  - p_patient_id
     *  - p_doctor_id
     *  - p_description
     *  - p_scheduled_date
     *  - p_scheduled_time
     * ============================================================ */
    public function registrarCita(array $params)
    {
        return $this->executeProcedure('sp_registrar_cita', $params);
    }

    /* ============================================================
     * 🟦 CONFIRMAR CITA
     * Procedimiento: sp_confirmar_cita
     * ============================================================ */
    public function confirmarCita($appointmentId)
    {
        return $this->executeProcedure('sp_confirmar_cita', [$appointmentId]);
    }

    /* ============================================================
     * 🟦 CANCELAR CITA
     * Procedimiento: sp_cancelar_cita
     * Parámetros:
     *  - p_appointment_id
     *  - p_reason
     * ============================================================ */
    public function cancelarCita($appointmentId, $reason, $cancelledBy)
{
    return $this->executeProcedure(
        'sp_cancelar_cita',
        [$appointmentId, $reason, $cancelledBy]
    );
}


    /* ============================================================
     * 🟦 REPROGRAMAR CITA
     * Procedimiento: sp_reprogramar_cita
     * Parámetros:
     *  - p_id
     *  - p_new_date
     *  - p_new_time
     * ============================================================ */
    public function reprogramarCita($appointmentId, $date, $time)
    {
        return $this->executeProcedure('sp_reprogramar_cita', [
            $appointmentId,
            $date,
            $time
        ]);
    }

        
    /* ============================================================
     * 🟦 LISTAR CITAS CON NOTIFICACIONES (NUEVO)
     * Procedimiento: sp_get_appointments_with_notifications
     * ============================================================ */
    public function listWithNotifications($userId, $userRole)
    {
        return $this->executeProcedure(
            'sp_get_appointments_with_notifications', 
            [$userId, $userRole]
        );
    }



}
