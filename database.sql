
-- Crear la base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS sparring
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

-- Seleccionar la base de datos
USE sparring;

-- Crear la tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    género VARCHAR(20) NOT NULL,
    experiencia VARCHAR(50) NOT NULL,
    peso DECIMAL(5,2) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


