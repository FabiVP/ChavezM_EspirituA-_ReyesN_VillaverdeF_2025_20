-- ============================================================
-- 🏥 SISTEMA DE GESTIÓN DE CITAS MÉDICAS V2
-- Script reorganizado para ejecución completa
-- ============================================================

-- ⚠️ Desactivar modo seguro para poder actualizar con JOIN
SET GLOBAL SQL_SAFE_UPDATES = 0;
SET SESSION SQL_SAFE_UPDATES = 0;

-- ============================================================
-- 📦 CREACIÓN DE LA BASE DE DATOS PRINCIPAL
-- ============================================================
DROP DATABASE IF EXISTS `bd_citas_v3`;
CREATE DATABASE `bd_citas_v3` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bd_citas_v3`;

-- ============================================================
-- 🗂️ CREACIÓN DE TABLAS
-- ============================================================

-- 1️⃣ TABLA: USERS (usuarios: admin, doctores, pacientes)
-- ============================================================
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `cedula` VARCHAR(255) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(255) DEFAULT NULL,
  `role` VARCHAR(255) NOT NULL, 
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2️⃣ TABLAS DEL SISTEMA
-- ============================================================
CREATE TABLE `password_resets` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX (`email`)
);

CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE (`uuid`)
);

CREATE TABLE `personal_access_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` VARCHAR(255) NOT NULL,
  `tokenable_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `token` VARCHAR(64) NOT NULL UNIQUE,
  `abilities` TEXT DEFAULT NULL,
  `last_used_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX (`tokenable_type`,`tokenable_id`)
);

-- 3️⃣ TABLA: SPECIALTIES
-- ============================================================
CREATE TABLE `specialties` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4️⃣ TABLA: HORARIOS
-- ============================================================
CREATE TABLE `horarios` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `day` SMALLINT UNSIGNED NOT NULL,
  `active` TINYINT(1) NOT NULL,
  `morning_start` TIME NOT NULL,
  `morning_end` TIME NOT NULL,
  `afternoon_start` TIME NOT NULL,
  `afternoon_end` TIME NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_day` (`user_id`, `day`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- 5️⃣ TABLA PIVOTE SPECIALTY_USER
-- ============================================================
CREATE TABLE `specialty_user` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `specialty_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`specialty_id`) REFERENCES `specialties`(`id`) ON DELETE CASCADE
);

-- 6️⃣ TABLA: APPOINTMENTS
-- ============================================================
CREATE TABLE `appointments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `scheduled_date` DATE NOT NULL,
  `scheduled_time` TIME NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `doctor_id` BIGINT UNSIGNED NOT NULL,
  `patient_id` BIGINT UNSIGNED NOT NULL,
  `specialty_id` BIGINT UNSIGNED NOT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'Reservada',
  `reprogrammed_from` BIGINT UNSIGNED NULL,
  `reprogrammed_by` BIGINT UNSIGNED NULL,
  `reprogramming_reason` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`specialty_id`) REFERENCES `specialties`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`reprogrammed_from`) REFERENCES `appointments`(`id`),
  FOREIGN KEY (`reprogrammed_by`) REFERENCES `users`(`id`)
);

-- 7️⃣ TABLA: CANCELLED_APPOINTMENTS
-- ============================================================
CREATE TABLE `cancelled_appointments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `justification` VARCHAR(255) NULL,
  `cancelled_by_id` BIGINT UNSIGNED NOT NULL,
  `appointment_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`cancelled_by_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE CASCADE
);

-- 8️⃣ TABLA: MEDICAL_HISTORIES
-- ============================================================
CREATE TABLE `medical_histories` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `appointment_id` BIGINT UNSIGNED NOT NULL,
  `patient_id` BIGINT UNSIGNED NOT NULL,
  `doctor_id` BIGINT UNSIGNED NOT NULL,
  `diagnosis` TEXT NULL,
  `history` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- 9️⃣ TABLA: EVOLUTIONS
-- ============================================================
CREATE TABLE `evolutions` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `medical_history_id` BIGINT UNSIGNED NOT NULL,
  `doctor_id` BIGINT UNSIGNED NOT NULL,
  `diagnosis` TEXT NOT NULL,
  `treatment` TEXT NULL,
  `observations` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`medical_history_id`) REFERENCES `medical_histories`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- ============================================================
-- 📥 DATOS INICIALES
-- ============================================================

-- ✅ Usuarios base
INSERT INTO `users` (`name`, `email`, `email_verified_at`, `password`, `cedula`, `address`, `phone`, `role`)
VALUES
('Marcos Chavez', 'admin@gmail.com', NOW(), '$2y$10$qSDUvCkT1AF79/2il6S0muP8vNUiDYeeYgILgpLIl0IhdDMdb0kUm', '76252483', 'Av. Universitaria', '996410860', 'admin'),
('Ruth Mallma', 'medicogeneral@gmail.com', NOW(), '$2y$10$qSDUvCkT1AF79/2il6S0muP8vNUiDYeeYgILgpLIl0IhdDMdb0kUm', '76252482', 'Clínica Central', '996410861', 'doctor');

-- ✅ Actualizar patient_id desde appointments (si existen datos previos)
UPDATE `medical_histories` mh
JOIN `appointments` a ON mh.appointment_id = a.id
SET mh.patient_id = a.patient_id;

-- ============================================================
-- 🔧 STORED PROCEDURES
-- ============================================================

-- ====================================
-- PROCEDIMIENTOS DE CITAS (APPOINTMENTS)
-- ====================================

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

DELIMITER $$
CREATE PROCEDURE sp_cancelar_cita(
    IN p_appointment_id BIGINT,
    IN p_justification VARCHAR(255),
    IN p_cancelled_by BIGINT
)
BEGIN
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

    UPDATE appointments
    SET status = 'Cancelada'
    WHERE id = p_appointment_id;
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
CREATE PROCEDURE sp_get_pending_appointments(IN p_user_id BIGINT)
BEGIN
    SELECT * 
    FROM appointments
    WHERE patient_id = p_user_id
      AND status = 'Reservada';
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

-- ====================================
-- PROCEDIMIENTOS DE PACIENTES
-- ====================================

DELIMITER $$
CREATE PROCEDURE sp_list_patients()
BEGIN
    SELECT id, name, email, cedula, address, phone, role,
           created_at, updated_at
    FROM users
    WHERE role = 'paciente';
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE sp_show_patient(IN p_id BIGINT)
BEGIN
    SELECT id, name, email, cedula, address, phone, role,
           created_at, updated_at
    FROM users
    WHERE id = p_id
      AND role = 'paciente'
    LIMIT 1;
END$$
DELIMITER ;

DELIMITER $$
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
END$$
DELIMITER ;

DELIMITER $$
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
    IF p_password IS NULL OR p_password = '' THEN
        UPDATE users
        SET 
            name = p_name,
            email = p_email,
            cedula = p_cedula,
            address = p_address,
            phone = p_phone
        WHERE id = p_id AND role = 'paciente';
    ELSE
        UPDATE users
        SET 
            name = p_name,
            email = p_email,
            password = p_password,
            cedula = p_cedula,
            address = p_address,
            phone = p_phone
        WHERE id = p_id AND role = 'paciente';
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE sp_delete_patient(IN p_id BIGINT)
BEGIN
    DELETE FROM users 
    WHERE id = p_id 
      AND role = 'paciente';

    SELECT ROW_COUNT() AS affected;
END$$
DELIMITER ;

-- ====================================
-- PROCEDIMIENTOS DE DOCTORES
-- ====================================

DELIMITER $$
CREATE PROCEDURE sp_list_doctors()
BEGIN
    SELECT id, name, email, cedula, address, phone, role,
           created_at, updated_at
    FROM users
    WHERE role = 'doctor';
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE sp_show_doctor(IN p_id BIGINT)
BEGIN
    SELECT id, name, email, cedula, address, phone, role,
           created_at, updated_at
    FROM users
    WHERE id = p_id
      AND role = 'doctor'
    LIMIT 1;
END$$
DELIMITER ;

DELIMITER $$
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
END$$
DELIMITER ;

DELIMITER $$
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
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE sp_delete_doctor(IN p_id BIGINT)
BEGIN
    DELETE FROM users
    WHERE id = p_id 
      AND role = 'doctor';

    SELECT ROW_COUNT() AS affected;
END$$
DELIMITER ;

-- ====================================
-- PROCEDIMIENTOS DE ESPECIALIDADES
-- ====================================

DELIMITER $$
CREATE PROCEDURE sp_list_specialties_by_doctor(IN p_doctor_id BIGINT)
BEGIN
    SELECT s.*
    FROM specialties s
    INNER JOIN specialty_user su ON su.specialty_id = s.id
    WHERE su.user_id = p_doctor_id;
END$$
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
CREATE PROCEDURE sp_sync_specialties(
    IN p_doctor_id BIGINT,
    IN p_specialty_list TEXT
)
BEGIN
    DELETE FROM specialty_user 
    WHERE user_id = p_doctor_id;

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

-- ====================================
-- PROCEDIMIENTOS DE HORARIOS
-- ====================================

DELIMITER $$
CREATE PROCEDURE sp_list_horarios_by_doctor(
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
END$$
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
    INSERT INTO horarios(day, active, morning_start, morning_end, afternoon_start, afternoon_end, user_id)
    VALUES(p_day, p_active, p_morning_start, p_morning_end, p_afternoon_start, p_afternoon_end, p_user_id)
    ON DUPLICATE KEY UPDATE
        active = VALUES(active),
        morning_start = VALUES(morning_start),
        morning_end = VALUES(morning_end),
        afternoon_start = VALUES(afternoon_start),
        afternoon_end = VALUES(afternoon_end),
        updated_at = NOW();
END$$
DELIMITER ;

-- ====================================
-- PROCEDIMIENTOS DE HISTORIAS MÉDICAS
-- ====================================

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
END$$
DELIMITER ;

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
END$$
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
END$$
DELIMITER ;

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
END$$
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
END$$
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
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE sp_delete_medical_history(
    IN p_id BIGINT
)
BEGIN
    DELETE FROM medical_histories WHERE id = p_id;
END$$
DELIMITER ;


-- =====================================
-- PROCEDIMIENTOS DE EVOLUCIONES MEDICAS
-- =====================================
USE `bd_citas_v3`;
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


-- ============================================================
-- ✅ SCRIPT COMPLETADO EXITOSAMENTE
-- ============================================================
-- Base de datos: bd_citas_v2
-- Tablas: 9 principales + 3 sistema
-- Stored Procedures: 30
-- ============================================================




-- ============================================================
-- CITAS PARA PRUEBAS - TODOS LOS ESTADOS
-- ============================================================

-- 👨‍⚕️ DOCTOR: Fabiola (ID: 5) | 👥 PACIENTES: Abel (3), Angie (4) | 🏥 ESPECIALIDAD: Cardiología (1)

-- ==================== CITAS CONFIRMADAS ====================
-- (Para probar notificaciones automáticas)

-- Citas confirmadas para HOY (urgentes - menos de 4 horas)
INSERT INTO appointments (scheduled_date, scheduled_time, type, description, doctor_id, patient_id, specialty_id, status, created_at, updated_at)
VALUES 
(CURDATE(), DATE_FORMAT(NOW() + INTERVAL 2 HOUR, '%H:%i:00'), 'Consulta', 'Dolor torácico agudo', 5, 3, 1, 'Confirmada', NOW(), NOW()),
(CURDATE(), DATE_FORMAT(NOW() + INTERVAL 3 HOUR, '%H:%i:00'), 'Control', 'Revisión post-operatoria', 5, 4, 1, 'Confirmada', NOW(), NOW());

-- Citas confirmadas para MAÑANA (recordatorio - 24 horas)
INSERT INTO appointments (scheduled_date, scheduled_time, type, description, doctor_id, patient_id, specialty_id, status, created_at, updated_at)
VALUES 
(CURDATE() + INTERVAL 1 DAY, '10:00:00', 'Evaluación', 'Chequeo cardíaco rutinario', 5, 3, 1, 'Confirmada', NOW(), NOW()),
(CURDATE() + INTERVAL 1 DAY, '15:30:00', 'Consulta', 'Mareos frecuentes', 5, 4, 1, 'Confirmada', NOW(), NOW());

-- Citas confirmadas para PASADO MAÑANA (normales - más de 24 horas)
INSERT INTO appointments (scheduled_date, scheduled_time, type, description, doctor_id, patient_id, specialty_id, status, created_at, updated_at)
VALUES 
(CURDATE() + INTERVAL 2 DAY, '09:00:00', 'Control', 'Seguimiento tratamiento', 5, 3, 1, 'Confirmada', NOW(), NOW()),
(CURDATE() + INTERVAL 3 DAY, '11:00:00', 'Consulta', 'Palpitaciones nocturnas', 5, 4, 1, 'Confirmada', NOW(), NOW());

-- ==================== CITAS PENDIENTES/RESERVADAS ====================
INSERT INTO appointments (scheduled_date, scheduled_time, type, description, doctor_id, patient_id, specialty_id, status, created_at, updated_at)
VALUES 
(CURDATE() + INTERVAL 4 DAY, '08:30:00', 'Consulta', 'Primera consulta', 5, 3, 1, 'Reservada', NOW(), NOW()),
(CURDATE() + INTERVAL 5 DAY, '14:00:00', 'Evaluación', 'Exámenes de laboratorio', 5, 4, 1, 'Reservada', NOW(), NOW());

-- ==================== CITAS ATENDIDAS ====================
-- (Para probar historial médico)
INSERT INTO appointments (scheduled_date, scheduled_time, type, description, doctor_id, patient_id, specialty_id, status, created_at, updated_at)
VALUES 
(CURDATE() - INTERVAL 2 DAY, '10:00:00', 'Consulta', 'Control presión arterial', 5, 3, 1, 'Atendida', NOW(), NOW()),
(CURDATE() - INTERVAL 1 DAY, '16:00:00', 'Evaluación', 'Electrocardiograma', 5, 4, 1, 'Atendida', NOW(), NOW()),
(CURDATE() - INTERVAL 3 DAY, '09:30:00', 'Control', 'Revisión medicación', 5, 3, 1, 'Atendida', NOW(), NOW());

-- ==================== CITAS CANCELADAS ====================
INSERT INTO appointments (scheduled_date, scheduled_time, type, description, doctor_id, patient_id, specialty_id, status, created_at, updated_at)
VALUES 
(CURDATE() + INTERVAL 6 DAY, '11:30:00', 'Consulta', 'No pudo asistir', 5, 3, 1, 'Cancelada', NOW(), NOW()),
(CURDATE() + INTERVAL 7 DAY, '15:00:00', 'Control', 'Viaje de emergencia', 5, 4, 1, 'Cancelada', NOW(), NOW());

-- ==================== CITAS REPROGRAMADAS ====================
INSERT INTO appointments (scheduled_date, scheduled_time, type, description, doctor_id, patient_id, specialty_id, status, created_at, updated_at)
VALUES 
(CURDATE() + INTERVAL 8 DAY, '10:30:00', 'Consulta', 'Conflicto de horario', 5, 3, 1, 'Reprogramada', NOW(), NOW());

-- ==================== CITAS YA PASADAS (COMPLETADAS) ====================
INSERT INTO appointments (scheduled_date, scheduled_time, type, description, doctor_id, patient_id, specialty_id, status, created_at, updated_at)
VALUES 
(CURDATE() - INTERVAL 5 DAY, '08:00:00', 'Consulta', 'Revisión general', 5, 3, 1, 'Confirmada', NOW(), NOW()),
(CURDATE() - INTERVAL 4 DAY, '17:00:00', 'Control', 'Seguimiento tratamiento', 5, 4, 1, 'Confirmada', NOW(), NOW());
