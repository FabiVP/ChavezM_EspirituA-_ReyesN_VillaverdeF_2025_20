DELIMITER //
CREATE PROCEDURE sp_list_patients()
BEGIN
    SELECT id, name, email, cedula, address, phone, role,
           created_at, updated_at
    FROM users
    WHERE role = 'paciente';
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_create_patient(
    IN p_name VARCHAR(255),
    IN p_email VARCHAR(255),
    IN p_password VARCHAR(255),
    IN p_cedula VARCHAR(255),
    IN p_address VARCHAR(255),
    IN p_phone VARCHAR(255)
)
BEGIN
    INSERT INTO users(name, email, password, cedula, address, phone, role)
    VALUES(p_name, p_email, p_password, p_cedula, p_address, p_phone, 'paciente');

    SELECT LAST_INSERT_ID() AS new_id;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_update_patient(
    IN p_id BIGINT,
    IN p_name VARCHAR(255),
    IN p_email VARCHAR(255),
    IN p_password VARCHAR(255),
    IN p_cedula VARCHAR(255),
    IN p_address VARCHAR(255),
    IN p_phone VARCHAR(255)
)
BEGIN
    UPDATE users
    SET name = p_name,
        email = p_email,
        password = p_password,
        cedula = p_cedula,
        address = p_address,
        phone = p_phone
    WHERE id = p_id AND role = 'paciente';
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_delete_patient(IN p_id BIGINT)
BEGIN
    DELETE FROM users 
    WHERE id = p_id 
      AND role = 'paciente';

    -- Devuelve cuántas filas se eliminaron
    SELECT ROW_COUNT() AS affected;
END //
DELIMITER ;



CREATE PROCEDURE sp_list_horarios_by_doctor (
    IN p_doctor_id BIGINT
)
BEGIN
    SELECT 
        id,
        day,
        active,
        morning_start,
        morning_end,
        afternoon_start,
        afternoon_end,
        user_id,
        created_at,
        updated_at
    FROM horarios
    WHERE user_id = p_doctor_id
    ORDER BY day ASC;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_assign_specialty(
    IN p_doctor_id BIGINT,
    IN p_specialty_id BIGINT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 
        FROM specialty_user
        WHERE user_id = p_doctor_id
          AND specialty_id = p_specialty_id
    ) THEN
        INSERT INTO specialty_user (user_id, specialty_id, created_at, updated_at)
        VALUES (p_doctor_id, p_specialty_id, NOW(), NOW());
    END IF;
END$$

DELIMITER ;



DELIMITER $$

CREATE PROCEDURE sp_remove_specialty(
    IN p_doctor_id BIGINT,
    IN p_specialty_id BIGINT
)
BEGIN
    DELETE FROM specialty_user
    WHERE user_id = p_doctor_id
      AND specialty_id = p_specialty_id;
END$$

DELIMITER ;

DELIMITER $$

DELIMITER $$

CREATE PROCEDURE sp_sync_specialties(
    IN p_doctor_id BIGINT,
    IN p_specialty_list TEXT  -- ejemplo: '1,4,6'
)
BEGIN
    -- Eliminar todas las especialidades actuales
    DELETE FROM specialty_user 
    WHERE user_id = p_doctor_id;

    -- Insertar nuevas especialidades dinámicamente
    SET @sql = CONCAT(
        'INSERT INTO specialty_user (user_id, specialty_id, created_at, updated_at)
         SELECT ', p_doctor_id, ', id, NOW(), NOW()
         FROM specialties
         WHERE FIND_IN_SET(id, "', p_specialty_list, '")'
    );

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_show_patient(IN p_id BIGINT)
BEGIN
    SELECT id, name, email, cedula, address, phone, role,
           created_at, updated_at
    FROM users
    WHERE id = p_id
      AND role = 'paciente'
    LIMIT 1;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_list_doctors()
BEGIN
    SELECT id, name, email, cedula, address, phone, role,
           created_at, updated_at
    FROM users
    WHERE role = 'doctor';
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_show_doctor(IN p_id BIGINT)
BEGIN
    SELECT id, name, email, cedula, address, phone, role,
           created_at, updated_at
    FROM users
    WHERE id = p_id
      AND role = 'doctor'
    LIMIT 1;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_create_doctor(
    IN p_name VARCHAR(255),
    IN p_email VARCHAR(255),
    IN p_password VARCHAR(255),
    IN p_cedula VARCHAR(255),
    IN p_address VARCHAR(255),
    IN p_phone VARCHAR(255)
)
BEGIN
    INSERT INTO users(name, email, password, cedula, address, phone, role)
    VALUES(p_name, p_email, p_password, p_cedula, p_address, p_phone, 'doctor');

    SELECT LAST_INSERT_ID() AS new_id;
END //
DELIMITER ;



DELIMITER //
CREATE PROCEDURE sp_update_doctor(
    IN p_id BIGINT,
    IN p_name VARCHAR(255),
    IN p_email VARCHAR(255),
    IN p_password VARCHAR(255),
    IN p_cedula VARCHAR(255),
    IN p_address VARCHAR(255),
    IN p_phone VARCHAR(255)
)
BEGIN
    UPDATE users
    SET name = p_name,
        email = p_email,
        password = p_password,
        cedula = p_cedula,
        address = p_address,
        phone = p_phone
    WHERE id = p_id
      AND role = 'doctor';
END //
DELIMITER ;




DELIMITER //
CREATE PROCEDURE sp_delete_doctor(IN p_id BIGINT)
BEGIN
    DELETE FROM users
    WHERE id = p_id 
      AND role = 'doctor';

    SELECT ROW_COUNT() AS affected;
END //
DELIMITER ;