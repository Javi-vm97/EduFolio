-- EduFolio - Portafolio Virtual Docente
-- Esquema de base de datos
-- Motor: MariaDB / MySQL  |  Codificacion: utf8mb4

CREATE DATABASE IF NOT EXISTS portafolio_docente
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE portafolio_docente;

-- Tabla: usuarios (docentes y administradores)
CREATE TABLE IF NOT EXISTS usuarios (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(80)  NOT NULL,
  apellidos     VARCHAR(120) NOT NULL,
  email         VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  institucion   VARCHAR(160) DEFAULT NULL,
  rol           ENUM('docente','admin') NOT NULL DEFAULT 'docente',
  creado_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seccion: Documentos (Fase 2)
CREATE TABLE IF NOT EXISTS documentos (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id  INT NOT NULL,
  titulo      VARCHAR(180) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  archivo     VARCHAR(255) DEFAULT NULL,
  tipo        VARCHAR(60)  DEFAULT NULL,
  creado_en   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_documentos_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seccion: Notas (Fase 2)
CREATE TABLE IF NOT EXISTS notas (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id    INT NOT NULL,
  titulo        VARCHAR(180) NOT NULL,
  contenido     TEXT DEFAULT NULL,
  creado_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_notas_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seccion: Material didactico (Fase 2)
CREATE TABLE IF NOT EXISTS materiales (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id  INT NOT NULL,
  titulo      VARCHAR(180) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  archivo     VARCHAR(255) DEFAULT NULL,
  materia     VARCHAR(120) DEFAULT NULL,
  creado_en   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_materiales_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seccion: Tareas (Fase 2)
CREATE TABLE IF NOT EXISTS tareas (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id    INT NOT NULL,
  titulo        VARCHAR(180) NOT NULL,
  descripcion   TEXT DEFAULT NULL,
  fecha_entrega DATE DEFAULT NULL,
  estado        ENUM('pendiente','en_progreso','completada') NOT NULL DEFAULT 'pendiente',
  creado_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tareas_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;
