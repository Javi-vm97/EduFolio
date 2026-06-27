<?php
/* EduFolio - Logica de la seccion Notas (Fase 2). */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/* Lista las notas de un usuario (mas recientes primero), con busqueda opcional. */
function notas_listar(int $usuario_id, string $q = ''): array
{
    if ($q !== '') {
        $like = '%' . $q . '%';
        $stmt = db()->prepare(
            'SELECT id, titulo, contenido, creado_en, actualizado_en
               FROM notas WHERE usuario_id = ? AND (titulo LIKE ? OR contenido LIKE ?)
              ORDER BY actualizado_en DESC'
        );
        $stmt->execute([$usuario_id, $like, $like]);
    } else {
        $stmt = db()->prepare(
            'SELECT id, titulo, contenido, creado_en, actualizado_en
               FROM notas WHERE usuario_id = ? ORDER BY actualizado_en DESC'
        );
        $stmt->execute([$usuario_id]);
    }
    return $stmt->fetchAll();
}

/* Obtiene una nota propia del usuario (o null). */
function notas_obtener(int $id, int $usuario_id): ?array
{
    $stmt = db()->prepare('SELECT * FROM notas WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario_id]);
    return $stmt->fetch() ?: null;
}

/* Crea una nota. */
function notas_crear(int $usuario_id, string $titulo, string $contenido): void
{
    $stmt = db()->prepare(
        'INSERT INTO notas (usuario_id, titulo, contenido) VALUES (?, ?, ?)'
    );
    $stmt->execute([$usuario_id, $titulo, $contenido]);
}

/* Actualiza una nota propia. */
function notas_actualizar(int $id, int $usuario_id, string $titulo, string $contenido): void
{
    $stmt = db()->prepare(
        'UPDATE notas SET titulo = ?, contenido = ? WHERE id = ? AND usuario_id = ?'
    );
    $stmt->execute([$titulo, $contenido, $id, $usuario_id]);
}

/* Elimina una nota propia. */
function notas_eliminar(int $id, int $usuario_id): void
{
    $stmt = db()->prepare('DELETE FROM notas WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario_id]);
}

/* Cuenta las notas del usuario. */
function notas_contar(int $usuario_id): int
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM notas WHERE usuario_id = ?');
    $stmt->execute([$usuario_id]);
    return (int)$stmt->fetchColumn();
}
