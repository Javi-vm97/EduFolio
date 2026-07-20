<?php
/* EduFolio - Notificaciones dentro de la plataforma. */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/* Crea una notificacion para un usuario. */
function notif_crear(int $usuario_id, string $mensaje, ?string $url = null): void
{
    $stmt = db()->prepare('INSERT INTO notificaciones (usuario_id, mensaje, url) VALUES (?, ?, ?)');
    $stmt->execute([$usuario_id, $mensaje, $url]);
}

/* Lista las notificaciones de un usuario (recientes primero). */
function notif_listar(int $usuario_id, int $limite = 30): array
{
    $limite = max(1, min(100, $limite));
    $stmt = db()->prepare(
        'SELECT id, mensaje, url, leida, creado_en FROM notificaciones
          WHERE usuario_id = ? ORDER BY creado_en DESC LIMIT ' . $limite
    );
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll();
}

/* Cuenta las notificaciones no leidas. */
function notif_no_leidas(int $usuario_id): int
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM notificaciones WHERE usuario_id = ? AND leida = 0');
    $stmt->execute([$usuario_id]);
    return (int)$stmt->fetchColumn();
}

/* Marca todas las notificaciones del usuario como leidas. */
function notif_marcar_leidas(int $usuario_id): void
{
    $stmt = db()->prepare('UPDATE notificaciones SET leida = 1 WHERE usuario_id = ? AND leida = 0');
    $stmt->execute([$usuario_id]);
}
