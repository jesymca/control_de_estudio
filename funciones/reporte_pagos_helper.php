<?php
/**
 * ==============================================================================
 * GENERADOR DE REPORTE PDF DE CONTROL DE PAGOS - UPTPC
 * Generación oficial de reporte de recaudación y pagos filtrado por fecha.
 * 
 * Formato institucional en orientación horizontal (Landscape) con:
 * - Agrupación cronológica día por día (Fecha por fecha)
 * - Subtotales diarios y Gran Total del período
 * - Desglose por Método de Pago y por Concepto / Arancel
 * - Membrete institucional con logos UPTPC y MPPEU
 * - Bloque de firmas de auditoría y sello
 * ==============================================================================
 */

require_once __DIR__ . '/../fpdf/fpdf.php';
require_once __DIR__ . '/functions.php';

if (!function_exists('conv_pdf_pagos')) {
    function conv_pdf_pagos($texto) {
        if ($texto === null || $texto === '') return '';
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding((string)$texto, 'ISO-8859-1', 'UTF-8');
        } elseif (function_exists('iconv')) {
            return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', (string)$texto);
        } else {
            return @utf8_decode((string)$texto);
        }
    }
}

if (!function_exists('obtenerDiaSemanaEspanol')) {
    function obtenerDiaSemanaEspanol($fecha_ymd) {
        $timestamp = strtotime($fecha_ymd);
        if (!$timestamp) return '';
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $w = (int)date('w', $timestamp);
        return $dias[$w] ?? '';
    }
}

if (!class_exists('ReportePagosPDF')) {
    class ReportePagosPDF extends FPDF {
        public $fecha_desde = '';
        public $fecha_hasta = '';
        public $status_filtro = 'TODOS';
        public $nombre_admin = 'Administración';
        public $mostrar_cabecera_tabla = true;

        function Header() {
            // Logos institucionales
            $logo_uptpc = __DIR__ . '/../images/uptpc.png';
            if (file_exists($logo_uptpc)) {
                $this->Image($logo_uptpc, 10, 8, 16, 16);
            }
            $logo_mppeu = __DIR__ . '/../images/mppeu_bb.png';
            if (file_exists($logo_mppeu)) {
                $this->Image($logo_mppeu, 270, 8, 17, 16);
            }

            $this->SetY(8);
            $this->SetTextColor(0, 0, 0);
            $this->SetDrawColor(0, 0, 0);

            // Membrete República
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(0, 3.5, conv_pdf_pagos('REPÚBLICA BOLIVARIANA DE VENEZUELA'), 0, 1, 'C');
            $this->Cell(0, 3.5, conv_pdf_pagos('MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN UNIVERSITARIA'), 0, 1, 'C');
            $this->Cell(0, 3.5, conv_pdf_pagos('UNIVERSIDAD POLITÉCNICA TERRITORIAL DE PUERTO CABELLO'), 0, 1, 'C');
            $this->SetFont('Arial', '', 7.5);
            $this->Cell(0, 3.5, conv_pdf_pagos('DEPARTAMENTO DE ADMINISTRACIÓN Y CONTROL DE ESTUDIOS'), 0, 1, 'C');
            
            // Línea separadora
            $this->SetLineWidth(0.3);
            $this->Line(10, 24, 287, 24);
            $this->Ln(3);

            // Título Principal
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 4.5, conv_pdf_pagos('REPORTE DETALLADO DE CONTROL DE PAGOS Y RECAUDACIÓN'), 0, 1, 'C');

            // Subtítulo con rango y filtros
            $this->SetFont('Arial', '', 7.5);
            $subt = "PERÍODO: " . date('d/m/Y', strtotime($this->fecha_desde)) . " AL " . date('d/m/Y', strtotime($this->fecha_hasta));
            $subt .= "   |   ESTADO: " . strtoupper($this->status_filtro ?: 'TODOS');
            $subt .= "   |   FECHA DE EMISIÓN: " . date('d/m/Y h:i A');
            $this->Cell(0, 3.5, conv_pdf_pagos($subt), 0, 1, 'C');
            $this->Ln(2);

            // Cabecera de tabla de transacciones si está en páginas posteriores
            if ($this->PageNo() > 1 && $this->mostrar_cabecera_tabla) {
                $this->imprimirCabeceraTabla();
            }
        }

        function Footer() {
            $this->SetY(-10);
            $this->SetFont('Arial', 'I', 7);
            $this->SetTextColor(80, 80, 80);
            $this->Cell(130, 4, conv_pdf_pagos('UPTPC - Sistema de Control de Estudios | Reporte Oficial de Recaudación'), 0, 0, 'L');
            $this->Cell(0, 4, conv_pdf_pagos('Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'R');
        }

        function imprimirCabeceraTabla() {
            $this->SetFont('Arial', 'B', 7);
            $this->SetFillColor(230, 230, 230);
            $this->SetDrawColor(0, 0, 0);
            $this->SetTextColor(0, 0, 0);

            // Anchos totales = 277 mm
            $this->Cell(8, 5, conv_pdf_pagos('N°'), 1, 0, 'C', true);
            $this->Cell(14, 5, conv_pdf_pagos('HORA'), 1, 0, 'C', true);
            $this->Cell(22, 5, conv_pdf_pagos('CÉDULA'), 1, 0, 'C', true);
            $this->Cell(50, 5, conv_pdf_pagos('ESTUDIANTE'), 1, 0, 'L', true);
            $this->Cell(38, 5, conv_pdf_pagos('CARRERA / PNF'), 1, 0, 'L', true);
            $this->Cell(40, 5, conv_pdf_pagos('CONCEPTO / ARANCEL'), 1, 0, 'L', true);
            $this->Cell(38, 5, conv_pdf_pagos('MÉTODO / BANCO'), 1, 0, 'L', true);
            $this->Cell(25, 5, conv_pdf_pagos('REFERENCIA'), 1, 0, 'C', true);
            $this->Cell(18, 5, conv_pdf_pagos('ESTADO'), 1, 0, 'C', true);
            $this->Cell(24, 5, conv_pdf_pagos('MONTO (Bs)'), 1, 1, 'R', true);
        }
    }
}

/**
 * Función principal para generar el reporte de pagos en PDF
 */
if (!function_exists('generarPDFReportePagos')) {
function generarPDFReportePagos($fecha_desde = '', $fecha_hasta = '', $filtro_status = '', $filtro_tipo = '', $filtro_metodo = '', $admin_nombre = '') {
    if (ob_get_length()) ob_clean();

    $fecha_desde = trim($fecha_desde ?? '');
    $fecha_hasta = trim($fecha_hasta ?? '');
    if (empty($fecha_desde)) $fecha_desde = date('Y-m-01');
    if (empty($fecha_hasta)) $fecha_hasta = date('Y-m-d');

    if ($fecha_desde > $fecha_hasta) {
        $tmp = $fecha_desde;
        $fecha_desde = $fecha_hasta;
        $fecha_hasta = $tmp;
    }

    $stats = obtenerEstadisticasPagosRango($fecha_desde, $fecha_hasta, $filtro_status, $filtro_tipo, $filtro_metodo);

    $pdf = new ReportePagosPDF('L', 'mm', 'A4');
    $pdf->AliasNbPages();
    $pdf->fecha_desde = $fecha_desde;
    $pdf->fecha_hasta = $fecha_hasta;
    $pdf->status_filtro = $filtro_status ? strtoupper($filtro_status) : 'TODOS';
    $pdf->nombre_admin = $admin_nombre ?: 'Control de Estudios';
    $pdf->SetMargins(10, 8, 10);
    $pdf->SetAutoPageBreak(true, 12);
    $pdf->AddPage();

    // ---------------------------------------------------------
    // 1. TARJETAS / CAJAS DE RESUMEN EJECUTIVO (PÁGINA 1)
    // ---------------------------------------------------------
    $pdf->SetFont('Arial', 'B', 8);

    // Caja 1: Total Recaudado (Aprobado)
    $pdf->SetFillColor(240, 248, 255);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->Rect(10, 29, 90, 13, 'DF');
    $pdf->SetXY(12, 30.5);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(86, 3.5, conv_pdf_pagos('TOTAL RECAUDADO (PAGOS APROBADOS):'), 0, 1, 'L');
    $pdf->SetXY(12, 34);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(86, 6, conv_pdf_pagos('Bs ' . number_format($stats['aprobados_monto'], 2, ',', '.')), 0, 0, 'L');

    // Caja 2: Total General Declarado
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Rect(103, 29, 90, 13, 'DF');
    $pdf->SetXY(105, 30.5);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(86, 3.5, conv_pdf_pagos('TOTAL GENERAL OPERACIONES DECLARADAS:'), 0, 1, 'L');
    $pdf->SetXY(105, 34);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(86, 6, conv_pdf_pagos('Bs ' . number_format($stats['total_monto'], 2, ',', '.') . '  (' . $stats['total_operaciones'] . ' Transacciones)'), 0, 0, 'L');

    // Caja 3: Estado de las Operaciones
    $pdf->SetFillColor(250, 250, 250);
    $pdf->Rect(196, 29, 91, 13, 'DF');
    $pdf->SetXY(198, 30.5);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(87, 3.5, conv_pdf_pagos('BALANCE DE TRANSACCIONES:'), 0, 1, 'L');
    $pdf->SetXY(198, 34.5);
    $pdf->SetFont('Arial', '', 7);
    $txt_bal = "Aprobados: " . $stats['aprobados_cant'] . " (Bs " . number_format($stats['aprobados_monto'], 2, ',', '.') . ") | ";
    $txt_bal .= "Pendientes: " . $stats['pendientes_cant'] . " | Rechazados: " . $stats['rechazados_cant'];
    $pdf->MultiCell(87, 3, conv_pdf_pagos($txt_bal), 0, 'L');

    $pdf->SetY(44);

    // Si no hay pagos en el rango
    if (empty($stats['por_fecha'])) {
        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->Cell(0, 10, conv_pdf_pagos('No se encontraron transacciones de pago registradas en el período seleccionado.'), 1, 1, 'C');
        $cedula_limpia = preg_replace('/[^a-zA-Z0-9]/', '_', $pdf->fecha_desde . '_' . $pdf->fecha_hasta);
        $pdf->Output('I', 'Reporte_Pagos_' . $cedula_limpia . '.pdf');
        exit;
    }

    // Imprimir cabecera de la tabla en página 1
    $pdf->imprimirCabeceraTabla();

    // ---------------------------------------------------------
    // 2. DETALLE DE PAGOS AGRUPADO FECHA POR FECHA
    // ---------------------------------------------------------
    $correlativo = 1;

    foreach ($stats['por_fecha'] as $fecha_grupo => $datos_dia) {
        $nombre_dia = obtenerDiaSemanaEspanol($fecha_grupo);
        $fecha_fmtd = date('d/m/Y', strtotime($fecha_grupo));

        // Verificar salto de página antes del encabezado del día
        if ($pdf->GetY() > 175) {
            $pdf->AddPage();
        }

        // Encabezado del Día (Fecha por fecha)
        $pdf->SetFont('Arial', 'B', 7.5);
        $pdf->SetFillColor(220, 225, 230);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetTextColor(0, 0, 0);

        $txt_dia_header = ">> FECHA: " . strtoupper($nombre_dia) . ", " . $fecha_fmtd;
        $txt_dia_header .= "  (" . $datos_dia['cantidad'] . " pago(s) registrados)";
        $pdf->Cell(253, 4.8, conv_pdf_pagos($txt_dia_header), 1, 0, 'L', true);
        $pdf->Cell(24, 4.8, conv_pdf_pagos('Bs ' . number_format($datos_dia['total_dia'], 2, ',', '.')), 1, 1, 'R', true);

        // Filas de pagos individuales
        foreach ($datos_dia['pagos'] as $pago) {
            if ($pdf->GetY() > 185) {
                $pdf->AddPage();
            }

            $monto_item = (float)($pago['monto'] ?? 0);
            $hora_str = !empty($pago['fecha_pago']) ? date('h:i A', strtotime($pago['fecha_pago'])) : '-';
            $cedula_str = $pago['cedula'] ?? '-';
            $nombre_str = substr($pago['nombre_estudiante'] ?? '-', 0, 32);
            $carrera_str = substr($pago['nombre_carrera'] ?? 'Sin Carrera', 0, 26);
            $concepto_str = substr($pago['nombre_tipo_pago'] ?? 'Arancel', 0, 26);
            
            $banco_dest = !empty($pago['nombre_banco_destino']) ? ' (' . substr($pago['nombre_banco_destino'], 0, 12) . ')' : '';
            $metodo_str = substr(($pago['metodo_pago'] ?: 'Pago') . $banco_dest, 0, 24);
            $ref_str = substr($pago['referencia'] ?: '-', 0, 16);
            $status_str = strtoupper($pago['status_pago'] ?? 'PENDIENTE');

            $pdf->SetFont('Arial', '', 6.8);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetTextColor(0, 0, 0);

            $pdf->Cell(8, 4.2, conv_pdf_pagos($correlativo), 1, 0, 'C');
            $pdf->Cell(14, 4.2, conv_pdf_pagos($hora_str), 1, 0, 'C');
            $pdf->Cell(22, 4.2, conv_pdf_pagos($cedula_str), 1, 0, 'C');
            $pdf->Cell(50, 4.2, conv_pdf_pagos($nombre_str), 1, 0, 'L');
            $pdf->Cell(38, 4.2, conv_pdf_pagos($carrera_str), 1, 0, 'L');
            $pdf->Cell(40, 4.2, conv_pdf_pagos($concepto_str), 1, 0, 'L');
            $pdf->Cell(38, 4.2, conv_pdf_pagos($metodo_str), 1, 0, 'L');
            $pdf->Cell(25, 4.2, conv_pdf_pagos($ref_str), 1, 0, 'C');
            
            // Estado con estilo
            $pdf->SetFont('Arial', 'B', 6.5);
            $pdf->Cell(18, 4.2, conv_pdf_pagos($status_str), 1, 0, 'C');
            
            // Monto
            $pdf->SetFont('Arial', 'B', 6.8);
            $pdf->Cell(24, 4.2, conv_pdf_pagos(number_format($monto_item, 2, ',', '.')), 1, 1, 'R');

            $correlativo++;
        }

        // Subtotal del Día
        if ($pdf->GetY() > 185) {
            $pdf->AddPage();
        }
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(229, 4.5, conv_pdf_pagos('SUBTOTAL DEL DÍA ' . $fecha_fmtd . ' (Aprobado: Bs ' . number_format($datos_dia['aprobado'], 2, ',', '.') . '):'), 1, 0, 'R', true);
        $pdf->Cell(24, 4.5, conv_pdf_pagos('Bs ' . number_format($datos_dia['total_dia'], 2, ',', '.')), 1, 0, 'R', true);
        $pdf->Cell(24, 4.5, '', 0, 1, 'C');
        $pdf->Ln(1);
    }

    // ---------------------------------------------------------
    // 3. GRAN TOTAL DEL PERÍODO
    // ---------------------------------------------------------
    if ($pdf->GetY() > 165) {
        $pdf->AddPage();
    }
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->SetFillColor(230, 235, 245);
    $pdf->Cell(229, 6, conv_pdf_pagos('GRAN TOTAL RECAUDADO EN EL PERÍODO (SOLO APROBADOS):'), 1, 0, 'R', true);
    $pdf->Cell(48, 6, conv_pdf_pagos('Bs ' . number_format($stats['aprobados_monto'], 2, ',', '.')), 1, 1, 'R', true);

    // ---------------------------------------------------------
    // 4. TABLAS RESUMEN POR MÉTODO Y POR CONCEPTO
    // ---------------------------------------------------------
    if ($pdf->GetY() > 140) {
        $pdf->AddPage();
    }
    $pdf->Ln(4);

    $y_resumen = $pdf->GetY();

    // Tabla Izquierda: Resumen por Método de Pago (135 mm)
    $pdf->SetXY(10, $y_resumen);
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->SetFillColor(235, 235, 235);
    $pdf->Cell(133, 4.5, conv_pdf_pagos('RESUMEN POR MÉTODO DE PAGO'), 1, 1, 'C', true);

    $pdf->SetX(10);
    $pdf->SetFont('Arial', 'B', 6.8);
    $pdf->Cell(65, 4.2, conv_pdf_pagos('Forma de Pago'), 1, 0, 'L');
    $pdf->Cell(23, 4.2, conv_pdf_pagos('Operaciones'), 1, 0, 'C');
    $pdf->Cell(45, 4.2, conv_pdf_pagos('Total Aprobado (Bs)'), 1, 1, 'R');

    $pdf->SetFont('Arial', '', 6.8);
    foreach ($stats['por_metodo'] as $nom_metodo => $info_met) {
        $pdf->SetX(10);
        $pdf->Cell(65, 4, conv_pdf_pagos(substr($nom_metodo, 0, 36)), 1, 0, 'L');
        $pdf->Cell(23, 4, conv_pdf_pagos($info_met['cantidad']), 1, 0, 'C');
        $pdf->Cell(45, 4, conv_pdf_pagos('Bs ' . number_format($info_met['aprobado'], 2, ',', '.')), 1, 1, 'R');
    }

    $y_fin_izq = $pdf->GetY();

    // Tabla Derecha: Resumen por Concepto / Arancel (135 mm)
    $pdf->SetXY(150, $y_resumen);
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->SetFillColor(235, 235, 235);
    $pdf->Cell(137, 4.5, conv_pdf_pagos('RESUMEN POR CONCEPTO / ARANCEL'), 1, 1, 'C', true);

    $pdf->SetXY(150, $y_resumen + 4.5);
    $pdf->SetFont('Arial', 'B', 6.8);
    $pdf->Cell(69, 4.2, conv_pdf_pagos('Concepto / Arancel'), 1, 0, 'L');
    $pdf->Cell(23, 4.2, conv_pdf_pagos('Operaciones'), 1, 0, 'C');
    $pdf->Cell(45, 4.2, conv_pdf_pagos('Total Aprobado (Bs)'), 1, 1, 'R');

    $pdf->SetFont('Arial', '', 6.8);
    $y_der_curr = $y_resumen + 8.7;
    foreach ($stats['por_concepto'] as $nom_concepto => $info_con) {
        $pdf->SetXY(150, $y_der_curr);
        $pdf->Cell(69, 4, conv_pdf_pagos(substr($nom_concepto, 0, 38)), 1, 0, 'L');
        $pdf->Cell(23, 4, conv_pdf_pagos($info_con['cantidad']), 1, 0, 'C');
        $pdf->Cell(45, 4, conv_pdf_pagos('Bs ' . number_format($info_con['aprobado'], 2, ',', '.')), 1, 1, 'R');
        $y_der_curr += 4;
    }

    $max_y = max($y_fin_izq, $y_der_curr);
    $pdf->SetY($max_y + 6);

    // ---------------------------------------------------------
    // 5. BLOQUE DE FIRMAS Y CONFORMIDAD INSTITUCIONAL
    // ---------------------------------------------------------
    if ($pdf->GetY() > 165) {
        $pdf->AddPage();
        $pdf->SetY(25);
    }

    $pdf->Ln(4);
    $y_firmas = $pdf->GetY();

    // Firma 1: Administrador / Operador
    $pdf->SetXY(18, $y_firmas);
    $pdf->Cell(70, 0, '', 'T');
    $pdf->SetXY(18, $y_firmas + 1);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(70, 3.5, conv_pdf_pagos('ELABORADO POR:'), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 6.8);
    $pdf->SetX(18);
    $pdf->Cell(70, 3, conv_pdf_pagos($pdf->nombre_admin), 0, 1, 'C');
    $pdf->SetX(18);
    $pdf->Cell(70, 3, conv_pdf_pagos('Operador de Administración / Pagos'), 0, 1, 'C');

    // Firma 2: Administración y Finanzas
    $pdf->SetXY(108, $y_firmas);
    $pdf->Cell(70, 0, '', 'T');
    $pdf->SetXY(108, $y_firmas + 1);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(70, 3.5, conv_pdf_pagos('REVISADO Y VALIDADO:'), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 6.8);
    $pdf->SetX(108);
    $pdf->Cell(70, 3, conv_pdf_pagos('Dirección de Administración y Finanzas'), 0, 1, 'C');
    $pdf->SetX(108);
    $pdf->Cell(70, 3, conv_pdf_pagos('UPTPC - Puerto Cabello'), 0, 1, 'C');

    // Firma 3: Control de Estudios y Sello
    $pdf->SetXY(198, $y_firmas);
    $pdf->Cell(70, 0, '', 'T');
    $pdf->SetXY(198, $y_firmas + 1);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(70, 3.5, conv_pdf_pagos('CONFORME Y SELLO HÚMEDO:'), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 6.8);
    $pdf->SetX(198);
    $pdf->Cell(70, 3, conv_pdf_pagos('Departamento de Control de Estudios'), 0, 1, 'C');
    $pdf->SetX(198);
    $pdf->Cell(70, 3, conv_pdf_pagos('Firma y Sello Oficial'), 0, 1, 'C');

    // Salida del archivo
    $nombre_archivo = 'Reporte_Pagos_' . date('Ymd_His') . '.pdf';
    $pdf->Output('I', $nombre_archivo);
    exit;
}
}
