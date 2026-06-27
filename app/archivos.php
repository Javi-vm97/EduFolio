<?php
/* EduFolio - Manejo seguro de archivos subidos (Fase 2). */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

const RUTA_STORAGE = __DIR__ . '/../storage/uploads';
const MAX_BYTES    = 10485760; // 10 MB

/* Extensiones permitidas => tipo legible */
const TIPOS_PERMITIDOS = [
    'pdf'  => 'PDF',
    'doc'  => 'Word',  'docx' => 'Word',
    'xls'  => 'Excel', 'xlsx' => 'Excel',
    'ppt'  => 'PowerPoint', 'pptx' => 'PowerPoint',
    'txt'  => 'Texto',
    'jpg'  => 'Imagen', 'jpeg' => 'Imagen', 'png' => 'Imagen', 'gif' => 'Imagen', 'webp' => 'Imagen',
    'zip'  => 'Comprimido',
];

/* Devuelve la extension en minusculas de un nombre de archivo. */
function extension_de(string $nombre): string
{
    return strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
}

/* Carpeta de almacenamiento de un usuario (la crea si no existe). */
function carpeta_usuario(int $usuario_id): string
{
    $dir = RUTA_STORAGE . '/' . $usuario_id;
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    return $dir;
}

/* Texto legible del tamano de un archivo. */
function tam_legible(int $bytes): string
{
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024) . ' KB';
    return $bytes . ' B';
}

/**
 * Guarda un archivo subido. Devuelve un arreglo:
 *   ['ok'=>bool, 'mensaje'=>string, 'archivo'=>string, 'original'=>string, 'tipo'=>string]
 */
function guardar_archivo(array $file, int $usuario_id): array
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'mensaje' => 'Selecciona un archivo.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'mensaje' => 'Error al subir el archivo (codigo ' . (int)$file['error'] . ').'];
    }
    if ($file['size'] > MAX_BYTES) {
        return ['ok' => false, 'mensaje' => 'El archivo supera el limite de 10 MB.'];
    }

    $original = $file['name'];
    $ext = extension_de($original);
    if (!array_key_exists($ext, TIPOS_PERMITIDOS)) {
        return ['ok' => false, 'mensaje' => 'Tipo de archivo no permitido (.' . e($ext) . ').'];
    }

    $dir = carpeta_usuario($usuario_id);
    $nombre = bin2hex(random_bytes(8)) . '.' . $ext;
    $destino = $dir . '/' . $nombre;

    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        return ['ok' => false, 'mensaje' => 'No se pudo guardar el archivo en el servidor.'];
    }

    return [
        'ok'       => true,
        'mensaje'  => 'Archivo guardado.',
        'archivo'  => $nombre,
        'original' => $original,
        'tipo'     => TIPOS_PERMITIDOS[$ext],
    ];
}

/* Ruta absoluta de un archivo guardado de un usuario. */
function ruta_archivo(int $usuario_id, string $nombre): string
{
    return RUTA_STORAGE . '/' . $usuario_id . '/' . basename($nombre);
}

/* Elimina fisicamente un archivo del almacenamiento. */
function eliminar_archivo_fisico(int $usuario_id, ?string $nombre): void
{
    if (!$nombre) {
        return;
    }
    $ruta = ruta_archivo($usuario_id, $nombre);
    if (is_file($ruta)) {
        @unlink($ruta);
    }
}

/* Elimina toda la carpeta de archivos de un usuario (al borrar la cuenta). */
function eliminar_carpeta_usuario(int $usuario_id): void
{
    $dir = RUTA_STORAGE . '/' . $usuario_id;
    if (!is_dir($dir)) {
        return;
    }
    foreach ((array)glob($dir . '/*') as $f) {
        if (is_file($f)) {
            @unlink($f);
        }
    }
    @rmdir($dir);
}
