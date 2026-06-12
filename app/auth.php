<?php
/* EduFolio - Funciones de autenticacion y sesion de usuario. */

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

/* Indica si hay un usuario autenticado. */
function esta_autenticado(): bool
{
    return !empty($_SESSION['usuario_id']);
}

/* Devuelve los datos del usuario en sesion (o null). */
function usuario_actual(): ?array
{
    if (!esta_autenticado()) {
        return null;
    }

    static $usuario = null;
    if ($usuario !== null) {
        return $usuario;
    }

    $stmt = db()->prepare('SELECT id, nombre, apellidos, email, institucion, rol, creado_en FROM usuarios WHERE id = ?');
    $stmt->execute([$_SESSION['usuario_id']]);
    $fila = $stmt->fetch();

    return $usuario = ($fila ?: null);
}

/* Obliga a iniciar sesion; si no, redirige al login. */
function requerir_login(): void
{
    if (!esta_autenticado()) {
        flash('error', 'Debes iniciar sesion para acceder.');
        redirigir('login.php');
    }
}

/* Registra un nuevo docente. Devuelve [exito, mensaje]. */
function registrar_usuario(string $nombre, string $apellidos, string $email, string $password, ?string $institucion): array
{
    $nombre    = trim($nombre);
    $apellidos = trim($apellidos);
    $email     = strtolower(trim($email));
    $institucion = $institucion !== null ? trim($institucion) : null;

    if ($nombre === '' || $apellidos === '' || $email === '' || $password === '') {
        return [false, 'Todos los campos obligatorios deben completarse.'];
    }
    if (!email_valido($email)) {
        return [false, 'El correo electronico no tiene un formato valido.'];
    }
    if (strlen($password) < 8) {
        return [false, 'La contrasena debe tener al menos 8 caracteres.'];
    }

    $existe = db()->prepare('SELECT 1 FROM usuarios WHERE email = ?');
    $existe->execute([$email]);
    if ($existe->fetch()) {
        return [false, 'Ya existe una cuenta registrada con ese correo.'];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare(
        'INSERT INTO usuarios (nombre, apellidos, email, password_hash, institucion)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$nombre, $apellidos, $email, $hash, $institucion ?: null]);

    return [true, 'Cuenta creada correctamente. Ya puedes iniciar sesion.'];
}

/* Intenta iniciar sesion. Devuelve [exito, mensaje]. */
function iniciar_sesion(string $email, string $password): array
{
    $email = strtolower(trim($email));

    $stmt = db()->prepare('SELECT id, password_hash FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
        return [false, 'Correo o contrasena incorrectos.'];
    }

    // Regenera el id de sesion para prevenir fijacion de sesion.
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int)$usuario['id'];

    return [true, 'Bienvenido de nuevo.'];
}

/* Cierra la sesion del usuario actual. */
function cerrar_sesion(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
