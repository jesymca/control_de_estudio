<?php
// Evitar que el archivo de funciones ejecute bloques de procesamiento
$__saved_GET = $_GET;
unset($_GET['materia_id'], $_GET['seccion_id'], $_GET['docente_id'], $_GET['periodo_id']);

require_once(__DIR__ . '/../funciones/functions.php');
require_once(__DIR__ . '/../fpdf/fpdf.php');

// Restaurar GET original
$_GET = $__saved_GET;

// Verificar autenticación y rol
if (!isLoggedIn() || !isDocente()) {
    die("Acceso denegado");
}

// Obtener ID del docente
if (isset($_SESSION['user']['id'])) {
    $docente_id = (int)$_SESSION['user']['id'];
} else {
    die("Error: No se pudo identificar al usuario");
}

// Verificar parámetros
if (!isset($_GET['seccion_id']) || !isset($_GET['materia_id'])) {
    die("Error: Parámetros incompletos");
}

$seccion_id = (int)$_GET['seccion_id'];
$materia_id = (int)$_GET['materia_id'];

// Verificar acceso del docente
if (!verificarAccesoDocente($docente_id, $seccion_id, $materia_id)) {
    die("Acceso denegado: el docente no está asignado a esa sección y materia.");
}

// Obtener información completa
$info = obtenerInfoCompletaSeccionMateria($seccion_id, $materia_id, $docente_id);
if (!$info) {
    die("Error: No se encontró información de la sección/materia.");
}

// Obtener lista de estudiantes
$estudiantes = obtenerEstudiantesSeccion($seccion_id);
if (empty($estudiantes)) {
    die("Error: No hay estudiantes activos en la sección seleccionada.");
}

function convertirTexto($texto) {
    if ($texto === null) return '';
    return mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
}

ob_clean();

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'REGISTRO DE NOTAS TRIMESTRALES', 0, 1, 'C');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'DATOS DEL GRUPO', 0, 1, 'L');
$pdf->SetFont('Arial', '', 9);

$ancho_label = 35;

$pdf->Cell($ancho_label, 5, 'Docente:', 0, 0);
$pdf->Cell(0, 5, ': ' . convertirTexto($info['nombre_docente']), 0, 1);

$pdf->Cell($ancho_label, 5, 'Cedula Docente:', 0, 0);
$pdf->Cell(0, 5, ': ' . convertirTexto($info['cedula_docente']), 0, 1);

$pdf->Cell($ancho_label, 5, 'Materia:', 0, 0);
$pdf->Cell(0, 5, ': ' . convertirTexto($info['nombre_materia']), 0, 1);

$pdf->Cell($ancho_label, 5, 'Codigo Materia:', 0, 0);
$pdf->Cell(0, 5, ': ' . convertirTexto($info['cod_materia']), 0, 1);

$pdf->Cell($ancho_label, 5, 'Seccion:', 0, 0);
$pdf->Cell(0, 5, ': ' . convertirTexto($info['codigo_seccion']), 0, 1);

$pdf->Cell($ancho_label, 5, 'Trayecto:', 0, 0);
$pdf->Cell(0, 5, ': ' . convertirTexto($info['nombre_trayecto']), 0, 1);

$pdf->Cell($ancho_label, 5, 'Carrera:', 0, 0);
$pdf->Cell(0, 5, ': ' . convertirTexto($info['nombre_carrera']), 0, 1);

$pdf->Cell($ancho_label, 5, 'Periodo:', 0, 0);
$pdf->Cell(0, 5, ': ' . convertirTexto($info['nombre_periodo']), 0, 1);

$pdf->Cell($ancho_label, 5, 'Fecha:', 0, 0);
$pdf->Cell(0, 5, ': ' . date('d/m/Y'), 0, 1);

$pdf->Ln(4);

// Encabezados de tabla
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(220, 220, 220);

$pdf->Cell(8, 6, 'N', 1, 0, 'C', true);
$pdf->Cell(20, 6, 'CEDULA', 1, 0, 'C', true);
$pdf->Cell(60, 6, 'ESTUDIANTE', 1, 0, 'C', true);
$pdf->Cell(15, 6, 'T1', 1, 0, 'C', true);
$pdf->Cell(15, 6, 'T2', 1, 0, 'C', true);
$pdf->Cell(15, 6, 'T3', 1, 0, 'C', true);
$pdf->Cell(20, 6, 'PROMEDIO', 1, 0, 'C', true);
$pdf->Cell(40, 6, 'OBSERVACIONES', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 8);

$fila = 1;
foreach ($estudiantes as $estudiante) {
    $cedula = $estudiante['idusuario'] ?? $estudiante['cedula'] ?? '';
    $nombre = $estudiante['nombre'] ?? $estudiante['nombres'] ?? '';
    
    $pdf->Cell(8, 6, $fila, 1, 0, 'C');
    $pdf->Cell(20, 6, convertirTexto($cedula), 1, 0, 'L');
    $pdf->Cell(60, 6, convertirTexto(substr($nombre, 0, 28)), 1, 0, 'L');
    $pdf->Cell(15, 6, '', 1, 0, 'C');
    $pdf->Cell(15, 6, '', 1, 0, 'C');
    $pdf->Cell(15, 6, '', 1, 0, 'C');
    $pdf->Cell(20, 6, '', 1, 0, 'C');
    $pdf->Cell(40, 6, '', 1, 1, 'C');
    $fila++;
}

$pdf->Output('I', 'Planilla_Notas_Seccion_' . $seccion_id . '.pdf');






/**
 * Obtener información completa de sección, materia y docente
 */
function obtenerInfoCompletaSeccionMateria($seccion_id, $materia_id, $docente_id) {
    global $db;
    
    $query = "SELECT 
                u.nombre as nombre_docente,
                u.idusuario as cedula_docente,
                m.nombre_materia,
                m.cod_materia,
                t.nombre_trayecto,
                s.codigo_seccion,
                c.nombre_carrera,
                pa.nombre_periodo
              FROM secciones s
              INNER JOIN carreras c ON s.id_carrera = c.id_carrera
              CROSS JOIN materias m 
              LEFT JOIN trayectos t ON m.trayecto = t.numero_trayecto
              LEFT JOIN periodos_academicos pa ON pa.activo = 1
              LEFT JOIN users u ON u.id = $docente_id
              WHERE s.id_seccion = $seccion_id 
              AND m.id_materia = $materia_id
              LIMIT 1";
    
    $result = $db->query($query);
    return $result->fetch_assoc();
}









?>