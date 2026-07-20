<?php
/* EduFolio - Logica de la seccion Material didactico (Fase 2). */

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/archivos.php';

/* Lista el material de un usuario (mas reciente primero), con busqueda opcional. */
function materiales_listar(int $usuario_id, string $q = ''): array
{
    $base = "SELECT m.id, m.titulo, m.descripcion, m.archivo, m.materia, m.creado_en,
                    m.grupo_id, g.nombre AS grupo_nombre
               FROM materiales m LEFT JOIN grupos g ON g.id = m.grupo_id
              WHERE m.usuario_id = ?";
    if ($q !== '') {
        $like = '%' . $q . '%';
        $stmt = db()->prepare("$base AND (m.titulo LIKE ? OR m.descripcion LIKE ? OR m.materia LIKE ?) ORDER BY m.creado_en DESC");
        $stmt->execute([$usuario_id, $like, $like, $like]);
    } else {
        $stmt = db()->prepare("$base ORDER BY m.creado_en DESC");
        $stmt->execute([$usuario_id]);
    }
    return $stmt->fetchAll();
}

/* Material compartido con un grupo (vista del alumno). */
function materiales_de_grupo(int $grupo_id): array
{
    $stmt = db()->prepare(
        'SELECT id, titulo, descripcion, archivo, materia, creado_en
           FROM materiales WHERE grupo_id = ? ORDER BY creado_en DESC'
    );
    $stmt->execute([$grupo_id]);
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
function materiales_crear(int $usuario_id, string $titulo, ?string $materia, ?string $descripcion, array $file, ?int $grupo_id = null): array
{
    if (trim($titulo) === '') {
        return [false, 'El titulo es obligatorio.'];
    }
    $sub = guardar_archivo($file, $usuario_id);
    if (!$sub['ok']) {
        return [false, $sub['mensaje']];
    }
    $grupo_id = $grupo_id ?: null;
    $stmt = db()->prepare(
        'INSERT INTO materiales (usuario_id, grupo_id, titulo, descripcion, archivo, materia)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$usuario_id, $grupo_id, trim($titulo), $descripcion ?: null, $sub['archivo'], $materia ?: null]);
    return [true, 'Material subido correctamente.'];
}

/* Actualiza los datos (titulo/materia/descripcion) de un material propio. */
function materiales_actualizar(int $id, int $usuario_id, string $titulo, ?string $materia, ?string $descripcion, ?int $grupo_id = null): array
{
    if (trim($titulo) === '') {
        return [false, 'El titulo es obligatorio.'];
    }
    $grupo_id = $grupo_id ?: null;
    $stmt = db()->prepare(
        'UPDATE materiales SET titulo = ?, materia = ?, descripcion = ?, grupo_id = ? WHERE id = ? AND usuario_id = ?'
    );
    $stmt->execute([trim($titulo), $materia ?: null, $descripcion ?: null, $grupo_id, $id, $usuario_id]);
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
