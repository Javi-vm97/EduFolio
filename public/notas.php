<?php
/* EduFolio - Seccion Notas (Fase 2). */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/notas.php';
requerir_login();

$u   = usuario_actual();
$uid = (int)$u['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $accion    = $_POST['accion'] ?? '';
    $titulo    = trim((string)($_POST['titulo'] ?? ''));
    $contenido = trim((string)($_POST['contenido'] ?? ''));
    $id        = (int)($_POST['id'] ?? 0);

    if ($accion === 'crear') {
        if ($titulo === '') {
            flash('error', 'El titulo es obligatorio.');
        } else {
            notas_crear($uid, $titulo, $contenido);
            flash('exito', 'Nota creada.');
        }
    } elseif ($accion === 'actualizar' && $id > 0) {
        notas_actualizar($id, $uid, $titulo, $contenido);
        flash('exito', 'Nota actualizada.');
    } elseif ($accion === 'eliminar' && $id > 0) {
        notas_eliminar($id, $uid);
        flash('exito', 'Nota eliminada.');
    }
    redirigir('notas.php');
}

$editar = null;
if (isset($_GET['editar'])) {
    $editar = notas_obtener((int)$_GET['editar'], $uid);
}
$q     = trim((string)($_GET['q'] ?? ''));
$notas = notas_listar($uid, $q);

$titulo    = 'Notas';
$vista_app = true;
require __DIR__ . '/../app/layout/header.php';
?>
<div class="seccion-encabezado reveal">
    <div class="ic-img"><img src="assets/icons/notas.svg" alt=""></div>
    <div><h1>Notas</h1><p>Tus apuntes y recordatorios rapidos.</p></div>
</div>

<section class="form-card reveal">
    <h3><?= $editar ? 'Editar nota' : 'Nueva nota' ?></h3>
    <form method="post" action="notas.php" class="formulario">
        <?= csrf_field() ?>
        <input type="hidden" name="accion" value="<?= $editar ? 'actualizar' : 'crear' ?>">
        <?php if ($editar): ?><input type="hidden" name="id" value="<?= (int)$editar['id'] ?>"><?php endif; ?>
        <label>Titulo
            <span class="campo"><i class="bi bi-type"></i>
                <input type="text" name="titulo" value="<?= e($editar['titulo'] ?? '') ?>" required maxlength="180">
            </span>
        </label>
        <label>Contenido
            <textarea name="contenido" rows="4" placeholder="Escribe tu nota..."><?= e($editar['contenido'] ?? '') ?></textarea>
        </label>
        <div class="form-acciones">
            <button type="submit" class="btn btn-primario"><i class="bi bi-check2"></i> <?= $editar ? 'Guardar cambios' : 'Agregar nota' ?></button>
            <?php if ($editar): ?><a class="btn btn-outline" href="notas.php">Cancelar</a><?php endif; ?>
        </div>
    </form>
</section>

<?php if ($notas || $q !== ''): ?>
<form method="get" action="notas.php" class="buscador reveal">
    <span class="campo"><i class="bi bi-search"></i>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar notas...">
    </span>
    <button class="btn btn-sm btn-primario" type="submit">Buscar</button>
    <?php if ($q !== ''): ?><a class="btn btn-sm btn-outline" href="notas.php">Limpiar</a><?php endif; ?>
</form>
<?php endif; ?>

<?php if (!$notas): ?>
    <div class="vacio reveal"><i class="bi bi-journal"></i><p><?= $q !== '' ? 'No se encontraron notas para “' . e($q) . '”.' : 'Aun no tienes notas. Crea la primera arriba.' ?></p></div>
<?php else: ?>
    <div class="lista-items">
        <?php foreach ($notas as $n): ?>
            <article class="item-card reveal">
                <div class="item-cuerpo">
                    <h4><?= e($n['titulo']) ?></h4>
                    <?php if ($n['contenido'] !== '' && $n['contenido'] !== null): ?>
                        <p class="item-texto"><?= nl2br(e($n['contenido'])) ?></p>
                    <?php endif; ?>
                    <span class="item-meta"><i class="bi bi-clock"></i> <?= e(date('d/m/Y H:i', strtotime($n['actualizado_en']))) ?></span>
                </div>
                <div class="item-acciones">
                    <a class="btn btn-sm btn-outline" href="notas.php?editar=<?= (int)$n['id'] ?>"><i class="bi bi-pencil"></i></a>
                    <form method="post" action="notas.php" onsubmit="return confirm('¿Eliminar esta nota?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                        <button class="btn btn-sm btn-peligro" type="submit"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
