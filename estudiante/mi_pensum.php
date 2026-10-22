<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$titulopag = "Mi Pensum Académico";
include('../funciones/functions.php');

if (!isLoggedIn() || !isEstudiante()) {
    $_SESSION['msg'] = "Debes iniciar sesión como estudiante para acceder";
    header('location: ../login.php');
    exit();
}

visita();

$user_id = (int)$_SESSION['user']['id'];
// Buscamos la carrera y también intentamos identificar la malla activa para esa carrera
$query_estudiante = "SELECT carrera FROM users WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($db, $query_estudiante);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result_estudiante = mysqli_stmt_get_result($stmt);

if (!$result_estudiante || mysqli_num_rows($result_estudiante) === 0) {
    header('location: index.php');
    die();
}

$estudiante = mysqli_fetch_assoc($result_estudiante);
$id_carrera = (int)$estudiante['carrera'];

if ($id_carrera === 0) {
    $_SESSION['error'] = "No tienes una carrera asignada";
    header('location: index.php');
    die();
}

// Obtener nombre de carrera y buscar el código de malla asociado
$query_info = "SELECT c.nombre_carrera, m.codigo_malla 
               FROM carreras c 
               LEFT JOIN mallas m ON c.id_carrera = m.id_carrera 
               WHERE c.id_carrera = ? 
               ORDER BY m.id_malla DESC LIMIT 1";
$stmt = mysqli_prepare($db, $query_info);
mysqli_stmt_bind_param($stmt, 'i', $id_carrera);
mysqli_stmt_execute($stmt);
$res_info = mysqli_stmt_get_result($stmt);
$info = mysqli_fetch_assoc($res_info);
$nombre_carrera = $info['nombre_carrera'];
$codigo_malla = $info['codigo_malla'] ?? '';

$tipo_periodo = obtenerTipoPeriodoPorCarrera($id_carrera);

// Obtener materias
$query_materias = "SELECT m.*, cm.semestre, m.trayecto 
                  FROM materias m 
                  JOIN carrera_materia cm ON m.id_materia = cm.id_materia 
                  WHERE cm.id_carrera = ? 
                  ORDER BY m.trayecto, m.duracion_periodo, m.nombre_materia";
$stmt = mysqli_prepare($db, $query_materias);
mysqli_stmt_bind_param($stmt, 'i', $id_carrera);
mysqli_stmt_execute($stmt);
$result_materias = mysqli_stmt_get_result($stmt);

$materias_agrupadas = [];
while ($materia = mysqli_fetch_assoc($result_materias)) {
    $trayecto = $materia['trayecto'];
    $texto_trayecto = ($trayecto == 0) ? 'Trayecto Inicial' : 'Trayecto ' . $trayecto;
    $materias_agrupadas[$texto_trayecto][] = $materia;
}

// PDF
if ((isset($_POST['pdf']) && $_POST['pdf'] == '1') || (isset($_GET['pdf']) && $_GET['pdf'] == '1')) {
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
    $pdf->Cell(0, 7, to_iso('PENSUM ACADÉMICO: ' . mb_strtoupper($nombre_carrera)), 0, 1, 'C');
    if(!empty($codigo_malla)) {
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, to_iso($codigo_malla), 0, 1, 'C');
    }
    $pdf->Ln(5);

    foreach ($materias_agrupadas as $trayecto_nombre => $materias) {
        if ($pdf->GetY() > 250) $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(0, 7, to_iso($trayecto_nombre), 1, 1, 'L', true);

        $w = [22, 58, 10, 14, 14, 14, 14, 24, 20];
        $pdf->SetFont('Arial', 'B', 8);
        $headers = ['CÓDIGO', 'ASIGNATURA', 'UC', 'H.T.', 'H.P.', 'H.L.', 'H.S.', 'DURACIÓN', 'ESTADO'];
        foreach($headers as $i => $h_text) $pdf->Cell($w[$i], 7, to_iso($h_text), 1, 0, 'C', true);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 8);
        $total_uc_trayecto = 0;
        foreach ($materias as $m) {
            $total_uc_trayecto += (int)($m['creditos'] ?? 0);
            $nb_lines = $pdf->GetStringWidth(to_iso($m['nombre_materia'])) > $w[1] ? 2 : 1;
            $h_fila = 6 * $nb_lines;
            if ($pdf->GetY() + $h_fila > 270) $pdf->AddPage();
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Cell($w[0], $h_fila, to_iso($m['cod_materia'] ?? ''), 1, 0, 'C');
            $pdf->MultiCell($w[1], ($h_fila/$nb_lines), to_iso($m['nombre_materia'] ?? ''), 1, 'L');
            $pdf->SetXY($x + $w[0] + $w[1], $y);
            $pdf->Cell($w[2], $h_fila, $m['creditos'] ?? '0', 1, 0, 'C');
            $pdf->Cell($w[3], $h_fila, $m['horas_teoricas'] ?? '0', 1, 0, 'C');
            $pdf->Cell($w[4], $h_fila, $m['horas_practicas'] ?? '0', 1, 0, 'C');
            $pdf->Cell($w[5], $h_fila, $m['horas_laboratorio'] ?? '0', 1, 0, 'C');
            $pdf->Cell($w[6], $h_fila, $m['horas_semanales'] ?? '0', 1, 0, 'C');
            $pdf->Cell($w[7], $h_fila, to_iso($m['duracion_periodo'] ?? ''), 1, 0, 'C');
            $pdf->Cell($w[8], $h_fila, ($m['activa'] ? 'Activa' : 'Inactiva'), 1, 1, 'C');
        }

        // Fila Total UC por Trayecto en PDF
        if ($pdf->GetY() + 6 > 270) $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell($w[0] + $w[1], 6, to_iso('Total Unidades de Crédito (UC):'), 1, 0, 'R', true);
        $pdf->Cell($w[2], 6, $total_uc_trayecto, 1, 0, 'C', true);
        $pdf->Cell($w[3] + $w[4] + $w[5] + $w[6] + $w[7] + $w[8], 6, '', 1, 1, 'C', true);
        $pdf->Ln(4);
    }
    
    // Leyenda de abreviaturas al pie del PDF
    $pdf->SetFont('Arial', 'I', 7);
    $pdf->Cell(0, 4, to_iso('Leyenda: UC = Unidades de Crédito | H.T. = Horas Teóricas | H.P. = Horas Prácticas | H.L. = Horas en Laboratorio | H.S. = Horas Semanales'), 0, 1, 'L');
    
    $pdf->Output('I', 'Mi_Pensum.pdf');
    exit();
}

include("includes/head.php");
?>

<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between mb-4">
        <div class="mb-2 mb-sm-0 text-center text-sm-left">
            <h1 class="h3 mb-0 text-gray-800 h2-sm">Mi Pensum Académico</h1>
            <?php if(!empty($codigo_malla)): ?>
                <span class="badge badge-info mt-1"><?= htmlspecialchars($codigo_malla) ?></span>
            <?php endif; ?>
        </div>
        <div class="d-flex align-items-center">
            <a href="index.php" class="btn btn-sm btn-primary shadow-sm no-print">
                <i class="fas fa-arrow-left mr-1"></i> <span class="d-none d-sm-inline">Volver</span>
            </a>
            <form method="POST" action="mi_pensum.php" target="_blank" class="d-inline m-0 ml-2">
                <input type="hidden" name="pdf" value="1">
                <button type="submit" class="btn btn-sm btn-success shadow-sm no-print">
                    <i class="fas fa-print mr-1"></i> <span class="d-none d-sm-inline">Imprimir PDF</span>
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h5 class="m-0 font-weight-bold text-center text-sm-left"><?= htmlspecialchars($nombre_carrera) ?></h5>
        </div>
        <div class="card-body p-2 p-sm-3">
            <?php foreach ($materias_agrupadas as $texto_trayecto => $materias): ?>
                <h5 class="font-weight-bold text-primary mt-4 mb-3 border-left-primary pl-3"><?= $texto_trayecto ?></h5>
                
                <!-- Vista para escritorio: tabla completa -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-hover table-sm text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>Código</th>
                                <th class="text-left">Nombre de la Asignatura</th>
                                <th>
                                    <span class="abbr-popover" 
                                          data-toggle="popover" 
                                          data-trigger="hover focus" 
                                          data-placement="top" 
                                          title="UC" 
                                          data-content="Unidades de Crédito">
                                        UC <i class="fas fa-info-circle text-primary ml-1" style="font-size: 0.65rem;"></i>
                                    </span>
                                </th>
                                <th>
                                    <span class="abbr-popover" 
                                          data-toggle="popover" 
                                          data-trigger="hover focus" 
                                          data-placement="top" 
                                          title="H.T." 
                                          data-content="Horas Teóricas">
                                        H.T. <i class="fas fa-info-circle text-primary" style="font-size: 0.65rem;"></i>
                                    </span>
                                </th>
                                <th>
                                    <span class="abbr-popover" 
                                          data-toggle="popover" 
                                          data-trigger="hover focus" 
                                          data-placement="top" 
                                          title="H.P." 
                                          data-content="Horas Prácticas">
                                        H.P. <i class="fas fa-info-circle text-primary" style="font-size: 0.65rem;"></i>
                                    </span>
                                </th>
                                <th>
                                    <span class="abbr-popover" 
                                          data-toggle="popover" 
                                          data-trigger="hover focus" 
                                          data-placement="top" 
                                          title="H.L." 
                                          data-content="Horas en Laboratorio">
                                        H.L. <i class="fas fa-info-circle text-primary" style="font-size: 0.65rem;"></i>
                                    </span>
                                </th>
                                <th>
                                    <span class="abbr-popover" 
                                          data-toggle="popover" 
                                          data-trigger="hover focus" 
                                          data-placement="top" 
                                          title="H.S." 
                                          data-content="Horas Semanales">
                                        H.S. <i class="fas fa-info-circle text-primary" style="font-size: 0.65rem;"></i>
                                    </span>
                                </th>
                                <th>Duración</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_uc_trayecto = 0;
                            foreach ($materias as $m): 
                                $total_uc_trayecto += (int)($m['creditos'] ?? 0);
                            ?>
                                <tr>
                                    <td class="align-middle"><?= htmlspecialchars($m['cod_materia'] ?? '') ?></td>
                                    <td class="text-left align-middle"><?= htmlspecialchars($m['nombre_materia'] ?? '') ?></td>
                                    <td class="align-middle"><?= (int)($m['creditos'] ?? 0) ?></td>
                                    <td class="align-middle"><?= (int)($m['horas_teoricas'] ?? 0) ?></td>
                                    <td class="align-middle"><?= (int)($m['horas_practicas'] ?? 0) ?></td>
                                    <td class="align-middle"><?= (int)($m['horas_laboratorio'] ?? 0) ?></td>
                                    <td class="align-middle"><?= (int)($m['horas_semanales'] ?? 0) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($m['duracion_periodo'] ?? '') ?></td>
                                    <td class="align-middle"><span class="badge badge-<?= $m['activa'] ? 'success' : 'secondary' ?>"><?= $m['activa'] ? 'Activa' : 'Inactiva' ?></span></td>
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
                                <td colspan="6" class="py-2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Vista para móviles: tarjetas -->
                <div class="d-block d-md-none">
                    <?php foreach ($materias as $m): ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-primary"><?= htmlspecialchars($m['cod_materia'] ?? '') ?></strong>
                                    <span class="badge badge-<?= $m['activa'] ? 'success' : 'secondary' ?>"><?= $m['activa'] ? 'Activa' : 'Inactiva' ?></span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="card-title mb-3"><?= htmlspecialchars($m['nombre_materia'] ?? '') ?></h6>
                                
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">
                                        <i class="fas fa-star"></i> Unidades de Crédito (UC):
                                    </div>
                                    <div class="col-6">
                                        <strong><?= (int)($m['creditos'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">
                                        <i class="fas fa-chalkboard"></i> Horas Teóricas (H.T.):
                                    </div>
                                    <div class="col-6">
                                        <?= (int)($m['horas_teoricas'] ?? 0) ?>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">
                                        <i class="fas fa-laptop-code"></i> Horas Prácticas (H.P.):
                                    </div>
                                    <div class="col-6">
                                        <?= (int)($m['horas_practicas'] ?? 0) ?>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">
                                        <i class="fas fa-flask"></i> Horas en Laboratorio (H.L.):
                                    </div>
                                    <div class="col-6">
                                        <?= (int)($m['horas_laboratorio'] ?? 0) ?>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">
                                        <i class="fas fa-clock"></i> Horas Semanales (H.S.):
                                    </div>
                                    <div class="col-6">
                                        <?= (int)($m['horas_semanales'] ?? 0) ?>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">
                                        <i class="fas fa-calendar-alt"></i> Duración:
                                    </div>
                                    <div class="col-6">
                                        <?= htmlspecialchars($m['duracion_periodo'] ?? '') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Total UC del Trayecto en vista móvil -->
                    <div class="card mb-4 border-left-primary shadow-sm bg-light">
                        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold text-gray-800">
                                <i class="fas fa-calculator text-primary mr-1"></i> Total Unidades de Crédito (UC):
                            </span>
                            <span class="badge badge-primary badge-pill font-weight-bold px-2 py-1" style="font-size: 1rem;">
                                <?= $total_uc_trayecto ?>
                            </span>
                        </div>
                    </div>
                </div>
                
            <?php endforeach; ?>
            
            <!-- Leyenda informativa de abreviaturas -->
            <div class="alert alert-info mt-4 d-none d-md-block">
                <small>
                    <i class="fas fa-info-circle mr-1"></i> 
                    <strong>Abreviaturas:</strong> 
                    <strong>UC</strong> = Unidades de Crédito | 
                    <strong>H.T.</strong> = Horas Teóricas | 
                    <strong>H.P.</strong> = Horas Prácticas | 
                    <strong>H.L.</strong> = Horas en Laboratorio | 
                    <strong>H.S.</strong> = Horas Semanales
                    <span class="text-muted ml-1">(Pase el cursor o toque cada abreviatura para ver su significado).</span>
                </small>
            </div>
            
            <!-- Resumen del Pensum para móviles -->
            <div class="d-block d-md-none mt-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="font-weight-bold mb-3">
                            <i class="fas fa-chart-line"></i> Resumen del Pensum
                        </h6>
                        <?php 
                        $total_uc = 0;
                        $total_hs = 0;
                        $materias_activas = 0;
                        $materias_totales = 0;
                        
                        foreach ($materias_agrupadas as $materias) {
                            foreach ($materias as $m) {
                                $materias_totales++;
                                $total_uc += (int)($m['creditos'] ?? 0);
                                $total_hs += (int)($m['horas_semanales'] ?? 0);
                                if ($m['activa']) $materias_activas++;
                            }
                        }
                        ?>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="h5 text-primary"><?= $materias_totales ?></div>
                                <small class="text-muted">Materias</small>
                            </div>
                            <div class="col-4">
                                <div class="h5 text-success"><?= $total_uc ?></div>
                                <small class="text-muted">Total UC</small>
                            </div>
                            <div class="col-4">
                                <div class="h5 text-info"><?= $total_hs ?></div>
                                <small class="text-muted">Horas/Semana</small>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: <?= ($materias_activas / $materias_totales) * 100 ?>%"></div>
                        </div>
                        <small class="text-muted d-block text-center mt-2">
                            <?= round(($materias_activas / $materias_totales) * 100) ?>% de materias activas
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para popovers de abreviaturas */
.abbr-popover {
    cursor: pointer;
    border-bottom: 1px dotted #007bff;
    display: inline-block;
    padding-bottom: 1px;
    transition: color 0.15s ease-in-out;
}
.abbr-popover:hover {
    color: #007bff;
}
.popover-header {
    font-weight: bold;
    background-color: #007bff;
    color: #fff;
}

/* Estilos responsivos */
@media (max-width: 767.98px) {
    .h2-sm {
        font-size: 1.4rem;
    }
    
    .card-header {
        padding: 0.75rem;
    }
    
    .border-left-primary {
        border-left: 4px solid #007bff !important;
        padding-left: 12px;
    }
    
    /* Mejoras para las tarjetas de materias */
    .d-block.d-md-none .card {
        transition: transform 0.2s ease;
        border-radius: 8px;
    }
    
    .d-block.d-md-none .card:active {
        transform: scale(0.98);
    }
    
    .d-block.d-md-none .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .d-block.d-md-none .card-title {
        font-size: 0.95rem;
        line-height: 1.4;
    }
    
    .d-block.d-md-none .row {
        margin-bottom: 0.5rem;
    }
    
    .d-block.d-md-none .col-6 {
        font-size: 0.85rem;
    }
}

/* Ajustes para tablets */
@media (min-width: 768px) and (max-width: 991.98px) {
    .table th, .table td {
        padding: 0.5rem;
        font-size: 0.8rem;
    }
    
    .table th {
        font-size: 0.75rem;
    }
}

/* Mejoras generales */
.card {
    border-radius: 0.5rem;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
}

.table th, .table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}

/* Tooltips para móviles */
@media (hover: none) {
    [data-toggle="tooltip"] {
        cursor: pointer;
    }
}
</style>

<!-- Script para popovers de Bootstrap -->
<script>
$(document).ready(function() {
    // Inicializar popovers de Bootstrap para abreviaturas
    $('[data-toggle="popover"]').popover({
        trigger: 'hover focus',
        placement: 'top',
        container: 'body'
    });

    // Cerrar popover al hacer clic fuera
    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>