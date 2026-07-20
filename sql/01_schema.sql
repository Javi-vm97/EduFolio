-- EduFolio - Portafolio Virtual Docente
-- Esquema de base de datos
-- Motor: MariaDB / MySQL  |  Codificacion: utf8mb4

CREATE DATABASE IF NOT EXISTS portafolio_docente
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE portafolio_docente;

-- Tabla: usuarios (docentes, administradores y alumnos)
CREATE TABLE IF NOT EXISTS usuarios (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(80)  NOT NULL,
  apellidos     VARCHAR(120) NOT NULL,
  email         VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  institucion   VARCHAR(160) DEFAULT NULL,
  rol           ENUM('docente','admin','alumno') NOT NULL DEFAULT 'docente',
  creado_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Grupos / clases del docente (con su materia)
CREATE TABLE IF NOT EXISTS grupos (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nombre     VARCHAR(120) NOT NULL,
  materia    VARCHAR(120) DEFAULT NULL,
  creado_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_grupos_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seccion: Documentos
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

-- Seccion: Notas
CREATE TABLE IF NOT EXISTS notas (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id     INT NOT NULL,
  titulo         VARCHAR(180) NOT NULL,
  contenido      TEXT DEFAULT NULL,
  creado_en      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_notas_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seccion: Material didactico (puede compartirse con un grupo)
CREATE TABLE IF NOT EXISTS materiales (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id  INT NOT NULL,
  grupo_id    INT DEFAULT NULL,
  titulo      VARCHAR(180) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  archivo     VARCHAR(255) DEFAULT NULL,
  materia     VARCHAR(120) DEFAULT NULL,
  creado_en   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_materiales_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_materiales_grupo
    FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Seccion: Tareas (pueden compartirse con un grupo)
CREATE TABLE IF NOT EXISTS tareas (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id    INT NOT NULL,
  grupo_id      INT DEFAULT NULL,
  titulo        VARCHAR(180) NOT NULL,
  descripcion   TEXT DEFAULT NULL,
  fecha_entrega DATE DEFAULT NULL,
  estado        ENUM('pendiente','en_progreso','completada') NOT NULL DEFAULT 'pendiente',
  creado_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tareas_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_tareas_grupo
    FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Lista de asistencia: alumnos (nombres) y registros
CREATE TABLE IF NOT EXISTS alumnos (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  grupo_id  INT NOT NULL,
  nombre    VARCHAR(160) NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_alumnos_grupo
    FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS asistencias (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  alumno_id INT NOT NULL,
  fecha     DATE NOT NULL,
  estado    ENUM('asistencia','falta','retardo') NOT NULL,
  CONSTRAINT fk_asistencias_alumno
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
  UNIQUE KEY uk_alumno_fecha (alumno_id, fecha)
) ENGINE=InnoDB;

-- Inscripciones de alumnos (usuarios) a grupos, por invitacion del docente
CREATE TABLE IF NOT EXISTS inscripciones (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  grupo_id  INT NOT NULL,
  alumno_id INT NOT NULL,
  estado    ENUM('invitado','aceptado','rechazado') NOT NULL DEFAULT 'invitado',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_grupo_alumno (grupo_id, alumno_id),
  CONSTRAINT fk_insc_grupo  FOREIGN KEY (grupo_id)  REFERENCES grupos(id)   ON DELETE CASCADE,
  CONSTRAINT fk_insc_alumno FOREIGN KEY (alumno_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Notificaciones dentro de la plataforma
CREATE TABLE IF NOT EXISTS notificaciones (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  mensaje    VARCHAR(255) NOT NULL,
  url        VARCHAR(255) DEFAULT NULL,
  leida      TINYINT(1) NOT NULL DEFAULT 0,
  creado_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notif_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;
