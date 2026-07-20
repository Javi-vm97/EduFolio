<?php
/* EduFolio - Panel principal (dashboard). Se adapta al rol del usuario. */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/documentos.php';
require_once __DIR__ . '/../app/notas.php';
require_once __DIR__ . '/../app/materiales.php';
require_once __DIR__ . '/../app/tareas.php';
require_once __DIR__ . '/../app/asistencia.php';
require_once __DIR__ . '/../app/inscripciones.php';
requerir_login();

$u   = usuario_actual();
$uid = (int)$u['id'];

// Alumno: responder invitaciones a grupos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && es_alumno()) {
    verificar_csrf();
    if (($_POST['accion'] ?? '') === 'responder_invitacion') {
        $resp = ($_POST['respuesta'] ?? '') === 'aceptado' ? 'aceptado' : 'rechazado';
        inscripcion_responder((int)($_POST['id'] ?? 0), $uid, $resp);
        flash('exito', $resp === 'aceptado' ? 'Te uniste al grupo.' : 'Invitacion rechazada.');
    }
    redirigir('dashboard.php');
}

$titulo    = 'Inicio';
$vista_app = true;
require __DIR__ . '/../app/layout/header.php';
?>
<?php if (es_alumno()): ?>
    <?php
        $invitaciones = invitaciones_del_alumno($uid);
        $clases       = clases_del_alumno($uid);
    ?>
    <div class="bienvenida reveal">
        <h1>Hola, <?= e($u['nombre']) ?> 👋</h1>
        <p>Aqui ves tus clases y el contenido que tus docentes comparten contigo.</p>
    </div>

    <?php if ($invitaciones): ?>
        <section class="panel-info reveal" style="margin-bottom:1.8rem">
            <h3><i class="bi bi-envelope-paper"></i> Invitaciones pendientes</h3>
            <div class="lista-items">
                <?php foreach ($invitaciones as $inv): ?>
                    <article class="item-card">
                        <div class="item-icono bg-verde"><i class="bi bi-people-fill"></i></div>
                        <div class="item-cuerpo">
                            <h4><?= e($inv['nombre']) ?><?= $inv['materia'] ? ' — ' . e($inv['materia']) : '' ?></h4>
                            <span class="item-meta">Docente: <?= e($inv['docente_nombre'] . ' ' . $inv['docente_apellidos']) ?></span>
                        </div>
                        <div class="item-acciones">
                            <form method="post" action="dashboard.php">
                                <?= csrf_field() ?>
                                <input type="hidden" name="accion" value="responder_invitacion">
                                <input type="hidden" name="id" value="<?= (int)$inv['id'] ?>">
                                <input type="hidden" name="respuesta" value="aceptado">
                                <button class="btn btn-sm btn-primario" type="submit"><i class="bi bi-check2"></i> Aceptar</button>
                            </form>
                            <form method="post" action="dashboard.php">
                                <?= csrf_field() ?>
                                <input type="hidden" name="accion" value="responder_invitacion">
                                <input type="hidden" name="id" value="<?= (int)$inv['id'] ?>">
                                <input type="hidden" name="respuesta" value="rechazado">
                                <button class="btn btn-sm btn-outline" type="submit">Rechazar</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <div class="bienvenida reveal"><h2 style="font-size:1.3rem">Mis clases</h2></div>
    <?php if (!$clases): ?>
        <div class="vacio reveal"><i class="bi bi-mortarboard"></i><p>Aun no perteneces a ningun grupo. Cuando un docente te invite, apareceran aqui tus clases.</p></div>
    <?php else: ?>
        <div class="grid-cards">
            <?php foreach ($clases as $i => $c): ?>
                <a class="card-seccion reveal d<?= $i ?>" href="clase.php?grupo=<?= (int)$c['id'] ?>">
                    <div class="card-top"><div class="ic-img"><i class="bi bi-journal-bookmark-fill" style="font-size:1.7rem;color:var(--indigo)"></i></div></div>
                    <h2><?= e($c['nombre']) ?></h2>
                    <p><?= $c['materia'] ? e($c['materia']) . ' · ' : '' ?><?= e($c['docente_nombre'] . ' ' . $c['docente_apellidos']) ?></p>
                    <span class="card-link">Entrar <i class="bi bi-arrow-right"></i></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php else: ?>
    <?php
        $tarjetas = [
            ['documentos.php', 'Documentos',         'Sube y resguarda tus archivos institucionales.', 'documentos.svg', documentos_contar($uid)],
            ['notas.php',      'Notas',              'Apuntes y recordatorios rapidos.',                'notas.svg',      notas_contar($uid)],
            ['material.php',   'Material didactico', 'Recursos organizados por materia.',               'material.svg',   materiales_contar($uid)],
            ['tareas.php',     'Tareas',             'Da seguimiento a actividades y entregas.',        'tareas.svg',     tareas_contar($uid)],
        ];
        if (es_docente()) {
            $tarjetas[] = ['asistencia.php', 'Lista de asistencia', 'Toma asistencia y descargala en Excel.', 'asistencia.svg', grupos_contar($uid)];
        }
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
<?php endif; ?>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
