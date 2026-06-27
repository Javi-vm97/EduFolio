<?php
/* EduFolio - Panel principal (dashboard) del docente. */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/documentos.php';
require_once __DIR__ . '/../app/notas.php';
require_once __DIR__ . '/../app/materiales.php';
require_once __DIR__ . '/../app/tareas.php';
requerir_login();

$u   = usuario_actual();
$uid = (int)$u['id'];

$tarjetas = [
    ['documentos.php', 'Documentos',         'Sube y resguarda tus archivos institucionales.', 'documentos.svg', documentos_contar($uid)],
    ['notas.php',      'Notas',              'Apuntes y recordatorios rapidos.',                'notas.svg',      notas_contar($uid)],
    ['material.php',   'Material didactico', 'Recursos organizados por materia.',               'material.svg',   materiales_contar($uid)],
    ['tareas.php',     'Tareas',             'Da seguimiento a actividades y entregas.',        'tareas.svg',     tareas_contar($uid)],
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
    <?php foreach ($tarjetas as $i => [$ruta, $titulo_c, $desc, $icono, $total]): ?>
        <a class="card-seccion reveal d<?= $i ?>" href="<?= e($ruta) ?>">
            <div class="card-top">
                <div class="ic-img"><img src="assets/icons/<?= e($icono) ?>" alt=""></div>
                <span class="card-contador"><?= (int)$total ?></span>
            </div>
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
