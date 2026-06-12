<?php
/* EduFolio - Seccion Material didactico (esqueleto Fase 1). */
require_once __DIR__ . '/../app/auth.php';
requerir_login();

$titulo    = 'Material didactico';
$vista_app = true;

$seccion_titulo = 'Material didactico';
$seccion_desc   = 'Organiza tus recursos de ensenanza por materia.';
$seccion_icono  = 'bi-easel2-fill';
$seccion_color  = 'bg-cian';
$seccion_funcs  = [
    'Subir material didactico asociado a una materia.',
    'Agrupar recursos por asignatura.',
    'Compartir y reutilizar materiales entre cursos.',
];

require __DIR__ . '/../app/layout/header.php';
require __DIR__ . '/../app/layout/seccion_placeholder.php';
require __DIR__ . '/../app/layout/footer.php';
