-- ============================================
-- RESETEAR Y CREAR BASE DE DATOS DESDE CERO
-- ============================================

DROP DATABASE IF EXISTS asistencia_qr;
CREATE DATABASE asistencia_qr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE asistencia_qr;

-- ============================================
-- TABLA: Docentes
-- ============================================
CREATE TABLE docentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: Estudiantes
-- ============================================
CREATE TABLE estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(150) NOT NULL,
    apellido VARCHAR(150),
    carrera VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: Sesiones de Clase
-- ============================================
CREATE TABLE sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    docente_id INT NOT NULL,
    codigo_sesion VARCHAR(20) NOT NULL UNIQUE,
    materia VARCHAR(150) NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME,
    hora_fin TIME,
    activa TINYINT(1) DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: Asistencias
-- ============================================
CREATE TABLE asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    estudiante_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unica_asistencia (sesion_id, estudiante_id),
    FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERTAR DATOS DE PRUEBA
-- ============================================

-- Docente de prueba (usuario: Demo / contraseña: Demo123)
INSERT INTO docentes (nombre, usuario, password) VALUES
('Profesor Demo', 'Demo', '$2y$10$pv9Dh6jAHhNWq4t7vZ3c0.5MH8Y6R2K1L9X4W7V2Q5P3N1M0O9B');

-- Estudiantes de prueba
INSERT INTO estudiantes (codigo, nombre, apellido, carrera) VALUES
('EST001', 'Juan', 'Pérez', 'Desarrollo de Software'),
('EST002', 'María', 'García', 'Desarrollo de Software'),
('EST003', 'Carlos', 'López', 'Mecánica Automotriz'),
('EST004', 'Ana', 'Martínez', 'Entrenamiento Deportivo'),
('EST005', 'Luis', 'Rodríguez', 'Educación Inicial'),
('EST006', 'Sofia', 'Herrera', 'Desarrollo de Software'),
('EST007', 'Miguel', 'Jiménez', 'Mecánica Automotriz'),
('EST008', 'Lucia', 'Flores', 'Entrenamiento Deportivo');
