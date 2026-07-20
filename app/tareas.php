<?php
/* EduFolio - Logica de la seccion Tareas (Fase 2). */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

const TAREA_ESTADOS = ['pendiente', 'en_progreso', 'completada'];

/* Etiqueta legible de un estado. */
function tarea_estado_texto(string $estado): string
{
    $map = ['pendiente' => 'Pendiente', 'en_progreso' => 'En progreso', 'completada' => 'Completada'];
    return $map[$estado] ?? $estado;
}

/* Lista las tareas de un usuario (pendientes primero, luego por fecha), con busqueda opcional. */
function tareas_listar(int $usuario_id, string $q = ''): array
{
    $orden = "ORDER BY FIELD(t.estado,'pendiente','en_progreso','completada'),
                       (t.fecha_entrega IS NULL), t.fecha_entrega ASC, t.creado_en DESC";
    $base = "SELECT t.id, t.titulo, t.descripcion, t.fecha_entrega, t.estado, t.creado_en,
                    t.grupo_id, g.nombre AS grupo_nombre
               FROM tareas t LEFT JOIN grupos g ON g.id = t.grupo_id
              WHERE t.usuario_id = ?";
    if ($q !== '') {
        $like = '%' . $q . '%';
        $stmt = db()->prepare("$base AND (t.titulo LIKE ? OR t.descripcion LIKE ?) $orden");
        $stmt->execute([$usuario_id, $like, $like]);
    } else {
        $stmt = db()->prepare("$base $orden");
        $stmt->execute([$usuario_id]);
    }
    return $stmt->fetchAll();
}

/* Tareas asignadas a un grupo (vista del alumno). */
function tareas_de_grupo(int $grupo_id): array
{
    $stmt = db()->prepare(
        "SELECT id, titulo, descripcion, fecha_entrega, estado, creado_en
           FROM tareas WHERE grupo_id = ?
          ORDER BY (fecha_entrega IS NULL), fecha_entrega ASC, creado_en DESC"
    );
    $stmt->execute([$grupo_id]);
    return $stmt->fetchAll();
}

/* Obtiene una tarea propia (o null). */
function tareas_obtener(int $id, int $usuario_id): ?array
{
    $stmt = db()->prepare('SELECT * FROM tareas WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario_id]);
    return $stmt->fetch() ?: null;
}

/* Crea una tarea. Devuelve [ok, mensaje]. */
function tareas_crear(int $usuario_id, string $titulo, ?string $descripcion, ?string $fecha, string $estado, ?int $grupo_id = null): array
{
    if (trim($titulo) === '') {
        return [false, 'El titulo es obligatorio.'];
    }
    if (!in_array($estado, TAREA_ESTADOS, true)) {
        $estado = 'pendiente';
    }
    $fecha = ($fecha !== null && trim($fecha) !== '') ? $fecha : null;
    $grupo_id = $grupo_id ?: null;

    $stmt = db()->prepare(
        'INSERT INTO tareas (usuario_id, grupo_id, titulo, descripcion, fecha_entrega, estado)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$usuario_id, $grupo_id, trim($titulo), $descripcion ?: null, $fecha, $estado]);
    return [true, 'Tarea creada.'];
}

/* Actualiza todos los datos de una tarea propia. Devuelve [ok, mensaje]. */
function tareas_actualizar(int $id, int $usuario_id, string $titulo, ?string $descripcion, ?string $fecha, string $estado, ?int $grupo_id = null): array
{
    if (trim($titulo) === '') {
        return [false, 'El titulo es obligatorio.'];
    }
    if (!in_array($estado, TAREA_ESTADOS, true)) {
        $estado = 'pendiente';
    }
    $fecha = ($fecha !== null && trim($fecha) !== '') ? $fecha : null;
    $grupo_id = $grupo_id ?: null;
    $stmt = db()->prepare(
        'UPDATE tareas SET titulo = ?, descripcion = ?, fecha_entrega = ?, estado = ?, grupo_id = ? WHERE id = ? AND usuario_id = ?'
    );
    $stmt->execute([trim($titulo), $descripcion ?: null, $fecha, $estado, $grupo_id, $id, $usuario_id]);
    return [true, 'Tarea actualizada.'];
}

/* Cambia el estado de una tarea propia. */
function tareas_cambiar_estado(int $id, int $usuario_id, string $estado): void
{
    if (!in_array($estado, TAREA_ESTADOS, true)) {
        return;
    }
    $stmt = db()->prepare('UPDATE tareas SET estado = ? WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$estado, $id, $usuario_id]);
}

/* Elimina una tarea propia. */
function tareas_eliminar(int $id, int $usuario_id): void
{
    $stmt = db()->prepare('DELETE FROM tareas WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario_id]);
}

/* Cuenta las tareas pendientes/en progreso del usuario. */
function tareas_contar(int $usuario_id): int
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM tareas WHERE usuario_id = ?');
    $stmt->execute([$usuario_id]);
    return (int)$stmt->fetchColumn();
}
