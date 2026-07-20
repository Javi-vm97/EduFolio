<?php
/* EduFolio - Vista de una clase para el alumno (tareas y material compartidos). */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/inscripciones.php';
require_once __DIR__ . '/../app/tareas.php';
require_once __DIR__ . '/../app/materiales.php';
requerir_alumno();

$u   = usuario_actual();
$uid = (int)$u['id'];

$grupo_id = (int)($_GET['grupo'] ?? 0);
if (!$grupo_id || !alumno_en_grupo($uid, $grupo_id)) {
    flash('error', 'No perteneces a esa clase.');
    redirigir('dashboard.php');
}

$stmt = db()->prepare(
    'SELECT g.nombre, g.materia, u.nombre AS docente_nombre, u.apellidos AS docente_apellidos
       FROM grupos g JOIN usuarios u ON u.id = g.usuario_id WHERE g.id = ?'
);
$stmt->execute([$grupo_id]);
$grupo = $stmt->fetch();

$tareas     = tareas_de_grupo($grupo_id);
$materiales = materiales_de_grupo($grupo_id);
$hoy        = date('Y-m-d');

$titulo    = $grupo['nombre'];
$vista_app = true;
require __DIR__ . '/../app/layout/header.php';
?>
<p class="reveal"><a class="auth-volver" href="dashboard.php"><i class="bi bi-arrow-left"></i> Mis clases</a></p>
<div class="seccion-encabezado reveal">
    <div class="ic bg-indigo"><i class="bi bi-journal-bookmark-fill"></i></div>
    <div>
        <h1><?= e($grupo['nombre']) ?><?= $grupo['materia'] ? ' — ' . e($grupo['materia']) : '' ?></h1>
        <p>Docente: <?= e($grupo['docente_nombre'] . ' ' . $grupo['docente_apellidos']) ?></p>
    </div>
</div>

<section class="form-card reveal">
    <h3><i class="bi bi-check2-square"></i> Tareas</h3>
    <?php if (!$tareas): ?>
        <div class="vacio"><i class="bi bi-check2-circle"></i><p>No hay tareas asignadas por ahora.</p></div>
    <?php else: ?>
        <div class="lista-items">
            <?php foreach ($tareas as $t):
                $vencida = $t['fecha_entrega'] && $t['fecha_entrega'] < $hoy; ?>
                <article class="item-card">
                    <div class="item-icono bg-morado"><i class="bi bi-check2-square"></i></div>
                    <div class="item-cuerpo">
                        <h4><?= e($t['titulo']) ?></h4>
                        <?php if ($t['descripcion']): ?><p class="item-texto"><?= e($t['descripcion']) ?></p><?php endif; ?>
                        <?php if ($t['fecha_entrega']): ?>
                            <span class="item-meta <?= $vencida ? 'vencida' : '' ?>"><i class="bi bi-calendar-event"></i>
                                Entrega: <?= e(date('d/m/Y', strtotime($t['fecha_entrega']))) ?><?= $vencida ? ' (vencida)' : '' ?></span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="form-card reveal">
    <h3><i class="bi bi-easel2-fill"></i> Material didactico</h3>
    <?php if (!$materiales): ?>
        <div class="vacio"><i class="bi bi-easel"></i><p>Tu docente aun no ha compartido material.</p></div>
    <?php else: ?>
        <div class="lista-items">
            <?php foreach ($materiales as $m): ?>
                <article class="item-card">
                    <div class="item-icono bg-cian"><i class="bi bi-file-earmark-arrow-down"></i></div>
                    <div class="item-cuerpo">
                        <h4><?= e($m['titulo']) ?></h4>
                        <?php if ($m['descripcion']): ?><p class="item-texto"><?= e($m['descripcion']) ?></p><?php endif; ?>
                        <span class="item-meta">
                            <?php if ($m['materia']): ?><span class="badge badge-cian"><?= e($m['materia']) ?></span><?php endif; ?>
                            <i class="bi bi-clock"></i> <?= e(date('d/m/Y', strtotime($m['creado_en']))) ?>
                        </span>
                    </div>
                    <div class="item-acciones">
                        <a class="btn btn-sm btn-primario" href="descargar.php?tipo=material&id=<?= (int)$m['id'] ?>"><i class="bi bi-download"></i> Descargar</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
