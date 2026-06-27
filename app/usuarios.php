<?php
/* EduFolio - Logica de administracion de usuarios (Fase 3). */

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/archivos.php';

/* Estadisticas globales del sistema (solo para administradores). */
function admin_estadisticas(): array
{
    $db = db();
    return [
        'usuarios'   => (int)$db->query('SELECT COUNT(*) FROM usuarios')->fetchColumn(),
        'documentos' => (int)$db->query('SELECT COUNT(*) FROM documentos')->fetchColumn(),
        'notas'      => (int)$db->query('SELECT COUNT(*) FROM notas')->fetchColumn(),
        'materiales' => (int)$db->query('SELECT COUNT(*) FROM materiales')->fetchColumn(),
        'tareas'     => (int)$db->query('SELECT COUNT(*) FROM tareas')->fetchColumn(),
    ];
}

/* Lista de usuarios con el conteo de contenidos de cada uno. */
function admin_listar_usuarios(): array
{
    $sql = "SELECT u.id, u.nombre, u.apellidos, u.email, u.institucion, u.rol, u.creado_en,
                   (SELECT COUNT(*) FROM documentos d WHERE d.usuario_id = u.id) AS docs,
                   (SELECT COUNT(*) FROM notas n      WHERE n.usuario_id = u.id) AS notas,
                   (SELECT COUNT(*) FROM materiales m WHERE m.usuario_id = u.id) AS materiales,
                   (SELECT COUNT(*) FROM tareas t     WHERE t.usuario_id = u.id) AS tareas
              FROM usuarios u
          ORDER BY u.creado_en DESC";
    return db()->query($sql)->fetchAll();
}

/* Elimina un usuario, sus archivos y (por cascada) todo su contenido. */
function admin_eliminar_usuario(int $id): void
{
    eliminar_carpeta_usuario($id);
    $stmt = db()->prepare('DELETE FROM usuarios WHERE id = ?');
    $stmt->execute([$id]);
}
