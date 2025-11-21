DELIMITER $$

CREATE PROCEDURE sp_list_medical_history_by_doctor(
    IN p_doctor_id BIGINT
)
BEGIN
    SELECT mh.*, a.scheduled_date, a.scheduled_time,
           u.name AS patient_name
    FROM medical_histories mh
    JOIN appointments a ON mh.appointment_id = a.id
    JOIN users u ON mh.patient_id = u.id
    WHERE mh.doctor_id = p_doctor_id
    ORDER BY mh.created_at DESC;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_list_medical_history_by_patient(
    IN p_patient_id BIGINT
)
BEGIN
    SELECT mh.*, a.scheduled_date, a.scheduled_time,
           u.name AS doctor_name
    FROM medical_histories mh
    JOIN appointments a ON mh.appointment_id = a.id
    JOIN users u ON mh.doctor_id = u.id
    WHERE mh.patient_id = p_patient_id
    ORDER BY mh.created_at DESC;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_list_medical_history_all()
BEGIN
    SELECT mh.*, 
           p.name AS patient_name,
           d.name AS doctor_name,
           a.scheduled_date, a.scheduled_time
    FROM medical_histories mh
    JOIN users p ON mh.patient_id = p.id
    JOIN users d ON mh.doctor_id = d.id
    JOIN appointments a ON mh.appointment_id = a.id
    ORDER BY mh.created_at DESC;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_create_medical_history(
    IN p_appointment_id BIGINT,
    IN p_doctor_id BIGINT,
    IN p_patient_id BIGINT,
    IN p_diagnosis TEXT,
    IN p_history TEXT
)
BEGIN
    INSERT INTO medical_histories(
        appointment_id,
        doctor_id,
        patient_id,
        diagnosis,
        history,
        created_at,
        updated_at
    ) VALUES (
        p_appointment_id,
        p_doctor_id,
        p_patient_id,
        p_diagnosis,
        p_history,
        NOW(),
        NOW()
    );
END $$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE sp_update_medical_history(
    IN p_id BIGINT,
    IN p_diagnosis TEXT,
    IN p_history TEXT
)
BEGIN
    UPDATE medical_histories
    SET diagnosis = p_diagnosis,
        history = p_history,
        updated_at = NOW()
    WHERE id = p_id;
END $$

DELIMITER ;

DELIMITER $$

DELIMITER $$

CREATE PROCEDURE sp_show_medical_history(IN p_id BIGINT)
BEGIN
    SELECT 
        mh.*, 
        a.scheduled_date, 
        a.scheduled_time,
        a.status AS appointment_status,
        p.name AS patient_name,
        d.name AS doctor_name
    FROM medical_histories mh
    JOIN appointments a ON mh.appointment_id = a.id
    JOIN users p ON mh.patient_id = p.id
    JOIN users d ON mh.doctor_id = d.id
    WHERE mh.id = p_id;
END $$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE sp_delete_medical_history(
    IN p_id BIGINT
)
BEGIN
    DELETE FROM medical_histories WHERE id = p_id;
END $$

DELIMITER ;
