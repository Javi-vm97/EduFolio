<?php
/* EduFolio - Panel principal (dashboard) del docente. */
require_once __DIR__ . '/../app/auth.php';
requerir_login();

$u = usuario_actual();

$tarjetas = [
    ['documentos.php', 'Documentos',         'Sube y resguarda tus archivos institucionales.', 'bi-folder-fill',     'bg-indigo'],
    ['notas.php',      'Notas',              'Apuntes y recordatorios rapidos.',                'bi-journal-text',    'bg-naranja'],
    ['material.php',   'Material didactico', 'Recursos organizados por materia.',               'bi-easel2-fill',     'bg-cian'],
    ['tareas.php',     'Tareas',             'Da seguimiento a actividades y entregas.',        'bi-check2-square',   'bg-morado'],
];

$titulo    = 'Inicio';
$vista_app = true;
require __DIR__ . '/../app/layout/header.php';
?>
<div class="bienvenida reveal">
    <h1>Hola, <?= e($u['nombre']) ?> 👋</h1>
    <p>Este es tu portafolio. Selecciona una seccion para comenzar.</p>
</div>

<div class="grid-cards">
    <?php foreach ($tarjetas as $i => [$ruta, $titulo_c, $desc, $icono, $color]): ?>
        <a class="card-seccion reveal d<?= $i ?>" href="<?= e($ruta) ?>">
            <div class="ic <?= e($color) ?>"><i class="bi <?= e($icono) ?>"></i></div>
            <h2><?= e($titulo_c) ?></h2>
            <p><?= e($desc) ?></p>
            <span class="card-link">Abrir <i class="bi bi-arrow-right"></i></span>
        </a>
    <?php endforeach; ?>
</div>

<section class="panel-info reveal">
    <h3>Tu cuenta</h3>
    <ul class="lista-datos">
        <li><span>Nombre</span><strong><?= e($u['nombre'] . ' ' . $u['apellidos']) ?></strong></li>
        <li><span>Correo</span><strong><?= e($u['email']) ?></strong></li>
        <li><span>Institucion</span><strong><?= e($u['institucion'] ?: 'No especificada') ?></strong></li>
        <li><span>Miembro desde</span><strong><?= e(date('d/m/Y', strtotime($u['creado_en']))) ?></strong></li>
    </ul>
</section>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
