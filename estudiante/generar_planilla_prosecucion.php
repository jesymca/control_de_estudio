<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../funciones/functions.php';
require_once __DIR__ . '/../fpdf/fpdf.php';

// Verificar sesión
if (!isLoggedIn()) {
    die("Debe iniciar sesión para acceder a este documento.");
}

if (!isset($_GET['id'])) {
    die("ID de solicitud no proporcionado.");
}
$id_solicitud = intval($_GET['id']);

$solicitud = obtenerSolicitudProsecucionPorId($id_solicitud);
if (!$solicitud) {
    die("Solicitud de prosecución no encontrada.");
}

// Validación de seguridad para estudiantes: solo pueden ver su propia solicitud
if (isEstudiante() && (int)$solicitud['id_usuario'] !== (int)($_SESSION['user']['id'] ?? 0)) {
    die("No tiene permisos para consultar esta planilla.");
}

// Ubicación formateada
$nombresUbicacion = function_exists('obtenerNombresUbicacion') ? obtenerNombresUbicacion(
    $solicitud['estado'] ?? null,
    $solicitud['municipio'] ?? null,
    $solicitud['parroquia'] ?? null
) : [
    'estado_nombre' => $solicitud['estado'],
    'municipio_nombre' => $solicitud['municipio'],
    'parroquia_nombre' => $solicitud['parroquia']
];

$solicitud['estado_nombre'] = $nombresUbicacion['estado_nombre'] ?: $solicitud['estado'];
$solicitud['municipio_nombre'] = $nombresUbicacion['municipio_nombre'] ?: $solicitud['municipio'];
$solicitud['parroquia_nombre'] = $nombresUbicacion['parroquia_nombre'] ?: $solicitud['parroquia'];

if (!function_exists('txt')) {
    function txt($texto) {
        if (function_exists('formatearTextoPDF')) return formatearTextoPDF($texto);
        if ($texto === null || $texto === '') return '';
        if (function_exists('iconv')) {
            $c = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', (string)$texto);
            if ($c !== false) return $c;
        }
        if (function_exists('mb_convert_encoding')) return mb_convert_encoding((string)$texto, 'ISO-8859-1', 'UTF-8');
        if (function_exists('utf8_decode')) return utf8_decode((string)$texto);
        return (string)$texto;
    }
}

class PDF_PlanillaProsecucion extends FPDF {
    function Header() {
        if (file_exists('../images/uptpc.png')) {
            $this->Image('../images/uptpc.png', 15, 8, 22);
        }
        $this->SetY(8);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 4, txt('REPÚBLICA BOLIVARIANA DE VENEZUELA'), 0, 1, 'C');
        $this->Cell(0, 4, txt('MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN UNIVERSITARIA'), 0, 1, 'C');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(0, 3.5, txt('UNIVERSIDAD POLITÉCNICA TERRITORIAL DE PUERTO CABELLO'), 0, 1, 'C');
        $this->Cell(0, 3.5, txt('DIRECCIÓN DE CONTROL DE ESTUDIOS Y GESTIÓN ACADÉMICA'), 0, 1, 'C');
        $this->Ln(3);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 5, txt('PLANILLA DE INSCRIPCIÓN PARA PROSECUCIÓN DE ESTUDIOS'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 8.5);
        $this->Cell(0, 4, txt('(CONTINUACIÓN DE ESTUDIOS DE T.S.U. A INGENIERÍA / LICENCIATURA)'), 0, 1, 'C');
        $this->Ln(4);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, txt('Página ') . $this->PageNo() . '/{nb} - UPTPC Sistema de Control de Estudios', 0, 0, 'C');
    }

    function Seccion($titulo) {
        $this->SetFillColor(230, 240, 250);
        $this->SetFont('Arial', 'B', 9.5);
        $this->Cell(185, 7, "  " . txt(strtoupper($titulo)), 1, 1, 'L', true);
    }

    function Dato($label, $valor, $ancho) {
        $this->SetFont('Arial', 'B', 8);
        $labelTxt = txt($label . ': ');
        $w = $this->GetStringWidth($labelTxt);
        $this->Cell($w, 7, $labelTxt, 'B', 0); 
        $this->SetFont('Arial', '', 8.5);
        $this->Cell($ancho - $w, 7, txt($valor ?: 'N/A'), 'B', 0);
    }
}

$pdf = new PDF_PlanillaProsecucion('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

// Cuadro de Control y Código
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(95, 6, txt('N° DE REGISTRO: ') . sprintf("PROS-%05d", $solicitud['id']), 1, 0, 'L');
$pdf->Cell(90, 6, txt('FECHA DE REGISTRO: ') . date('d/m/Y h:i A', strtotime($solicitud['fecha_solicitud'] ?? 'now')), 1, 1, 'R');
$pdf->Ln(2);

// --- 1. IDENTIFICACIÓN DEL ESTUDIANTE ---
$pdf->Seccion('1. DATOS DE IDENTIFICACIÓN DEL ASPIRANTE');
$y_ini = $pdf->GetY();
$pdf->SetX(15);
$pdf->Dato('Cédula de Identidad', $solicitud['cedula_estudiante'] ?: $solicitud['idusuario'], 50);
$pdf->Dato('Apellidos y Nombres', $solicitud['nombre_estudiante'], 135);
$pdf->Ln(7); $pdf->SetX(15);
$pdf->Dato('Género', $solicitud['genero'], 40);
$pdf->Dato('Estado Civil', $solicitud['edo_civil'], 45);
$fecha_nac = (!empty($solicitud['fecha_nac']) && $solicitud['fecha_nac'] !== '0000-00-00') ? date('d/m/Y', strtotime($solicitud['fecha_nac'])) : 'N/A';
$pdf->Dato('Fecha de Nacimiento', $fecha_nac, 50);
$pdf->Dato('Estatus', strtoupper($solicitud['estatus'] ?? 'PENDIENTE'), 50);
$pdf->Ln(7);

// --- 2. UBICACIÓN Y CONTACTO ---
$pdf->Ln(2);
$pdf->Seccion('2. INFORMACIÓN DE CONTACTO Y HABITACIÓN');
$y_ini = $pdf->GetY();
$pdf->SetX(15);
$pdf->Dato('Teléfono Principal', $solicitud['telefono_contacto'], 55);
$pdf->Dato('Correo Electrónico', $solicitud['email_contacto'], 130);
$pdf->Ln(7); $pdf->SetX(15);
$pdf->Dato('Estado', $solicitud['estado_nombre'], 60);
$pdf->Dato('Municipio', $solicitud['municipio_nombre'], 65);
$pdf->Dato('Parroquia', $solicitud['parroquia_nombre'], 60);
$pdf->Ln(7); $pdf->SetX(15);
$pdf->SetFont('Arial', 'B', 8); 
$pdf->Cell(28, 7, txt('Dirección Actual: '), 'B', 0);
$pdf->SetFont('Arial', '', 8.5); 
$pdf->MultiCell(157, 7, txt($solicitud['direccion_actual'] ?: 'No especificada'), 'B');
$pdf->Ln(2);

// --- 3. DATOS DE PROSECUCIÓN ACADÉMICA ---
$pdf->Seccion('3. DATOS ACADÉMICOS Y PROGRAMA DE PROSECUCIÓN');
$pdf->SetX(15);
$pdf->Dato('Programa de Origen (T.S.U.)', $solicitud['nombre_carrera_origen'], 120);
$pdf->Dato('Título Obtenido', $solicitud['titulo_obtenido'] ?: 'T.S.U.', 65);
$pdf->Ln(7); $pdf->SetX(15);
$pdf->Dato('Programa a Cursar (Prosecución)', $solicitud['titulo_solicitado'], 120);
$pdf->Dato('Turno Solicitado', $solicitud['turno'], 65);
$pdf->Ln(7); $pdf->SetX(15);
$pdf->Dato('Sede Académica', $solicitud['sede'], 95);
$pdf->Dato('Condición de Ingreso', 'Prosecución de Estudios (Art. 13 Normativa UPTPC)', 90);
$pdf->Ln(7);

if (!empty($solicitud['observaciones'])) {
    $pdf->SetX(15);
    $pdf->SetFont('Arial', 'B', 8); 
    $pdf->Cell(25, 7, txt('Observaciones: '), 'B', 0);
    $pdf->SetFont('Arial', '', 8.5); 
    $pdf->MultiCell(160, 7, txt($solicitud['observaciones']), 'B');
    $pdf->Ln(2);
}

// --- CONSTANCIA Y COMPROMISO ---
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 7.5);
$textoDeclaracion = "DECLARACIÓN JURADA: El estudiante abajo firmante declara bajo fe de juramento que la información suministrada es fidedigna y que cumple a cabalidad con la aprobación académica del ciclo correspondiente a Técnico Superior Universitario para ingresar formalmente al ciclo de Ingeniería o Licenciatura según el plan curricular institucional.";
$pdf->MultiCell(185, 4, txt($textoDeclaracion), 1, 'J');

// --- INSTRUCCIONES ---
$pdf->Ln(3);
$pdf->SetFillColor(255, 250, 235);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(185, 5, txt('  INSTRUCCIONES PARA FORMALIZAR LA PROSECUCIÓN:'), 1, 1, 'L', true);
$pdf->SetFont('Arial', '', 7.5);
$instrucciones = "1. Imprima dos (2) ejemplares de esta planilla.\n2. Consigne copia simple del Título de T.S.U. y fondo negro, notas certificadas y cédula de identidad ampliada en Control de Estudios.\n3. Acuda en el cronograma establecido para la asignación de sección y carga académica.";
$pdf->MultiCell(185, 4, txt($instrucciones), 1, 'L', true);

// --- FIRMAS ---
$pdf->Ln(18);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(92, 5, '__________________________________', 0, 0, 'C');
$pdf->Cell(92, 5, '__________________________________', 0, 1, 'C');
$pdf->Cell(92, 4, txt($solicitud['nombre_estudiante']), 0, 0, 'C');
$pdf->Cell(92, 4, txt('Dirección de Control de Estudios'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(92, 4, txt('C.I. ') . txt($solicitud['cedula_estudiante'] ?: $solicitud['idusuario']), 0, 0, 'C');
$pdf->Cell(92, 4, txt('Firma y Sello Autorizado UPTPC'), 0, 1, 'C');

ob_end_clean();
$pdf->Output('I', 'Planilla_Prosecucion_' . ($solicitud['cedula_estudiante'] ?: $solicitud['idusuario']) . '.pdf');
?>
