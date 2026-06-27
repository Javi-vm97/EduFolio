<?php
/* EduFolio - Panel de administracion (Fase 3). Solo para rol admin. */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/usuarios.php';
requerir_admin();

$u   = usuario_actual();
$uid = (int)$u['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    if (($_POST['accion'] ?? '') === 'eliminar') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === $uid) {
            flash('error', 'No puedes eliminar tu propia cuenta de administrador.');
        } elseif ($id > 0) {
            admin_eliminar_usuario($id);
            flash('exito', 'Usuario eliminado junto con su contenido.');
        }
    }
    redirigir('admin.php');
}

$stats    = admin_estadisticas();
$usuarios = admin_listar_usuarios();

$tarjetas = [
    ['Usuarios',   $stats['usuarios'],   'bi-people-fill',     'bg-indigo'],
    ['Documentos', $stats['documentos'], 'bi-folder-fill',     'bg-naranja'],
    ['Notas',      $stats['notas'],      'bi-journal-text',    'bg-cian'],
    ['Materiales', $stats['materiales'], 'bi-easel2-fill',     'bg-morado'],
    ['Tareas',     $stats['tareas'],     'bi-check2-square',   'bg-verde'],
];

$titulo    = 'Administracion';
$vista_app = true;
require __DIR__ . '/../app/layout/header.php';
?>
<div class="seccion-encabezado reveal">
    <div class="ic bg-morado"><i class="bi bi-shield-lock-fill"></i></div>
    <div><h1>Administracion</h1><p>Resumen del sistema y gestion de usuarios.</p></div>
</div>

<div class="stats-admin reveal">
    <?php foreach ($tarjetas as [$lbl, $val, $ic, $color]): ?>
        <div class="stat-admin">
            <div class="ic <?= e($color) ?>"><i class="bi <?= e($ic) ?>"></i></div>
            <div><div class="stat-num"><?= (int)$val ?></div><div class="stat-lbl"><?= e($lbl) ?></div></div>
        </div>
    <?php endforeach; ?>
</div>

<section class="panel-info reveal">
    <h3>Usuarios registrados (<?= count($usuarios) ?>)</h3>
    <div class="tabla-scroll">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Nombre</th><th>Correo</th><th>Institucion</th><th>Rol</th>
                    <th>Docs</th><th>Notas</th><th>Mat.</th><th>Tareas</th><th>Alta</th><th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usr): ?>
                    <tr>
                        <td><?= e($usr['nombre'] . ' ' . $usr['apellidos']) ?></td>
                        <td><?= e($usr['email']) ?></td>
                        <td><?= e($usr['institucion'] ?: '—') ?></td>
                        <td><span class="badge <?= $usr['rol'] === 'admin' ? 'badge-en_progreso' : '' ?>"><?= e($usr['rol']) ?></span></td>
                        <td><?= (int)$usr['docs'] ?></td>
                        <td><?= (int)$usr['notas'] ?></td>
                        <td><?= (int)$usr['materiales'] ?></td>
                        <td><?= (int)$usr['tareas'] ?></td>
                        <td><?= e(date('d/m/Y', strtotime($usr['creado_en']))) ?></td>
                        <td>
                            <?php if ((int)$usr['id'] !== $uid): ?>
                                <form method="post" action="admin.php" onsubmit="return confirm('¿Eliminar a <?= e(addslashes($usr['email'])) ?> y todo su contenido?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?= (int)$usr['id'] ?>">
                                    <button class="btn btn-sm btn-peligro" type="submit" title="Eliminar usuario"><i class="bi bi-trash"></i></button>
                                </form>
                            <?php else: ?>
                                <span class="tu-cuenta">Tú</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
