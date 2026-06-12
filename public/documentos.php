<?php
/* EduFolio - Seccion Documentos (esqueleto Fase 1). */
require_once __DIR__ . '/../app/auth.php';
requerir_login();

$titulo    = 'Documentos';
$vista_app = true;

$seccion_titulo = 'Documentos';
$seccion_desc   = 'Resguarda y organiza los archivos de tu labor docente.';
$seccion_icono  = 'bi-folder-fill';
$seccion_color  = 'bg-indigo';
$seccion_funcs  = [
    'Subir documentos (PDF, Word, imagenes) con titulo y descripcion.',
    'Listar, descargar y eliminar tus documentos.',
    'Clasificar por tipo y fecha de carga.',
];

require __DIR__ . '/../app/layout/header.php';
require __DIR__ . '/../app/layout/seccion_placeholder.php';
require __DIR__ . '/../app/layout/footer.php';
