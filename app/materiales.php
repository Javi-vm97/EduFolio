<?php
/* EduFolio - Logica de la seccion Material didactico (Fase 2). */

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/archivos.php';

/* Lista el material de un usuario (mas reciente primero), con busqueda opcional. */
function materiales_listar(int $usuario_id, string $q = ''): array
{
    if ($q !== '') {
        $like = '%' . $q . '%';
        $stmt = db()->prepare(
            'SELECT id, titulo, descripcion, archivo, materia, creado_en
               FROM materiales WHERE usuario_id = ? AND (titulo LIKE ? OR descripcion LIKE ? OR materia LIKE ?)
              ORDER BY creado_en DESC'
        );
        $stmt->execute([$usuario_id, $like, $like, $like]);
    } else {
        $stmt = db()->prepare(
            'SELECT id, titulo, descripcion, archivo, materia, creado_en
               FROM materiales WHERE usuario_id = ? ORDER BY creado_en DESC'
        );
        $stmt->execute([$usuario_id]);
    }
    return $stmt->fetchAll();
}

/* Obtiene un material propio (o null). */
function materiales_obtener(int $id, int $usuario_id): ?array
{
    $stmt = db()->prepare('SELECT * FROM materiales WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario_id]);
    return $stmt->fetch() ?: null;
}

/* Crea un material subiendo su archivo. Devuelve [ok, mensaje]. */
function materiales_crear(int $usuario_id, string $titulo, ?string $materia, ?string $descripcion, array $file): array
{
    if (trim($titulo) === '') {
        return [false, 'El titulo es obligatorio.'];
    }
    $sub = guardar_archivo($file, $usuario_id);
    if (!$sub['ok']) {
        return [false, $sub['mensaje']];
    }
    $stmt = db()->prepare(
        'INSERT INTO materiales (usuario_id, titulo, descripcion, archivo, materia)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$usuario_id, trim($titulo), $descripcion ?: null, $sub['archivo'], $materia ?: null]);
    return [true, 'Material subido correctamente.'];
}

/* Actualiza los datos (titulo/materia/descripcion) de un material propio. */
function materiales_actualizar(int $id, int $usuario_id, string $titulo, ?string $materia, ?string $descripcion): array
{
    if (trim($titulo) === '') {
        return [false, 'El titulo es obligatorio.'];
    }
    $stmt = db()->prepare(
        'UPDATE materiales SET titulo = ?, materia = ?, descripcion = ? WHERE id = ? AND usuario_id = ?'
    );
    $stmt->execute([trim($titulo), $materia ?: null, $descripcion ?: null, $id, $usuario_id]);
    return [true, 'Material actualizado.'];
}

/* Elimina un material propio y su archivo fisico. */
function materiales_eliminar(int $id, int $usuario_id): void
{
    $mat = materiales_obtener($id, $usuario_id);
    if (!$mat) {
        return;
    }
    eliminar_archivo_fisico($usuario_id, $mat['archivo']);
    $stmt = db()->prepare('DELETE FROM materiales WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario_id]);
}

/* Cuenta el material del usuario. */
function materiales_contar(int $usuario_id): int
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM materiales WHERE usuario_id = ?');
    $stmt->execute([$usuario_id]);
    return (int)$stmt->fetchColumn();
}
