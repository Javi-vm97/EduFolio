<?php
/* EduFolio - Perfil del usuario: editar datos y cambiar contrasena (Fase 3). */
require_once __DIR__ . '/../app/auth.php';
requerir_login();

$u   = usuario_actual();
$uid = (int)$u['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'datos') {
        [$ok, $msg] = perfil_actualizar(
            $uid,
            trim((string)($_POST['nombre'] ?? '')),
            trim((string)($_POST['apellidos'] ?? '')),
            (string)($_POST['institucion'] ?? '')
        );
        flash($ok ? 'exito' : 'error', $msg);
    } elseif ($accion === 'password') {
        [$ok, $msg] = password_cambiar(
            $uid,
            (string)($_POST['actual'] ?? ''),
            (string)($_POST['nueva'] ?? ''),
            (string)($_POST['confirmar'] ?? '')
        );
        flash($ok ? 'exito' : 'error', $msg);
    }
    redirigir('perfil.php');
}

$titulo    = 'Mi perfil';
$vista_app = true;
require __DIR__ . '/../app/layout/header.php';
?>
<div class="seccion-encabezado reveal">
    <div class="ic bg-indigo"><i class="bi bi-person-gear"></i></div>
    <div><h1>Mi perfil</h1><p>Actualiza tus datos y tu contrasena.</p></div>
</div>

<section class="form-card reveal">
    <h3>Datos personales</h3>
    <form method="post" action="perfil.php" class="formulario">
        <?= csrf_field() ?>
        <input type="hidden" name="accion" value="datos">
        <div class="grid-2">
            <label>Nombre(s)
                <span class="campo"><i class="bi bi-person"></i>
                    <input type="text" name="nombre" value="<?= e($u['nombre']) ?>" required maxlength="80">
                </span>
            </label>
            <label>Apellidos
                <span class="campo"><i class="bi bi-person"></i>
                    <input type="text" name="apellidos" value="<?= e($u['apellidos']) ?>" required maxlength="120">
                </span>
            </label>
        </div>
        <label>Institucion (opcional)
            <span class="campo"><i class="bi bi-building"></i>
                <input type="text" name="institucion" value="<?= e($u['institucion'] ?? '') ?>" maxlength="160">
            </span>
        </label>
        <label>Correo electronico
            <span class="campo"><i class="bi bi-envelope"></i>
                <input type="email" value="<?= e($u['email']) ?>" disabled>
            </span>
        </label>
        <p class="ayuda">El correo no puede modificarse.</p>
        <div class="form-acciones">
            <button type="submit" class="btn btn-primario"><i class="bi bi-check2"></i> Guardar datos</button>
        </div>
    </form>
</section>

<section class="form-card reveal">
    <h3>Cambiar contrasena</h3>
    <form method="post" action="perfil.php" class="formulario">
        <?= csrf_field() ?>
        <input type="hidden" name="accion" value="password">
        <label>Contrasena actual
            <span class="campo"><i class="bi bi-lock"></i>
                <input type="password" name="actual" required>
            </span>
        </label>
        <div class="grid-2">
            <label>Nueva contrasena
                <span class="campo"><i class="bi bi-lock-fill"></i>
                    <input type="password" name="nueva" required minlength="8">
                </span>
            </label>
            <label>Confirmar nueva contrasena
                <span class="campo"><i class="bi bi-lock-fill"></i>
                    <input type="password" name="confirmar" required minlength="8">
                </span>
            </label>
        </div>
        <p class="ayuda">Minimo 8 caracteres.</p>
        <div class="form-acciones">
            <button type="submit" class="btn btn-primario"><i class="bi bi-shield-lock"></i> Actualizar contrasena</button>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
