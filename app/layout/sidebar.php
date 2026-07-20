<?php
/* EduFolio - Barra lateral de navegacion del panel. */
$actual = basename($_SERVER['SCRIPT_NAME'] ?? '');

if (es_alumno()) {
    $secciones = ['dashboard.php' => ['Mis clases', 'bi-mortarboard-fill']];
} else {
    $secciones = [
        'dashboard.php'  => ['Inicio',             'bi-grid-1x2-fill'],
        'documentos.php' => ['Documentos',         'bi-folder-fill'],
        'notas.php'      => ['Notas',              'bi-journal-text'],
        'material.php'   => ['Material didactico', 'bi-easel2-fill'],
        'tareas.php'     => ['Tareas',             'bi-check2-square'],
    ];
    if (es_docente()) {
        $secciones['asistencia.php'] = ['Lista de asistencia', 'bi-clipboard-check'];
    }
}
$secciones['notificaciones.php'] = ['Notificaciones', 'bi-bell'];
$secciones['perfil.php']         = ['Mi perfil', 'bi-person-gear'];
if (es_admin()) {
    $secciones['admin.php'] = ['Administracion', 'bi-shield-lock'];
}
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
        <small><?= APP_DESC ?></small>
    </div>
</aside>
