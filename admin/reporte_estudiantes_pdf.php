<?php
// Archivo: admin/reporte_estudiantes_pdf.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../funciones/functions.php';
require_once __DIR__ . '/../funciones/reporte_estudiantes_helper.php';

// Validar inicio de sesión y rol con permisos administrativos
if (!isLoggedIn() || (!isAdmin() && !isSuperUser())) {
    $_SESSION['msg'] = "Debes iniciar sesión con permisos administrativos para acceder a este reporte.";
    header('location: ../login.php');
    exit();
}

// Cargar y verificar permisos
cargarPermisosUsuario();
verificarPermiso('ver_estudiantes');

// Parámetros filtrados (priorizando POST, fallback GET)
$ids = $_POST['ids'] ?? $_GET['ids'] ?? '';
$incluirEstadisticas = trim($_POST['estadisticas'] ?? $_GET['estadisticas'] ?? 'si');
$tipoReporte = trim($_POST['tipo_reporte'] ?? $_GET['tipo_reporte'] ?? 'estudiantes');
$admin_nombre = $_SESSION['user']['nombre'] ?? 'Administrador';

generarPDFReporteEstudiantes($ids, $incluirEstadisticas, $admin_nombre, $tipoReporte);
