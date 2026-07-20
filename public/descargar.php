<?php
/* EduFolio - Descarga segura de archivos.
   El dueno descarga sus documentos/material; un alumno puede descargar el
   material compartido con un grupo al que pertenece. */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/archivos.php';
require_once __DIR__ . '/../app/documentos.php';
require_once __DIR__ . '/../app/materiales.php';
require_once __DIR__ . '/../app/inscripciones.php';
requerir_login();

$u    = usuario_actual();
$uid  = (int)$u['id'];
$tipo = $_GET['tipo'] ?? '';
$id   = (int)($_GET['id'] ?? 0);

$reg     = null;
$duenoId = $uid;

if ($tipo === 'documento') {
    $reg = documentos_obtener($id, $uid);        // solo el dueno
} elseif ($tipo === 'material') {
    $reg = materiales_obtener($id, $uid);        // dueno (docente)
    if (!$reg && es_alumno()) {
        // material compartido con un grupo del alumno
        $stmt = db()->prepare('SELECT * FROM materiales WHERE id = ?');
        $stmt->execute([$id]);
        $m = $stmt->fetch();
        if ($m && !empty($m['grupo_id']) && alumno_en_grupo($uid, (int)$m['grupo_id'])) {
            $reg     = $m;
            $duenoId = (int)$m['usuario_id']; // el archivo esta en la carpeta del docente
        }
    }
}

if (!$reg || empty($reg['archivo'])) {
    http_response_code(404);
    die('Archivo no encontrado.');
}

$ruta = ruta_archivo($duenoId, $reg['archivo']);
if (!is_file($ruta)) {
    http_response_code(404);
    die('El archivo ya no existe en el servidor.');
}

$ext    = extension_de($reg['archivo']);
$nombre = preg_replace('/[^\w\-. ]+/u', '_', $reg['titulo']) . '.' . $ext;

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $nombre . '"');
header('Content-Length: ' . filesize($ruta));
header('Cache-Control: private');
readfile($ruta);
exit;
