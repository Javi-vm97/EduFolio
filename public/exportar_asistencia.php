<?php
/* EduFolio - Exporta la asistencia de un grupo a Excel (.xlsx). Solo docentes. */
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/asistencia.php';
require_once __DIR__ . '/../app/excel.php';
requerir_docente();

$u   = usuario_actual();
$uid = (int)$u['id'];

$grupo_id = (int)($_GET['grupo'] ?? 0);
$grupo    = $grupo_id ? grupos_obtener($grupo_id, $uid) : null;
if (!$grupo) {
    http_response_code(404);
    die('Grupo no encontrado.');
}

$alumnos = alumnos_listar($grupo_id);
$fechas  = asistencia_fechas($grupo_id);
$matriz  = asistencia_matriz($grupo_id);

// Construye la cuadricula del Excel
$grid = [];
$grid[] = ['Lista de asistencia: ' . $grupo['nombre']];
$grid[] = ['Docente: ' . $u['nombre'] . ' ' . $u['apellidos']];
$grid[] = ['Generado: ' . date('d/m/Y H:i')];
$grid[] = ['A = Asistencia   F = Falta   R = Retardo'];
$grid[] = [];

$encabezado = ['Alumno'];
foreach ($fechas as $f) {
    $encabezado[] = date('d/m/Y', strtotime($f));
}
$encabezado[] = 'Asistencias';
$encabezado[] = 'Faltas';
$encabezado[] = 'Retardos';
$grid[] = $encabezado;

foreach ($alumnos as $al) {
    $aid = (int)$al['id'];
    $fila = [$al['nombre']];
    $ca = $cf = $cr = 0;
    foreach ($fechas as $f) {
        $est = $matriz[$aid][$f] ?? '';
        $fila[] = asistencia_estado_abrev($est);
        if ($est === 'asistencia') { $ca++; }
        elseif ($est === 'falta') { $cf++; }
        elseif ($est === 'retardo') { $cr++; }
    }
    $fila[] = (string)$ca;
    $fila[] = (string)$cf;
    $fila[] = (string)$cr;
    $grid[] = $fila;
}

$slug = preg_replace('/[^A-Za-z0-9]+/', '_', $grupo['nombre']);
xlsx_descargar('asistencia_' . trim($slug, '_') . '.xlsx', $grid);
