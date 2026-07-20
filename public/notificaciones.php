<?php
/* EduFolio - Bandeja de notificaciones (todos los usuarios). */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/notificaciones.php';
requerir_login();

$u   = usuario_actual();
$uid = (int)$u['id'];

$items = notif_listar($uid);
notif_marcar_leidas($uid);   // al abrir la bandeja se marcan como leidas

$titulo    = 'Notificaciones';
$vista_app = true;
require __DIR__ . '/../app/layout/header.php';
?>
<div class="seccion-encabezado reveal">
    <div class="ic bg-naranja"><i class="bi bi-bell-fill"></i></div>
    <div><h1>Notificaciones</h1><p>Avisos e invitaciones dentro de la plataforma.</p></div>
</div>

<?php if (!$items): ?>
    <div class="vacio reveal"><i class="bi bi-bell-slash"></i><p>No tienes notificaciones.</p></div>
<?php else: ?>
    <div class="lista-items">
        <?php foreach ($items as $n): ?>
            <article class="item-card <?= !$n['leida'] ? 'notif-nueva' : '' ?>">
                <div class="item-icono <?= !$n['leida'] ? 'bg-indigo' : 'bg-cian' ?>"><i class="bi bi-<?= !$n['leida'] ? 'bell-fill' : 'bell' ?>"></i></div>
                <div class="item-cuerpo">
                    <p style="margin:0"><?= e($n['mensaje']) ?></p>
                    <span class="item-meta"><i class="bi bi-clock"></i> <?= e(date('d/m/Y H:i', strtotime($n['creado_en']))) ?></span>
                </div>
                <?php if (!empty($n['url'])): ?>
                    <div class="item-acciones">
                        <a class="btn btn-sm btn-outline" href="<?= e($n['url']) ?>">Ver <i class="bi bi-arrow-right"></i></a>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
