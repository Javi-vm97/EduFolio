<?php
/* EduFolio - Registro de nuevos docentes. */
require_once __DIR__ . '/../app/auth.php';

if (esta_autenticado()) {
    redirigir('dashboard.php');
}

$datos = ['nombre' => '', 'apellidos' => '', 'email' => '', 'institucion' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $datos['nombre']      = (string)($_POST['nombre'] ?? '');
    $datos['apellidos']   = (string)($_POST['apellidos'] ?? '');
    $datos['email']       = (string)($_POST['email'] ?? '');
    $datos['institucion'] = (string)($_POST['institucion'] ?? '');
    $password             = (string)($_POST['password'] ?? '');
    $password2            = (string)($_POST['password2'] ?? '');

    if ($password !== $password2) {
        flash('error', 'Las contrasenas no coinciden.');
    } else {
        [$ok, $msg] = registrar_usuario(
            $datos['nombre'],
            $datos['apellidos'],
            $datos['email'],
            $password,
            $datos['institucion']
        );
        if ($ok) {
            flash('exito', $msg);
            redirigir('login.php');
        }
        flash('error', $msg);
    }
}

$titulo      = 'Crear cuenta';
$ocultar_pie = true;
require __DIR__ . '/../app/layout/header.php';
?>
<section class="auth">
    <aside class="auth-aside">
        <span class="blob b1"></span><span class="blob b2"></span>
        <a class="marca" href="index.php"><img class="marca-img" src="img/logo.png" alt="<?= APP_NAME ?>"></a>
        <h2>Crea tu portafolio docente</h2>
        <p>Un solo espacio para reunir, organizar y resguardar todo tu trabajo. Es gratis y toma menos de un minuto.</p>
        <ul class="lista">
            <li><i class="bi bi-check-circle-fill"></i> Sin costo ni anuncios</li>
            <li><i class="bi bi-shield-lock-fill"></i> Tus datos cifrados y protegidos</li>
            <li><i class="bi bi-phone-fill"></i> Accesible desde cualquier dispositivo</li>
        </ul>
    </aside>

    <div class="auth-main">
        <div class="auth-card ancha reveal">
            <a class="auth-volver" href="index.php"><i class="bi bi-arrow-left"></i> Volver al inicio</a>
            <h1>Crear cuenta</h1>
            <p class="auth-sub">Registrate como docente para comenzar.</p>

            <form method="post" action="registro.php" class="formulario" novalidate>
                <?= csrf_field() ?>
                <div class="grid-2">
                    <label>Nombre(s)
                        <span class="campo"><i class="bi bi-person"></i>
                            <input type="text" name="nombre" value="<?= e($datos['nombre']) ?>" required autofocus>
                        </span>
                    </label>
                    <label>Apellidos
                        <span class="campo"><i class="bi bi-person"></i>
                            <input type="text" name="apellidos" value="<?= e($datos['apellidos']) ?>" required>
                        </span>
                    </label>
                </div>
                <label>Correo electronico
                    <span class="campo"><i class="bi bi-envelope"></i>
                        <input type="email" name="email" value="<?= e($datos['email']) ?>" required placeholder="tucorreo@escuela.mx">
                    </span>
                </label>
                <label>Institucion (opcional)
                    <span class="campo"><i class="bi bi-building"></i>
                        <input type="text" name="institucion" value="<?= e($datos['institucion']) ?>">
                    </span>
                </label>
                <div class="grid-2">
                    <label>Contrasena
                        <span class="campo"><i class="bi bi-lock"></i>
                            <input type="password" name="password" required minlength="8" placeholder="••••••••">
                        </span>
                    </label>
                    <label>Confirmar contrasena
                        <span class="campo"><i class="bi bi-lock-fill"></i>
                            <input type="password" name="password2" required minlength="8" placeholder="••••••••">
                        </span>
                    </label>
                </div>
                <p class="ayuda">La contrasena debe tener al menos 8 caracteres.</p>
                <button type="submit" class="btn btn-primario btn-bloque btn-lg">Registrarme <i class="bi bi-arrow-right"></i></button>
            </form>

            <p class="auth-pie">¿Ya tienes cuenta? <a href="login.php">Inicia sesion</a></p>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../app/layout/footer.php'; ?>
