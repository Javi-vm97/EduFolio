<?php
/* EduFolio - Conexion a la base de datos mediante PDO. Devuelve una unica instancia de PDO (patron singleton sencillo). */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
    } catch (PDOException $e) {
        http_response_code(500);
        die('Error de conexion a la base de datos. Verifica que MySQL este activo en XAMPP.');
    }

    return $pdo;
}
