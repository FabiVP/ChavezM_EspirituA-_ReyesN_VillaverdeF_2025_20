DELIMITER $$

CREATE PROCEDURE sp_cancelar_cita(
    IN p_appointment_id BIGINT,
    IN p_justification VARCHAR(255),
    IN p_cancelled_by BIGINT
)
BEGIN
    -- Registrar la cancelación
    INSERT INTO cancelled_appointments (
        justification,
        cancelled_by_id,
        appointment_id,
        created_at,
        updated_at
    ) VALUES (
        p_justification,
        p_cancelled_by,
        p_appointment_id,
        NOW(),
        NOW()
    );

    -- Actualizar estado de la cita
    UPDATE appointments
    SET status = 'Cancelada'
    WHERE id = p_appointment_id;
END$$

DELIMITER ;
