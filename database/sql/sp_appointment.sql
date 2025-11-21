-- ============================================================
-- 📦 PROCEDIMIENTO: sp_listar_citas (CORREGIDO)
-- Ahora devuelve doctor_id, patient_id, specialty_id
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_listar_citas()
BEGIN
    SELECT 
        a.id,
        a.doctor_id,
        a.patient_id,
        a.specialty_id,
        a.scheduled_date,
        a.scheduled_time,
        a.type,
        a.description,
        a.status,
        d.name AS doctor_name,
        p.name AS patient_name,
        s.name AS specialty_name
    FROM appointments a
    INNER JOIN users d ON a.doctor_id = d.id
    INNER JOIN users p ON a.patient_id = p.id
    INNER JOIN specialties s ON a.specialty_id = s.id
    ORDER BY a.scheduled_date DESC, a.scheduled_time DESC;
END$$
DELIMITER ;


-- ============================================================
-- 📦 PROCEDIMIENTOS PARA PACIENTES
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_get_pending_appointments(IN p_user_id BIGINT)
BEGIN
    SELECT * 
    FROM appointments
    WHERE patient_id = p_user_id
      AND status = 'Reservada';
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE sp_get_confirmed_appointments(IN p_user_id BIGINT)
BEGIN
    SELECT * 
    FROM appointments
    WHERE patient_id = p_user_id
      AND status = 'Confirmada';
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE sp_get_old_appointments(IN p_user_id BIGINT)
BEGIN
    SELECT * 
    FROM appointments
    WHERE patient_id = p_user_id
      AND status IN ('Atendida','Cancelada','Reprogramada');
END$$
DELIMITER ;


-- ============================================================
-- 📦 sp_mostrar_cita
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_mostrar_cita(IN p_id BIGINT)
BEGIN
    SELECT 
        a.*,
        d.name AS doctor_name,
        p.name AS patient_name,
        s.name AS specialty_name
    FROM appointments a
    INNER JOIN users d ON a.doctor_id = d.id
    INNER JOIN users p ON a.patient_id = p.id
    INNER JOIN specialties s ON a.specialty_id = s.id
    WHERE a.id = p_id
    LIMIT 1;
END$$
DELIMITER ;


-- ============================================================
-- 📦 REGISTRAR CITA
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_registrar_cita(
    IN p_patient_id BIGINT,
    IN p_doctor_id BIGINT,
    IN p_description TEXT,
    IN p_scheduled_date DATE,
    IN p_scheduled_time TIME
)
BEGIN
    INSERT INTO appointments (
        patient_id,
        doctor_id,
        description,
        scheduled_date,
        scheduled_time,
        status,
        type
    )
    VALUES (
        p_patient_id,
        p_doctor_id,
        p_description,
        p_scheduled_date,
        p_scheduled_time,
        'Reservada',
        'Consulta'
    );
END$$
DELIMITER ;


-- ============================================================
-- 📦 CONFIRMAR CITA
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_confirmar_cita(IN p_id BIGINT)
BEGIN
    UPDATE appointments
    SET status = 'Confirmada'
    WHERE id = p_id;
END$$
DELIMITER ;


-- ============================================================
-- 📦 REPROGRAMAR CITA
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_reprogramar_cita(
    IN p_id BIGINT,
    IN p_new_date DATE,
    IN p_new_time TIME
)
BEGIN
    UPDATE appointments
    SET 
        scheduled_date = p_new_date,
        scheduled_time = p_new_time,
        status = 'Reprogramada'
    WHERE id = p_id;
END$$
DELIMITER ;


-- ============================================================
-- 📦 PROCEDIMIENTO: sp_get_appointments_with_notifications
-- Consistent con tus otros SP (mismos campos + hours_to_appointment)
-- ============================================================
DELIMITER $$

CREATE PROCEDURE sp_get_appointments_with_notifications(
    IN p_user_id BIGINT,
    IN p_user_role VARCHAR(50)
)
BEGIN
    IF p_user_role = 'admin' THEN
        SELECT 
            a.id,
            a.doctor_id,
            a.patient_id,
            a.specialty_id,
            a.scheduled_date,
            a.scheduled_time,
            a.type,
            a.description,
            a.status,
            d.name AS doctor_name,
            p.name AS patient_name,
            s.name AS specialty_name,
            TIMESTAMPDIFF(HOUR, NOW(), CONCAT(a.scheduled_date, ' ', a.scheduled_time)) AS hours_to_appointment
        FROM appointments a
        INNER JOIN users d ON a.doctor_id = d.id
        INNER JOIN users p ON a.patient_id = p.id
        INNER JOIN specialties s ON a.specialty_id = s.id
        ORDER BY a.scheduled_date DESC, a.scheduled_time DESC;
    
    ELSEIF p_user_role = 'doctor' THEN
        SELECT 
            a.id,
            a.doctor_id,
            a.patient_id,
            a.specialty_id,
            a.scheduled_date,
            a.scheduled_time,
            a.type,
            a.description,
            a.status,
            d.name AS doctor_name,
            p.name AS patient_name,
            s.name AS specialty_name,
            TIMESTAMPDIFF(HOUR, NOW(), CONCAT(a.scheduled_date, ' ', a.scheduled_time)) AS hours_to_appointment
        FROM appointments a
        INNER JOIN users d ON a.doctor_id = d.id
        INNER JOIN users p ON a.patient_id = p.id
        INNER JOIN specialties s ON a.specialty_id = s.id
        WHERE a.doctor_id = p_user_id
        ORDER BY a.scheduled_date DESC, a.scheduled_time DESC;
    
    ELSEIF p_user_role = 'paciente' THEN
        SELECT 
            a.id,
            a.doctor_id,
            a.patient_id,
            a.specialty_id,
            a.scheduled_date,
            a.scheduled_time,
            a.type,
            a.description,
            a.status,
            d.name AS doctor_name,
            p.name AS patient_name,
            s.name AS specialty_name,
            TIMESTAMPDIFF(HOUR, NOW(), CONCAT(a.scheduled_date, ' ', a.scheduled_time)) AS hours_to_appointment
        FROM appointments a
        INNER JOIN users d ON a.doctor_id = d.id
        INNER JOIN users p ON a.patient_id = p.id
        INNER JOIN specialties s ON a.specialty_id = s.id
        WHERE a.patient_id = p_user_id
        ORDER BY a.scheduled_date DESC, a.scheduled_time DESC;
    
    END IF;
END$$

DELIMITER ;