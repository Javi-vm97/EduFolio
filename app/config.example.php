<?php
/* EduFolio - Plantilla de configuracion.
   Copia este archivo como "config.php" y completa tus datos.
   (config.php no se versiona en Git para no exponer credenciales). */

declare(strict_types=1);

/* Compatibilidad con PHP 7.x (estas funciones existen desde PHP 8.0) */
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return strpos($haystack, $needle) === 0;
    }
}

// Datos de la aplicacion
const APP_NAME = 'EduFolio';
const APP_DESC = 'Portafolio Virtual Docente';

// Detecta el entorno: local (XAMPP) o produccion (hosting)
$__host = $_SERVER['HTTP_HOST'] ?? '';
$__es_local = $__host === ''
    || str_contains($__host, 'localhost')
    || str_starts_with($__host, '127.0.0.1');

if ($__es_local) {
    // LOCAL (XAMPP / MariaDB)
    define('DB_HOST', '127.0.0.1');
    define('DB_PORT', '3306');
    define('DB_NAME', 'portafolio_docente');
    define('DB_USER', 'root');
    define('DB_PASS', '');               // XAMPP: contrasena vacia por defecto
} else {
    // PRODUCCION -- completa con los datos de tu hosting
    define('DB_HOST', 'TU_MYSQL_HOST');
    define('DB_PORT', '3306');
    define('DB_NAME', 'TU_BASE_DE_DATOS');
    define('DB_USER', 'TU_USUARIO');
    define('DB_PASS', 'TU_CONTRASENA');
}
define('DB_CHARSET', 'utf8mb4');

date_default_timezone_set('America/Mexico_City');

// Sesiones seguras (compatible con PHP 7.2 y posteriores)
if (session_status() === PHP_SESSION_NONE) {
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(0, '/', '', false, true);
    }
    session_start();
}
