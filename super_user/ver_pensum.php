<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$titulopag = "Pensum de la Carrera";
include('../funciones/functions.php');

// Verificar conexión MySQLi
if (!$db) {
    die("Error de conexión: " . mysqli_connect_error());
}

$id_carrera = null;
$carrera = null;
$version_year = null;
$codigo_malla = null;
$id_malla = null;

// --- LÓGICA DE BÚSQUEDA ---
if (isset($_GET['id_malla'])) {
    $malla_id = intval($_GET['id_malla']);
    $stmt = $db->prepare("SELECT m.id_malla, m.id_carrera, m.codigo_malla, m.anio, c.nombre_carrera, c.tipo_formacion FROM mallas m JOIN carreras c ON m.id_carrera = c.id_carrera WHERE m.id_malla = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $malla_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $carrera = ['nombre_carrera' => $row['nombre_carrera'], 'tipo_formacion' => $row['tipo_formacion']];
            $id_carrera = (int)$row['id_carrera'];
            $codigo_malla = $row['codigo_malla'];
            $id_malla = $malla_id;
        }
        $stmt->close();
    }
} elseif (isset($_GET['id_carrera'])) {
    $id_carrera = (int)$_GET['id_carrera'];
    $stmt = $db->prepare("SELECT nombre_carrera, tipo_formacion FROM carreras WHERE id_carrera = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $id_carrera);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) { $carrera = $res->fetch_assoc(); }
        $stmt->close();
    }
}

if (!$carrera) { header("Location: lista_carreras.php"); exit(); }

if (empty($id_malla) && !empty($id_carrera)) {
    $mallas_disponibles = obtenerMallasPorCarrera($id_carrera);
    if (!empty($mallas_disponibles)) {
        $id_malla = intval($mallas_disponibles[0]['id_malla']);
        $codigo_malla = $mallas_disponibles[0]['codigo_malla'];
    }
}

$tipo_periodo = obtenerTipoPeriodoPorCarrera($id_carrera);
$texto_duracion = ($tipo_periodo == 'trimestre') ? 'trimestres' : 'semestres';

// --- CONSULTA CON LOS NUEVOS CAMPOS ---
if (!empty($id_malla)) {
    $query_materias = "SELECT m.*, mm.semestre, m.trayecto FROM materias m JOIN malla_materia mm ON m.id_materia = mm.id_materia WHERE mm.id_malla = " . intval($id_malla) . " ORDER BY m.trayecto, m.duracion_periodo, m.nombre_materia";
} else {
    $query_materias = "SELECT m.*, cm.semestre, m.trayecto FROM materias m JOIN carrera_materia cm ON m.id_materia = cm.id_materia WHERE cm.id_carrera = " . intval($id_carrera) . " ORDER BY m.trayecto, m.duracion_periodo, m.nombre_materia";
}

$result_materias = mysqli_query($db, $query_materias);
$materias_agrupadas = [];

// Determinar el texto a usar basado en el tipo de formación
$es_ptf = (isset($carrera['tipo_formacion']) && strtoupper($carrera['tipo_formacion']) == 'PTF');
$texto_trayecto = $es_ptf ? 'Semestre' : 'Trayecto';

while ($materia = mysqli_fetch_assoc($result_materias)) {
    $trayecto = $materia['trayecto'];
    $nombre_grupo = '';
    
    if ($es_ptf) {
        // Para PTF: "Semestre 1", "Semestre 2", etc.
        $nombre_grupo = ($trayecto == 0) ? 'Semestre Inicial' : 'Semestre ' . $trayecto;
    } else {
        // Para otros tipos: "Trayecto 1", "Trayecto 2", etc.
        $nombre_grupo = ($trayecto == 0) ? 'Trayecto Inicial' : 'Trayecto ' . $trayecto;
    }
    
    $materias_agrupadas[$nombre_grupo][] = $materia;
}

// ==========================================
// GENERACIÓN DE PDF (VERTICAL OPTIMIZADO)
// ==========================================
if (isset($_GET['pdf']) && $_GET['pdf'] == '1') {
    ini_set('display_errors', '0');
    require_once __DIR__ . '/../fpdf/fpdf.php';

    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->AddPage();

    if (function_exists('agregarMembreteFPDF')) {
        agregarMembreteFPDF($pdf);
        $pdf->SetY(45);
    }

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, to_iso('PENSUM DE ESTUDIOS: ' . mb_strtoupper($carrera['nombre_carrera'])), 0, 1, 'C');
    
    // Solo el código, sin la frase "Código de malla"
    if(!empty($codigo_malla)) {
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, to_iso($codigo_malla), 0, 1, 'C');
    }
    $pdf->Ln(5);

    foreach ($materias_agrupadas as $grupo_nombre => $materias) {
        // Verificar espacio restante antes de imprimir el encabezado del grupo
        if ($pdf->GetY() > 250) $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(0, 7, to_iso($grupo_nombre), 1, 1, 'L', true);

        // Anchos de columna redistribuidos (Total 190mm)
        $w = [22, 70, 14, 16, 16, 16, 16, 20]; // Total: 190mm
        $pdf->SetFont('Arial', 'B', 8);
        $headers = ['CÓDIGO', 'ASIGNATURA', 'UC', 'H.T.', 'H.P.', 'H.L.', 'H.S.', 'ESTADO'];
        foreach($headers as $i => $h_text) $pdf->Cell($w[$i], 7, to_iso($h_text), 1, 0, 'C', true);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 8);
        $total_uc_trayecto = 0;
        foreach ($materias as $m) {
            $total_uc_trayecto += (int)($m['creditos'] ?? 0);
            // Calcular altura de la fila basándose en el nombre de la materia
            $nb_lines = $pdf->GetStringWidth(to_iso($m['nombre_materia'])) > $w[1] ? 2 : 1;
            $h_fila = 6 * $nb_lines;

            // Salto de página preventivo si la fila no cabe
            if ($pdf->GetY() + $h_fila > 270) $pdf->AddPage();

            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->Cell($w[0], $h_fila, to_iso($m['cod_materia']), 1, 0, 'C');
            
            // Celda multi-línea para el nombre
            $pdf->MultiCell($w[1], ($h_fila/$nb_lines), to_iso($m['nombre_materia']), 1, 'L');
            
            $pdf->SetXY($x + $w[0] + $w[1], $y);
            $pdf->Cell($w[2], $h_fila, $m['creditos'], 1, 0, 'C');
            $pdf->Cell($w[3], $h_fila, $m['horas_teoricas'], 1, 0, 'C');
            $pdf->Cell($w[4], $h_fila, $m['horas_practicas'], 1, 0, 'C');
            $pdf->Cell($w[5], $h_fila, $m['horas_laboratorio'], 1, 0, 'C');
            $pdf->Cell($w[6], $h_fila, $m['horas_semanales'], 1, 0, 'C');
            $pdf->Cell($w[7], $h_fila, ($m['activa'] ? 'Activa' : 'Inactiva'), 1, 1, 'C');
        }

        // Fila Total UC por Trayecto en PDF
        if ($pdf->GetY() + 6 > 270) $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell($w[0] + $w[1], 6, to_iso('Total Unidades de Crédito (UC):'), 1, 0, 'R', true);
        $pdf->Cell($w[2], 6, $total_uc_trayecto, 1, 0, 'C', true);
        $pdf->Cell($w[3] + $w[4] + $w[5] + $w[6] + $w[7], 6, '', 1, 1, 'C', true);
        $pdf->Ln(4);
    }
    $pdf->Output('I', 'Pensum_Academico.pdf');
    exit();
}

include("includes/head.php");
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pensum: <?php echo htmlspecialchars($carrera['nombre_carrera']); ?></h1>
        <div>
            <a href="lista_carreras.php" class="btn btn-sm btn-primary shadow-sm no-print"><i class="fas fa-arrow-left"></i> Volver</a>
            <a href="?<?= $_SERVER['QUERY_STRING'] ?>&pdf=1" class="btn btn-sm btn-success shadow-sm no-print ml-2">
                <i class="fas fa-print"></i> Generar PDF
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-secondary text-white">
            <h6 class="m-0 font-weight-bold">Plan de Estudios Completo</h6>
        </div>
        <div class="card-body">
            <?php if (empty($materias_agrupadas)): ?>
                <div class="alert alert-warning">No hay materias registradas.</div>
            <?php else: ?>
                <?php foreach ($materias_agrupadas as $grupo => $materias): ?>
                    <h5 class="font-weight-bold text-primary mt-4 mb-3"><?= htmlspecialchars($grupo) ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" style="table-layout: fixed; width: 100%;">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th style="width: 12%">Código</th>
                                    <th style="width: 38%">Nombre de la Asignatura</th>
                                    <th style="width: 8%">Unidades Crédito</th>
                                    <th style="width: 8%">Horas Teóricas</th>
                                    <th style="width: 8%">Horas Prácticas</th>
                                    <th style="width: 8%">Horas Laboratorio</th>
                                    <th style="width: 8%">Horas Semanales</th>
                                    <th style="width: 10%">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_uc_trayecto = 0;
                                foreach ($materias as $m): 
                                    $total_uc_trayecto += (int)($m['creditos'] ?? 0);
                                ?>
                                    <tr>
                                        <td class="text-center"><?= htmlspecialchars($m['cod_materia']) ?></td>
                                        <td><?= htmlspecialchars($m['nombre_materia']) ?></td>
                                        <td class="text-center"><?= $m['creditos'] ?></td>
                                        <td class="text-center"><?= $m['horas_teoricas'] ?></td>
                                        <td class="text-center"><?= $m['horas_practicas'] ?></td>
                                        <td class="text-center"><?= $m['horas_laboratorio'] ?></td>
                                        <td class="text-center"><?= $m['horas_semanales'] ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $m['activa'] ? 'success' : 'secondary' ?>">
                                                <?= $m['activa'] ? 'Activa' : 'Inactiva' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="thead-light">
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="2" class="text-right align-middle py-2">
                                        <i class="fas fa-calculator text-primary mr-1"></i> Total Unidades de Crédito (UC):
                                    </td>
                                    <td class="text-center align-middle py-2 text-primary font-weight-bold" style="font-size: 1.05rem;">
                                        <?= $total_uc_trayecto ?>
                                    </td>
                                    <td colspan="5" class="py-2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>