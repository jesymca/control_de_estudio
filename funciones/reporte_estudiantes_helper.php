<?php
/**
 * ==============================================================================
 * GENERADOR DE REPORTE PDF DE ESTUDIANTES - UPTPC
 * Generación oficial de listado de estudiantes filtrados con resumen estadístico.
 * 
 * Formato institucional en orientación horizontal (Landscape) con:
 * - Soporte nativo para envíos masivos vía POST y fallback GET.
 * - Membrete oficial de la UPTPC y MPPEU con logos institucionales.
 * - Tabla estructurada con paginación automática y cabecera fija por página.
 * - Resumen estadístico (totales, géneros, estatus, edades).
 * ==============================================================================
 */

require_once __DIR__ . '/../fpdf/fpdf.php';
require_once __DIR__ . '/functions.php';

if (!function_exists('conv_pdf_estudiantes')) {
    function conv_pdf_estudiantes($texto) {
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

if (!function_exists('safe_pdf_substr')) {
    function safe_pdf_substr($texto, $longitudMax) {
        if ($texto === null || $texto === '') return '';
        $str = (string)$texto;
        if (function_exists('mb_substr')) {
            return mb_substr($str, 0, $longitudMax, 'UTF-8');
        }
        return substr($str, 0, $longitudMax);
    }
}

if (!class_exists('ReporteEstudiantesPDF')) {
    class ReporteEstudiantesPDF extends FPDF {
        public $admin_nombre = 'Administración';
        public $total_estudiantes = 0;
        public $mostrar_cabecera_tabla = true;
        public $tipo_reporte = 'estudiantes';

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
            $this->Cell(0, 3.5, conv_pdf_estudiantes('REPÚBLICA BOLIVARIANA DE VENEZUELA'), 0, 1, 'C');
            $this->Cell(0, 3.5, conv_pdf_estudiantes('MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN UNIVERSITARIA'), 0, 1, 'C');
            $this->Cell(0, 3.5, conv_pdf_estudiantes('UNIVERSIDAD POLITÉCNICA TERRITORIAL DE PUERTO CABELLO'), 0, 1, 'C');
            $this->SetFont('Arial', '', 7.5);
            $this->Cell(0, 3.5, conv_pdf_estudiantes('DEPARTAMENTO DE CONTROL DE ESTUDIOS'), 0, 1, 'C');
            
            // Línea separadora
            $this->SetLineWidth(0.3);
            $this->Line(10, 24, 287, 24);
            $this->Ln(3);

            // Título Principal dinámico
            $this->SetFont('Arial', 'B', 10);
            $tituloReporte = ($this->tipo_reporte === 'egresados') ? 'REPORTE OFICIAL DE EGRESADOS Y GRADUADOS' : 'REPORTE OFICIAL DE ESTUDIANTES';
            $this->Cell(0, 4.5, conv_pdf_estudiantes($tituloReporte), 0, 1, 'C');

            // Subtítulo con fecha y detalles
            $this->SetFont('Arial', '', 7.5);
            $subt = "FECHA DE EMISIÓN: " . date('d/m/Y h:i A');
            $subt .= "   |   GENERADO POR: " . strtoupper($this->admin_nombre);
            $subt .= "   |   TOTAL REGISTROS: " . $this->total_estudiantes;
            $this->Cell(0, 3.5, conv_pdf_estudiantes($subt), 0, 1, 'C');
            $this->Ln(2);

            // Cabecera de la tabla en páginas subsecuentes
            if ($this->PageNo() > 1 && $this->mostrar_cabecera_tabla) {
                $this->imprimirCabeceraTabla();
            }
        }

        function Footer() {
            $this->SetY(-10);
            $this->SetFont('Arial', 'I', 7);
            $this->SetTextColor(80, 80, 80);
            $docNombre = ($this->tipo_reporte === 'egresados') ? 'Reporte Oficial de Egresados' : 'Reporte Oficial de Estudiantes';
            $this->Cell(130, 4, conv_pdf_estudiantes('UPTPC - Sistema de Control de Estudios | ' . $docNombre), 0, 0, 'L');
            $this->Cell(0, 4, conv_pdf_estudiantes('Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'R');
        }

        function imprimirCabeceraTabla() {
            $this->SetFont('Arial', 'B', 6.5);
            $this->SetFillColor(0, 51, 102); // Azul institucional
            $this->SetDrawColor(0, 51, 102);
            $this->SetTextColor(255, 255, 255);

            $colEstadoOFecha = ($this->tipo_reporte === 'egresados') ? 'F. GRADO' : 'STATUS';

            // Anchos totales = 277 mm
            $this->Cell(6, 6, conv_pdf_estudiantes('N°'), 1, 0, 'C', true);
            $this->Cell(17, 6, conv_pdf_estudiantes('CÉDULA'), 1, 0, 'C', true);
            $this->Cell(39, 6, conv_pdf_estudiantes('NOMBRES Y APELLIDOS'), 1, 0, 'L', true);
            $this->Cell(34, 6, conv_pdf_estudiantes('CARRERA / PNF'), 1, 0, 'L', true);
            $this->Cell(22, 6, conv_pdf_estudiantes('MODALIDAD'), 1, 0, 'C', true);
            $this->Cell(16, 6, conv_pdf_estudiantes('SEDE'), 1, 0, 'C', true);
            $this->Cell(13, 6, conv_pdf_estudiantes('TURNO'), 1, 0, 'C', true);
            $this->Cell(8, 6, conv_pdf_estudiantes('GÉN.'), 1, 0, 'C', true);
            $this->Cell(8, 6, conv_pdf_estudiantes('EDAD'), 1, 0, 'C', true);
            $this->Cell(17, 6, conv_pdf_estudiantes('ESTADO'), 1, 0, 'C', true);
            $this->Cell(19, 6, conv_pdf_estudiantes('CONDICIÓN'), 1, 0, 'C', true);
            $this->Cell(17, 6, conv_pdf_estudiantes('TELÉFONO'), 1, 0, 'C', true);
            $this->Cell(32, 6, conv_pdf_estudiantes('CORREO ELECTRÓNICO'), 1, 0, 'L', true);
            $this->Cell(14, 6, conv_pdf_estudiantes($colEstadoOFecha), 1, 0, 'C', true);
            $this->Cell(15, 6, conv_pdf_estudiantes('F. INGR.'), 1, 1, 'C', true);

            // Restablecer estilos para filas
            $this->SetTextColor(0, 0, 0);
            $this->SetDrawColor(200, 200, 200);
            $this->SetFont('Arial', '', 6.5);
        }
    }
}

if (!function_exists('generarPDFReporteEstudiantes')) {
    function generarPDFReporteEstudiantes($ids, $incluirEstadisticas = 'si', $admin_nombre = '', $tipoReporte = 'estudiantes') {
        global $db;

        // Limpiar cualquier búfer de salida para no corromper el binario PDF
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Sanitizar array de IDs
        $idsArray = [];
        if (is_array($ids)) {
            $idsArray = array_filter(array_map('intval', $ids));
        } elseif (!empty($ids)) {
            $idsArray = array_filter(array_map('intval', explode(',', (string)$ids)));
        }

        if (empty($idsArray)) {
            die('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Error</title><link rel="stylesheet" href="../funciones/bootstrap-4.6.2-dist/css/bootstrap.min.css"></head><body class="p-5 text-center"><div class="alert alert-warning d-inline-block">No se seleccionó ningún estudiante para generar el reporte.</div><br><button onclick="window.close()" class="btn btn-secondary mt-3">Cerrar pestaña</button></body></html>');
        }

        $idsString = implode(',', $idsArray);

        // Mapeo normalizado de estados geográficos
        $estadoMap = [];
        $resEstados = $db->query("SELECT id_estado, estado FROM estados");
        if ($resEstados) {
            while ($eRow = $resEstados->fetch_assoc()) {
                $estadoMap[(string)$eRow['id_estado']] = $eRow['estado'];
                $estadoMap[strtolower(trim($eRow['estado']))] = $eRow['estado'];
            }
        }

        // Consultar estudiantes seleccionados con todos sus campos académicos y sociodemográficos
        $query = "SELECT 
            u.id,
            u.idusuario,
            u.nombre,
            u.email,
            u.tlf,
            u.cel,
            u.carrera,
            u.genero,
            u.fecha_nac,
            u.edo_civil,
            u.status,
            u.fecha_ingreso,
            u.fecha_graduacion as user_fecha_graduacion,
            u.sede,
            u.turno,
            u.estado,
            u.ciudad,
            u.inscrito_por,
            u.asignado_opsu,
            u.discapacidad,
            u.etnia,
            u.embarazada,
            c.nombre_carrera,
            g.id as id_graduado,
            g.estado as estado_graduado,
            g.fecha_graduacion as grad_fecha_graduacion
        FROM users u
        LEFT JOIN carreras c ON u.carrera = c.id_carrera
        LEFT JOIN graduados g ON u.id = g.id_usuario
        WHERE u.id IN ($idsString)
        ORDER BY u.nombre ASC";

        $result = $db->query($query);
        $estudiantes = [];

        if ($result && $result->num_rows > 0) {
            $hoy = new DateTime();
            while ($row = $result->fetch_assoc()) {
                // Calcular edad
                $edad = '';
                if (!empty($row['fecha_nac'])) {
                    try {
                        $fechaNac = new DateTime($row['fecha_nac']);
                        $edad = $fechaNac->diff($hoy)->y;
                    } catch (Exception $e) {
                        $edad = '';
                    }
                }
                $row['edad'] = $edad;
                $row['telefono'] = !empty($row['tlf']) ? $row['tlf'] : ($row['cel'] ?? '');

                // Fecha de graduación
                $fechaGrad = !empty($row['grad_fecha_graduacion']) ? $row['grad_fecha_graduacion'] : (!empty($row['user_fecha_graduacion']) ? $row['user_fecha_graduacion'] : '');
                $row['fecha_graduacion'] = $fechaGrad;

                // Modalidad de ingreso
                $modalidad = trim((string)($row['inscrito_por'] ?? ''));
                if (empty($modalidad) && ($row['asignado_opsu'] ?? '') === 'Sí') {
                    $modalidad = 'OPSU';
                }
                $row['modalidad'] = !empty($modalidad) ? $modalidad : 'No especificado';

                // Sede y Turno
                $row['sede_nombre'] = !empty($row['sede']) ? trim($row['sede']) : 'No especificada';
                $row['turno_nombre'] = !empty($row['turno']) ? trim($row['turno']) : 'No especificado';

                // Estado geográfico normalizado
                $rawEstado = trim((string)($row['estado'] ?? ''));
                if (isset($estadoMap[$rawEstado])) {
                    $row['nombre_estado'] = $estadoMap[$rawEstado];
                } elseif (isset($estadoMap[strtolower($rawEstado)])) {
                    $row['nombre_estado'] = $estadoMap[strtolower($rawEstado)];
                } else {
                    $row['nombre_estado'] = !empty($rawEstado) ? $rawEstado : 'No especificado';
                }

                // Condición especial / Inclusión (Discapacidad / Etnia)
                $noValidos = ['ninguna', 'no especificado', 'no', '0', 'ninguno', ''];
                $tieneDisc = !empty($row['discapacidad']) && !in_array(strtolower(trim($row['discapacidad'])), $noValidos);
                $tieneEtnia = !empty($row['etnia']) && !in_array(strtolower(trim($row['etnia'])), $noValidos);

                $row['tiene_discapacidad'] = $tieneDisc;
                $row['tiene_etnia'] = $tieneEtnia;

                if ($tieneDisc && $tieneEtnia) {
                    $row['condicion_texto'] = 'Disc. / Etnia';
                } elseif ($tieneDisc) {
                    $row['condicion_texto'] = 'Disc: ' . trim($row['discapacidad']);
                } elseif ($tieneEtnia) {
                    $row['condicion_texto'] = 'Etnia: ' . trim($row['etnia']);
                } else {
                    $row['condicion_texto'] = 'Regular';
                }

                $estudiantes[] = $row;
            }
        }

        if (empty($estudiantes)) {
            die('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Error</title><link rel="stylesheet" href="../funciones/bootstrap-4.6.2-dist/css/bootstrap.min.css"></head><body class="p-5 text-center"><div class="alert alert-danger d-inline-block">No se encontraron registros de estudiantes con los IDs proporcionados.</div><br><button onclick="window.close()" class="btn btn-secondary mt-3">Cerrar pestaña</button></body></html>');
        }

        $pdf = new ReporteEstudiantesPDF('L', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->admin_nombre = $admin_nombre ?: 'Control de Estudios';
        $pdf->total_estudiantes = count($estudiantes);
        $pdf->tipo_reporte = $tipoReporte;
        $pdf->SetMargins(10, 8, 10);
        $pdf->SetAutoPageBreak(true, 12);
        $pdf->AddPage();

        // -------------------------------------------------------------
        // RESUMEN ESTADÍSTICO COMPLETO (15 Indicadores Oficiales)
        // -------------------------------------------------------------
        if (strtolower($incluirEstadisticas) === 'si') {
            $total = count($estudiantes);
            $activos = 0;
            $inactivos = 0;
            $nuevosIngresos = 0;
            $masculinos = 0;
            $femeninos = 0;
            $menores = 0;
            $mayores = 0;
            $embarazadas = 0;
            $sumaEdad = 0;
            $cantEdad = 0;
            $conDiscapacidad = 0;
            $conEtnia = 0;
            $viaOpsu = 0;
            $sedePC = 0;
            $sedeCOEF = 0;
            $anioActual = date('Y');

            foreach ($estudiantes as $e) {
                if ((int)$e['status'] === 1) {
                    $activos++;
                } else {
                    $inactivos++;
                }

                if (!empty($e['fecha_ingreso']) && strpos($e['fecha_ingreso'], $anioActual) === 0) {
                    $nuevosIngresos++;
                }

                $gen = strtoupper(trim($e['genero'] ?? ''));
                if ($gen === 'MASCULINO' || $gen === 'M') {
                    $masculinos++;
                } elseif ($gen === 'FEMENINO' || $gen === 'F') {
                    $femeninos++;
                    if (isset($e['embarazada']) && trim((string)$e['embarazada']) === '1') {
                        $embarazadas++;
                    }
                }

                if ($e['edad'] !== '' && is_numeric($e['edad'])) {
                    $edadNum = (int)$e['edad'];
                    if ($edadNum > 0) {
                        $sumaEdad += $edadNum;
                        $cantEdad++;
                    }
                    if ($edadNum < 18) {
                        $menores++;
                    } else {
                        $mayores++;
                    }
                }

                if (!empty($e['tiene_discapacidad'])) $conDiscapacidad++;
                if (!empty($e['tiene_etnia'])) $conEtnia++;

                if (strtoupper($e['modalidad']) === 'OPSU' || ($e['asignado_opsu'] ?? '') === 'Sí') {
                    $viaOpsu++;
                }

                $sedeLower = strtolower($e['sede_nombre']);
                if (strpos($sedeLower, 'puerto cabello') !== false) {
                    $sedePC++;
                } elseif (strpos($sedeLower, 'coef') !== false) {
                    $sedeCOEF++;
                }
            }

            $edadPromedio = $cantEdad > 0 ? number_format($sumaEdad / $cantEdad, 1) : '-';

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(241, 245, 249);
            $pdf->SetDrawColor(0, 51, 102);
            $pdf->SetLineWidth(0.2);

            // Caja contenedor del resumen (3 filas: 21 mm alto)
            $xInicial = 10;
            $yInicial = $pdf->GetY();
            $pdf->Rect($xInicial, $yInicial, 277, 21, 'DF');

            $pdf->SetXY($xInicial + 2, $yInicial + 1.2);
            $pdf->SetTextColor(0, 51, 102);
            $tituloResumen = ($tipoReporte === 'egresados') ? 'RESUMEN ESTADÍSTICO DE EGRESADOS SELECCIONADOS' : 'RESUMEN ESTADÍSTICO DE ESTUDIANTES SELECCIONADOS';
            $pdf->Cell(0, 3.8, conv_pdf_estudiantes($tituloResumen), 0, 1, 'L');

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 7);

            $anchoCol = 55.4;
            $labelTotal = ($tipoReporte === 'egresados') ? 'Total Egresados: ' : 'Total Estudiantes: ';

            // Fila 1 de datos estadísticos
            $pdf->SetXY($xInicial + 4, $yInicial + 5.2);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes($labelTotal) . $total, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Activos: ") . $activos, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Inactivos: ") . $inactivos, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Ingreso $anioActual: ") . $nuevosIngresos, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Edad Promedio: ") . $edadPromedio . ($edadPromedio !== '-' ? ' años' : ''), 0, 1);

            // Fila 2 de datos estadísticos
            $pdf->SetXY($xInicial + 4, $yInicial + 9.8);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Masculinos: ") . $masculinos, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Femeninos: ") . $femeninos, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Menores (<18 años): ") . $menores, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Mayores (>=18 años): ") . $mayores, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Embarazadas: ") . $embarazadas, 0, 1);

            // Fila 3 de datos estadísticos
            $pdf->SetXY($xInicial + 4, $yInicial + 14.4);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Vía OPSU: ") . $viaOpsu, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Con Discapacidad: ") . $conDiscapacidad, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Etnia Indígena: ") . $conEtnia, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Sede Pto. Cabello: ") . $sedePC, 0, 0);
            $pdf->Cell($anchoCol, 4.5, conv_pdf_estudiantes("Sede COEF: ") . $sedeCOEF, 0, 1);

            $pdf->SetY($yInicial + 23);
        }

        // -------------------------------------------------------------
        // CABECERA Y CUERPO DE LA TABLA
        // -------------------------------------------------------------
        $pdf->imprimirCabeceraTabla();

        $numero = 1;
        foreach ($estudiantes as $e) {
            // Alternar color de fondo en filas
            $fill = ($numero % 2 === 0);
            if ($fill) {
                $pdf->SetFillColor(248, 249, 250);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }

            if ($tipoReporte === 'egresados') {
                $colTexto = !empty($e['fecha_graduacion']) ? date('d/m/Y', strtotime($e['fecha_graduacion'])) : 'Egresado';
            } else {
                $colTexto = ((int)$e['status'] === 1) ? 'Activo' : 'Inactivo';
            }

            $fechaIngreso = !empty($e['fecha_ingreso']) ? date('d/m/Y', strtotime($e['fecha_ingreso'])) : '-';
            $carreraNombre = !empty($e['nombre_carrera']) ? $e['nombre_carrera'] : ($e['carrera'] ?: 'Sin Carrera');

            $genUpper = strtoupper(trim($e['genero'] ?? ''));
            $generoCorto = ($genUpper === 'MASCULINO' || $genUpper === 'M') ? 'M' : (($genUpper === 'FEMENINO' || $genUpper === 'F') ? 'F' : '-');

            $pdf->Cell(6, 5, $numero, 1, 0, 'C', true);
            $pdf->Cell(17, 5, conv_pdf_estudiantes(safe_pdf_substr($e['idusuario'], 12)), 1, 0, 'C', true);
            $pdf->Cell(39, 5, conv_pdf_estudiantes(safe_pdf_substr($e['nombre'], 25)), 1, 0, 'L', true);
            $pdf->Cell(34, 5, conv_pdf_estudiantes(safe_pdf_substr($carreraNombre, 22)), 1, 0, 'L', true);
            $pdf->Cell(22, 5, conv_pdf_estudiantes(safe_pdf_substr($e['modalidad'], 15)), 1, 0, 'C', true);
            $pdf->Cell(16, 5, conv_pdf_estudiantes(safe_pdf_substr($e['sede_nombre'], 11)), 1, 0, 'C', true);
            $pdf->Cell(13, 5, conv_pdf_estudiantes(safe_pdf_substr($e['turno_nombre'], 9)), 1, 0, 'C', true);
            $pdf->Cell(8, 5, conv_pdf_estudiantes($generoCorto), 1, 0, 'C', true);
            $pdf->Cell(8, 5, $e['edad'] !== '' ? $e['edad'] : '-', 1, 0, 'C', true);
            $pdf->Cell(17, 5, conv_pdf_estudiantes(safe_pdf_substr($e['nombre_estado'], 11)), 1, 0, 'C', true);
            $pdf->Cell(19, 5, conv_pdf_estudiantes(safe_pdf_substr($e['condicion_texto'], 13)), 1, 0, 'C', true);
            $pdf->Cell(17, 5, conv_pdf_estudiantes(safe_pdf_substr($e['telefono'], 12)), 1, 0, 'C', true);
            $pdf->Cell(32, 5, conv_pdf_estudiantes(safe_pdf_substr($e['email'], 22)), 1, 0, 'L', true);
            $pdf->Cell(14, 5, conv_pdf_estudiantes($colTexto), 1, 0, 'C', true);
            $pdf->Cell(15, 5, conv_pdf_estudiantes($fechaIngreso), 1, 1, 'C', true);

            $numero++;
        }

        // Espacio para firmas institucionales al pie del reporte
        $pdf->Ln(5);
        if ($pdf->GetY() > 175) {
            $pdf->AddPage();
            $pdf->Ln(10);
        }
        $yFirmas = $pdf->GetY();
        $pdf->SetFont('Arial', 'B', 7.5);
        $pdf->SetTextColor(0, 0, 0);

        // Firma 1: Elaborado por
        $pdf->Line(25, $yFirmas + 14, 95, $yFirmas + 14);
        $pdf->SetXY(25, $yFirmas + 15);
        $pdf->Cell(70, 3.5, conv_pdf_estudiantes(strtoupper($admin_nombre)), 0, 1, 'C');
        $pdf->SetX(25);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(70, 3.5, conv_pdf_estudiantes('ELABORADO POR / CONTROL DE ESTUDIOS'), 0, 1, 'C');

        // Firma 2: Dirección de Control de Estudios
        $pdf->Line(195, $yFirmas + 14, 265, $yFirmas + 14);
        $pdf->SetXY(195, $yFirmas + 15);
        $pdf->SetFont('Arial', 'B', 7.5);
        $pdf->Cell(70, 3.5, conv_pdf_estudiantes('DIRECCIÓN DE CONTROL DE ESTUDIOS'), 0, 1, 'C');
        $pdf->SetX(195);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(70, 3.5, conv_pdf_estudiantes('FIRMA Y SELLO OFICIAL UPTPC'), 0, 1, 'C');

        // Salida limpia a navegador
        $prefijo = ($tipoReporte === 'egresados') ? 'reporte_egresados_' : 'reporte_estudiantes_';
        $pdf->Output('I', $prefijo . date('Ymd_His') . '.pdf');
        exit();
    }
}
