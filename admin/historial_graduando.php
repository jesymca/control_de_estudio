<?php
// Archivo: historial_graduando.php
require_once __DIR__ . '/../funciones/functions.php';

$estudiante_id = isset($_POST['estudiante_id']) ? intval($_POST['estudiante_id']) : (isset($_GET['estudiante_id']) ? intval($_GET['estudiante_id']) : 0);

if ($estudiante_id <= 0) {
    die("Error: No se proporcionó el ID del estudiante para generar el reporte de Graduando.");
}

generarPDFHistorialDesglozado('graduando', $estudiante_id);
