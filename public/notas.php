<?php
/* EduFolio - Seccion Notas (esqueleto Fase 1). */
require_once __DIR__ . '/../app/auth.php';
requerir_login();

$titulo    = 'Notas';
$vista_app = true;

$seccion_titulo = 'Notas';
$seccion_desc   = 'Toma apuntes y recordatorios rapidos para tu dia a dia.';
$seccion_icono  = 'bi-journal-text';
$seccion_color  = 'bg-naranja';
$seccion_funcs  = [
    'Crear, editar y eliminar notas de texto.',
    'Busqueda rapida por titulo o contenido.',
    'Ordenar por fecha de creacion o modificacion.',
];

require __DIR__ . '/../app/layout/header.php';
require __DIR__ . '/../app/layout/seccion_placeholder.php';
require __DIR__ . '/../app/layout/footer.php';
