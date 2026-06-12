<?php
/* EduFolio - Funciones de utilidad generales. */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/* Escapa texto para imprimirlo de forma segura en HTML. */
function e(?string $valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

/* Redirige a una ruta relativa y detiene la ejecucion. */
function redirigir(string $ruta): void
{
    header('Location: ' . $ruta);
    exit;
}

/* Guarda un mensaje flash que se mostrara en la siguiente vista. */
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

/* Devuelve y limpia los mensajes flash acumulados. */
function obtener_flash(): array
{
    $mensajes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensajes;
}

/* Genera (una vez) y devuelve el token CSRF de la sesion. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/* Campo oculto con el token CSRF para insertar en formularios. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/* Valida el token CSRF recibido por POST. */
function verificar_csrf(): void
{
    $enviado = $_POST['csrf'] ?? '';
    if (!is_string($enviado) || !hash_equals($_SESSION['csrf'] ?? '', $enviado)) {
        http_response_code(419);
        die('Token de seguridad invalido. Recarga la pagina e intentalo de nuevo.');
    }
}

/* Valida que el correo tenga un formato correcto. */
function email_valido(string $email): bool
{
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}
