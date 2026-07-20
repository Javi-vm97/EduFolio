-- EduFolio - Migracion: rol alumno, inscripciones a grupos y notificaciones.
-- Ejecutar sobre una base existente (local o produccion) para actualizarla.

-- 1. Agregar el rol 'alumno'
ALTER TABLE usuarios
  MODIFY rol ENUM('docente','admin','alumno') NOT NULL DEFAULT 'docente';

-- 2. Materia (asignatura) del grupo
ALTER TABLE grupos
  ADD COLUMN materia VARCHAR(120) DEFAULT NULL AFTER nombre;

-- 3. Inscripciones de alumnos a grupos (por invitacion del docente)
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

-- 4. Notificaciones dentro de la plataforma
CREATE TABLE IF NOT EXISTS notificaciones (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  mensaje    VARCHAR(255) NOT NULL,
  url        VARCHAR(255) DEFAULT NULL,
  leida      TINYINT(1) NOT NULL DEFAULT 0,
  creado_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notif_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Compartir tareas y material con un grupo (visibles para sus alumnos)
ALTER TABLE tareas
  ADD COLUMN grupo_id INT DEFAULT NULL AFTER usuario_id,
  ADD CONSTRAINT fk_tareas_grupo FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE SET NULL;

ALTER TABLE materiales
  ADD COLUMN grupo_id INT DEFAULT NULL AFTER usuario_id,
  ADD CONSTRAINT fk_materiales_grupo FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE SET NULL;
