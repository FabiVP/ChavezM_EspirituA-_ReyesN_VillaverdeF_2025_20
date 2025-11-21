DELIMITER //

CREATE PROCEDURE sp_list_evolutions_with_patient()
BEGIN
    SELECT 
        e.id,
        e.medical_history_id,
        e.doctor_id,
        e.diagnosis,
        e.treatment,
        e.observations,
        e.created_at,
        e.updated_at,
        
        -- Información del historial médico
        mh.patient_id,
        mh.doctor_id as history_doctor_id,
        mh.appointment_id,
        
        -- Información del paciente
        p.name as patient_name,
        p.email as patient_email,
        
        -- Información del médico
        d.name as doctor_name,
        d.email as doctor_email,
        
        -- Información de la cita
        a.scheduled_date,
        a.scheduled_time,
        a.type,
        a.patient_id as appointment_patient_id,
        a.doctor_id as appointment_doctor_id,
        
        -- Campos calculados para reportes
        DATE(e.created_at) as evolution_date,
        CASE 
            WHEN e.treatment IS NOT NULL AND e.observations IS NOT NULL THEN 'Completa'
            WHEN e.treatment IS NULL AND e.observations IS NULL THEN 'Básica'
            ELSE 'Parcial'
        END as evolution_status,
        
        SUBSTRING(e.diagnosis, 1, 100) as diagnosis_short,
        SUBSTRING(e.observations, 1, 100) as observations_short
        
    FROM evolutions e
    INNER JOIN medical_histories mh ON e.medical_history_id = mh.id
    INNER JOIN users p ON mh.patient_id = p.id
    INNER JOIN users d ON e.doctor_id = d.id
    LEFT JOIN appointments a ON mh.appointment_id = a.id
    ORDER BY e.created_at DESC;
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE sp_list_evolutions_by_medical_history(IN p_medical_history_id BIGINT)
BEGIN
    SELECT 
        e.id,
        e.medical_history_id,
        e.doctor_id,
        e.diagnosis,
        e.treatment,
        e.observations,
        e.created_at,
        e.updated_at,
        d.name as doctor_name,
        DATE(e.created_at) as evolution_date,
        CASE 
            WHEN e.treatment IS NOT NULL AND e.observations IS NOT NULL THEN 'Completa'
            WHEN e.treatment IS NULL AND e.observations IS NULL THEN 'Básica'
            ELSE 'Parcial'
        END as evolution_status
    FROM evolutions e
    INNER JOIN users d ON e.doctor_id = d.id
    WHERE e.medical_history_id = p_medical_history_id
    ORDER BY e.created_at DESC;
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE sp_get_evolution_by_id(IN p_evolution_id BIGINT)
BEGIN
    SELECT 
        e.*,
        mh.patient_id,
        mh.doctor_id as history_doctor_id,
        p.name as patient_name,
        d.name as doctor_name,
        a.scheduled_date as appointment_date,
        a.scheduled_time as appointment_time
    FROM evolutions e
    INNER JOIN medical_histories mh ON e.medical_history_id = mh.id
    INNER JOIN users p ON mh.patient_id = p.id
    INNER JOIN users d ON e.doctor_id = d.id
    LEFT JOIN appointments a ON mh.appointment_id = a.id
    WHERE e.id = p_evolution_id;
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE sp_create_evolution(
    IN p_medical_history_id BIGINT,
    IN p_doctor_id BIGINT,
    IN p_diagnosis TEXT,
    IN p_treatment TEXT,
    IN p_observations TEXT
)
BEGIN
    DECLARE new_id BIGINT;
    
    INSERT INTO evolutions (
        medical_history_id,
        doctor_id,
        diagnosis,
        treatment,
        observations,
        created_at,
        updated_at
    ) VALUES (
        p_medical_history_id,
        p_doctor_id,
        p_diagnosis,
        p_treatment,
        p_observations,
        NOW(),
        NOW()
    );
    
    SET new_id = LAST_INSERT_ID();
    
    -- Retornar la evolución creada
    CALL sp_get_evolution_by_id(new_id);
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE sp_update_evolution(
    IN p_evolution_id BIGINT,
    IN p_diagnosis TEXT,
    IN p_treatment TEXT,
    IN p_observations TEXT
)
BEGIN
    UPDATE evolutions 
    SET 
        diagnosis = p_diagnosis,
        treatment = p_treatment,
        observations = p_observations,
        updated_at = NOW()
    WHERE id = p_evolution_id;
    
    -- Retornar la evolución actualizada
    CALL sp_get_evolution_by_id(p_evolution_id);
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE sp_get_medical_history_with_info(IN p_medical_history_id BIGINT)
BEGIN
    SELECT 
        mh.id,
        mh.patient_id,
        mh.doctor_id as history_doctor_id,
        mh.appointment_id,
        mh.diagnosis,
        mh.history,
        mh.created_at,
        
        -- Información para validaciones de seguridad
        p.name as patient_name,
        d.name as doctor_name,
        a.patient_id as appointment_patient_id,
        a.doctor_id as appointment_doctor_id,
        a.scheduled_date,
        a.scheduled_time,
        a.status as appointment_status
        
    FROM medical_histories mh
    INNER JOIN users p ON mh.patient_id = p.id
    INNER JOIN users d ON mh.doctor_id = d.id
    LEFT JOIN appointments a ON mh.appointment_id = a.id
    WHERE mh.id = p_medical_history_id;
END //

DELIMITER ;