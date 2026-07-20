<?php
/* EduFolio - Modulo Lista de asistencia (solo docentes). */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/asistencia.php';
require_once __DIR__ . '/../app/inscripciones.php';
requerir_docente();

$u   = usuario_actual();
$uid = (int)$u['id'];

function _fecha_valida($f)
{
    $f = (string)$f;
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $f) ? $f : date('Y-m-d');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $accion   = $_POST['accion'] ?? '';
    $grupo_id = (int)($_POST['grupo_id'] ?? 0);
    $grupo    = $grupo_id ? grupos_obtener($grupo_id, $uid) : null;

    if ($accion === 'crear_grupo') {
        [$ok, $msg] = grupos_crear($uid, (string)($_POST['nombre'] ?? ''), (string)($_POST['materia'] ?? ''));
        flash($ok ? 'exito' : 'error', $msg);
        redirigir('asistencia.php');
    } elseif ($accion === 'invitar_alumno' && $grupo) {
        [$ok, $msg] = inscripcion_invitar($grupo, (string)($_POST['email'] ?? ''));
        flash($ok ? 'exito' : 'error', $msg);
        redirigir('asistencia.php?grupo=' . $grupo_id);
    } elseif ($accion === 'quitar_inscripcion' && $grupo) {
        inscripcion_quitar((int)($_POST['inscripcion_id'] ?? 0), $grupo_id);
        flash('exito', 'Inscripcion eliminada.');
        redirigir('asistencia.php?grupo=' . $grupo_id);
    } elseif ($accion === 'eliminar_grupo') {
        grupos_eliminar($grupo_id, $uid);
        flash('exito', 'Grupo eliminado.');
        redirigir('asistencia.php');
    } elseif ($accion === 'agregar_alumno' && $grupo) {
        [$ok, $msg] = alumnos_crear($grupo_id, (string)($_POST['nombre'] ?? ''));
        flash($ok ? 'exito' : 'error', $msg);
        redirigir('asistencia.php?grupo=' . $grupo_id);
    } elseif ($accion === 'eliminar_alumno' && $grupo) {
        alumnos_eliminar((int)($_POST['alumno_id'] ?? 0), $grupo_id);
        flash('exito', 'Alumno eliminado.');
        redirigir('asistencia.php?grupo=' . $grupo_id);
    } elseif ($accion === 'guardar_asistencia' && $grupo) {
        $fecha   = _fecha_valida($_POST['fecha'] ?? '');
        $estados = (array)($_POST['estado'] ?? []);
        asistencia_guardar($grupo_id, $fecha, $estados);
        flash('exito', 'Asistencia guardada para el ' . date('d/m/Y', strtotime($fecha)) . '.');
        redirigir('asistencia.php?grupo=' . $grupo_id . '&fecha=' . $fecha);
    }
    redirigir('asistencia.php');
}

$grupo_id = (int)($_GET['grupo'] ?? 0);
$grupo    = $grupo_id ? grupos_obtener($grupo_id, $uid) : null;
$fecha    = _fecha_valida($_GET['fecha'] ?? date('Y-m-d'));

$titulo    = 'Lista de asistencia';
$vista_app = true;
require __DIR__ . '/../app/layout/header.php';
?>
<div class="seccion-encabezado reveal">
    <div class="ic bg-verde"><i class="bi bi-clipboard-check"></i></div>
    <div><h1>Lista de asistencia</h1><p>Registra la asistencia de tus grupos y descargala en Excel.</p></div>
</div>

<?php if (!$grupo): ?>
    <!-- Seleccion / creacion de grupo -->
    <section class="form-card reveal">
        <h3>Nuevo grupo</h3>
        <form method="post" action="asistencia.php" class="formulario">
            <?= csrf_field() ?>
            <input type="hidden" name="accion" value="crear_grupo">
            <div class="grid-2">
                <label>Nombre del grupo
                    <span class="campo"><i class="bi bi-people"></i>
                        <input type="text" name="nombre" required maxlength="120" placeholder="Ej. 3ro A">
                    </span>
                </label>
                <label>Materia (opcional)
                    <span class="campo"><i class="bi bi-book"></i>
                        <input type="text" name="materia" maxlength="120" placeholder="Ej. Matematicas">
                    </span>
                </label>
            </div>
            <div class="form-acciones">
                <button type="submit" class="btn btn-primario"><i class="bi bi-plus-lg"></i> Crear grupo</button>
            </div>
        </form>
    </section>

    <?php $grupos = grupos_listar($uid); ?>
    <?php if (!$grupos): ?>
        <div class="vacio reveal"><i class="bi bi-people"></i><p>Aun no tienes grupos. Crea el primero arriba.</p></div>
    <?php else: ?>
        <div class="grid-cards">
            <?php foreach ($grupos as $g): ?>
                <div class="card-seccion" style="cursor:default">
                    <div class="card-top">
                        <div class="ic-img"><i class="bi bi-people-fill" style="font-size:1.8rem;color:var(--verde)"></i></div>
                        <span class="card-contador"><?= (int)$g['alumnos'] ?></span>
                    </div>
                    <h2><?= e($g['nombre']) ?></h2>
                    <p><?= $g['materia'] ? e($g['materia']) . ' · ' : '' ?><?= (int)$g['alumnos'] ?> en lista · <?= (int)$g['inscritos'] ?> inscrito(s)</p>
                    <div class="item-acciones">
                        <a class="btn btn-sm btn-primario" href="asistencia.php?grupo=<?= (int)$g['id'] ?>">Abrir <i class="bi bi-arrow-right"></i></a>
                        <form method="post" action="asistencia.php" onsubmit="return confirm('¿Eliminar el grupo y toda su asistencia?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="accion" value="eliminar_grupo">
                            <input type="hidden" name="grupo_id" value="<?= (int)$g['id'] ?>">
                            <button class="btn btn-sm btn-peligro" type="submit" title="Eliminar grupo"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php else: ?>
    <!-- Espacio de trabajo de un grupo -->
    <?php
        $alumnos = alumnos_listar($grupo_id);
        $delDia  = asistencia_del_dia($grupo_id, $fecha);
        $tieneRegistros = count(asistencia_fechas($grupo_id)) > 0;
    ?>
    <p class="reveal"><a class="auth-volver" href="asistencia.php"><i class="bi bi-arrow-left"></i> Cambiar de grupo</a></p>
    <div class="grupo-barra reveal">
        <div>
            <span class="badge badge-completada">Grupo</span> <strong><?= e($grupo['nombre']) ?></strong>
            <?php if ($grupo['materia']): ?> <span class="badge badge-cian"><?= e($grupo['materia']) ?></span><?php endif; ?>
        </div>
        <?php if ($alumnos && $tieneRegistros): ?>
            <a class="btn btn-sm btn-calido" href="exportar_asistencia.php?grupo=<?= $grupo_id ?>"><i class="bi bi-file-earmark-excel"></i> Descargar Excel</a>
        <?php endif; ?>
    </div>

    <!-- Alumnos inscritos (cuentas de alumno) -->
    <?php $inscritos = inscripciones_por_grupo($grupo_id); ?>
    <section class="form-card reveal">
        <h3>Alumnos inscritos (cuentas)</h3>
        <p class="ayuda">Invita por correo a alumnos ya registrados en EduFolio. Al aceptar, podran ver las tareas y el material que compartas con este grupo.</p>
        <form method="post" action="asistencia.php?grupo=<?= $grupo_id ?>" class="formulario">
            <?= csrf_field() ?>
            <input type="hidden" name="accion" value="invitar_alumno">
            <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">
            <label>Correo del alumno
                <span class="campo"><i class="bi bi-envelope"></i>
                    <input type="email" name="email" required placeholder="alumno@correo.com">
                </span>
            </label>
            <div class="form-acciones">
                <button type="submit" class="btn btn-primario"><i class="bi bi-send"></i> Enviar invitacion</button>
            </div>
        </form>
        <?php if ($inscritos): ?>
            <ul class="lista-alumnos">
                <?php foreach ($inscritos as $ins):
                    $bcls = ['aceptado' => 'badge-completada', 'invitado' => 'badge-pendiente', 'rechazado' => 'badge-falta'];
                    $btxt = ['aceptado' => 'Inscrito', 'invitado' => 'Invitado', 'rechazado' => 'Rechazo']; ?>
                    <li>
                        <span><i class="bi bi-person-badge"></i> <?= e($ins['nombre'] . ' ' . $ins['apellidos']) ?>
                            — <?= e($ins['email']) ?>
                            <span class="badge <?= $bcls[$ins['estado']] ?? '' ?>"><?= e($btxt[$ins['estado']] ?? $ins['estado']) ?></span>
                        </span>
                        <form method="post" action="asistencia.php?grupo=<?= $grupo_id ?>" onsubmit="return confirm('¿Quitar a este alumno del grupo?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="accion" value="quitar_inscripcion">
                            <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">
                            <input type="hidden" name="inscripcion_id" value="<?= (int)$ins['id'] ?>">
                            <button class="btn btn-sm btn-peligro" type="submit" title="Quitar"><i class="bi bi-trash"></i></button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <!-- Alumnos -->
    <section class="form-card reveal">
        <h3>Lista para asistencia — nombres (<?= count($alumnos) ?>)</h3>
        <p class="ayuda">Estos son los nombres para pasar lista (no requieren cuenta). Los alumnos inscritos de arriba son cuentas que ven el contenido compartido.</p>
        <form method="post" action="asistencia.php?grupo=<?= $grupo_id ?>" class="formulario">
            <?= csrf_field() ?>
            <input type="hidden" name="accion" value="agregar_alumno">
            <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">
            <label>Nombre del alumno
                <span class="campo"><i class="bi bi-person-plus"></i>
                    <input type="text" name="nombre" required maxlength="160" placeholder="Nombre y apellidos">
                </span>
            </label>
            <div class="form-acciones">
                <button type="submit" class="btn btn-primario"><i class="bi bi-plus-lg"></i> Agregar alumno</button>
            </div>
        </form>
        <?php if ($alumnos): ?>
            <ul class="lista-alumnos">
                <?php foreach ($alumnos as $al): ?>
                    <li>
                        <span><i class="bi bi-person"></i> <?= e($al['nombre']) ?></span>
                        <form method="post" action="asistencia.php?grupo=<?= $grupo_id ?>" onsubmit="return confirm('¿Eliminar a este alumno?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="accion" value="eliminar_alumno">
                            <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">
                            <input type="hidden" name="alumno_id" value="<?= (int)$al['id'] ?>">
                            <button class="btn btn-sm btn-peligro" type="submit" title="Eliminar"><i class="bi bi-trash"></i></button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <!-- Tomar asistencia -->
    <section class="form-card reveal">
        <h3>Tomar asistencia</h3>
        <form method="get" action="asistencia.php" class="buscador" style="margin-bottom:1rem">
            <input type="hidden" name="grupo" value="<?= $grupo_id ?>">
            <label style="font-weight:700">Fecha
                <span class="campo" style="margin-top:.3rem"><i class="bi bi-calendar-event"></i>
                    <input type="date" name="fecha" value="<?= e($fecha) ?>" style="padding-left:2.6rem">
                </span>
            </label>
            <button type="submit" class="btn btn-sm btn-outline">Ver fecha</button>
        </form>

        <?php if (!$alumnos): ?>
            <div class="vacio"><i class="bi bi-person-x"></i><p>Agrega alumnos para poder tomar asistencia.</p></div>
        <?php else: ?>
            <form method="post" action="asistencia.php">
                <?= csrf_field() ?>
                <input type="hidden" name="accion" value="guardar_asistencia">
                <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">
                <input type="hidden" name="fecha" value="<?= e($fecha) ?>">
                <div class="tabla-scroll">
                    <table class="tabla tabla-asistencia">
                        <thead>
                            <tr><th>Alumno</th><th>Asistencia</th><th>Falta</th><th>Retardo</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alumnos as $al):
                                $est = $delDia[(int)$al['id']] ?? 'asistencia'; ?>
                                <tr>
                                    <td><?= e($al['nombre']) ?></td>
                                    <?php foreach (ASISTENCIA_ESTADOS as $op): ?>
                                        <td class="col-radio">
                                            <label class="rad rad-<?= $op ?>">
                                                <input type="radio" name="estado[<?= (int)$al['id'] ?>]" value="<?= $op ?>" <?= $est === $op ? 'checked' : '' ?>>
                                                <span></span>
                                            </label>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="form-acciones" style="margin-top:1rem">
                    <button type="submit" class="btn btn-primario"><i class="bi bi-save"></i> Guardar asistencia del <?= e(date('d/m/Y', strtotime($fecha))) ?></button>
                </div>
            </form>
        <?php endif; ?>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
