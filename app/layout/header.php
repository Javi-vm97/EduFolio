<?php
/* EduFolio - Cabecera HTML comun. Variables: $titulo (string), $vista_app (bool), $cuerpo_clase (string opcional). */
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../notificaciones.php';

$titulo       = $titulo ?? APP_NAME;
$vista_app    = $vista_app ?? false;
$cuerpo_clase = $cuerpo_clase ?? ($vista_app ? 'con-panel' : 'publica');
$u            = usuario_actual();
$noLeidas     = ($vista_app && $u) ? notif_no_leidas((int)$u['id']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> · <?= APP_NAME ?></title>
    <meta name="description" content="<?= APP_DESC ?>: organiza y resguarda tus documentos, notas, material didactico y tareas en un solo lugar.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= e($cuerpo_clase) ?> cargando">

<!-- Preloader / efecto de carga -->
<div id="preloader">
    <div class="preloader-caja">
        <img class="preloader-logo" src="img/logo.png" alt="<?= APP_NAME ?>">
        <div class="preloader-spin"></div>
        <div class="preloader-puntos"><span></span><span></span><span></span></div>
    </div>
</div>
<noscript><style>#preloader{display:none!important}body.cargando{overflow:auto!important}</style></noscript>

<?php if ($vista_app && $u): ?>
<header class="topbar">
    <button class="menu-toggle" id="menuToggle" aria-label="Menu"><i class="bi bi-list"></i></button>
    <a class="marca" href="dashboard.php">
        <img class="marca-img" src="img/logo.png" alt="<?= APP_NAME ?>">
    </a>
    <div class="topbar-derecha">
        <a class="campana" href="notificaciones.php" title="Notificaciones">
            <i class="bi bi-bell"></i>
            <?php if ($noLeidas > 0): ?><span class="campana-num"><?= $noLeidas > 9 ? '9+' : $noLeidas ?></span><?php endif; ?>
        </a>
        <span class="usuario-chip">
            <span class="av"></span>
            <?= e($u['nombre'] . ' ' . $u['apellidos']) ?>
        </span>
        <a class="btn btn-outline btn-sm" href="logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a>
    </div>
</header>
<div class="layout-app">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="contenido">
<?php endif; ?>

<?php
$flashes = obtener_flash();
foreach ($flashes as $f):
?>
    <div class="alerta alerta-<?= e($f['tipo']) ?> <?= $vista_app ? '' : 'flash-flotante' ?>" data-flash><?= e($f['mensaje']) ?></div>
<?php endforeach; ?>
