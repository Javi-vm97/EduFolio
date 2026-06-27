<?php
/* EduFolio - Descarga segura de archivos (valida que el archivo sea del usuario). */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/archivos.php';
require_once __DIR__ . '/../app/documentos.php';
require_once __DIR__ . '/../app/materiales.php';
requerir_login();

$u    = usuario_actual();
$tipo = $_GET['tipo'] ?? '';
$id   = (int)($_GET['id'] ?? 0);

if ($tipo === 'documento') {
    $reg = documentos_obtener($id, (int)$u['id']);
} elseif ($tipo === 'material') {
    $reg = materiales_obtener($id, (int)$u['id']);
} else {
    $reg = null;
}

if (!$reg || empty($reg['archivo'])) {
    http_response_code(404);
    die('Archivo no encontrado.');
}

$ruta = ruta_archivo((int)$u['id'], $reg['archivo']);
if (!is_file($ruta)) {
    http_response_code(404);
    die('El archivo ya no existe en el servidor.');
}

// Nombre de descarga amigable: titulo + extension original
$ext    = extension_de($reg['archivo']);
$nombre = preg_replace('/[^\w\-. ]+/u', '_', $reg['titulo']) . '.' . $ext;

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $nombre . '"');
header('Content-Length: ' . filesize($ruta));
header('Cache-Control: private');
readfile($ruta);
exit;
