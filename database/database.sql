-- ============================================
-- RESETEAR Y CREAR BASE DE DATOS DESDE CERO
-- ============================================

DROP DATABASE IF EXISTS asistencia_qr;
CREATE DATABASE asistencia_qr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE asistencia_qr;

-- ============================================
-- TABLA: Docentes / Usuarios del Sistema
-- ============================================
CREATE TABLE docentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('docente', 'admin') NOT NULL DEFAULT 'docente',
    activo TINYINT(1) NOT NULL DEFAULT 1,
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

-- Usuarios del sistema (Administrador y Docentes)
-- Admin:    usuario: admin    / clave: admin123  (Rol: admin)
-- Docente:  usuario: profesor / clave: 12345     (Rol: docente)
-- Docente:  usuario: Demo     / clave: Demo123   (Rol: docente)
INSERT INTO docentes (nombre, usuario, password, rol, activo) VALUES
('Administrador General', 'admin', '$2y$10$hvA7RLlZu0zY6ra.qQ3R...YDzYlovxVLLJVCfRtxumr5GkzgOyU2', 'admin', 1),
('Ing. Docente Titular', 'profesor', '$2y$10$sjeeJXsdpd.qkFYS6LZfD.8unT/gnk.hiysbOb9uRI4z60eF3l/km', 'docente', 1),
('Profesor Demo', 'Demo', '$2y$10$HYRlu/.cbxrjZ2AYGLkyDOiiSpItAClovLSYdb64mvmd8KS.vwRmy', 'docente', 1);

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
