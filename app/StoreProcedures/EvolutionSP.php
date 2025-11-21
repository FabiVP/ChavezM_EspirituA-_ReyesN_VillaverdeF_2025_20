<?php

namespace App\StoreProcedures;

class EvolutionSP extends BaseSP
{
    /**
     * ============================================================
     * 📋 LISTAR TODAS LAS EVOLUCIONES CON INFORMACIÓN DE PACIENTE
     * ============================================================
     */
    public static function listAllWithPatient()
    {
        return (new self)->executeProcedure('sp_list_evolutions_with_patient');
    }

    /**
     * ============================================================
     * 📋 LISTAR EVOLUCIONES POR HISTORIAL MÉDICO
     * ============================================================
     */
    public static function listByMedicalHistory($medicalHistoryId)
    {
        return (new self)->executeProcedure('sp_list_evolutions_by_medical_history', [$medicalHistoryId]);
    }

    /**
     * ============================================================
     * 📋 OBTENER EVOLUCIÓN ESPECÍFICA POR ID
     * ============================================================
     */
    public static function getById($evolutionId)
    {
        return (new self)->callProcedureSingle('sp_get_evolution_by_id', [$evolutionId]);
    }

    /**
     * ============================================================
     * 📋 OBTENER HISTORIAL MÉDICO CON INFORMACIÓN PARA COMPATIBILIDAD
     * ============================================================
     */
    public static function getMedicalHistoryWithInfo($medicalHistoryId)
    {
        return (new self)->callProcedureSingle('sp_get_medical_history_with_info', [$medicalHistoryId]);
    }

    /**
     * ============================================================
     * ➕ CREAR NUEVA EVOLUCIÓN MÉDICA
     * ============================================================
     */
    public static function create($evolutionData)
    {
        return (new self)->callProcedureSingle('sp_create_evolution', [
            $evolutionData['medical_history_id'],
            $evolutionData['doctor_id'],
            $evolutionData['diagnosis'],
            $evolutionData['treatment'] ?? null,
            $evolutionData['observations'] ?? null
        ]);
    }

    /**
     * ============================================================
     * ✏️ ACTUALIZAR EVOLUCIÓN MÉDICA
     * ============================================================
     */
    public static function update($evolutionId, $evolutionData)
    {
        return (new self)->callProcedureSingle('sp_update_evolution', [
            $evolutionId,
            $evolutionData['diagnosis'],
            $evolutionData['treatment'] ?? null,
            $evolutionData['observations'] ?? null
        ]);
    }

    /**
     * ============================================================
     * 🗑️ ELIMINAR EVOLUCIÓN MÉDICA
     * ============================================================
     */
    public static function delete($evolutionId)
    {
        return (new self)->executeProcedure('sp_delete_evolution', [$evolutionId]);
    }
}