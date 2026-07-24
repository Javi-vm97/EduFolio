-- EduFolio - Actualizar base de datos de PRODUCCION (InfinityFree) al ultimo estado.
-- Seguro de ejecutar una vez sobre la base existente (MariaDB 10.x). Idempotente:
-- crea lo que falte y agrega columnas solo si no existen.

-- Tablas de Lista de asistencia (si no existen)
CREATE TABLE IF NOT EXISTS grupos (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nombre     VARCHAR(120) NOT NULL,
  materia    VARCHAR(120) DEFAULT NULL,
  creado_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_grupos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS alumnos (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  grupo_id  INT NOT NULL,
  nombre    VARCHAR(160) NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_alumnos_grupo FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS asistencias (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  alumno_id INT NOT NULL,
  fecha     DATE NOT NULL,
  estado    ENUM('asistencia','falta','retardo') NOT NULL,
  CONSTRAINT fk_asistencias_alumno FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
  UNIQUE KEY uk_alumno_fecha (alumno_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tablas del modulo de Alumnos (si no existen)
CREATE TABLE IF NOT EXISTS inscripciones (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  grupo_id  INT NOT NULL,
  alumno_id INT NOT NULL,
  estado    ENUM('invitado','aceptado','rechazado') NOT NULL DEFAULT 'invitado',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_grupo_alumno (grupo_id, alumno_id),
  CONSTRAINT fk_insc_grupo  FOREIGN KEY (grupo_id)  REFERENCES grupos(id)   ON DELETE CASCADE,
  CONSTRAINT fk_insc_alumno FOREIGN KEY (alumno_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notificaciones (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  mensaje    VARCHAR(255) NOT NULL,
  url        VARCHAR(255) DEFAULT NULL,
  leida      TINYINT(1) NOT NULL DEFAULT 0,
  creado_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notif_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Columnas nuevas en tablas que ya existian
ALTER TABLE usuarios   MODIFY rol ENUM('docente','admin','alumno') NOT NULL DEFAULT 'docente';
ALTER TABLE grupos     ADD COLUMN IF NOT EXISTS materia  VARCHAR(120) DEFAULT NULL;
ALTER TABLE tareas     ADD COLUMN IF NOT EXISTS grupo_id INT DEFAULT NULL;
ALTER TABLE materiales ADD COLUMN IF NOT EXISTS grupo_id INT DEFAULT NULL;
