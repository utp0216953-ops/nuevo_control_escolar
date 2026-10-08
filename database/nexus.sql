-- ============================================================
-- Base de Datos: Control Escolar "Nexus"
-- ============================================================

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS nexus
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Seleccionar la base de datos
USE nexus;

-- ============================================================
-- 1. Tabla: Alumnos
-- ============================================================
CREATE TABLE IF NOT EXISTS alumnos (
    id_alumno INT AUTO_INCREMENT PRIMARY KEY,
    matricula VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50),
    email VARCHAR(100) NOT NULL UNIQUE,
    fecha_nacimiento DATE NOT NULL,
    genero ENUM('M', 'F', 'Otro') DEFAULT 'Otro',
    estatus ENUM('Activo', 'Baja', 'Egresado') DEFAULT 'Activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 2. Tabla: Profesores
-- ============================================================
CREATE TABLE IF NOT EXISTS profesores (
    id_profesor INT AUTO_INCREMENT PRIMARY KEY,
    num_empleado VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50),
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    especialidad VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 3. Tabla: Materias
-- ============================================================
CREATE TABLE IF NOT EXISTS materias (
    id_materia INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(10) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    creditos INT NOT NULL CHECK (creditos > 0),
    descripcion TEXT
) ENGINE=InnoDB;

-- ============================================================
-- 4. Tabla: Grupos
-- ============================================================
CREATE TABLE IF NOT EXISTS grupos (
    id_grupo INT AUTO_INCREMENT PRIMARY KEY,
    clave_grupo VARCHAR(20) NOT NULL UNIQUE,
    id_materia INT NOT NULL,
    id_profesor INT NOT NULL,
    periodo VARCHAR(20) NOT NULL, -- Ej. "2026-1"
    aula VARCHAR(20),
    cupo_maximo INT DEFAULT 30,
    FOREIGN KEY (id_materia) REFERENCES materias(id_materia) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_profesor) REFERENCES profesores(id_profesor) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 5. Tabla: Inscripciones (Relación Alumnos - Grupos)
-- ============================================================
CREATE TABLE IF NOT EXISTS inscripciones (
    id_inscripcion INT AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT NOT NULL,
    id_grupo INT NOT NULL,
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uc_alumno_grupo UNIQUE (id_alumno, id_grupo),
    FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_grupo) REFERENCES grupos(id_grupo) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 6. Tabla: Calificaciones
-- ============================================================
CREATE TABLE IF NOT EXISTS calificaciones (
    id_calificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_inscripcion INT NOT NULL UNIQUE,
    calificacion_parcial1 DECIMAL(4,2) DEFAULT 0.00,
    calificacion_parcial2 DECIMAL(4,2) DEFAULT 0.00,
    calificacion_final DECIMAL(4,2) DEFAULT 0.00,
    observaciones TEXT,
    FOREIGN KEY (id_inscripcion) REFERENCES inscripciones(id_inscripcion) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- DATOS DE PRUEBA (OPCIONAL)
-- ============================================================

-- Insertar Materias
INSERT INTO materias (clave, nombre, creditos) VALUES
('MAT101', 'Matemáticas Discretas', 8),
('PRG102', 'Programación Web', 10),
('BD103', 'Bases de Datos', 8);

-- Insertar Profesores
INSERT INTO profesores (num_empleado, nombre, apellido_paterno, email) VALUES
('EMP001', 'Carlos', 'Mendoza', 'carlos.mendoza@nexus.edu'),
('EMP002', 'Ana', 'García', 'ana.garcia@nexus.edu');

-- Insertar Alumnos
INSERT INTO alumnos (matricula, nombre, apellido_paterno, apellido_materno, email, fecha_nacimiento) VALUES
('2026001', 'Juan', 'Pérez', 'López', 'juan.perez@nexus.edu', '2004-05-15'),
('2026002', 'Maria', 'Sánchez', 'Torres', 'maria.sanchez@nexus.edu', '2005-01-22');

-- Insertar Grupo
INSERT INTO grupos (clave_grupo, id_materia, id_profesor, periodo, aula) VALUES
('BD103-A', 3, 1, '2026-1', 'AULA-102');

-- Inscripción de Alumno a Grupo
INSERT INTO inscripciones (id_alumno, id_grupo) VALUES (1, 1);