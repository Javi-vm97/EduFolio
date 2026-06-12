<?php
/* EduFolio - Inicio de sesion. */
require_once __DIR__ . '/../app/auth.php';

if (esta_autenticado()) {
    redirigir('dashboard.php');
}

$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $email    = (string)($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    [$ok, $msg] = iniciar_sesion($email, $password);
    if ($ok) {
        flash('exito', $msg);
        redirigir('dashboard.php');
    }
    flash('error', $msg);
}

$titulo      = 'Iniciar sesion';
$ocultar_pie = true;
require __DIR__ . '/../app/layout/header.php';
?>
<section class="auth">
    <aside class="auth-aside">
        <span class="blob b1"></span><span class="blob b2"></span>
        <a class="marca" href="index.php"><img class="marca-img" src="img/logo.png" alt="<?= APP_NAME ?>"></a>
        <h2>Bienvenido de nuevo</h2>
        <p>Accede a tu portafolio docente y retoma tu trabajo justo donde lo dejaste.</p>
        <ul class="lista">
            <li><i class="bi bi-folder-fill"></i> Tus documentos y materiales</li>
            <li><i class="bi bi-journal-text"></i> Tus notas y apuntes</li>
            <li><i class="bi bi-check2-square"></i> El seguimiento de tus tareas</li>
        </ul>
    </aside>

    <div class="auth-main">
        <div class="auth-card reveal">
            <a class="auth-volver" href="index.php"><i class="bi bi-arrow-left"></i> Volver al inicio</a>
            <h1>Iniciar sesion</h1>
            <p class="auth-sub">Ingresa tus credenciales para continuar.</p>

            <form method="post" action="login.php" class="formulario" novalidate>
                <?= csrf_field() ?>
                <label>Correo electronico
                    <span class="campo"><i class="bi bi-envelope"></i>
                        <input type="email" name="email" value="<?= e($email) ?>" required autofocus placeholder="tucorreo@escuela.mx">
                    </span>
                </label>
                <label>Contrasena
                    <span class="campo"><i class="bi bi-lock"></i>
                        <input type="password" name="password" required placeholder="••••••••">
                    </span>
                </label>
                <button type="submit" class="btn btn-primario btn-bloque btn-lg">Entrar <i class="bi bi-box-arrow-in-right"></i></button>
            </form>

            <p class="auth-pie">¿No tienes cuenta? <a href="registro.php">Registrate aqui</a></p>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
