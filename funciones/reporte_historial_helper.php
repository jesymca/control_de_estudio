<?php
/**
 * ==============================================================================
 * GENERADOR DE REPORTES PDF DE HISTORIAL DE NOTAS - UPTPC
 * Generación unificada de 4 reportes en PDF:
 * 1. TSU (Trayectos 0, 1, 2)
 * 2. Ingeniería / Licenciatura (Trayectos 3, 4)
 * 3. Historial Graduando (Validación 100% materias/créditos y dictamen de grado)
 * 4. Historial Desglosado Completo (Trayectos 0 al 4 + desglose de intentos)
 * 
 * Formato estrictamente institucional en blanco y negro / escala de grises.
 * Incluye columna TIPO (R, E, T, P, S), cálculo de IRA y promedio general.
 * ==============================================================================
 */

require_once __DIR__ . '/../fpdf/fpdf.php';
require_once __DIR__ . '/functions.php';

if (!function_exists('conv_pdf')) {
    function conv_pdf($texto) {
        if ($texto === null || $texto === '') {
            return '';
        }
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding((string)$texto, 'ISO-8859-1', 'UTF-8');
        } elseif (function_exists('iconv')) {
            return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', (string)$texto);
        } else {
            return @utf8_decode((string)$texto);
        }
    }
}

if (!class_exists('HistorialPDF')) {
    class HistorialPDF extends FPDF {
        public $titulo_doc = '';
        public $subtitulo_doc = '';

        function Header() {
            // Logos institucionales
            $logo_uptpc = __DIR__ . '/../images/uptpc.png';
            if (file_exists($logo_uptpc)) {
                $this->Image($logo_uptpc, 10, 8, 17, 17);
            }
            $logo_mppeu = __DIR__ . '/../images/mppeu_bb.png';
            if (file_exists($logo_mppeu)) {
                $this->Image($logo_mppeu, 183, 8, 17, 17);
            }

            $this->SetY(8);
            $this->SetTextColor(0, 0, 0);
            $this->SetDrawColor(0, 0, 0);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(0, 3.5, conv_pdf('REPÚBLICA BOLIVARIANA DE VENEZUELA'), 0, 1, 'C');
            $this->Cell(0, 3.5, conv_pdf('MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN UNIVERSITARIA'), 0, 1, 'C');
            $this->Cell(0, 3.5, conv_pdf('UNIVERSIDAD POLITÉCNICA TERRITORIAL DE PUERTO CABELLO'), 0, 1, 'C');
            $this->SetFont('Arial', '', 7.5);
            $this->Cell(0, 3.5, conv_pdf('DEPARTAMENTO DE CONTROL DE ESTUDIOS'), 0, 1, 'C');
            $this->Ln(2);

            // Título específico del reporte
            $this->SetFont('Arial', 'B', 9.5);
            $this->Cell(0, 4.5, conv_pdf($this->titulo_doc), 0, 1, 'C');
            if (!empty($this->subtitulo_doc)) {
                $this->SetFont('Arial', 'I', 7.5);
                $this->Cell(0, 3.5, conv_pdf($this->subtitulo_doc), 0, 1, 'C');
            }
            $this->Ln(2);
        }

        function Footer() {
            $this->SetY(-18);
            $this->SetDrawColor(0, 0, 0);
            $this->SetTextColor(0, 0, 0);
            $this->Line(10, $this->GetY(), 200, $this->GetY());
            $this->Ln(1);
            $this->SetFont('Arial', 'I', 6.5);
            $this->Cell(100, 3.5, conv_pdf('Documento oficial expedido por el Sistema de Control de Estudios - UPTPC'), 0, 0, 'L');
            $this->Cell(90, 3.5, conv_pdf('Página ') . $this->PageNo() . ' de {nb}', 0, 1, 'R');
            $this->SetFont('Arial', 'B', 6.5);
            $this->Cell(0, 3.5, conv_pdf('Válido únicamente con firma autógrafa y sello húmedo del Departamento de Control de Estudios'), 0, 0, 'C');
        }
    }
}

/**
 * Función central para generar cualquiera de los 4 reportes de historial académico en PDF
 *
 * @param string $tipo 'tsu' | 'ingenieria' | 'graduando' | 'desglosado'
 * @param int $estudiante_id ID del estudiante
 */
if (!function_exists('generarPDFHistorialDesglozado')) {
function generarPDFHistorialDesglozado($tipo = 'tsu', $estudiante_id = 0) {
    global $db;

    // Obtener parámetros priorizando POST
    if ($estudiante_id <= 0) {
        if (isset($_POST['estudiante_id']) && intval($_POST['estudiante_id']) > 0) {
            $estudiante_id = intval($_POST['estudiante_id']);
        } elseif (isset($_GET['estudiante_id']) && intval($_GET['estudiante_id']) > 0) {
            $estudiante_id = intval($_GET['estudiante_id']);
        }
    }

    if ($estudiante_id <= 0) {
        die("Error: No se especificó el ID del estudiante para generar el reporte. Por favor acceda mediante el formulario correspondiente.");
    }

    // Cargar información del estudiante
    $estudiante = obtenerEstudiantePorId($estudiante_id);
    if (!$estudiante) {
        die("Error: Estudiante no encontrado en la base de datos.");
    }

    $carrera = obtenerCarreraEstudiante($estudiante_id);
    if (!$carrera) {
        die("Error: El estudiante no tiene una carrera asignada.");
    }

    $carrera_id = (int)$carrera['id_carrera'];
    $nombre_carrera_formateado = formatearNombreCarrera(
        $carrera['nombre_carrera'] ?? '',
        $carrera['tipo_formacion'] ?? ''
    );

    // Obtener materias del plan de estudios y el historial completo de notas
    $materias_carrera_res = obtenerMateriasCarrera($carrera_id);
    $historial_notas = obtenerHistorialCompletoNotasEstudiante($estudiante_id);
    $info_apto = esAptoParaGradoConsulta($estudiante_id, $carrera_id);
    $ira_data = calcularIRAEstudiante($estudiante_id, $carrera_id);

    // Definir configuración según el tipo de reporte solicitado
    $tipo = strtolower(trim($tipo));
    if ($tipo === 'ingenieria' || $tipo === 'ing') {
        $tipo_normalizado = 'ingenieria';
        $trayectos_permitidos = [3, 4];
        $titulo_doc = 'HISTORIAL DESGLOZADO DE CALIFICACIONES - INGENIERÍA / LICENCIATURA';
        $subtitulo_doc = 'NIVEL PROFESIONAL (TRAYECTOS III Y IV)';
        $archivo_prefix = 'Historial_Ingenieria_';
    } elseif ($tipo === 'graduando') {
        $tipo_normalizado = 'graduando';
        $trayectos_permitidos = [0, 1, 2, 3, 4];
        $titulo_doc = 'HISTORIAL ACADÉMICO PARA GRADUANDO';
        $subtitulo_doc = 'EXPEDIENTE DE CULMINACIÓN Y DICTAMEN DE GRADO ACADÉMICO';
        $archivo_prefix = 'Historial_Graduando_';
    } elseif ($tipo === 'desglosado' || $tipo === 'completo') {
        $tipo_normalizado = 'desglosado';
        $trayectos_permitidos = [0, 1, 2, 3, 4];
        $titulo_doc = 'HISTORIAL DESGLOZADO INTEGRAL DE CALIFICACIONES';
        $subtitulo_doc = 'PLAN DE ESTUDIOS COMPLETO (TRAYECTOS INICIAL AL IV)';
        $archivo_prefix = 'Historial_Desglosado_';
    } else {
        // Por defecto TSU
        $tipo_normalizado = 'tsu';
        $trayectos_permitidos = [0, 1, 2];
        $titulo_doc = 'HISTORIAL DESGLOZADO DE CALIFICACIONES - TSU';
        $subtitulo_doc = 'TÉCNICO SUPERIOR UNIVERSITARIO (TRAYECTOS INICIAL, I Y II)';
        $archivo_prefix = 'Historial_TSU_';
    }

    // Filtrar materias por los trayectos permitidos
    $materias_filtradas = [];
    $materias_agrupadas = [];
    if ($materias_carrera_res && $materias_carrera_res->num_rows > 0) {
        $materias_carrera_res->data_seek(0);
        while ($m = $materias_carrera_res->fetch_assoc()) {
            $t = (int)$m['trayecto'];
            if (in_array($t, $trayectos_permitidos, true)) {
                $materias_filtradas[] = $m;
                if (!isset($materias_agrupadas[$t])) {
                    $materias_agrupadas[$t] = [];
                }
                $materias_agrupadas[$t][] = $m;
            }
        }
    }
    ksort($materias_agrupadas);

    // Estadísticas y totales
    $total_materias_plan = count($materias_filtradas);
    $materias_aprobadas_count = 0;
    $materias_reprobadas_count = 0;
    $materias_cursando_count = 0;
    $materias_pendientes_count = 0;

    $total_uc_plan = 0;
    $uc_aprobadas = 0;

    $suma_calificaciones_scope = 0;
    $total_evaluaciones_scope = 0;

    foreach ($materias_filtradas as $m) {
        $mid = (int)$m['id_materia'];
        $uc = intval($m['creditos'] ?? 0);
        $total_uc_plan += $uc;
        $nota_min = (!empty($m['es_proyecto_socio']) && $m['es_proyecto_socio'] == 1) ? 16 : 12;

        $intentos = $historial_notas[$mid] ?? [];
        if (!empty($intentos)) {
            // Contabilizar todas las notas obtenidas en este alcance para promedio
            foreach ($intentos as $it) {
                if ($it['nota_final'] !== null && is_numeric($it['nota_final'])) {
                    $suma_calificaciones_scope += floatval($it['nota_final']);
                    $total_evaluaciones_scope++;
                }
            }

            // El último intento es la situación académica vigente
            $actual = end($intentos);
            if ($actual['nota_final'] !== null) {
                if ($actual['nota_final'] >= $nota_min) {
                    $materias_aprobadas_count++;
                    $uc_aprobadas += $uc;
                } else {
                    $materias_reprobadas_count++;
                }
            } else {
                $materias_cursando_count++;
            }
        } else {
            $materias_pendientes_count++;
        }
    }

    $uc_pendientes = max(0, $total_uc_plan - $uc_aprobadas);
    $promedio_scope = ($total_evaluaciones_scope > 0) ? round($suma_calificaciones_scope / $total_evaluaciones_scope, 2) : 0;
    $ira_final = $ira_data['ira'];
    $promedio_general_global = $ira_data['promedio_simple'];

    // Determinar condición para grado real
    $es_apto_graduando = false;
    if ($tipo_normalizado === 'tsu') {
        if (!empty($info_apto['solo_trayecto_0'])) {
            $estatus_grado = 'NO APTO (Solo Trayecto Inicial cursado)';
        } elseif (!empty($info_apto['apto_tsu'])) {
            $estatus_grado = 'APTO PARA GRADO TSU';
            $es_apto_graduando = true;
        } else {
            $faltan = max(0, $total_materias_plan - $materias_aprobadas_count);
            $estatus_grado = "EN CURSO (Faltan $faltan materias de TSU)";
        }
    } elseif ($tipo_normalizado === 'ingenieria') {
        if (!empty($info_apto['apto_grado_completo'])) {
            $estatus_grado = 'APTO PARA GRADO INGENIERÍA / LIC.';
            $es_apto_graduando = true;
        } else {
            $faltan = max(0, $total_materias_plan - $materias_aprobadas_count);
            $estatus_grado = "EN CURSO (Faltan $faltan materias para grado)";
        }
    } elseif ($tipo_normalizado === 'graduando') {
        if (!empty($info_apto['apto_grado_completo'])) {
            $estatus_grado = 'APTO PARA GRADO (CULMINACIÓN 100%)';
            $es_apto_graduando = true;
        } elseif (!empty($info_apto['apto_tsu'])) {
            $estatus_grado = 'APTO PARA GRADO TSU (Ingeniería en Curso)';
            $es_apto_graduando = true;
        } elseif (!empty($info_apto['solo_trayecto_0'])) {
            $estatus_grado = 'NO APTO (Solo Trayecto Inicial cursado)';
        } else {
            $faltan = max(0, $total_materias_plan - $materias_aprobadas_count);
            $estatus_grado = "NO APTO / EN CULMINACIÓN (Faltan $faltan materias)";
        }
    } else {
        // Desglosado completo
        if (!empty($info_apto['apto_grado_completo'])) {
            $estatus_grado = 'APTO PARA GRADO COMPLETO';
        } elseif (!empty($info_apto['apto_tsu'])) {
            $estatus_grado = 'APTO TSU (Ingeniería en Curso)';
        } elseif (!empty($info_apto['solo_trayecto_0'])) {
            $estatus_grado = 'NO APTO (Solo Trayecto Inicial cursado)';
        } else {
            $faltan = max(0, $total_materias_plan - $materias_aprobadas_count);
            $estatus_grado = "EN CURSO (Faltan $faltan materias)";
        }
    }

    $emitido_por = !empty($_SESSION['user']['nombre']) 
        ? $_SESSION['user']['nombre'] 
        : (!empty($_SESSION['user']['username']) ? $_SESSION['user']['username'] : 'Departamento de Control de Estudios');

    // Inicializar FPDF
    $pdf = new HistorialPDF('P', 'mm', 'A4');
    $pdf->titulo_doc = $titulo_doc;
    $pdf->subtitulo_doc = $subtitulo_doc;
    $pdf->AliasNbPages();
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 18);
    $pdf->AddPage();

    // -------------------------------------------------------------
    // Ficha de Información del Estudiante (Estilo estricto B&N)
    // -------------------------------------------------------------
    $pdf->SetFillColor(245, 245, 245);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Rect(10, $pdf->GetY(), 190, 15, 'DF');

    $pdf->SetY($pdf->GetY() + 1.5);
    $pdf->SetFont('Arial', 'B', 7.5);

    // Fila 1
    $pdf->Cell(18, 4, conv_pdf('Cédula:'), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7.5);
    $pdf->Cell(38, 4, conv_pdf($estudiante['idusuario']), 0, 0, 'L');

    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->Cell(20, 4, conv_pdf('Estudiante:'), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7.5);
    $pdf->Cell(70, 4, conv_pdf(strtoupper($estudiante['nombre'])), 0, 0, 'L');

    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->Cell(16, 4, conv_pdf('Fecha:'), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7.5);
    $pdf->Cell(28, 4, date('d/m/Y'), 0, 1, 'R');

    // Fila 2
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->Cell(18, 4, conv_pdf('Programa:'), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7.5);
    $pdf->Cell(108, 4, conv_pdf(strtoupper($nombre_carrera_formateado)), 0, 0, 'L');

    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->Cell(20, 4, conv_pdf('Condición:'), 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->Cell(44, 4, conv_pdf($estatus_grado), 0, 1, 'R');

    $pdf->Ln(4);

    // -------------------------------------------------------------
    // Encabezado de la Tabla de Asignaturas con columna TIPO
    // CÓDIGO (18) | ASIGNATURA (64) | TRAY. (10) | UC (10) | CALIF. (12) | TIPO (12) | ESTADO (18) | LAPSO (18) | DOCENTE (28) = 190 mm
    // -------------------------------------------------------------
    $pdf->SetFillColor(235, 235, 235);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', 'B', 7);

    $pdf->Cell(18, 5, conv_pdf('CÓDIGO'), 1, 0, 'C', true);
    $pdf->Cell(64, 5, conv_pdf('ASIGNATURA'), 1, 0, 'C', true);
    $pdf->Cell(10, 5, conv_pdf('TRAY.'), 1, 0, 'C', true);
    $pdf->Cell(10, 5, conv_pdf('UC'), 1, 0, 'C', true);
    $pdf->Cell(12, 5, conv_pdf('CALIF.'), 1, 0, 'C', true);
    $pdf->Cell(12, 5, conv_pdf('TIPO'), 1, 0, 'C', true);
    $pdf->Cell(18, 5, conv_pdf('ESTADO'), 1, 0, 'C', true);
    $pdf->Cell(18, 5, conv_pdf('LAPSO'), 1, 0, 'C', true);
    $pdf->Cell(28, 5, conv_pdf('DOCENTE'), 1, 1, 'C', true);

    // -------------------------------------------------------------
    // Cuerpo de la Tabla agrupado por Trayectos
    // -------------------------------------------------------------
    if (!empty($materias_agrupadas)) {
        foreach ($materias_agrupadas as $trayecto_num => $materias_tr) {
            $info_tr = obtenerInfoTrayecto($trayecto_num);
            $nombre_trayecto_header = !empty($info_tr['nombre_trayecto']) 
                ? $info_tr['nombre_trayecto'] 
                : ($trayecto_num == 0 ? 'Trayecto Inicial (Nivelatorio)' : 'Trayecto ' . $trayecto_num);

            $pdf->SetFillColor(245, 245, 245);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(190, 4.5, conv_pdf('>> ' . strtoupper($nombre_trayecto_header)), 1, 1, 'L', true);

            foreach ($materias_tr as $m) {
                $mid = (int)$m['id_materia'];
                $uc_mat = intval($m['creditos'] ?? 0);
                $nota_min = (!empty($m['es_proyecto_socio']) && $m['es_proyecto_socio'] == 1) ? 16 : 12;

                $intentos = $historial_notas[$mid] ?? [];
                $total_intentos = count($intentos);

                if ($total_intentos > 0) {
                    $actual = $intentos[$total_intentos - 1];
                    $previos = array_slice($intentos, 0, $total_intentos - 1);

                    $nota_final = $actual['nota_final'];
                    $lapso_str = !empty($actual['nombre_periodo']) ? $actual['nombre_periodo'] : '-';
                    $docente_str = !empty($actual['nombre_docente']) ? $actual['nombre_docente'] : '-';
                    $tipo_exam_str = !empty($actual['tipo_examen']) ? strtoupper($actual['tipo_examen']) : 'R';

                    if ($nota_final !== null) {
                        if ($nota_final >= $nota_min) {
                            $estado_str = 'APROBADA';
                        } else {
                            $estado_str = 'REPROBADA';
                        }
                        $calif_str = number_format($nota_final, 1);
                    } else {
                        $estado_str = 'CURSANDO';
                        $calif_str = 'CURS.';
                        $tipo_exam_str = 'R';
                    }
                } else {
                    $calif_str = '-';
                    $tipo_exam_str = '-';
                    $estado_str = 'PENDIENTE';
                    $lapso_str = '-';
                    $docente_str = '-';
                    $previos = [];
                }

                $pdf->SetFont('Arial', '', 6.5);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetDrawColor(0, 0, 0);

                $pdf->Cell(18, 4.5, conv_pdf($m['cod_materia'] ?? '-'), 1, 0, 'C');
                $pdf->Cell(64, 4.5, conv_pdf(substr($m['nombre_materia'] ?? '', 0, 42)), 1, 0, 'L');
                $pdf->Cell(10, 4.5, conv_pdf('T-' . $trayecto_num), 1, 0, 'C');
                $pdf->Cell(10, 4.5, conv_pdf($uc_mat), 1, 0, 'C');

                if ($calif_str !== '-' && $calif_str !== 'CURS.') {
                    $pdf->SetFont('Arial', 'B', 6.5);
                }
                $pdf->Cell(12, 4.5, conv_pdf($calif_str), 1, 0, 'C');
                $pdf->SetFont('Arial', '', 6.5);

                // Columna TIPO
                $pdf->SetFont('Arial', 'B', 6.5);
                $pdf->Cell(12, 4.5, conv_pdf($tipo_exam_str), 1, 0, 'C');
                $pdf->SetFont('Arial', '', 6.5);

                // Columna ESTADO (en negrita, blanco y negro)
                if ($estado_str === 'APROBADA' || $estado_str === 'REPROBADA') {
                    $pdf->SetFont('Arial', 'B', 6.5);
                }
                $pdf->Cell(18, 4.5, conv_pdf($estado_str), 1, 0, 'C');
                $pdf->SetFont('Arial', '', 6.5);

                $pdf->Cell(18, 4.5, conv_pdf($lapso_str), 1, 0, 'C');
                $pdf->Cell(28, 4.5, conv_pdf(substr($docente_str, 0, 20)), 1, 1, 'L');

                // Desglose de intentos previos (Materias repitientes o modalidades anteriores)
                if (!empty($previos)) {
                    foreach ($previos as $p) {
                        $p_nota = ($p['nota_final'] !== null) ? number_format($p['nota_final'], 1) : '-';
                        $p_est = ($p['nota_final'] !== null && $p['nota_final'] >= $nota_min) ? 'APROBADA' : 'REPROBADA';
                        $p_tipo = !empty($p['tipo_examen']) ? strtoupper($p['tipo_examen']) : 'R';
                        $p_lap = !empty($p['nombre_periodo']) ? $p['nombre_periodo'] : '-';
                        $p_doc = !empty($p['nombre_docente']) ? $p['nombre_docente'] : '-';

                        $pdf->SetFont('Arial', 'I', 6);
                        $pdf->Cell(18, 3.8, '', 'L', 0);
                        $pdf->Cell(172, 3.8, conv_pdf("   >> Intento Previo: Lapso $p_lap  |  Calif: $p_nota ($p_est)  |  Tipo: $p_tipo  |  Docente: $p_doc"), 'R', 1, 'L');
                    }
                    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
                }
            }
        }
    } else {
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(190, 8, conv_pdf('No hay asignaturas registradas para este tramo en el plan de estudios.'), 1, 1, 'C');
    }

    // Leyenda de tipos de examen
    $pdf->SetFont('Arial', '', 6);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(190, 3.5, conv_pdf('Tipo de Evaluación / Examen: [R] Regular  |  [E] Extraordinario  |  [T] Intensivo  |  [P] Plan Especial de Recuperación (PER)  |  [S] Suficiencia'), 0, 1, 'L');

    // -------------------------------------------------------------
    // Bloque de Resumen Académico Real con Promedio e IRA
    // -------------------------------------------------------------
    $pdf->Ln(2);

    // Evitar salto de página no deseado para el resumen
    if ($pdf->GetY() > 215) {
        $pdf->AddPage();
    }

    $pdf->SetFillColor(235, 235, 235);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->Cell(190, 5, conv_pdf('RESUMEN DE RENDIMIENTO ACADÉMICO'), 1, 1, 'L', true);

    $pdf->SetFont('Arial', '', 7);
    $pdf->SetFillColor(250, 250, 250);

    // Fila 1: Asignaturas evaluadas y Total UC
    $pdf->Cell(50, 4.5, conv_pdf('Asignaturas del Plan Evaluadas:'), 1, 0, 'L', true);
    $pdf->Cell(45, 4.5, conv_pdf($total_materias_plan . ' asignaturas'), 1, 0, 'C');
    $pdf->Cell(50, 4.5, conv_pdf('Total Créditos del Plan (UC):'), 1, 0, 'L', true);
    $pdf->Cell(45, 4.5, conv_pdf($total_uc_plan . ' UC'), 1, 1, 'C');

    // Fila 2: Aprobadas
    $pdf->Cell(50, 4.5, conv_pdf('Asignaturas Aprobadas:'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(45, 4.5, conv_pdf($materias_aprobadas_count), 1, 0, 'C');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(50, 4.5, conv_pdf('Créditos Aprobados (UC):'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(45, 4.5, conv_pdf($uc_aprobadas . ' UC'), 1, 1, 'C');
    $pdf->SetFont('Arial', '', 7);

    // Fila 3: Pendientes
    $pdf->Cell(50, 4.5, conv_pdf('Asignaturas Pendientes / Por Cursar:'), 1, 0, 'L', true);
    $pdf->Cell(45, 4.5, conv_pdf(max(0, $total_materias_plan - $materias_aprobadas_count)), 1, 0, 'C');
    $pdf->Cell(50, 4.5, conv_pdf('Créditos Pendientes (UC):'), 1, 0, 'L', true);
    $pdf->Cell(45, 4.5, conv_pdf($uc_pendientes . ' UC'), 1, 1, 'C');

    // Fila 4: Promedio General y Rendimiento Académico IRA (incluye repitientes)
    $promedio_texto = ($total_evaluaciones_scope > 0) ? number_format($promedio_scope, 2) . ' pts' : 'N/A';
    $ira_texto = ($ira_final > 0) ? number_format($ira_final, 3) . ' pts' : ($promedio_texto);

    $pdf->Cell(50, 4.5, conv_pdf('Promedio General Calificaciones:'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(45, 4.5, conv_pdf($promedio_texto), 1, 0, 'C');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(50, 4.5, conv_pdf('Índice Rendimiento Académico (IRA):'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(45, 4.5, conv_pdf($ira_texto), 1, 1, 'C');
    $pdf->SetFont('Arial', '', 7);

    // Fila 5: Condición académica
    $pdf->Cell(50, 4.5, conv_pdf('Condición Académica Actual:'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(140, 4.5, conv_pdf($estatus_grado), 1, 1, 'L');
    $pdf->SetFont('Arial', '', 7);

    // -------------------------------------------------------------
    // Sección Específica para Reporte de Graduando (Dictamen Oficial)
    // -------------------------------------------------------------
    if ($tipo_normalizado === 'graduando') {
        $pdf->Ln(2);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('Arial', 'B', 7.5);
        $pdf->Cell(190, 4.5, conv_pdf('DICTAMEN Y CERTIFICACIÓN DE CULMINACIÓN DE REQUISITOS DE GRADO'), 1, 1, 'L', true);
        $pdf->SetFont('Arial', '', 6.8);
        
        if ($es_apto_graduando) {
            $dictamen_texto = "El Departamento de Control de Estudios de la Universidad Politécnica Territorial de Puerto Cabello certifica que el bachiller " 
                . strtoupper($estudiante['nombre']) . ", titular de la Cédula de Identidad N° " . $estudiante['idusuario'] . ", ha cumplido y aprobado satisfactoriamente la totalidad de las Unidades Curriculares y Créditos Académicos (" . $uc_aprobadas . " UC) exigidos en el Plan de Estudios del " 
                . strtoupper($nombre_carrera_formateado) . ", habiendo obtenido un Índice de Rendimiento Académico (IRA) de " . $ira_texto . ". En consecuencia, se declara APTO PARA OPTAR AL TÍTULO ACADÉMICO CORRESPONDIENTE conforme al Reglamento de Evaluación del Desempeño Estudiantil.";
        } else {
            $dictamen_texto = "El Departamento de Control de Estudios hace constar el presente expediente académico del bachiller " 
                . strtoupper($estudiante['nombre']) . ", titular de la Cédula de Identidad N° " . $estudiante['idusuario'] . ", quien a la fecha ha aprobado " . $materias_aprobadas_count . " asignaturas y " . $uc_aprobadas . " UC de un total de " . $total_uc_plan . " UC requeridas. El estudiante se encuentra EN PROCESO DE CULMINACIÓN (NO APTO PARA GRADO HASTA APROBAR LA TOTALIDAD DEL PLAN DE ESTUDIOS).";
        }
        $pdf->MultiCell(190, 3.5, conv_pdf($dictamen_texto), 1, 'J');
    }

    $pdf->Ln(2);

    // Metadatos de emisión y base legal
    $pdf->SetFont('Arial', 'I', 6.5);
    $pdf->Cell(95, 3.5, conv_pdf('Emitido por: ' . strtoupper($emitido_por)), 0, 0, 'L');
    $pdf->Cell(95, 3.5, conv_pdf('Generado el: ' . date('d/m/Y h:i A')), 0, 1, 'R');

    $pdf->MultiCell(190, 3, conv_pdf('Institución autorizada para gestionar el Programa Nacional de Formación según Gaceta Oficial de la República Bolivariana de Venezuela N° 39.721 de fecha 26 de Julio de 2011.'), 0, 'L');

    // -------------------------------------------------------------
    // Sección de Firmas Oficiales
    // -------------------------------------------------------------
    $pdf->Ln(4);
    if ($pdf->GetY() > 245) {
        $pdf->AddPage();
    }
    $pdf->SetY($pdf->GetY() + 8);

    if ($tipo_normalizado === 'graduando') {
        // Para Graduando: 3 firmas (Control de Estudios, Coordinación de Carrera, Secretaría General)
        $ancho_col = 60;
        $gap = 5;
        
        $y_firmas = $pdf->GetY();
        $pdf->Line(10, $y_firmas, 10 + $ancho_col, $y_firmas);
        $pdf->Line(10 + $ancho_col + $gap, $y_firmas, 10 + ($ancho_col * 2) + $gap, $y_firmas);
        $pdf->Line(10 + (($ancho_col + $gap) * 2), $y_firmas, 200, $y_firmas);

        $pdf->SetFont('Arial', 'B', 6.5);
        $pdf->SetXY(10, $y_firmas + 1);
        $pdf->Cell($ancho_col, 3, conv_pdf('CONTROL DE ESTUDIOS'), 0, 0, 'C');
        $pdf->Cell($gap, 3, '', 0, 0);
        $pdf->Cell($ancho_col, 3, conv_pdf('COORDINACIÓN DE CARRERA'), 0, 0, 'C');
        $pdf->Cell($gap, 3, '', 0, 0);
        $pdf->Cell($ancho_col, 3, conv_pdf('SECRETARÍA GENERAL'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell($ancho_col, 3, conv_pdf('Firma y Sello'), 0, 0, 'C');
        $pdf->Cell($gap, 3, '', 0, 0);
        $pdf->Cell($ancho_col, 3, conv_pdf('Firma y Sello'), 0, 0, 'C');
        $pdf->Cell($gap, 3, '', 0, 0);
        $pdf->Cell($ancho_col, 3, conv_pdf('Firma y Sello'), 0, 1, 'C');
    } else {
        // Firma única de Control de Estudios
        $x_centro = 105;
        $ancho_linea = 80;
        $x_inicio = $x_centro - ($ancho_linea / 2);

        $pdf->Line($x_inicio, $pdf->GetY(), $x_inicio + $ancho_linea, $pdf->GetY());
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(0, 3.5, conv_pdf('DEPARTAMENTO DE CONTROL DE ESTUDIOS'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 6.5);
        $pdf->Cell(0, 3, conv_pdf('Firma Autógrafa y Sello Húmedo'), 0, 1, 'C');
    }

    // Salida del archivo
    $cedula_limpia = preg_replace('/[^a-zA-Z0-9]/', '_', $estudiante['idusuario']);
    $nombre_archivo = $archivo_prefix . $cedula_limpia . '_' . date('Ymd_His') . '.pdf';

    $pdf->Output('I', $nombre_archivo);
    exit;
}
}
