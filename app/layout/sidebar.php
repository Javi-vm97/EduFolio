<?php
/* EduFolio - Barra lateral de navegacion del panel. */
$actual = basename($_SERVER['SCRIPT_NAME'] ?? '');

$secciones = [
    'dashboard.php'  => ['Inicio',             'bi-grid-1x2-fill'],
    'documentos.php' => ['Documentos',         'bi-folder-fill'],
    'notas.php'      => ['Notas',              'bi-journal-text'],
    'material.php'   => ['Material didactico', 'bi-easel2-fill'],
    'tareas.php'     => ['Tareas',             'bi-check2-square'],
];
?>
<aside class="sidebar" id="sidebar">
    <nav>
        <ul class="nav-lista">
            <?php foreach ($secciones as $archivo => [$etiqueta, $icono]): ?>
                <li>
                    <a href="<?= e($archivo) ?>" class="nav-item <?= $actual === $archivo ? 'activo' : '' ?>">
                        <i class="bi <?= e($icono) ?>"></i>
                        <span><?= e($etiqueta) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="sidebar-pie">
        <span class="fase-badge">Fase 1</span>
        <small><?= APP_DESC ?></small>
    </div>
</aside>
