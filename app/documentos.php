<?php
/* EduFolio - Logica de la seccion Documentos (Fase 2). */

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/archivos.php';

/* Lista los documentos de un usuario (mas recientes primero), con busqueda opcional. */
function documentos_listar(int $usuario_id, string $q = ''): array
{
    if ($q !== '') {
        $like = '%' . $q . '%';
        $stmt = db()->prepare(
            'SELECT id, titulo, descripcion, archivo, tipo, creado_en
               FROM documentos WHERE usuario_id = ? AND (titulo LIKE ? OR descripcion LIKE ? OR tipo LIKE ?)
              ORDER BY creado_en DESC'
        );
        $stmt->execute([$usuario_id, $like, $like, $like]);
    } else {
        $stmt = db()->prepare(
            'SELECT id, titulo, descripcion, archivo, tipo, creado_en
               FROM documentos WHERE usuario_id = ? ORDER BY creado_en DESC'
        );
        $stmt->execute([$usuario_id]);
    }
    return $stmt->fetchAll();
}

/* Obtiene un documento propio (o null). */
function documentos_obtener(int $id, int $usuario_id): ?array
{
    $stmt = db()->prepare('SELECT * FROM documentos WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario_id]);
    return $stmt->fetch() ?: null;
}

/* Crea un documento subiendo su archivo. Devuelve [ok, mensaje]. */
function documentos_crear(int $usuario_id, string $titulo, ?string $descripcion, array $file): array
{
    if (trim($titulo) === '') {
        return [false, 'El titulo es obligatorio.'];
    }
    $sub = guardar_archivo($file, $usuario_id);
    if (!$sub['ok']) {
        return [false, $sub['mensaje']];
    }
    $stmt = db()->prepare(
        'INSERT INTO documentos (usuario_id, titulo, descripcion, archivo, tipo)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$usuario_id, trim($titulo), $descripcion ?: null, $sub['archivo'], $sub['tipo']]);
    return [true, 'Documento subido correctamente.'];
}

/* Actualiza los datos (titulo/descripcion) de un documento propio. */
function documentos_actualizar(int $id, int $usuario_id, string $titulo, ?string $descripcion): array
{
    if (trim($titulo) === '') {
        return [false, 'El titulo es obligatorio.'];
    }
    $stmt = db()->prepare(
        'UPDATE documentos SET titulo = ?, descripcion = ? WHERE id = ? AND usuario_id = ?'
    );
    $stmt->execute([trim($titulo), $descripcion ?: null, $id, $usuario_id]);
    return [true, 'Documento actualizado.'];
}

/* Elimina un documento propio y su archivo fisico. */
function documentos_eliminar(int $id, int $usuario_id): void
{
    $doc = documentos_obtener($id, $usuario_id);
    if (!$doc) {
        return;
    }
    eliminar_archivo_fisico($usuario_id, $doc['archivo']);
    $stmt = db()->prepare('DELETE FROM documentos WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario_id]);
}

/* Cuenta los documentos del usuario. */
function documentos_contar(int $usuario_id): int
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM documentos WHERE usuario_id = ?');
    $stmt->execute([$usuario_id]);
    return (int)$stmt->fetchColumn();
}
