<?php
/* EduFolio - Seccion Documentos (Fase 2). */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/documentos.php';
requerir_login();

$u   = usuario_actual();
$uid = (int)$u['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $titulo = trim((string)($_POST['titulo'] ?? ''));
        $desc   = trim((string)($_POST['descripcion'] ?? ''));
        [$ok, $msg] = documentos_crear($uid, $titulo, $desc, $_FILES['archivo'] ?? []);
        flash($ok ? 'exito' : 'error', $msg);
    } elseif ($accion === 'actualizar') {
        $titulo = trim((string)($_POST['titulo'] ?? ''));
        $desc   = trim((string)($_POST['descripcion'] ?? ''));
        [$ok, $msg] = documentos_actualizar((int)($_POST['id'] ?? 0), $uid, $titulo, $desc);
        flash($ok ? 'exito' : 'error', $msg);
    } elseif ($accion === 'eliminar') {
        documentos_eliminar((int)($_POST['id'] ?? 0), $uid);
        flash('exito', 'Documento eliminado.');
    }
    redirigir('documentos.php');
}

$editar = isset($_GET['editar']) ? documentos_obtener((int)$_GET['editar'], $uid) : null;
$q      = trim((string)($_GET['q'] ?? ''));
$docs   = documentos_listar($uid, $q);

$titulo    = 'Documentos';
$vista_app = true;
require __DIR__ . '/../app/layout/header.php';
?>
<div class="seccion-encabezado reveal">
    <div class="ic-img"><img src="assets/icons/documentos.svg" alt=""></div>
    <div><h1>Documentos</h1><p>Sube y resguarda los archivos de tu labor docente.</p></div>
</div>

<section class="form-card reveal">
    <?php if ($editar): ?>
        <h3>Editar documento</h3>
        <form method="post" action="documentos.php" class="formulario">
            <?= csrf_field() ?>
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id" value="<?= (int)$editar['id'] ?>">
            <label>Titulo
                <span class="campo"><i class="bi bi-type"></i>
                    <input type="text" name="titulo" value="<?= e($editar['titulo']) ?>" required maxlength="180">
                </span>
            </label>
            <label>Descripcion (opcional)
                <textarea name="descripcion" rows="2"><?= e($editar['descripcion'] ?? '') ?></textarea>
            </label>
            <p class="ayuda">El archivo (<?= e($editar['tipo']) ?>) no cambia. Para reemplazarlo, elimina el documento y subelo de nuevo.</p>
            <div class="form-acciones">
                <button type="submit" class="btn btn-primario"><i class="bi bi-check2"></i> Guardar cambios</button>
                <a class="btn btn-outline" href="documentos.php">Cancelar</a>
            </div>
        </form>
    <?php else: ?>
        <h3>Subir documento</h3>
        <form method="post" action="documentos.php" class="formulario" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="accion" value="crear">
            <label>Titulo
                <span class="campo"><i class="bi bi-type"></i>
                    <input type="text" name="titulo" required maxlength="180">
                </span>
            </label>
            <label>Descripcion (opcional)
                <textarea name="descripcion" rows="2"></textarea>
            </label>
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

<?php if ($docs || $q !== ''): ?>
<form method="get" action="documentos.php" class="buscador reveal">
    <span class="campo"><i class="bi bi-search"></i>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar documentos...">
    </span>
    <button class="btn btn-sm btn-primario" type="submit">Buscar</button>
    <?php if ($q !== ''): ?><a class="btn btn-sm btn-outline" href="documentos.php">Limpiar</a><?php endif; ?>
</form>
<?php endif; ?>

<?php if (!$docs): ?>
    <div class="vacio reveal"><i class="bi bi-folder2-open"></i><p><?= $q !== '' ? 'No se encontraron documentos para “' . e($q) . '”.' : 'Aun no has subido documentos.' ?></p></div>
<?php else: ?>
    <div class="lista-items">
        <?php foreach ($docs as $d): ?>
            <article class="item-card reveal">
                <div class="item-icono bg-indigo"><i class="bi bi-file-earmark-text"></i></div>
                <div class="item-cuerpo">
                    <h4><?= e($d['titulo']) ?></h4>
                    <?php if ($d['descripcion']): ?><p class="item-texto"><?= e($d['descripcion']) ?></p><?php endif; ?>
                    <span class="item-meta">
                        <span class="badge"><?= e($d['tipo']) ?></span>
                        <i class="bi bi-clock"></i> <?= e(date('d/m/Y', strtotime($d['creado_en']))) ?>
                    </span>
                </div>
                <div class="item-acciones">
                    <a class="btn btn-sm btn-outline" href="descargar.php?tipo=documento&id=<?= (int)$d['id'] ?>" title="Descargar"><i class="bi bi-download"></i></a>
                    <a class="btn btn-sm btn-outline" href="documentos.php?editar=<?= (int)$d['id'] ?>" title="Editar"><i class="bi bi-pencil"></i></a>
                    <form method="post" action="documentos.php" onsubmit="return confirm('¿Eliminar este documento?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                        <button class="btn btn-sm btn-peligro" type="submit" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
