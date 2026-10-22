<?php
// Evitar que cualquier error previo ensucie la salida del PDF
ob_start();

require_once __DIR__ . '/../funciones/functions.php';
require_once __DIR__ . '/../fpdf/fpdf.php';

// Seguridad y datos
if (!isLoggedIn() || !isEstudiante()) die('Acceso denegado');
$estudiante_id = (int)$_SESSION['user']['id'];
$seccion_estudiante = obtenerSeccionEstudiante($db, $estudiante_id);
if (!$seccion_estudiante) die('No se encontró sección asignada');

$id_seccion = $seccion_estudiante['id_seccion'];
$turno_seccion = $seccion_estudiante['turno'] ?? 'Diurno';
$horarios = obtenerHorariosSeccion($db, $id_seccion);
$horarios = is_array($horarios) ? $horarios : [];

// Configuración de la cuadrícula
$inicio_h = ($turno_seccion == 'Diurno') ? 7 : 17;
$fin_h = ($turno_seccion == 'Diurno') ? 18 : 22; 
$intervalo_minutos = 30;
$altura_fila = 8;
$ancho_hora = 20;

// Función moderna para reemplazar utf8_decode
function txt_pdf($texto) {
    return mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
}

class PDF_Horario extends FPDF {
    function Header() {
        if(file_exists('../images/uptpc.png')) {
            $this->Image('../images/uptpc.png', 10, 8, 18);
        }
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, txt_pdf('REPÚBLICA BOLIVARIANA DE VENEZUELA'), 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 5, txt_pdf('UNIVERSIDAD POLITÉCNICA TERRITORIAL DE PUERTO CABELLO'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, txt_pdf('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF_Horario('L', 'mm', 'Letter');
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

// Título e Información
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, txt_pdf('HORARIO DE CLASES - SECCIÓN: ' . ($seccion_estudiante['codigo_seccion'] ?? 'N/A')), 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(140, 5, txt_pdf('Carrera: ' . ($seccion_estudiante['nombre_carrera'] ?? 'N/A')), 0, 0);
$pdf->Cell(0, 5, txt_pdf('Período: ' . ($seccion_estudiante['nombre_periodo'] ?? 'N/A')), 0, 1);
$pdf->Ln(5);

// Cabecera de tabla
$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$ancho_dia = ($pdf->GetPageWidth() - 20 - $ancho_hora) / 6;

$pdf->SetFillColor(44, 62, 80); 
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($ancho_hora, 10, 'Hora', 1, 0, 'C', true);
foreach($dias as $d) {
    $pdf->Cell($ancho_dia, 10, txt_pdf($d), 1, 0, 'C', true);
}
$pdf->Ln();

$y_tabla_inicio = $pdf->GetY();
$x_tabla_inicio = $pdf->GetX();

// Dibujar cuadrícula base
$pdf->SetTextColor(0);
$pdf->SetFont('Arial', '', 8);
for ($h = $inicio_h; $h < $fin_h; $h++) {
    for ($m = 0; $m < 60; $m += $intervalo_minutos) {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell($ancho_hora, $altura_fila, sprintf("%02d:%02d", $h, $m), 1, 0, 'C', true);
        for ($i = 0; $i < 6; $i++) {
            $pdf->Cell($ancho_dia, $altura_fila, '', 1, 0);
        }
        $pdf->Ln();
    }
}

// Superponer materias
foreach ($horarios as $clase) {
    $dia_idx = (int)$clase['dia']; 
    $h_ini = strtotime($clase['hora_inicio']);
    $h_fin = strtotime($clase['hora_fin']);
    
    $minutos_desde_inicio = (date('H', $h_ini) - $inicio_h) * 60 + date('i', $h_ini);
    $filas_offset = $minutos_desde_inicio / $intervalo_minutos;
    $y_materia = $y_tabla_inicio + ($filas_offset * $altura_fila);
    
    $duracion_minutos = ($h_fin - $h_ini) / 60;
    $altura_materia = ($duracion_minutos / $intervalo_minutos) * $altura_fila;
    $x_materia = $x_tabla_inicio + $ancho_hora + ($dia_idx * $ancho_dia);
    
    $pdf->SetXY($x_materia, $y_materia);
    $pdf->SetFillColor(214, 234, 248);
    $pdf->SetFont('Arial', 'B', 7);
    
    // Contenido de la celda
    $texto_materia = $clase['nombre_materia'] . "\n" . ($clase['aula'] ?? '');
    $pdf->MultiCell($ancho_dia, 4, txt_pdf($texto_materia), 1, 'C', true);
}

// Limpiar cualquier salida extraña y generar
ob_end_clean();
$pdf->Output('I', 'Horario_Seccion.pdf');