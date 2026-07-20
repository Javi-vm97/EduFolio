<?php
/* EduFolio - Seccion Material didactico (Fase 2). */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/materiales.php';
require_once __DIR__ . '/../app/asistencia.php';
requerir_login();
bloquear_alumno();

$u   = usuario_actual();
$uid = (int)$u['id'];

function _grupo_valido($gid, int $uid)
{
    $gid = (int)$gid;
    return ($gid && grupos_obtener($gid, $uid)) ? $gid : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $titulo  = trim((string)($_POST['titulo'] ?? ''));
        $materia = trim((string)($_POST['materia'] ?? ''));
        $desc    = trim((string)($_POST['descripcion'] ?? ''));
        $gid     = _grupo_valido($_POST['grupo_id'] ?? 0, $uid);
        [$ok, $msg] = materiales_crear($uid, $titulo, $materia, $desc, $_FILES['archivo'] ?? [], $gid);
        flash($ok ? 'exito' : 'error', $msg);
    } elseif ($accion === 'actualizar') {
        $titulo  = trim((string)($_POST['titulo'] ?? ''));
        $materia = trim((string)($_POST['materia'] ?? ''));
        $desc    = trim((string)($_POST['descripcion'] ?? ''));
        $gid     = _grupo_valido($_POST['grupo_id'] ?? 0, $uid);
        [$ok, $msg] = materiales_actualizar((int)($_POST['id'] ?? 0), $uid, $titulo, $materia, $desc, $gid);
        flash($ok ? 'exito' : 'error', $msg);
    } elseif ($accion === 'eliminar') {
        materiales_eliminar((int)($_POST['id'] ?? 0), $uid);
        flash('exito', 'Material eliminado.');
    }
    redirigir('material.php');
}

$editar     = isset($_GET['editar']) ? materiales_obtener((int)$_GET['editar'], $uid) : null;
$q          = trim((string)($_GET['q'] ?? ''));
$materiales = materiales_listar($uid, $q);
$grupos     = grupos_listar($uid);

$titulo    = 'Material didactico';
$vista_app = true;
require __DIR__ . '/../app/layout/header.php';
?>
<div class="seccion-encabezado reveal">
    <div class="ic-img"><img src="assets/icons/material.svg" alt=""></div>
    <div><h1>Material didactico</h1><p>Organiza tus recursos de ensenanza por materia.</p></div>
</div>

<section class="form-card reveal">
    <?php if ($editar): ?>
        <h3>Editar material</h3>
        <form method="post" action="material.php" class="formulario">
            <?= csrf_field() ?>
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id" value="<?= (int)$editar['id'] ?>">
            <div class="grid-2">
                <label>Titulo
                    <span class="campo"><i class="bi bi-type"></i>
                        <input type="text" name="titulo" value="<?= e($editar['titulo']) ?>" required maxlength="180">
                    </span>
                </label>
                <label>Materia (opcional)
                    <span class="campo"><i class="bi bi-bookmark"></i>
                        <input type="text" name="materia" value="<?= e($editar['materia'] ?? '') ?>" maxlength="120">
                    </span>
                </label>
            </div>
            <label>Descripcion (opcional)
                <textarea name="descripcion" rows="2"><?= e($editar['descripcion'] ?? '') ?></textarea>
            </label>
            <label>Compartir con un grupo (opcional)
                <select name="grupo_id">
                    <option value="0">— Solo para mi (no compartir) —</option>
                    <?php foreach ($grupos as $g): ?>
                        <option value="<?= (int)$g['id'] ?>" <?= (int)($editar['grupo_id'] ?? 0) === (int)$g['id'] ? 'selected' : '' ?>><?= e($g['nombre'] . ($g['materia'] ? ' - ' . $g['materia'] : '')) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <p class="ayuda">El archivo no cambia. Para reemplazarlo, elimina el material y subelo de nuevo.</p>
            <div class="form-acciones">
                <button type="submit" class="btn btn-primario"><i class="bi bi-check2"></i> Guardar cambios</button>
                <a class="btn btn-outline" href="material.php">Cancelar</a>
            </div>
        </form>
    <?php else: ?>
        <h3>Subir material</h3>
        <form method="post" action="material.php" class="formulario" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="accion" value="crear">
            <div class="grid-2">
                <label>Titulo
                    <span class="campo"><i class="bi bi-type"></i>
                        <input type="text" name="titulo" required maxlength="180">
                    </span>
                </label>
                <label>Materia (opcional)
                    <span class="campo"><i class="bi bi-bookmark"></i>
                        <input type="text" name="materia" maxlength="120" placeholder="Ej. Matematicas">
                    </span>
                </label>
            </div>
            <label>Descripcion (opcional)
                <textarea name="descripcion" rows="2"></textarea>
            </label>
            <label>Compartir con un grupo (opcional)
                <select name="grupo_id">
                    <option value="0">— Solo para mi (no compartir) —</option>
                    <?php foreach ($grupos as $g): ?>
                        <option value="<?= (int)$g['id'] ?>"><?= e($g['nombre'] . ($g['materia'] ? ' - ' . $g['materia'] : '')) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php if (!$grupos): ?><p class="ayuda">Crea grupos en "Lista de asistencia" para compartir con tus alumnos.</p><?php endif; ?>
            <label>Archivo
                <input type="file" name="archivo" required>
            </label>
            <p class="ayuda">Permitido: PDF, Word, Excel, PowerPoint, imagenes, texto, zip. Maximo 10 MB.</p>
            <div class="form-acciones">
                <button type="submit" class="btn btn-primario"><i class="bi bi-cloud-arrow-up"></i> Subir</button>
            </div>
        </form>
    <?php endif; ?>
</section>

<?php if ($materiales || $q !== ''): ?>
<form method="get" action="material.php" class="buscador reveal">
    <span class="campo"><i class="bi bi-search"></i>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar material o materia...">
    </span>
    <button class="btn btn-sm btn-primario" type="submit">Buscar</button>
    <?php if ($q !== ''): ?><a class="btn btn-sm btn-outline" href="material.php">Limpiar</a><?php endif; ?>
</form>
<?php endif; ?>

<?php if (!$materiales): ?>
    <div class="vacio reveal"><i class="bi bi-easel"></i><p><?= $q !== '' ? 'No se encontro material para “' . e($q) . '”.' : 'Aun no has subido material.' ?></p></div>
<?php else: ?>
    <div class="lista-items">
        <?php foreach ($materiales as $m): ?>
            <article class="item-card reveal">
                <div class="item-icono bg-cian"><i class="bi bi-easel2"></i></div>
                <div class="item-cuerpo">
                    <h4><?= e($m['titulo']) ?></h4>
                    <?php if ($m['descripcion']): ?><p class="item-texto"><?= e($m['descripcion']) ?></p><?php endif; ?>
                    <span class="item-meta">
                        <?php if ($m['materia']): ?><span class="badge badge-cian"><?= e($m['materia']) ?></span><?php endif; ?>
                        <?php if (!empty($m['grupo_nombre'])): ?><span class="badge"><i class="bi bi-people-fill"></i> <?= e($m['grupo_nombre']) ?></span><?php endif; ?>
                        <i class="bi bi-clock"></i> <?= e(date('d/m/Y', strtotime($m['creado_en']))) ?>
                    </span>
                </div>
                <div class="item-acciones">
                    <a class="btn btn-sm btn-outline" href="descargar.php?tipo=material&id=<?= (int)$m['id'] ?>" title="Descargar"><i class="bi bi-download"></i></a>
                    <a class="btn btn-sm btn-outline" href="material.php?editar=<?= (int)$m['id'] ?>" title="Editar"><i class="bi bi-pencil"></i></a>
                    <form method="post" action="material.php" onsubmit="return confirm('¿Eliminar este material?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                        <button class="btn btn-sm btn-peligro" type="submit" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
