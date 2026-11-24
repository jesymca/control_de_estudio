<?php
// Archivo: admin/reporte_pagos_csv.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../funciones/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('location: ../login.php');
    exit();
}

$fecha_desde   = trim($_POST['fecha_desde'] ?? $_GET['fecha_desde'] ?? date('Y-m-01'));
$fecha_hasta   = trim($_POST['fecha_hasta'] ?? $_GET['fecha_hasta'] ?? date('Y-m-d'));
$filtro_status = trim($_POST['filtro_status'] ?? $_GET['filtro_status'] ?? '');
$filtro_tipo   = trim($_POST['filtro_tipo'] ?? $_GET['filtro_tipo'] ?? '');
$filtro_metodo = trim($_POST['filtro_metodo'] ?? $_GET['filtro_metodo'] ?? '');

$pagos = obtenerPagosPorRangoFechas($fecha_desde, $fecha_hasta, $filtro_status, $filtro_tipo, $filtro_metodo, 'ASC');

$filename = 'reporte_pagos_' . date('Ymd_His', strtotime($fecha_desde)) . '_al_' . date('Ymd_His', strtotime($fecha_hasta)) . '.csv';

// Limpiar cualquier buffer de salida previo
if (ob_get_level()) ob_end_clean();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
// UTF-8 BOM para que Excel abra con tildes correctamente
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, [
    'ID Pago',
    'Fecha Efectiva',
    'Hora',
    'Cédula Estudiante',
    'Nombre Estudiante',
    'Carrera / Programa',
    'Concepto / Arancel',
    'Forma de Pago',
    'Banco Emisor',
    'Banco Destino UPTPC',
    'N° Referencia',
    'Monto (Bs)',
    'Estado',
    'Registrado Por',
    'Observaciones'
], ';', '"', "\\");

foreach ($pagos as $p) {
    fputcsv($output, [
        $p['id'],
        $p['fecha_efectiva'] ?: date('Y-m-d', strtotime($p['fecha_pago'])),
        date('H:i:s', strtotime($p['fecha_pago'])),
        $p['cedula'],
        $p['nombre_estudiante'],
        $p['nombre_carrera'],
        $p['nombre_tipo_pago'],
        $p['metodo_pago'] ?: 'N/D',
        $p['banco_origen'] ?: 'N/D',
        $p['nombre_banco_destino'] ?: 'Directo / Institucional',
        $p['referencia'] ?: 'S/R',
        number_format((float)$p['monto'], 2, '.', ''),
        strtoupper($p['status_pago'] ?? 'PENDIENTE'),
        $p['nombre_registrador'] ?: 'Sistema',
        $p['observaciones'] ?: ''
    ], ';', '"', "\\");
}

fclose($output);
exit;
