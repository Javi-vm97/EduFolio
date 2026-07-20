<?php
/* EduFolio - Logica del modulo Lista de asistencia (grupos, alumnos, asistencias). */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

const ASISTENCIA_ESTADOS = ['asistencia', 'falta', 'retardo'];

/* Etiqueta legible de un estado de asistencia. */
function asistencia_estado_texto(string $estado): string
{
    $map = ['asistencia' => 'Asistencia', 'falta' => 'Falta', 'retardo' => 'Retardo'];
    return $map[$estado] ?? $estado;
}

/* Abreviatura de un estado (para el Excel). */
function asistencia_estado_abrev(string $estado): string
{
    $map = ['asistencia' => 'A', 'falta' => 'F', 'retardo' => 'R'];
    return $map[$estado] ?? '';
}

/* ---------- Grupos ---------- */

function grupos_listar(int $usuario_id): array
{
    $stmt = db()->prepare(
        'SELECT g.id, g.nombre, g.materia, g.creado_en,
                (SELECT COUNT(*) FROM alumnos a WHERE a.grupo_id = g.id) AS alumnos,
                (SELECT COUNT(*) FROM inscripciones i WHERE i.grupo_id = g.id AND i.estado = "aceptado") AS inscritos
           FROM grupos g WHERE g.usuario_id = ? ORDER BY g.nombre'
    );
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll();
}

function grupos_obtener(int $id, int $usuario_id): ?array
{
    $stmt = db()->prepare('SELECT * FROM grupos WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario_id]);
    return $stmt->fetch() ?: null;
}

function grupos_crear(int $usuario_id, string $nombre, ?string $materia = null): array
{
    if (trim($nombre) === '') {
        return [false, 'El nombre del grupo es obligatorio.'];
    }
    $materia = ($materia !== null && trim($materia) !== '') ? trim($materia) : null;
    $stmt = db()->prepare('INSERT INTO grupos (usuario_id, nombre, materia) VALUES (?, ?, ?)');
    $stmt->execute([$usuario_id, trim($nombre), $materia]);
    return [true, 'Grupo creado.'];
}

function grupos_eliminar(int $id, int $usuario_id): void
{
    $stmt = db()->prepare('DELETE FROM grupos WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario_id]);
}

function grupos_contar(int $usuario_id): int
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM grupos WHERE usuario_id = ?');
    $stmt->execute([$usuario_id]);
    return (int)$stmt->fetchColumn();
}

/* ---------- Alumnos ---------- */

function alumnos_listar(int $grupo_id): array
{
    $stmt = db()->prepare('SELECT id, nombre FROM alumnos WHERE grupo_id = ? ORDER BY nombre');
    $stmt->execute([$grupo_id]);
    return $stmt->fetchAll();
}

function alumnos_crear(int $grupo_id, string $nombre): array
{
    if (trim($nombre) === '') {
        return [false, 'El nombre del alumno es obligatorio.'];
    }
    $stmt = db()->prepare('INSERT INTO alumnos (grupo_id, nombre) VALUES (?, ?)');
    $stmt->execute([$grupo_id, trim($nombre)]);
    return [true, 'Alumno agregado.'];
}

function alumnos_eliminar(int $id, int $grupo_id): void
{
    $stmt = db()->prepare('DELETE FROM alumnos WHERE id = ? AND grupo_id = ?');
    $stmt->execute([$id, $grupo_id]);
}

/* ---------- Asistencias ---------- */

/* Estados registrados de un grupo en una fecha: [alumno_id => estado]. */
function asistencia_del_dia(int $grupo_id, string $fecha): array
{
    $stmt = db()->prepare(
        'SELECT a.alumno_id, a.estado
           FROM asistencias a JOIN alumnos al ON al.id = a.alumno_id
          WHERE al.grupo_id = ? AND a.fecha = ?'
    );
    $stmt->execute([$grupo_id, $fecha]);
    $out = [];
    foreach ($stmt->fetchAll() as $r) {
        $out[(int)$r['alumno_id']] = $r['estado'];
    }
    return $out;
}

/* Guarda (o actualiza) la asistencia de una fecha. $estados = [alumno_id => estado]. */
function asistencia_guardar(int $grupo_id, string $fecha, array $estados): void
{
    $validos = [];
    foreach (alumnos_listar($grupo_id) as $al) {
        $validos[] = (int)$al['id'];
    }
    $stmt = db()->prepare(
        'INSERT INTO asistencias (alumno_id, fecha, estado) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE estado = VALUES(estado)'
    );
    foreach ($estados as $alumno_id => $estado) {
        $alumno_id = (int)$alumno_id;
        if (!in_array($alumno_id, $validos, true)) {
            continue;
        }
        if (!in_array($estado, ASISTENCIA_ESTADOS, true)) {
            continue;
        }
        $stmt->execute([$alumno_id, $fecha, $estado]);
    }
}

/* Fechas distintas con registro en el grupo (ordenadas). */
function asistencia_fechas(int $grupo_id): array
{
    $stmt = db()->prepare(
        'SELECT DISTINCT a.fecha
           FROM asistencias a JOIN alumnos al ON al.id = a.alumno_id
          WHERE al.grupo_id = ? ORDER BY a.fecha'
    );
    $stmt->execute([$grupo_id]);
    $out = [];
    foreach ($stmt->fetchAll() as $r) {
        $out[] = $r['fecha'];
    }
    return $out;
}

/* Matriz de estados: [alumno_id][fecha] => estado. */
function asistencia_matriz(int $grupo_id): array
{
    $stmt = db()->prepare(
        'SELECT a.alumno_id, a.fecha, a.estado
           FROM asistencias a JOIN alumnos al ON al.id = a.alumno_id
          WHERE al.grupo_id = ?'
    );
    $stmt->execute([$grupo_id]);
    $out = [];
    foreach ($stmt->fetchAll() as $r) {
        $out[(int)$r['alumno_id']][$r['fecha']] = $r['estado'];
    }
    return $out;
}
