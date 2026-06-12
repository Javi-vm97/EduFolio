<?php
/**
 * EduFolio - Cierre de sesion.
 */
require_once __DIR__ . '/../app/auth.php';

cerrar_sesion();
session_start();           // reinicia para poder guardar el mensaje flash
flash('exito', 'Has cerrado sesion correctamente.');
redirigir('login.php');
