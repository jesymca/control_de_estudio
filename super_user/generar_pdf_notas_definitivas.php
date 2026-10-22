<?php
// Iniciar buffer de salida para evitar errores de FPDF
ob_start();
require_once('../funciones/functions.php');

// Verificar autenticación y permisos
if (!isLoggedIn()) {
    ob_end_clean();
    exit("Acceso denegado");
}

// Obtener parámetros por POST (prioritario) o GET (compatibilidad)
$docente_id = isset($_POST['docente_id']) ? (int)$_POST['docente_id'] : (isset($_GET['docente_id']) ? (int)$_GET['docente_id'] : 0);
$materia_id = isset($_POST['materia_id']) ? (int)$_POST['materia_id'] : (isset($_GET['materia_id']) ? (int)$_GET['materia_id'] : 0);
$periodo_id = isset($_POST['periodo_id']) ? (int)$_POST['periodo_id'] : (isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : 0);

if (!$docente_id || !$materia_id || !$periodo_id) {
    ob_end_clean();
    exit("Error: Parámetros incompletos");
}

// Generar PDF
try {
    $resultado = generarPDFNotasDefinitivas($docente_id, $materia_id, $periodo_id);
    if (!$resultado) {
        ob_end_clean();
        exit("Error: No se pudo generar el reporte PDF.");
    }
} catch (Exception $e) {
    ob_end_clean();
    exit("Error al generar PDF: " . $e->getMessage());
}
ob_end_flush();