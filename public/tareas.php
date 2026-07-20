<?php
/* EduFolio - Seccion Tareas (Fase 2). */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/tareas.php';
require_once __DIR__ . '/../app/asistencia.php';
requerir_login();
bloquear_alumno();

$u   = usuario_actual();
$uid = (int)$u['id'];

/* Devuelve el grupo_id si pertenece al docente; si no, null. */
function _grupo_valido($gid, int $uid)
{
    $gid = (int)$gid;
    return ($gid && grupos_obtener($gid, $uid)) ? $gid : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        [$ok, $msg] = tareas_crear(
            $uid,
            trim((string)($_POST['titulo'] ?? '')),
            trim((string)($_POST['descripcion'] ?? '')),
            (string)($_POST['fecha_entrega'] ?? ''),
            (string)($_POST['estado'] ?? 'pendiente'),
            _grupo_valido($_POST['grupo_id'] ?? 0, $uid)
        );
        flash($ok ? 'exito' : 'error', $msg);
    } elseif ($accion === 'actualizar') {
        [$ok, $msg] = tareas_actualizar(
            (int)($_POST['id'] ?? 0), $uid,
            trim((string)($_POST['titulo'] ?? '')),
            trim((string)($_POST['descripcion'] ?? '')),
            (string)($_POST['fecha_entrega'] ?? ''),
            (string)($_POST['estado'] ?? 'pendiente'),
            _grupo_valido($_POST['grupo_id'] ?? 0, $uid)
        );
        flash($ok ? 'exito' : 'error', $msg);
    } elseif ($accion === 'estado') {
        tareas_cambiar_estado((int)($_POST['id'] ?? 0), $uid, (string)($_POST['estado'] ?? ''));
        flash('exito', 'Estado actualizado.');
    } elseif ($accion === 'eliminar') {
        tareas_eliminar((int)($_POST['id'] ?? 0), $uid);
        flash('exito', 'Tarea eliminada.');
    }
    redirigir('tareas.php');
}

$editar = isset($_GET['editar']) ? tareas_obtener((int)$_GET['editar'], $uid) : null;
$q      = trim((string)($_GET['q'] ?? ''));
$tareas = tareas_listar($uid, $q);
$grupos = grupos_listar($uid);
$hoy    = date('Y-m-d');

$titulo    = 'Tareas';
$vista_app = true;
require __DIR__ . '/../app/layout/header.php';
?>
<div class="seccion-encabezado reveal">
    <div class="ic-img"><img src="assets/icons/tareas.svg" alt=""></div>
    <div><h1>Tareas</h1><p>Da seguimiento a tus actividades y entregas.</p></div>
</div>

<section class="form-card reveal">
    <h3><?= $editar ? 'Editar tarea' : 'Nueva tarea' ?></h3>
    <form method="post" action="tareas.php" class="formulario">
        <?= csrf_field() ?>
        <input type="hidden" name="accion" value="<?= $editar ? 'actualizar' : 'crear' ?>">
        <?php if ($editar): ?><input type="hidden" name="id" value="<?= (int)$editar['id'] ?>"><?php endif; ?>
        <label>Titulo
            <span class="campo"><i class="bi bi-type"></i>
                <input type="text" name="titulo" value="<?= e($editar['titulo'] ?? '') ?>" required maxlength="180">
            </span>
        </label>
        <label>Descripcion (opcional)
            <textarea name="descripcion" rows="2"><?= e($editar['descripcion'] ?? '') ?></textarea>
        </label>
        <div class="grid-2">
            <label>Fecha de entrega (opcional)
                <span class="campo"><i class="bi bi-calendar-event"></i>
                    <input type="date" name="fecha_entrega" value="<?= e($editar['fecha_entrega'] ?? '') ?>">
                </span>
            </label>
            <label>Estado
                <select name="estado">
                    <?php foreach (TAREA_ESTADOS as $es): ?>
                        <option value="<?= $es ?>" <?= ($editar['estado'] ?? '') === $es ? 'selected' : '' ?>><?= e(tarea_estado_texto($es)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <label>Compartir con un grupo (opcional)
            <select name="grupo_id">
                <option value="0">— Solo para mi (no compartir) —</option>
                <?php foreach ($grupos as $g): ?>
                    <option value="<?= (int)$g['id'] ?>" <?= (int)($editar['grupo_id'] ?? 0) === (int)$g['id'] ? 'selected' : '' ?>>
                        <?= e($g['nombre'] . ($g['materia'] ? ' - ' . $g['materia'] : '')) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <?php if (!$grupos): ?><p class="ayuda">Crea grupos en "Lista de asistencia" para poder compartir con tus alumnos.</p><?php endif; ?>
        <div class="form-acciones">
            <button type="submit" class="btn btn-primario"><i class="bi bi-<?= $editar ? 'check2' : 'plus-lg' ?>"></i> <?= $editar ? 'Guardar cambios' : 'Agregar tarea' ?></button>
            <?php if ($editar): ?><a class="btn btn-outline" href="tareas.php">Cancelar</a><?php endif; ?>
        </div>
    </form>
</section>

<?php if ($tareas || $q !== ''): ?>
<form method="get" action="tareas.php" class="buscador reveal">
    <span class="campo"><i class="bi bi-search"></i>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar tareas...">
    </span>
    <button class="btn btn-sm btn-primario" type="submit">Buscar</button>
    <?php if ($q !== ''): ?><a class="btn btn-sm btn-outline" href="tareas.php">Limpiar</a><?php endif; ?>
</form>
<?php endif; ?>

<?php if (!$tareas): ?>
    <div class="vacio reveal"><i class="bi bi-check2-circle"></i><p><?= $q !== '' ? 'No se encontraron tareas para “' . e($q) . '”.' : 'No tienes tareas. Agrega la primera arriba.' ?></p></div>
<?php else: ?>
    <div class="lista-items">
        <?php foreach ($tareas as $t):
            $vencida = $t['fecha_entrega'] && $t['estado'] !== 'completada' && $t['fecha_entrega'] < $hoy; ?>
            <article class="item-card tarea-<?= e($t['estado']) ?> reveal">
                <div class="item-cuerpo">
                    <h4 class="<?= $t['estado'] === 'completada' ? 'tachado' : '' ?>"><?= e($t['titulo']) ?></h4>
                    <?php if ($t['descripcion']): ?><p class="item-texto"><?= e($t['descripcion']) ?></p><?php endif; ?>
                    <span class="item-meta">
                        <span class="badge badge-<?= e($t['estado']) ?>"><?= e(tarea_estado_texto($t['estado'])) ?></span>
                        <?php if (!empty($t['grupo_nombre'])): ?>
                            <span class="badge"><i class="bi bi-people-fill"></i> <?= e($t['grupo_nombre']) ?></span>
                        <?php endif; ?>
                        <?php if ($t['fecha_entrega']): ?>
                            <span class="<?= $vencida ? 'vencida' : '' ?>"><i class="bi bi-calendar-event"></i>
                                <?= e(date('d/m/Y', strtotime($t['fecha_entrega']))) ?><?= $vencida ? ' (vencida)' : '' ?></span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="item-acciones">
                    <form method="post" action="tareas.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="accion" value="estado">
                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                        <select name="estado" onchange="this.form.submit()" title="Cambiar estado">
                            <?php foreach (TAREA_ESTADOS as $es): ?>
                                <option value="<?= $es ?>" <?= $t['estado'] === $es ? 'selected' : '' ?>><?= e(tarea_estado_texto($es)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <a class="btn btn-sm btn-outline" href="tareas.php?editar=<?= (int)$t['id'] ?>" title="Editar"><i class="bi bi-pencil"></i></a>
                    <form method="post" action="tareas.php" onsubmit="return confirm('¿Eliminar esta tarea?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                        <button class="btn btn-sm btn-peligro" type="submit" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
