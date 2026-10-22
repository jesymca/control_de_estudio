<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Mi Horario";
require_once('../funciones/functions.php');

// Verificar autenticación y rol
if (!isLoggedIn() || !isEstudiante()) {
    $_SESSION['msg'] = "Debes iniciar sesión como estudiante para acceder";
    header('location: ../login.php');
    exit();
}

visita();

$estudiante_id = (int)$_SESSION['user']['id'];

// Obtener todas las secciones activas/aprobadas del estudiante
$secciones_disponibles = function_exists('obtenerSeccionesEstudiante') 
    ? obtenerSeccionesEstudiante($db, $estudiante_id) 
    : [];

/**
 * Renderiza el HTML de la tabla de horario para una sección dada
 */
if (!function_exists('renderizarTablaHorarioHtml')) {
    function renderizarTablaHorarioHtml($seccion_estudiante, $horarios) {
        ob_start();
        if (empty($horarios)):
?>
            <div class="p-5 text-center">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <p class="mt-3 text-muted">No hay horarios cargados para esta sección.</p>
            </div>
<?php 
        else:
            $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            $turno_seccion = $seccion_estudiante['turno'] ?? 'Diurno';
            
            if (!function_exists('horaToNumPrint')) {
                function horaToNumPrint($hora) {
                    return (int)substr($hora, 0, 2) + (int)substr($hora, 3, 2) / 60;
                }
            }
            
            $hora_min = 24;
            $hora_max = 0;
            foreach ($horarios as $horario) {
                $hora_inicio_num = horaToNumPrint($horario['hora_inicio']);
                $hora_fin_num = horaToNumPrint($horario['hora_fin']);
                if ($hora_inicio_num < $hora_min) $hora_min = $hora_inicio_num;
                if ($hora_fin_num > $hora_max) $hora_max = $hora_fin_num;
            }
            
            if ($turno_seccion == 'Diurno') {
                $hay_clases_fuera = false;
                foreach ($horarios as $horario) {
                    $hora_inicio_num = horaToNumPrint($horario['hora_inicio']);
                    $hora_fin_num = horaToNumPrint($horario['hora_fin']);
                    if ($hora_inicio_num < 7 || $hora_fin_num > 17.5) {
                        $hay_clases_fuera = true;
                        break;
                    }
                }
                if ($hay_clases_fuera) {
                    $inicio = max(7, floor($hora_min));
                    $fin = min(20, ceil($hora_max));
                } else {
                    $inicio = 7;
                    $fin = 17;
                }
            } else {
                $hay_clases_fuera = false;
                foreach ($horarios as $horario) {
                    $hora_inicio_num = horaToNumPrint($horario['hora_inicio']);
                    if ($hora_inicio_num < 17.5) {
                        $hay_clases_fuera = true;
                        break;
                    }
                }
                if ($hay_clases_fuera) {
                    $inicio = max(7, floor($hora_min));
                    $fin = min(20, ceil($hora_max));
                } else {
                    $inicio = 17;
                    $fin = 20;
                }
            }
            
            $horas_tabla = [];
            for ($h = $inicio; $h <= $fin; $h++) {
                $horas_tabla[] = sprintf("%02d:00", $h);
                if ($h < $fin) {
                    $horas_tabla[] = sprintf("%02d:30", $h);
                }
            }

            $horarios_por_dia = array_fill(0, 6, []);
            foreach ($horarios as $h) {
                $horarios_por_dia[(int)$h['dia']][] = $h;
            }
?>
            <div class="table-responsive">
                <table class="table table-bordered m-0 text-center" id="tablaHorario">
                    <thead>
                        <tr>
                            <th class="hora-col">HORA</th>
                            <?php foreach ($dias_semana as $dia): ?>
                                <th><?= $dia ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $skip_cells = [];
                        foreach ($horas_tabla as $hora): 
                        ?>
                            <tr>
                                <td class="hora-col"><?= $hora ?></td>
                                <?php for ($dia = 0; $dia <= 5; $dia++): ?>
                                    <?php
                                    if (isset($skip_cells[$dia][$hora])) continue;

                                    $clase_encontrada = null;
                                    foreach ($horarios_por_dia[$dia] as $clase) {
                                        if ($hora >= $clase['hora_inicio'] && $hora < $clase['hora_fin']) {
                                            $clase_encontrada = $clase;
                                            break;
                                        }
                                    }

                                    if ($clase_encontrada): 
                                        $h_ini = strtotime($hora);
                                        $h_fin_clase = strtotime($clase_encontrada['hora_fin']);
                                        $rowspan = ($h_fin_clase - $h_ini) / 1800;

                                        $temp_hora = $h_ini;
                                        for ($i = 1; $i < $rowspan; $i++) {
                                            $temp_hora += 1800;
                                            $skip_cells[$dia][date('H:i', $temp_hora)] = true;
                                        }
                                    ?>
                                        <td rowspan="<?= $rowspan ?>" class="materia-container">
                                            <div class="bloque-clase">
                                                <span class="materia-nombre"><?= htmlspecialchars($clase_encontrada['nombre_materia']) ?></span>
                                                <span class="docente-nombre"><?= htmlspecialchars($clase_encontrada['nombre_docente']) ?></span>
                                                <div><span class="aula-tag"><i class="fas fa-door-open me-1"></i> <?= htmlspecialchars($clase_encontrada['aula']) ?></span></div>
                                            </div>
                                         </td>
                                    <?php else: ?>
                                        <td class="bg-light"></td>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
<?php 
        endif;
        return ob_get_clean();
    }
}

// Procesamiento de petición AJAX vía POST para actualización dinámica con JSON
$es_peticion_ajax = (
    ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && (
        !empty($_POST['ajax']) ||
        (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
        (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
    )
);

if ($es_peticion_ajax) {
    header('Content-Type: application/json; charset=utf-8');
    
    $req_id_seccion = isset($_POST['id_seccion']) ? (int)$_POST['id_seccion'] : 0;
    $seccion_encontrada = null;
    
    if ($req_id_seccion > 0 && !empty($secciones_disponibles)) {
        foreach ($secciones_disponibles as $sec) {
            if ((int)$sec['id_seccion'] === $req_id_seccion) {
                $seccion_encontrada = $sec;
                break;
            }
        }
    }
    
    if (!$seccion_encontrada && !empty($secciones_disponibles)) {
        $seccion_encontrada = $secciones_disponibles[0];
        $req_id_seccion = (int)$seccion_encontrada['id_seccion'];
    } elseif (!$seccion_encontrada) {
        $seccion_encontrada = obtenerSeccionEstudiante($db, $estudiante_id, $req_id_seccion);
        if ($seccion_encontrada) {
            $req_id_seccion = (int)$seccion_encontrada['id_seccion'];
        }
    }
    
    if (!$seccion_encontrada) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontró la sección solicitada para el estudiante.'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    $_SESSION['mi_horario_seccion_id'] = $req_id_seccion;
    $horarios_seccion = obtenerHorariosSeccion($db, $req_id_seccion);
    $horarios_seccion = is_array($horarios_seccion) ? $horarios_seccion : [];
    
    $trayecto_label = !empty($seccion_encontrada['nombre_trayecto'])
        ? $seccion_encontrada['nombre_trayecto']
        : 'Trayecto ' . ($seccion_encontrada['numero_trayecto'] ?? '0');
        
    $html_tabla = renderizarTablaHorarioHtml($seccion_encontrada, $horarios_seccion);
    
    echo json_encode([
        'status' => 'success',
        'id_seccion' => $req_id_seccion,
        'seccion' => [
            'id_seccion' => (int)$seccion_encontrada['id_seccion'],
            'codigo_seccion' => $seccion_encontrada['codigo_seccion'] ?? '',
            'nombre_carrera' => $seccion_encontrada['nombre_carrera'] ?? '',
            'turno' => $seccion_encontrada['turno'] ?? 'Diurno',
            'numero_trayecto' => $seccion_encontrada['numero_trayecto'] ?? '0',
            'nombre_trayecto' => $trayecto_label,
            'nombre_periodo' => $seccion_encontrada['nombre_periodo'] ?? 'N/A'
        ],
        'total_clases' => count($horarios_seccion),
        'horarios' => $horarios_seccion,
        'html' => $html_tabla
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Manejo de sección para carga tradicional (SSR)
$id_seccion_seleccionada = 0;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['id_seccion'])) {
    $id_seccion_seleccionada = (int)$_POST['id_seccion'];
    $_SESSION['mi_horario_seccion_id'] = $id_seccion_seleccionada;
} elseif (isset($_SESSION['mi_horario_seccion_id'])) {
    $id_seccion_seleccionada = (int)$_SESSION['mi_horario_seccion_id'];
} elseif (isset($_GET['id_seccion'])) {
    $id_seccion_seleccionada = (int)$_GET['id_seccion'];
}

$seccion_estudiante = null;
if ($id_seccion_seleccionada > 0 && !empty($secciones_disponibles)) {
    foreach ($secciones_disponibles as $sec) {
        if ((int)$sec['id_seccion'] === $id_seccion_seleccionada) {
            $seccion_estudiante = $sec;
            break;
        }
    }
}

// Si no se encontró o no se indicó, seleccionar por defecto la primera disponible
if (!$seccion_estudiante && !empty($secciones_disponibles)) {
    $seccion_estudiante = $secciones_disponibles[0];
    $id_seccion_seleccionada = (int)$seccion_estudiante['id_seccion'];
} elseif (!$seccion_estudiante) {
    // Fallback a función singular
    $seccion_estudiante = obtenerSeccionEstudiante($db, $estudiante_id, $id_seccion_seleccionada);
    if ($seccion_estudiante) {
        $id_seccion_seleccionada = (int)$seccion_estudiante['id_seccion'];
    }
}

$horarios = [];
if ($seccion_estudiante && !empty($seccion_estudiante['id_seccion'])) {
    $horarios = obtenerHorariosSeccion($db, (int)$seccion_estudiante['id_seccion']);
    $horarios = is_array($horarios) ? $horarios : [];
}

include("includes/head.php");
?>

<style>
    :root {
        --color-clase: #e8f5e9;
        --color-texto-clase: #2e7d32;
        --border-clase: #388e3c;
    }

    /* Estilos de la Tabla */
    #tablaHorario {
        table-layout: fixed;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #6c757d;
        width: 100%;
    }

    #tablaHorario thead th {
        background-color: #2c3e50;
        color: white;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        border: 1px solid #495057;
        padding: 6px 4px;
    }

    #tablaHorario tbody td {
        border: 1px solid #6c757d;
    }

    .hora-col {
        background-color: #f8f9fa;
        font-weight: bold;
        color: #495057;
        width: 65px;
        text-align: center;
        padding: 6px 4px;
        font-size: 0.7rem;
        border: 1px solid #6c757d;
    }

    .materia-container {
        padding: 4px !important;
        vertical-align: middle !important;
        border: 1px solid #6c757d;
    }

    .bloque-clase {
        background-color: var(--color-clase);
        color: var(--color-texto-clase);
        border: 1px solid var(--border-clase);
        border-radius: 4px;
        padding: 4px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .materia-nombre {
        font-weight: 800;
        font-size: 0.65rem;
        line-height: 1.1;
        margin-bottom: 2px;
        text-transform: uppercase;
    }

    .docente-nombre {
        font-size: 0.6rem;
        opacity: 0.9;
    }

    .aula-tag {
        font-size: 0.55rem;
        font-weight: bold;
        margin-top: 2px;
        display: inline-block;
        background: rgba(255,255,255,0.5);
        padding: 1px 4px;
        border-radius: 3px;
    }

    /* Badges con colores claros */
    .badge-seccion {
        background-color: #e3f2fd;
        color: #1565c0;
        font-size: 0.65rem;
        padding: 4px 8px;
        margin-right: 5px;
        border-radius: 4px;
        display: inline-block;
    }

    .badge-carrera {
        background-color: #e8f5e9;
        color: #2e7d32;
        font-size: 0.65rem;
        padding: 4px 8px;
        margin-right: 5px;
        border-radius: 4px;
        display: inline-block;
    }

    .badge-turno {
        background-color: #fff3e0;
        color: #e65100;
        font-size: 0.65rem;
        padding: 4px 8px;
        margin-right: 5px;
        border-radius: 4px;
        display: inline-block;
    }

    .badge-trayecto {
        background-color: #f3e5f5;
        color: #6a1b9a;
        font-size: 0.65rem;
        padding: 4px 8px;
        margin-right: 5px;
        border-radius: 4px;
        display: inline-block;
    }

    .badge-periodo {
        background-color: #e0f7fa;
        color: #006064;
        font-size: 0.65rem;
        padding: 4px 8px;
        margin-right: 5px;
        border-radius: 4px;
        display: inline-block;
    }

    /* CONFIGURACIÓN DE IMPRESIÓN - UNA SOLA HOJA */
    @media print {
        @page {
            size: landscape;
            margin: 0.3cm;
        }
        body * {
            visibility: hidden;
        }
        #seccionImprimir, #seccionImprimir * {
            visibility: visible;
        }
        #seccionImprimir {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .no-print {
            display: none !important;
        }
        .bloque-clase {
            border: 1px solid #2e7d32 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #6c757d !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .card-body {
            padding: 0 !important;
        }
        #tablaHorario td, #tablaHorario th {
            border: 1px solid #6c757d !important;
            padding: 4px !important;
            font-size: 0.6rem !important;
        }
        #tablaHorario thead th {
            padding: 4px !important;
            font-size: 0.6rem !important;
            border: 1px solid #495057 !important;
        }
        .hora-col {
            padding: 4px !important;
            font-size: 0.6rem !important;
            width: 55px !important;
            border: 1px solid #6c757d !important;
        }
        .materia-container {
            border: 1px solid #6c757d !important;
        }
        .materia-nombre {
            font-size: 0.55rem !important;
        }
        .docente-nombre {
            font-size: 0.5rem !important;
        }
        .aula-tag {
            font-size: 0.45rem !important;
        }
        h2 {
            font-size: 1rem !important;
            margin: 0 !important;
        }
        .mt-2 {
            margin-top: 2px !important;
        }
        .mb-4 {
            margin-bottom: 5px !important;
        }
        .py-4 {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
        }
        .badge-seccion, .badge-carrera, .badge-turno, .badge-trayecto, .badge-periodo {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="container-fluid py-4" id="seccionImprimir">
    <?php if ($seccion_estudiante && !empty($seccion_estudiante)): ?>
        
        <?php if (count($secciones_disponibles) > 1): ?>
        <!-- PANEL DE SELECCIÓN DE SECCIÓN (VÍA POST - NO IMPRIMIBLE) -->
        <div class="card border-0 shadow-sm mb-3 no-print" style="background-color: #f1f8ff; border-left: 4px solid #007bff !important;">
            <div class="card-body p-3">
                <form id="formSeleccionarSeccion" method="POST" action="mi_horario.php" class="m-0">
                    <div class="row align-items-center">
                        <div class="col-lg-5 mb-2 mb-lg-0">
                            <div class="d-flex align-items-center">
                                <span class="mr-2 text-primary">
                                    <i class="fas fa-layer-group fa-2x"></i>
                                </span>
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-dark">
                                        Mis Secciones Asignadas (<?= count($secciones_disponibles) ?>)
                                    </h6>
                                    <small class="text-muted">Selecciona una sección para visualizar su horario correspondiente:</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-7">
                            <!-- Selector Dropdown interactivo vía POST -->
                            <div class="d-flex flex-wrap align-items-center justify-content-lg-end">
                                <label for="selectorSeccion" class="small font-weight-bold text-muted mr-2 mb-1 d-none d-sm-inline">
                                    <i class="fas fa-filter mr-1"></i> Horario de:
                                </label>
                                <select id="selectorSeccion" name="id_seccion" class="custom-select custom-select-sm font-weight-bold border-primary shadow-sm" style="max-width: 380px;" onchange="cambiarSeccionHorarioAjax(this.value, event);">
                                    <?php foreach ($secciones_disponibles as $sec): 
                                        $es_activa = ((int)$sec['id_seccion'] === (int)$id_seccion_seleccionada);
                                        $trayecto_label = !empty($sec['nombre_trayecto']) ? $sec['nombre_trayecto'] : 'Trayecto ' . ($sec['numero_trayecto'] ?? '0');
                                    ?>
                                        <option value="<?= (int)$sec['id_seccion'] ?>" <?= $es_activa ? 'selected' : '' ?>>
                                            <?= $es_activa ? '✔ ' : '' ?>Sección <?= htmlspecialchars($sec['codigo_seccion']) ?> — <?= htmlspecialchars($trayecto_label) ?> (<?= htmlspecialchars($sec['turno'] ?? 'Diurno') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Botones de selección rápida en pantallas grandes (submit vía POST) -->
                            <div class="mt-2 d-none d-md-flex flex-wrap justify-content-lg-end">
                                <?php foreach ($secciones_disponibles as $sec): 
                                    $es_activa = ((int)$sec['id_seccion'] === (int)$id_seccion_seleccionada);
                                    $trayecto_label = !empty($sec['nombre_trayecto']) ? $sec['nombre_trayecto'] : 'Trayecto ' . ($sec['numero_trayecto'] ?? '0');
                                ?>
                                    <button type="submit" name="id_seccion" value="<?= (int)$sec['id_seccion'] ?>" 
                                            data-id-seccion="<?= (int)$sec['id_seccion'] ?>"
                                            onclick="cambiarSeccionHorarioAjax(<?= (int)$sec['id_seccion'] ?>, event);"
                                            class="btn btn-sm btn-seccion-horario <?= $es_activa ? 'btn-primary font-weight-bold shadow-sm active' : 'btn-outline-secondary bg-white' ?> mr-1 mb-1" style="font-size: 0.78rem;">
                                        <i class="fas <?= $es_activa ? 'fa-check-circle' : 'fa-chalkboard' ?> mr-1 icon-btn-seccion"></i>
                                        Sec. <strong><?= htmlspecialchars($sec['codigo_seccion']) ?></strong> 
                                        <span class="badge badge-pill-trayecto <?= $es_activa ? 'badge-light text-primary' : 'badge-secondary' ?> ml-1"><?= htmlspecialchars($trayecto_label) ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="row mb-3 align-items-center">
            <div class="col-md-8">
                <h2 class="mb-0 fw-bold d-flex align-items-center" style="font-size: 1.2rem;">
                    <span><i class="fas fa-calendar-check text-primary me-2"></i> HORARIO ACADÉMICO</span>
                    <span id="spinnerCargandoHorario" class="spinner-border spinner-border-sm text-primary ml-2 d-none" role="status" aria-hidden="true" title="Cargando horario..."></span>
                </h2>
                <div class="mt-1" id="contenedorBadgesHorario">
                    <span class="badge-seccion" id="badgeCodigoSeccion"><i class="fas fa-code-branch me-1"></i> SECCIÓN: <?= htmlspecialchars($seccion_estudiante['codigo_seccion'] ?? 'N/A') ?></span>
                    <span class="badge-carrera" id="badgeNombreCarrera"><i class="fas fa-graduation-cap me-1"></i> CARRERA: <?= htmlspecialchars($seccion_estudiante['nombre_carrera'] ?? 'N/A') ?></span>
                    <span class="badge-turno" id="badgeTurno"><i class="fas fa-clock me-1"></i> TURNO: <?= htmlspecialchars($seccion_estudiante['turno'] ?? 'N/A') ?></span>
                    <span class="badge-trayecto" id="badgeTrayecto"><i class="fas fa-layer-group me-1"></i> TRAYECTO: <?= htmlspecialchars($seccion_estudiante['numero_trayecto'] ?? 'N/A') ?></span>
                    <span class="badge-periodo" id="badgePeriodo"><i class="fas fa-calendar-alt me-1"></i> PERÍODO: <?= htmlspecialchars($seccion_estudiante['nombre_periodo'] ?? 'N/A') ?></span>
                </div>
            </div>
            <div class="col-md-4 text-md-end no-print">
                <button onclick="window.print();" class="btn btn-primary btn-sm shadow-sm">
                    <i class="fas fa-print me-2"></i> Imprimir / Guardar PDF
                </button>
            </div>
        </div>

        <div class="card border-0 shadow">
            <div class="card-body p-0" id="cuerpoTablaHorario" style="transition: opacity 0.2s ease;">
                <?= renderizarTablaHorarioHtml($seccion_estudiante, $horarios) ?>
            </div>
        </div>
        
        <div class="mt-2 no-print">
            <div class="alert alert-light border shadow-sm py-2" style="font-size: 0.8rem;">
                <i class="fas fa-info-circle text-primary me-2"></i>
                <strong>Consejo:</strong> Para descargar este horario, haz clic en el botón azul y selecciona <strong>"Guardar como PDF"</strong>.
            </div>
        </div>

    <?php else: ?>
        <div class="text-center py-5">
            <div class="card shadow">
                <div class="card-body py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                    <h3>No tienes una sección asignada</h3>
                    <p class="text-muted">Actualmente no estás asignado a ninguna sección.</p>
                    <p class="text-muted">Por favor, contacta con la administración.</p>
                    <a href="index.php" class="btn btn-primary mt-3">
                        <i class="fas fa-home"></i> Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
/**
 * Cambia dinámicamente el horario mediante AJAX (POST) y JSON sin recargar la página
 * @param {number|string} idSeccion ID de la sección seleccionada
 * @param {Event} [event] Evento del navegador (click / change)
 */
function cambiarSeccionHorarioAjax(idSeccion, event) {
    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();
    }

    idSeccion = parseInt(idSeccion, 10);
    if (!idSeccion || isNaN(idSeccion)) return;

    var selector = document.getElementById('selectorSeccion');
    var cuerpo = document.getElementById('cuerpoTablaHorario');
    var spinner = document.getElementById('spinnerCargandoHorario');
    var formFallback = document.getElementById('formSeleccionarSeccion');

    // Efecto visual de carga
    if (spinner) spinner.classList.remove('d-none');
    if (cuerpo) cuerpo.style.opacity = '0.35';
    if (selector) selector.disabled = true;

    var formData = new FormData();
    formData.append('ajax', '1');
    formData.append('id_seccion', idSeccion);

    fetch('mi_horario.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(function(res) {
        if (!res.ok) {
            throw new Error('Respuesta HTTP no válida: ' + res.status);
        }
        return res.json();
    })
    .then(function(data) {
        if (data && data.status === 'success') {
            // Actualizar tabla HTML
            if (cuerpo && data.html) {
                cuerpo.innerHTML = data.html;
            }

            // Actualizar etiquetas / badges informativos
            if (data.seccion) {
                var badgeSec = document.getElementById('badgeCodigoSeccion');
                if (badgeSec) badgeSec.innerHTML = '<i class="fas fa-code-branch me-1"></i> SECCIÓN: ' + escapeHtml(data.seccion.codigo_seccion || 'N/A');

                var badgeCar = document.getElementById('badgeNombreCarrera');
                if (badgeCar) badgeCar.innerHTML = '<i class="fas fa-graduation-cap me-1"></i> CARRERA: ' + escapeHtml(data.seccion.nombre_carrera || 'N/A');

                var badgeTur = document.getElementById('badgeTurno');
                if (badgeTur) badgeTur.innerHTML = '<i class="fas fa-clock me-1"></i> TURNO: ' + escapeHtml(data.seccion.turno || 'N/A');

                var badgeTra = document.getElementById('badgeTrayecto');
                if (badgeTra) badgeTra.innerHTML = '<i class="fas fa-layer-group me-1"></i> TRAYECTO: ' + escapeHtml(data.seccion.numero_trayecto !== undefined ? data.seccion.numero_trayecto : 'N/A');

                var badgePer = document.getElementById('badgePeriodo');
                if (badgePer) badgePer.innerHTML = '<i class="fas fa-calendar-alt me-1"></i> PERÍODO: ' + escapeHtml(data.seccion.nombre_periodo || 'N/A');
            }

            // Sincronizar Dropdown
            if (selector) {
                selector.value = data.id_seccion;
            }

            // Sincronizar Botones / Pills
            var botones = document.querySelectorAll('.btn-seccion-horario');
            botones.forEach(function(btn) {
                var btnId = parseInt(btn.getAttribute('data-id-seccion'), 10);
                var icon = btn.querySelector('.icon-btn-seccion');
                var badge = btn.querySelector('.badge-pill-trayecto');

                if (btnId === data.id_seccion) {
                    btn.className = 'btn btn-sm btn-seccion-horario btn-primary font-weight-bold shadow-sm active mr-1 mb-1';
                    if (icon) icon.className = 'fas fa-check-circle mr-1 icon-btn-seccion';
                    if (badge) badge.className = 'badge badge-pill-trayecto badge-light text-primary ml-1';
                } else {
                    btn.className = 'btn btn-sm btn-seccion-horario btn-outline-secondary bg-white mr-1 mb-1';
                    if (icon) icon.className = 'fas fa-chalkboard mr-1 icon-btn-seccion';
                    if (badge) badge.className = 'badge badge-pill-trayecto badge-secondary ml-1';
                }
            });

            // Actualizar URL sin recargar para soportar favoritos/compartir
            if (window.history && window.history.replaceState) {
                var nuevaUrl = new URL(window.location.href);
                nuevaUrl.searchParams.set('id_seccion', data.id_seccion);
                window.history.replaceState({}, '', nuevaUrl.toString());
            }
        } else {
            console.warn('Error al procesar JSON:', data ? data.message : 'Respuesta vacía');
            // Fallback en caso de error
            if (formFallback) {
                if (selector) selector.value = idSeccion;
                formFallback.submit();
            }
        }
    })
    .catch(function(error) {
        console.error('Error en petición AJAX:', error);
        // Fallback robusto a submit POST tradicional
        if (formFallback) {
            if (selector) selector.value = idSeccion;
            formFallback.submit();
        }
    })
    .finally(function() {
        if (spinner) spinner.classList.add('d-none');
        if (cuerpo) cuerpo.style.opacity = '1';
        if (selector) selector.disabled = false;
    });
}

/**
 * Escapa cadenas para prevenir inyecciones XSS en el DOM
 */
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>

<?php include("includes/footer.php"); ?>