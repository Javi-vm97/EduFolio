<?php
/* EduFolio - Seccion Tareas (esqueleto Fase 1). */
require_once __DIR__ . '/../app/auth.php';
requerir_login();

$titulo    = 'Tareas';
$vista_app = true;

$seccion_titulo = 'Tareas';
$seccion_desc   = 'Da seguimiento a actividades, pendientes y entregas.';
$seccion_icono  = 'bi-check2-square';
$seccion_color  = 'bg-morado';
$seccion_funcs  = [
    'Crear tareas con fecha de entrega y estado.',
    'Marcar tareas como pendiente, en progreso o completada.',
    'Visualizar las tareas proximas a vencer.',
];

require __DIR__ . '/../app/layout/header.php';
require __DIR__ . '/../app/layout/seccion_placeholder.php';
require __DIR__ . '/../app/layout/footer.php';
