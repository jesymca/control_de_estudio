<?php
// Archivo: admin/reporte_pagos_excel.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../funciones/functions.php';
require_once __DIR__ . '/../funciones/reporte_pagos_excel_helper.php';

// Validar sesión y permisos de administrador
if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['msg'] = "Debes iniciar sesión como administrador para acceder a este reporte.";
    header('location: ../login.php');
    exit();
}

cargarPermisosUsuario();
verificarPermiso('pagos');

$fecha_desde   = trim($_POST['fecha_desde'] ?? $_GET['fecha_desde'] ?? date('Y-01-01'));
$fecha_hasta   = trim($_POST['fecha_hasta'] ?? $_GET['fecha_hasta'] ?? date('Y-m-d'));
$filtro_status = trim($_POST['filtro_status'] ?? $_GET['filtro_status'] ?? '');
$filtro_tipo   = trim($_POST['filtro_tipo'] ?? $_GET['filtro_tipo'] ?? '');
$filtro_metodo = trim($_POST['filtro_metodo'] ?? $_GET['filtro_metodo'] ?? '');

$admin_nombre = $_SESSION['user']['nombre'] ?? 'Administrador';

generarExcelReportePagos($fecha_desde, $fecha_hasta, $filtro_status, $filtro_tipo, $filtro_metodo, $admin_nombre, 'D');
