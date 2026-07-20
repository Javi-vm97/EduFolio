<?php
/* EduFolio - Inscripciones de alumnos a grupos (invitaciones del docente). */

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/notificaciones.php';

/* El docente invita a un alumno (por correo) a un grupo. Devuelve [ok, mensaje]. */
function inscripcion_invitar(array $grupo, string $email): array
{
    $email = strtolower(trim($email));
    if ($email === '') {
        return [false, 'Escribe el correo del alumno.'];
    }
    // Busca un usuario alumno con ese correo
    $stmt = db()->prepare("SELECT id, nombre, apellidos FROM usuarios WHERE email = ? AND rol = 'alumno'");
    $stmt->execute([$email]);
    $alumno = $stmt->fetch();
    if (!$alumno) {
        return [false, 'No existe una cuenta de alumno con ese correo. El alumno debe registrarse primero.'];
    }

    // ¿Ya existe una inscripcion?
    $ex = db()->prepare('SELECT estado FROM inscripciones WHERE grupo_id = ? AND alumno_id = ?');
    $ex->execute([(int)$grupo['id'], (int)$alumno['id']]);
    $prev = $ex->fetch();
    if ($prev && $prev['estado'] === 'aceptado') {
        return [false, 'Ese alumno ya pertenece al grupo.'];
    }

    $stmt = db()->prepare(
        "INSERT INTO inscripciones (grupo_id, alumno_id, estado) VALUES (?, ?, 'invitado')
         ON DUPLICATE KEY UPDATE estado = 'invitado', creado_en = CURRENT_TIMESTAMP"
    );
    $stmt->execute([(int)$grupo['id'], (int)$alumno['id']]);

    $etiqueta = $grupo['nombre'] . ($grupo['materia'] ? ' (' . $grupo['materia'] . ')' : '');
    notif_crear((int)$alumno['id'], 'Te invitaron al grupo: ' . $etiqueta, 'dashboard.php');

    return [true, 'Invitacion enviada a ' . $alumno['nombre'] . '.'];
}

/* Inscripciones de un grupo (vista del docente). */
function inscripciones_por_grupo(int $grupo_id): array
{
    $stmt = db()->prepare(
        'SELECT i.id, i.estado, u.nombre, u.apellidos, u.email
           FROM inscripciones i JOIN usuarios u ON u.id = i.alumno_id
          WHERE i.grupo_id = ? ORDER BY u.nombre'
    );
    $stmt->execute([$grupo_id]);
    return $stmt->fetchAll();
}

/* El docente elimina una inscripcion (saca al alumno / cancela invitacion). */
function inscripcion_quitar(int $id, int $grupo_id): void
{
    $stmt = db()->prepare('DELETE FROM inscripciones WHERE id = ? AND grupo_id = ?');
    $stmt->execute([$id, $grupo_id]);
}

/* Invitaciones pendientes de un alumno. */
function invitaciones_del_alumno(int $alumno_id): array
{
    $stmt = db()->prepare(
        "SELECT i.id, g.nombre, g.materia, u.nombre AS docente_nombre, u.apellidos AS docente_apellidos
           FROM inscripciones i
           JOIN grupos g ON g.id = i.grupo_id
           JOIN usuarios u ON u.id = g.usuario_id
          WHERE i.alumno_id = ? AND i.estado = 'invitado' ORDER BY i.creado_en DESC"
    );
    $stmt->execute([$alumno_id]);
    return $stmt->fetchAll();
}

/* Grupos (clases) aceptados de un alumno. */
function clases_del_alumno(int $alumno_id): array
{
    $stmt = db()->prepare(
        "SELECT g.id, g.nombre, g.materia, u.nombre AS docente_nombre, u.apellidos AS docente_apellidos
           FROM inscripciones i
           JOIN grupos g ON g.id = i.grupo_id
           JOIN usuarios u ON u.id = g.usuario_id
          WHERE i.alumno_id = ? AND i.estado = 'aceptado' ORDER BY g.nombre"
    );
    $stmt->execute([$alumno_id]);
    return $stmt->fetchAll();
}

/* El alumno responde una invitacion: 'aceptado' o 'rechazado'. */
function inscripcion_responder(int $id, int $alumno_id, string $respuesta): void
{
    if (!in_array($respuesta, ['aceptado', 'rechazado'], true)) {
        return;
    }
    // Trae la inscripcion (validando que sea del alumno) y el grupo/docente
    $stmt = db()->prepare(
        'SELECT i.grupo_id, g.nombre, g.usuario_id AS docente_id,
                a.nombre AS alumno_nombre, a.apellidos AS alumno_apellidos
           FROM inscripciones i
           JOIN grupos g ON g.id = i.grupo_id
           JOIN usuarios a ON a.id = i.alumno_id
          WHERE i.id = ? AND i.alumno_id = ? AND i.estado = "invitado"'
    );
    $stmt->execute([$id, $alumno_id]);
    $insc = $stmt->fetch();
    if (!$insc) {
        return;
    }
    $up = db()->prepare('UPDATE inscripciones SET estado = ? WHERE id = ? AND alumno_id = ?');
    $up->execute([$respuesta, $id, $alumno_id]);

    $accion = $respuesta === 'aceptado' ? 'acepto unirse a' : 'rechazo la invitacion de';
    notif_crear(
        (int)$insc['docente_id'],
        $insc['alumno_nombre'] . ' ' . $insc['alumno_apellidos'] . ' ' . $accion . ' ' . $insc['nombre'] . '.',
        'asistencia.php?grupo=' . (int)$insc['grupo_id']
    );
}

/* Verifica si un alumno pertenece (aceptado) a un grupo. */
function alumno_en_grupo(int $alumno_id, int $grupo_id): bool
{
    $stmt = db()->prepare("SELECT 1 FROM inscripciones WHERE alumno_id = ? AND grupo_id = ? AND estado = 'aceptado'");
    $stmt->execute([$alumno_id, $grupo_id]);
    return (bool)$stmt->fetch();
}
