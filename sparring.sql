-- =====================================================================
-- sparring.sql
-- Script de instalación de la base de datos para la app de sparring.
--
-- Cómo usar en XAMPP + HeidiSQL:
--   1. Arranca Apache y MySQL desde el panel de XAMPP.
--   2. Abre HeidiSQL y conéctate a:
--        Host: 127.0.0.1   Usuario: root   Contraseña: (vacía)   Puerto: 3306
--   3. Menú "Archivo" > "Cargar archivo SQL..." y selecciona este archivo.
--   4. Pulsa F9 (o el botón ▶) para ejecutarlo entero.
--
-- El script crea la base de datos `sparring`, la tabla `usuarios`
-- e inserta varios oponentes de ejemplo para que la pantalla
-- "Descubrir" tenga perfiles que mostrar.
-- =====================================================================


-- ---------------------------------------------------------------------
-- 1) Base de datos
-- ---------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `sparring`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `sparring`;


-- ---------------------------------------------------------------------
-- 2) Tabla `usuarios`
--    Coincide con los campos que usa el código PHP
--    (nombre, email, contraseña, género, experiencia, peso, ciudad).
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
    `id`            INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `nombre`        VARCHAR(80)    NOT NULL,
    `email`         VARCHAR(120)   NOT NULL,
    `contraseña`    VARCHAR(255)   NOT NULL,
    `género`        ENUM('Masculino', 'Femenino', 'Otro') NOT NULL,
    `experiencia`   ENUM('Principiante', 'Intermedio', 'Avanzado', 'Profesional') NOT NULL,
    `peso`          DECIMAL(5,2)   NOT NULL,
    `ciudad`        VARCHAR(80)    NOT NULL,
    `creado_en`     TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- 3) Usuarios de ejemplo
--
--    Todas las contraseñas son: 123456
--    (el hash es el resultado real de password_hash('123456', PASSWORD_DEFAULT))
--
--    Puedes iniciar sesión con cualquiera de estos emails y la
--    contraseña "123456", o simplemente dejarlos como oponentes
--    para que aparezcan al hacer swipe.
-- ---------------------------------------------------------------------
INSERT INTO `usuarios`
    (`nombre`, `email`, `contraseña`, `género`, `experiencia`, `peso`, `ciudad`)
VALUES
    ('Carlos Martínez', 'carlos@test.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Masculino', 'Intermedio',  72.50, 'Madrid'),

    ('Lucía Fernández', 'lucia@test.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Femenino',  'Avanzado',    61.00, 'Barcelona'),

    ('Diego Romero',    'diego@test.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Masculino', 'Principiante', 80.00, 'Valencia'),

    ('Andrea Soler',    'andrea@test.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Femenino',  'Intermedio',  58.30, 'Sevilla'),

    ('Marcos Iglesias', 'marcos@test.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Masculino', 'Profesional', 75.20, 'Bilbao'),

    ('Sara Domínguez',  'sara@test.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Femenino',  'Avanzado',    65.40, 'Madrid'),

    ('Javier Ruiz',     'javier@test.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Masculino', 'Intermedio',  68.00, 'Zaragoza'),

    ('Noa Vidal',       'noa@test.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Otro',      'Principiante', 70.00, 'Málaga');


-- ---------------------------------------------------------------------
-- 4) Comprobaciones rápidas (opcional)
-- ---------------------------------------------------------------------
-- SELECT COUNT(*) AS total_usuarios FROM `usuarios`;
-- SELECT id, nombre, ciudad, experiencia, peso FROM `usuarios`;
