DELIMITER $$

CREATE PROCEDURE sp_list_horarios_by_doctor(IN p_doctor_id BIGINT)
BEGIN
    SELECT *
    FROM horarios
    WHERE user_id = p_doctor_id
    ORDER BY day ASC;
END $$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE sp_save_horarios(
    IN p_user_id BIGINT,
    IN p_day SMALLINT,
    IN p_active BOOLEAN,
    IN p_morning_start TIME,
    IN p_morning_end TIME,
    IN p_afternoon_start TIME,
    IN p_afternoon_end TIME
)
BEGIN
    DECLARE day_to_use SMALLINT;

    -- Si p_day es NULL, generamos días del 0 al 6 (Domingo a Sábado)
    IF p_day IS NULL THEN
        
        INSERT INTO horarios (day, active, morning_start, morning_end, afternoon_start, afternoon_end, user_id, created_at, updated_at)
        SELECT 
            seq.idx AS day,
            TRUE AS active,
            p_morning_start,
            p_morning_end,
            p_afternoon_start,
            p_afternoon_end,
            p_user_id,
            NOW(),
            NOW()
        FROM (
            SELECT 0 AS idx UNION ALL
            SELECT 1 UNION ALL
            SELECT 2 UNION ALL
            SELECT 3 UNION ALL
            SELECT 4 UNION ALL
            SELECT 5 UNION ALL
            SELECT 6
        ) AS seq;

    ELSE
        -- Si viene p_day específico, solo actualizamos o insertamos ese día
        SET day_to_use = p_day;

        INSERT INTO horarios (day, active, morning_start, morning_end, afternoon_start, afternoon_end, user_id, created_at, updated_at)
        VALUES (day_to_use, p_active, p_morning_start, p_morning_end, p_afternoon_start, p_afternoon_end, p_user_id, NOW(), NOW())
        ON DUPLICATE KEY UPDATE 
            active = VALUES(active),
            morning_start = VALUES(morning_start),
            morning_end = VALUES(morning_end),
            afternoon_start = VALUES(afternoon_start),
            afternoon_end = VALUES(afternoon_end),
            updated_at = NOW();
    END IF;

END$$

DELIMITER ;
