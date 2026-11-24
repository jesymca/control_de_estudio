<?php
// Archivo: funciones/reporte_pagos_excel_helper.php
// Generador nativo OpenXML Spreadsheet (.xlsx) sin dependencias externas

require_once __DIR__ . '/functions.php';

/**
 * Obtiene la letra de columna estilo Excel (1 -> A, 15 -> O, 27 -> AA, etc.)
 */
function getExcelColNamePagos($n) {
    $s = '';
    while ($n > 0) {
        $m = ($n - 1) % 26;
        $s = chr(65 + $m) . $s;
        $n = intval(($n - $m) / 26);
    }
    return $s;
}

/**
 * Escapa strings para formato XML seguro en OpenXML
 */
function xmlEscapePagos($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

/**
 * Genera y descarga o retorna un archivo .xlsx completo con diseño institucional
 */
function generarExcelReportePagos($fecha_desde, $fecha_hasta, $filtro_status = '', $filtro_tipo = '', $filtro_metodo = '', $admin_nombre = 'Administrador', $modo_salida = 'D') {
    $pagos = obtenerPagosPorRangoFechas($fecha_desde, $fecha_hasta, $filtro_status, $filtro_tipo, $filtro_metodo, 'ASC');
    $stats = obtenerEstadisticasPagosRango($fecha_desde, $fecha_hasta, $filtro_status, $filtro_tipo, $filtro_metodo);
    
    // Mapeo días en español
    $dias_semana = [
        'Sunday' => 'Domingo', 'Monday' => 'Lunes', 'Tuesday' => 'Martes', 
        'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado'
    ];

    // Construir estructura OpenXML
    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';

    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';

    $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';

    $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Reporte de Pagos" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';

    // Tabla de Estilos
    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <numFmts count="1">
    <numFmt numFmtId="164" formatCode="#,##0.00"/>
  </numFmts>
  <fonts count="9">
    <!-- 0: Normal 10pt -->
    <font><name val="Calibri"/><sz val="10"/></font>
    <!-- 1: Bold White 10pt (Header) -->
    <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FFFFFFFF"/></font>
    <!-- 2: Bold Dark Blue 10pt (Banners, subtotals) -->
    <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FF1B365D"/></font>
    <!-- 3: Bold Title 14pt Dark Blue -->
    <font><b/><name val="Calibri"/><sz val="14"/><color rgb="FF1B365D"/></font>
    <!-- 4: Bold Green 10pt (Approved Currency) -->
    <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FF1E7E34"/></font>
    <!-- 5: Bold 10pt General -->
    <font><b/><name val="Calibri"/><sz val="10"/></font>
    <!-- 6: Italic 9pt Muted -->
    <font><i/><name val="Calibri"/><sz val="9"/><color rgb="FF555555"/></font>
    <!-- 7: Bold Red 10pt (Rejected) -->
    <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FFBD2130"/></font>
    <!-- 8: Bold Orange/Brown 10pt (Pending) -->
    <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FF856404"/></font>
  </fonts>
  <fills count="8">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <!-- 2: Navy Blue (Header) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FF1B365D"/></patternFill></fill>
    <!-- 3: Soft Blue (Date Banner) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFE8F0FE"/></patternFill></fill>
    <!-- 4: Soft Gray (Subtotal row) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFF2F4F7"/></patternFill></fill>
    <!-- 5: Soft Green (Approved badge) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFD4EDDA"/></patternFill></fill>
    <!-- 6: Soft Yellow (Pending badge) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFFFF3CD"/></patternFill></fill>
    <!-- 7: Soft Red (Rejected badge) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFF8D7DA"/></patternFill></fill>
  </fills>
  <borders count="3">
    <!-- 0: None -->
    <border><left/><right/><top/><bottom/></border>
    <!-- 1: Thin light border all around -->
    <border>
      <left style="thin"><color rgb="FFD0D7DE"/></left>
      <right style="thin"><color rgb="FFD0D7DE"/></right>
      <top style="thin"><color rgb="FFD0D7DE"/></top>
      <bottom style="thin"><color rgb="FFD0D7DE"/></bottom>
    </border>
    <!-- 2: Double bottom border (Grand Total) -->
    <border>
      <left style="thin"><color rgb="FFD0D7DE"/></left>
      <right style="thin"><color rgb="FFD0D7DE"/></right>
      <top style="thin"><color rgb="FF1B365D"/></top>
      <bottom style="double"><color rgb="FF1B365D"/></bottom>
    </border>
  </borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="16">
    <!-- 0: Normal Left -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 1: Normal Center -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 2: Main Table Header (Navy Blue, White Text) -->
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
    <!-- 3: Date Banner Row (Soft Blue, Bold Navy) -->
    <xf numFmtId="0" fontId="2" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 4: Subtotal Label (Soft Gray, Bold) -->
    <xf numFmtId="0" fontId="5" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <!-- 5: Subtotal Currency (Soft Gray, Bold Green, #,##0.00) -->
    <xf numFmtId="164" fontId="4" fillId="4" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <!-- 6: Currency Normal Right (#,##0.00) -->
    <xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <!-- 7: Title Big -->
    <xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 8: Title Sub / Meta -->
    <xf numFmtId="0" fontId="6" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 9: Badge Aprobado -->
    <xf numFmtId="0" fontId="4" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 10: Badge Pendiente -->
    <xf numFmtId="0" fontId="8" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 11: Badge Rechazado -->
    <xf numFmtId="0" fontId="7" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 12: Grand Total Label -->
    <xf numFmtId="0" fontId="2" fillId="4" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="2" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <!-- 13: Grand Total Currency -->
    <xf numFmtId="164" fontId="4" fillId="5" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="2" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <!-- 14: KPI Label -->
    <xf numFmtId="0" fontId="2" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 15: KPI Value -->
    <xf numFmtId="0" fontId="5" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  </cellXfs>
</styleSheet>';

    // Definición de anchos de columnas (15 columnas en total)
    $colWidths = [
        1 => 10, // A: ID
        2 => 14, // B: Fecha Efectiva
        3 => 12, // C: Hora
        4 => 14, // D: Cédula
        5 => 32, // E: Estudiante
        6 => 28, // F: Carrera / PNF
        7 => 26, // G: Concepto / Arancel
        8 => 18, // H: Forma de Pago
        9 => 20, // I: Banco Emisor
        10 => 24, // J: Banco Receptor
        11 => 18, // K: N° Referencia
        12 => 16, // L: Monto (Bs)
        13 => 14, // M: Estado
        14 => 22, // N: Registrado Por
        15 => 30  // O: Observaciones
    ];

    $colsXml = '<cols>';
    foreach ($colWidths as $colIdx => $w) {
        $colsXml .= '<col min="' . $colIdx . '" max="' . $colIdx . '" width="' . $w . '" customWidth="1"/>';
    }
    $colsXml .= '</cols>';

    // Construcción de filas de la hoja
    $rowsXml = '';
    $mergeCells = [];
    $currentRow = 1;

    // Helper para celdas
    $addCell = function($colLetter, $rowNum, $val, $styleId = 0, $type = 'inlineStr') {
        if ($type === 'n') {
            return '<c r="' . $colLetter . $rowNum . '" s="' . $styleId . '"><v>' . (float)$val . '</v></c>';
        }
        return '<c r="' . $colLetter . $rowNum . '" s="' . $styleId . '" t="inlineStr"><is><t>' . xmlEscapePagos($val) . '</t></is></c>';
    };

    // 1. TÍTULO PRINCIPAL (Fila 1)
    $rowsXml .= '<row r="' . $currentRow . '" ht="26" customHeight="1">';
    $rowsXml .= $addCell('A', $currentRow, 'UNIVERSIDAD POLITÉCNICA TERRITORIAL DE PUERTO CABELLO (UPTPC)', 7);
    $rowsXml .= '</row>';
    $mergeCells[] = 'A' . $currentRow . ':O' . $currentRow;
    $currentRow++;

    // 2. SUBTÍTULO INSTITUCIONAL (Fila 2)
    $rowsXml .= '<row r="' . $currentRow . '" ht="20" customHeight="1">';
    $rowsXml .= $addCell('A', $currentRow, 'CONTROL DE ESTUDIO - REPORTE Y CONTROL DE PAGOS (FECHA POR FECHA)', 8);
    $rowsXml .= '</row>';
    $mergeCells[] = 'A' . $currentRow . ':O' . $currentRow;
    $currentRow++;

    // 3. PARÁMETROS DEL FILTRO (Fila 3)
    $desc_status = empty($filtro_status) ? 'Todos' : ucfirst($filtro_status);
    $desc_tipo   = empty($filtro_tipo) ? 'Todos' : 'ID ' . $filtro_tipo;
    $desc_metodo = empty($filtro_metodo) ? 'Todas' : strtoupper($filtro_metodo);
    $metaText = 'Período: ' . date('d/m/Y', strtotime($fecha_desde)) . ' al ' . date('d/m/Y', strtotime($fecha_hasta)) .
                '  |  Estado: ' . $desc_status . '  |  Concepto: ' . $desc_tipo . '  |  Forma de Pago: ' . $desc_metodo;
    $rowsXml .= '<row r="' . $currentRow . '" ht="18" customHeight="1">';
    $rowsXml .= $addCell('A', $currentRow, $metaText, 8);
    $rowsXml .= '</row>';
    $mergeCells[] = 'A' . $currentRow . ':O' . $currentRow;
    $currentRow++;

    // 4. METADATOS DE GENERACIÓN (Fila 4)
    $emitText = 'Generado por: ' . $admin_nombre . '  |  Fecha de emisión: ' . date('d/m/Y h:i:s A');
    $rowsXml .= '<row r="' . $currentRow . '" ht="18" customHeight="1">';
    $rowsXml .= $addCell('A', $currentRow, $emitText, 8);
    $rowsXml .= '</row>';
    $mergeCells[] = 'A' . $currentRow . ':O' . $currentRow;
    $currentRow++;

    // Fila en blanco (Fila 5)
    $currentRow++;

    // 5. RESUMEN EJECUTIVO (KPIs) (Fila 6 y 7)
    // Encabezados KPI
    $rowsXml .= '<row r="' . $currentRow . '" ht="20" customHeight="1">';
    $rowsXml .= $addCell('A', $currentRow, 'TOTAL RECAUDADO (APROBADO)', 14);
    $rowsXml .= $addCell('E', $currentRow, 'PENDIENTE DE VERIFICACIÓN', 14);
    $rowsXml .= $addCell('I', $currentRow, 'TOTAL RECHAZADO', 14);
    $rowsXml .= $addCell('M', $currentRow, 'TOTAL OPERACIONES', 14);
    $rowsXml .= '</row>';
    $mergeCells[] = 'A' . $currentRow . ':D' . $currentRow;
    $mergeCells[] = 'E' . $currentRow . ':H' . $currentRow;
    $mergeCells[] = 'I' . $currentRow . ':L' . $currentRow;
    $mergeCells[] = 'M' . $currentRow . ':O' . $currentRow;
    $currentRow++;

    // Valores KPI
    $rowsXml .= '<row r="' . $currentRow . '" ht="22" customHeight="1">';
    $rowsXml .= $addCell('A', $currentRow, 'Bs ' . number_format($stats['aprobados_monto'], 2, ',', '.') . ' (' . $stats['aprobados_cant'] . ' pagos)', 15);
    $rowsXml .= $addCell('E', $currentRow, 'Bs ' . number_format($stats['pendientes_monto'], 2, ',', '.') . ' (' . $stats['pendientes_cant'] . ' pagos)', 15);
    $rowsXml .= $addCell('I', $currentRow, 'Bs ' . number_format($stats['rechazados_monto'], 2, ',', '.') . ' (' . $stats['rechazados_cant'] . ' pagos)', 15);
    $rowsXml .= $addCell('M', $currentRow, $stats['total_operaciones'] . ' Transacciones registradas', 15);
    $rowsXml .= '</row>';
    $mergeCells[] = 'A' . $currentRow . ':D' . $currentRow;
    $mergeCells[] = 'E' . $currentRow . ':H' . $currentRow;
    $mergeCells[] = 'I' . $currentRow . ':L' . $currentRow;
    $mergeCells[] = 'M' . $currentRow . ':O' . $currentRow;
    $currentRow++;

    // Fila en blanco
    $currentRow++;

    // 6. ENCABEZADO DE TABLA DE PAGOS
    $tableHeaders = [
        'A' => 'N° ID',
        'B' => 'Fecha Efectiva',
        'C' => 'Hora',
        'D' => 'Cédula',
        'E' => 'Nombre del Estudiante',
        'F' => 'Carrera / PNF',
        'G' => 'Concepto / Arancel',
        'H' => 'Forma de Pago',
        'I' => 'Banco Emisor',
        'J' => 'Banco Receptor UPTPC',
        'K' => 'N° Referencia',
        'L' => 'Monto (Bs)',
        'Estado',
        'M' => 'Estado',
        'N' => 'Registrado Por',
        'O' => 'Observaciones'
    ];

    $rowsXml .= '<row r="' . $currentRow . '" ht="24" customHeight="1">';
    $rowsXml .= $addCell('A', $currentRow, 'N° ID', 2);
    $rowsXml .= $addCell('B', $currentRow, 'Fecha Efectiva', 2);
    $rowsXml .= $addCell('C', $currentRow, 'Hora', 2);
    $rowsXml .= $addCell('D', $currentRow, 'Cédula', 2);
    $rowsXml .= $addCell('E', $currentRow, 'Nombre del Estudiante', 2);
    $rowsXml .= $addCell('F', $currentRow, 'Carrera / PNF', 2);
    $rowsXml .= $addCell('G', $currentRow, 'Concepto / Arancel', 2);
    $rowsXml .= $addCell('H', $currentRow, 'Forma de Pago', 2);
    $rowsXml .= $addCell('I', $currentRow, 'Banco Emisor', 2);
    $rowsXml .= $addCell('J', $currentRow, 'Banco Receptor UPTPC', 2);
    $rowsXml .= $addCell('K', $currentRow, 'N° Referencia', 2);
    $rowsXml .= $addCell('L', $currentRow, 'Monto (Bs)', 2);
    $rowsXml .= $addCell('M', $currentRow, 'Estado', 2);
    $rowsXml .= $addCell('N', $currentRow, 'Registrado Por', 2);
    $rowsXml .= $addCell('O', $currentRow, 'Observaciones', 2);
    $rowsXml .= '</row>';
    $currentRow++;

    // 7. FILAS AGRUPADAS FECHA POR FECHA
    if (!empty($stats['por_fecha'])) {
        foreach ($stats['por_fecha'] as $fecha_grupo => $datos_dia) {
            $timestamp = strtotime($fecha_grupo);
            $dia_ingles = date('l', $timestamp);
            $nombre_dia = $dias_semana[$dia_ingles] ?? $dia_ingles;
            $cant_op = $datos_dia['cantidad'];
            
            // Fila de encabezado por fecha
            $bannerText = '>> FECHA: ' . strtoupper($nombre_dia) . ', ' . date('d/m/Y', $timestamp) . 
                          '  (' . $cant_op . ' ' . ($cant_op === 1 ? 'operación registrada' : 'operaciones registradas') . ')';
            
            $rowsXml .= '<row r="' . $currentRow . '" ht="20" customHeight="1">';
            $rowsXml .= $addCell('A', $currentRow, $bannerText, 3);
            for ($c = 2; $c <= 15; $c++) {
                $rowsXml .= $addCell(getExcelColNamePagos($c), $currentRow, '', 3);
            }
            $rowsXml .= '</row>';
            $mergeCells[] = 'A' . $currentRow . ':O' . $currentRow;
            $currentRow++;

            // Filas de pagos de esta fecha
            $subtotal_dia_aprobado = 0.0;
            $count_dia_aprobado = 0;
            $count_dia_pendiente = 0;

            foreach ($datos_dia['pagos'] as $p) {
                $monto_val = (float)$p['monto'];
                $status = $p['status_pago'] ?? 'pendiente';

                if ($status === 'aprobado') {
                    $subtotal_dia_aprobado += $monto_val;
                    $count_dia_aprobado++;
                    $style_badge = 9; // Verde
                } elseif ($status === 'rechazado') {
                    $style_badge = 11; // Rojo
                } else {
                    $count_dia_pendiente++;
                    $style_badge = 10; // Amarillo
                }

                $hora_pago = !empty($p['fecha_pago']) ? date('h:i A', strtotime($p['fecha_pago'])) : 'N/D';

                $rowsXml .= '<row r="' . $currentRow . '" ht="19" customHeight="1">';
                $rowsXml .= $addCell('A', $currentRow, $p['id'], 1, 'n');
                $rowsXml .= $addCell('B', $currentRow, date('d/m/Y', $timestamp), 1);
                $rowsXml .= $addCell('C', $currentRow, $hora_pago, 1);
                $rowsXml .= $addCell('D', $currentRow, $p['cedula'], 1);
                $rowsXml .= $addCell('E', $currentRow, $p['nombre_estudiante'], 0);
                $rowsXml .= $addCell('F', $currentRow, $p['nombre_carrera'] ?: 'N/A', 0);
                $rowsXml .= $addCell('G', $currentRow, $p['nombre_tipo_pago'], 0);
                $rowsXml .= $addCell('H', $currentRow, $p['metodo_pago'] ?: 'N/D', 1);
                $rowsXml .= $addCell('I', $currentRow, $p['banco_origen'] ?: 'N/D', 0);
                $rowsXml .= $addCell('J', $currentRow, $p['nombre_banco_destino'] ?: 'Directo UPTPC', 0);
                $rowsXml .= $addCell('K', $currentRow, $p['referencia'] ?: 'S/R', 1);
                $rowsXml .= $addCell('L', $currentRow, $monto_val, 6, 'n');
                $rowsXml .= $addCell('M', $currentRow, strtoupper($status), $style_badge);
                $rowsXml .= $addCell('N', $currentRow, $p['nombre_registrador'] ?: 'Sistema', 0);
                $rowsXml .= $addCell('O', $currentRow, $p['observaciones'] ?: '', 0);
                $rowsXml .= '</row>';
                $currentRow++;
            }

            // Fila de Subtotal del día
            $subText = 'Subtotal del Día (' . date('d/m/Y', $timestamp) . ') - Aprobados: ' . $count_dia_aprobado . 
                       ($count_dia_pendiente > 0 ? ' | Pendientes: ' . $count_dia_pendiente : '') . ':';
            
            $rowsXml .= '<row r="' . $currentRow . '" ht="20" customHeight="1">';
            $rowsXml .= $addCell('A', $currentRow, $subText, 4);
            for ($c = 2; $c <= 11; $c++) {
                $rowsXml .= $addCell(getExcelColNamePagos($c), $currentRow, '', 4);
            }
            $rowsXml .= $addCell('L', $currentRow, $subtotal_dia_aprobado, 5, 'n');
            $rowsXml .= $addCell('M', $currentRow, '', 4);
            $rowsXml .= $addCell('N', $currentRow, '', 4);
            $rowsXml .= $addCell('O', $currentRow, '', 4);
            $rowsXml .= '</row>';
            $mergeCells[] = 'A' . $currentRow . ':K' . $currentRow;
            $mergeCells[] = 'M' . $currentRow . ':O' . $currentRow;
            $currentRow++;
        }
    } else {
        $rowsXml .= '<row r="' . $currentRow . '" ht="22" customHeight="1">';
        $rowsXml .= $addCell('A', $currentRow, 'No se encontraron registros de pagos para los filtros seleccionados.', 1);
        for ($c = 2; $c <= 15; $c++) {
            $rowsXml .= $addCell(getExcelColNamePagos($c), $currentRow, '', 1);
        }
        $rowsXml .= '</row>';
        $mergeCells[] = 'A' . $currentRow . ':O' . $currentRow;
        $currentRow++;
    }

    // 8. FILA DE GRAN TOTAL GENERAL DEL PERÍODO
    $rowsXml .= '<row r="' . $currentRow . '" ht="24" customHeight="1">';
    $rowsXml .= $addCell('A', $currentRow, 'TOTAL GENERAL RECAUDADO (APROBADO) DEL PERÍODO:', 12);
    for ($c = 2; $c <= 11; $c++) {
        $rowsXml .= $addCell(getExcelColNamePagos($c), $currentRow, '', 12);
    }
    $rowsXml .= $addCell('L', $currentRow, $stats['aprobados_monto'], 13, 'n');
    $rowsXml .= $addCell('M', $currentRow, $stats['aprobados_cant'] . ' aprobados / ' . $stats['total_operaciones'] . ' operaciones', 12);
    $rowsXml .= $addCell('N', $currentRow, '', 12);
    $rowsXml .= $addCell('O', $currentRow, '', 12);
    $rowsXml .= '</row>';
    $mergeCells[] = 'A' . $currentRow . ':K' . $currentRow;
    $mergeCells[] = 'M' . $currentRow . ':O' . $currentRow;
    $currentRow++;

    // Filas en blanco antes del resumen por concepto y forma de pago
    $currentRow += 2;

    // 9. TABLAS DE RESUMEN EJECUTIVO AL PIE DE PÁGINA
    // Sub-tabla 1: Resumen por Forma de Pago
    $rowsXml .= '<row r="' . $currentRow . '" ht="22" customHeight="1">';
    $rowsXml .= $addCell('A', $currentRow, 'RESUMEN POR FORMA DE PAGO', 2);
    $rowsXml .= $addCell('C', $currentRow, 'RESUMEN POR CONCEPTO / ARANCEL', 2);
    $rowsXml .= '</row>';
    $mergeCells[] = 'A' . $currentRow . ':B' . $currentRow;
    $mergeCells[] = 'C' . $currentRow . ':E' . $currentRow;
    $currentRow++;

    // Encabezados resumen
    $rowsXml .= '<row r="' . $currentRow . '" ht="20" customHeight="1">';
    $rowsXml .= $addCell('A', $currentRow, 'Forma de Pago', 14);
    $rowsXml .= $addCell('B', $currentRow, 'Monto Aprobado (Bs)', 14);
    $rowsXml .= $addCell('C', $currentRow, 'Concepto / Arancel', 14);
    $rowsXml .= $addCell('D', $currentRow, 'Cant.', 14);
    $rowsXml .= $addCell('E', $currentRow, 'Monto Aprobado (Bs)', 14);
    $rowsXml .= '</row>';
    $currentRow++;

    $metodosKeys = array_keys($stats['por_metodo'] ?? []);
    $conceptosKeys = array_keys($stats['por_concepto'] ?? []);
    $maxSubRows = max(count($metodosKeys), count($conceptosKeys));

    for ($i = 0; $i < $maxSubRows; $i++) {
        $rowsXml .= '<row r="' . $currentRow . '" ht="19" customHeight="1">';
        
        // Forma de pago
        if (isset($metodosKeys[$i])) {
            $mKey = $metodosKeys[$i];
            $mData = $stats['por_metodo'][$mKey];
            $rowsXml .= $addCell('A', $currentRow, $mKey . ' (' . $mData['cantidad'] . ')', 0);
            $rowsXml .= $addCell('B', $currentRow, (float)$mData['aprobado'], 6, 'n');
        } else {
            $rowsXml .= $addCell('A', $currentRow, '', 0);
            $rowsXml .= $addCell('B', $currentRow, '', 0);
        }

        // Concepto
        if (isset($conceptosKeys[$i])) {
            $cKey = $conceptosKeys[$i];
            $cData = $stats['por_concepto'][$cKey];
            $rowsXml .= $addCell('C', $currentRow, $cKey, 0);
            $rowsXml .= $addCell('D', $currentRow, $cData['cantidad'], 1, 'n');
            $rowsXml .= $addCell('E', $currentRow, (float)$cData['aprobado'], 6, 'n');
        } else {
            $rowsXml .= $addCell('C', $currentRow, '', 0);
            $rowsXml .= $addCell('D', $currentRow, '', 0);
            $rowsXml .= $addCell('E', $currentRow, '', 0);
        }

        $rowsXml .= '</row>';
        $currentRow++;
    }

    // Merge cells XML
    $mergeXml = '';
    if (!empty($mergeCells)) {
        $mergeXml = '<mergeCells count="' . count($mergeCells) . '">';
        foreach ($mergeCells as $ref) {
            $mergeXml .= '<mergeCell ref="' . $ref . '"/>';
        }
        $mergeXml .= '</mergeCells>';
    }

    // XML de Worksheet
    $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  ' . $colsXml . '
  <sheetData>
    ' . $rowsXml . '
  </sheetData>
  ' . $mergeXml . '
</worksheet>';

    // Crear archivo temporal ZIP para el archivo .xlsx
    $tempXlsx = tempnam(sys_get_temp_dir(), 'uptpc_pago_excel_');
    $zip = new ZipArchive();
    if ($zip->open($tempXlsx, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        die("Error: No se pudo crear el archivo temporal de Excel.");
    }

    $zip->addFromString('[Content_Types].xml', $contentTypes);
    $zip->addFromString('_rels/.rels', $rels);
    $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
    $zip->addFromString('xl/workbook.xml', $workbook);
    $zip->addFromString('xl/styles.xml', $styles);
    $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
    $zip->close();

    $filename = 'reporte_pagos_' . date('Ymd', strtotime($fecha_desde)) . '_al_' . date('Ymd', strtotime($fecha_hasta)) . '.xlsx';

    if ($modo_salida === 'S') {
        $data = file_get_contents($tempXlsx);
        unlink($tempXlsx);
        return $data;
    }

    // Descarga directa al navegador
    if (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($tempXlsx));
    header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
    header('Pragma: public');

    readfile($tempXlsx);
    unlink($tempXlsx);
    exit;
}
