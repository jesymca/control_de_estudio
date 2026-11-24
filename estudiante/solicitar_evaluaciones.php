<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$titulopag = "Solicitud de Evaluaciones Especiales";
require_once('../funciones/functions.php');

// Verificar que sea estudiante
if (empty($_SESSION['user']['estudiante']) || $_SESSION['user']['estudiante'] != 1) {
    header('Location: ../login.php');
    exit();
}

visita();

$user_id = (int)$_SESSION['user']['id'];
$carrera = obtenerCarreraEstudiante($user_id);
$periodo_activo = obtenerPeriodoActivo();

$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario POST
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['accion_solicitar'])) {
    $tipo_solicitud = trim($_POST['tipo_solicitud'] ?? '');
    $id_materias = $_POST['id_materia'] ?? [];
    $motivo = trim($_POST['motivo'] ?? '');
    
    if (!is_array($id_materias)) {
        $id_materias = !empty($id_materias) ? [$id_materias] : [];
    }
    
    $res = registrarSolicitudEvaluacionEspecial($user_id, $tipo_solicitud, $id_materias, $motivo);
    if ($res['success']) {
        $mensaje = $res['mensaje'];
        $tipo_mensaje = 'success';
    } else {
        $mensaje = $res['mensaje'];
        $tipo_mensaje = 'danger';
    }
}

// Cargar diagnósticos de aptitud para cada modalidad
$aptas_per = obtenerMateriasAptasPER($user_id);
$apto_ext = esAptoExtraordinario($user_id);
$aptas_int = obtenerMateriasAptasIntensivo($user_id);
$info_suf = obtenerMateriasAptasSuficiencia($user_id);

// Cargar historial de solicitudes del estudiante
$mis_solicitudes = obtenerSolicitudesEvaluacionesEspeciales(0, 0, '', '', $user_id);

include("includes/head.php");
?>

<div class="container-fluid px-2 px-sm-3 px-md-4 mt-3">
    <!-- Encabezado y Breadcrumbs -->
    <div class="row">
        <div class="col-12">
            <h1 class="h3 font-weight-bold text-dark mb-1">
                <i class="fas fa-clipboard-list text-primary mr-2"></i><?= htmlspecialchars($titulopag) ?>
            </h1>
            <ol class="breadcrumb mb-3 bg-light">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="mis_constancias.php">Solicitudes</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($titulopag) ?></li>
            </ol>
        </div>
    </div>

    <!-- Alertas Flash -->
    <?php if (!empty($mensaje)): ?>
    <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i>
        <?= htmlspecialchars($mensaje) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <!-- Resumen del Estudiante -->
    <div class="card mb-4 border-0 shadow-sm" style="border-left: 5px solid #007bff !important;">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <span class="text-muted small d-block">Estudiante:</span>
                    <strong class="text-dark"><?= htmlspecialchars($_SESSION['user']['nombre'] ?? 'N/D') ?></strong>
                    <span class="badge badge-secondary ml-1">C.I: <?= htmlspecialchars($_SESSION['user']['idusuario'] ?? '') ?></span>
                </div>
                <div class="col-md-4 mt-2 mt-md-0">
                    <span class="text-muted small d-block">Programa / Carrera:</span>
                    <strong class="text-dark"><?= htmlspecialchars($carrera['nombre_carrera'] ?? 'Sin Asignar') ?></strong>
                </div>
                <div class="col-md-4 mt-2 mt-md-0">
                    <span class="text-muted small d-block">Período Académico Activo:</span>
                    <span class="badge badge-success px-2 py-1">
                        <i class="fas fa-calendar-alt mr-1"></i><?= htmlspecialchars($periodo_activo['nombre_periodo'] ?? 'No Configurado') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pestañas de Modalidades -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs nav-fill card-header-tabs m-0" id="tabsModalidades" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold py-3" id="tab-per" data-toggle="tab" href="#content-per" role="tab">
                        <i class="fas fa-redo-alt mr-1 text-warning"></i> PER (Plan Especial)
                        <?php if (!empty($aptas_per)): ?>
                            <span class="badge badge-warning text-dark ml-1"><?= count($aptas_per) ?> apta(s)</span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold py-3" id="tab-extra" data-toggle="tab" href="#content-extra" role="tab">
                        <i class="fas fa-graduation-cap mr-1 text-danger"></i> Extraordinario (E)
                        <?php if (!empty($apto_ext['apto'])): ?>
                            <span class="badge badge-danger ml-1">Apto</span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold py-3" id="tab-intensivo" data-toggle="tab" href="#content-intensivo" role="tab">
                        <i class="fas fa-bolt mr-1 text-info"></i> Intensivo (T)
                        <?php if (!empty($aptas_int)): ?>
                            <span class="badge badge-info ml-1"><?= count($aptas_int) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold py-3" id="tab-suficiencia" data-toggle="tab" href="#content-suficiencia" role="tab">
                        <i class="fas fa-award mr-1 text-primary"></i> Suficiencia (S)
                        <span class="badge badge-primary ml-1"><?= $info_suf['suficiencias_este_ano'] ?>/2 año</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="tab-content" id="tabsContent">
                
                <!-- 1. TAB: PER -->
                <div class="tab-pane fade show active" id="content-per" role="tabpanel">
                    <div class="alert alert-warning border-0">
                        <h6 class="font-weight-bold mb-1"><i class="fas fa-info-circle mr-1"></i> Condiciones del Plan Especial de Recuperación (PER)</h6>
                        <ul class="mb-0 pl-3 small">
                            <li>Aplica únicamente a asignaturas reprobadas con nota definitiva estrictamente entre <strong>06 y 11 puntos</strong>.</li>
                            <li><strong>Exclusiones:</strong> No aplica para asignaturas del Trayecto Inicial (T0) ni Proyectos Socio-Integradores / Tecnológicos.</li>
                            <li>Permite cursar un máximo de <strong>hasta 3 asignaturas</strong> por estudiante. Calificación tope aprobatoria: <strong>12 puntos</strong>.</li>
                        </ul>
                    </div>

                    <?php if (!empty($aptas_per)): ?>
                        <form method="POST" action="solicitar_evaluaciones.php" onsubmit="return confirm('¿Confirmas que deseas enviar la solicitud de PER para las asignaturas seleccionadas?');">
                            <input type="hidden" name="accion_solicitar" value="1">
                            <input type="hidden" name="tipo_solicitud" value="per">
                            
                            <h6 class="font-weight-bold text-dark mt-3 mb-2">Asignaturas elegibles para PER:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 50px;" class="text-center">Seleccionar</th>
                                            <th>Código</th>
                                            <th>Asignatura</th>
                                            <th class="text-center">Trayecto</th>
                                            <th class="text-center">UC</th>
                                            <th class="text-center">Nota Reprobada</th>
                                            <th class="text-center">Período</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($aptas_per as $ap): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="id_materia[]" value="<?= $ap['id_materia'] ?>" checked style="transform: scale(1.3);">
                                            </td>
                                            <td><strong><?= htmlspecialchars($ap['cod_materia']) ?></strong></td>
                                            <td><?= htmlspecialchars($ap['nombre_materia']) ?></td>
                                            <td class="text-center"><span class="badge badge-light">Trayecto <?= $ap['trayecto'] ?></span></td>
                                            <td class="text-center"><?= $ap['creditos'] ?></td>
                                            <td class="text-center"><span class="badge badge-danger font-weight-bold"><?= $ap['nota_reprobada'] ?> pts</span></td>
                                            <td class="text-center"><?= htmlspecialchars($ap['periodo_reprobado']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group mt-3">
                                <label for="motivo_per" class="font-weight-bold small text-muted">Motivo / Justificación de la solicitud:</label>
                                <textarea name="motivo" id="motivo_per" class="form-control" rows="2" placeholder="Describe brevemente el motivo de tu solicitud..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-warning font-weight-bold px-4 shadow-sm">
                                <i class="fas fa-paper-plane mr-1"></i> Enviar Solicitud de PER
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted bg-light rounded">
                            <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                            <h6 class="font-weight-bold text-dark">No posees asignaturas aptas para PER</h6>
                            <p class="small mb-0">No se encontraron asignaturas reprobadas con calificación entre 6 y 11 puntos que cumplan los requisitos de PER.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 2. TAB: EXTRAORDINARIO -->
                <div class="tab-pane fade" id="content-extra" role="tabpanel">
                    <div class="alert alert-danger border-0">
                        <h6 class="font-weight-bold mb-1"><i class="fas fa-info-circle mr-1"></i> Condiciones de Examen Extraordinario (E)</h6>
                        <ul class="mb-0 pl-3 small">
                            <li>Aplica únicamente cuando al estudiante le falta <strong>exactamente una (1) asignatura</strong> para culminar el plan de estudios (TSU o Ingeniería/Licenciatura).</li>
                            <li>El estudiante ya debió haber <strong>cursado previamente</strong> dicha asignatura.</li>
                        </ul>
                    </div>

                    <?php if (!empty($apto_ext['apto']) && !empty($apto_ext['materia_faltante'])): 
                        $mf = $apto_ext['materia_faltante'];
                    ?>
                        <form method="POST" action="solicitar_evaluaciones.php" onsubmit="return confirm('¿Confirmas que deseas enviar la solicitud de Examen Extraordinario?');">
                            <input type="hidden" name="accion_solicitar" value="1">
                            <input type="hidden" name="tipo_solicitud" value="extraordinario">
                            <input type="hidden" name="id_materia[]" value="<?= $mf['id_materia'] ?>">

                            <div class="card border-danger mb-3">
                                <div class="card-header bg-danger text-white font-weight-bold">
                                    <i class="fas fa-star mr-1"></i> Asignatura Culminante para Extraordinario
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title text-dark font-weight-bold"><?= htmlspecialchars($mf['nombre_materia']) ?></h5>
                                    <p class="card-text mb-1"><strong>Código:</strong> <?= htmlspecialchars($mf['cod_materia']) ?></p>
                                    <p class="card-text mb-1"><strong>Trayecto:</strong> <?= htmlspecialchars($mf['trayecto']) ?> | <strong>Unidades de Crédito:</strong> <?= htmlspecialchars($mf['creditos']) ?></p>
                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Cumples los requisitos de 1 materia faltante</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="motivo_extra" class="font-weight-bold small text-muted">Motivo / Justificación:</label>
                                <textarea name="motivo" id="motivo_extra" class="form-control" rows="2" placeholder="Motivo de solicitud de examen extraordinario..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-danger font-weight-bold px-4 shadow-sm">
                                <i class="fas fa-paper-plane mr-1"></i> Enviar Solicitud de Extraordinario
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted bg-light rounded">
                            <i class="fas fa-info-circle fa-3x text-secondary mb-2"></i>
                            <h6 class="font-weight-bold text-dark">No cumples los requisitos de Examen Extraordinario</h6>
                            <p class="small mb-0"><?= htmlspecialchars($apto_ext['mensaje'] ?? 'Para solicitar Extraordinario debes tener pendiente exactamente 1 asignatura para culminar.') ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 3. TAB: INTENSIVO -->
                <div class="tab-pane fade" id="content-intensivo" role="tabpanel">
                    <div class="alert alert-info border-0">
                        <h6 class="font-weight-bold mb-1"><i class="fas fa-info-circle mr-1"></i> Condiciones del Período Intensivo (T)</h6>
                        <ul class="mb-0 pl-3 small">
                            <li>Aplica para asignaturas que ya hayas <strong>cursado con anterioridad</strong> y tengas pendientes por aprobar.</li>
                            <li>La calificación definitiva obtenida en el intensivo reemplazará el intento previo en el récord activo, conservando el antecedente para el cálculo del IRA.</li>
                        </ul>
                    </div>

                    <?php if (!empty($aptas_int)): ?>
                        <form method="POST" action="solicitar_evaluaciones.php" onsubmit="return confirm('¿Confirmas que deseas enviar la solicitud de Intensivo?');">
                            <input type="hidden" name="accion_solicitar" value="1">
                            <input type="hidden" name="tipo_solicitud" value="intensivo">

                            <h6 class="font-weight-bold text-dark mt-3 mb-2">Selecciona la(s) asignatura(s) para cursar en Intensivo:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 50px;" class="text-center">Seleccionar</th>
                                            <th>Código</th>
                                            <th>Asignatura</th>
                                            <th class="text-center">Trayecto</th>
                                            <th class="text-center">UC</th>
                                            <th class="text-center">Nota Anterior</th>
                                            <th class="text-center">Período Anterior</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($aptas_int as $ai): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="id_materia[]" value="<?= $ai['id_materia'] ?>" style="transform: scale(1.3);">
                                            </td>
                                            <td><strong><?= htmlspecialchars($ai['cod_materia']) ?></strong></td>
                                            <td><?= htmlspecialchars($ai['nombre_materia']) ?></td>
                                            <td class="text-center"><span class="badge badge-light">Trayecto <?= $ai['trayecto'] ?></span></td>
                                            <td class="text-center"><?= $ai['creditos'] ?></td>
                                            <td class="text-center"><span class="badge badge-secondary"><?= $ai['nota_anterior'] ?> pts</span></td>
                                            <td class="text-center"><?= htmlspecialchars($ai['periodo_anterior']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group mt-3">
                                <label for="motivo_int" class="font-weight-bold small text-muted">Motivo / Justificación:</label>
                                <textarea name="motivo" id="motivo_int" class="form-control" rows="2" placeholder="Motivo de la solicitud para intensivo..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-info font-weight-bold px-4 shadow-sm text-white">
                                <i class="fas fa-paper-plane mr-1"></i> Enviar Solicitud de Intensivo
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted bg-light rounded">
                            <i class="fas fa-info-circle fa-3x text-secondary mb-2"></i>
                            <h6 class="font-weight-bold text-dark">No posees asignaturas cursadas pendientes para Intensivo</h6>
                            <p class="small mb-0">Solo puedes solicitar intensivo para asignaturas que ya hayas visto anteriormente.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 4. TAB: SUFICIENCIA -->
                <div class="tab-pane fade" id="content-suficiencia" role="tabpanel">
                    <div class="alert alert-primary border-0">
                        <h6 class="font-weight-bold mb-1"><i class="fas fa-info-circle mr-1"></i> Condiciones del Examen de Suficiencia (S)</h6>
                        <ul class="mb-0 pl-3 small">
                            <li>Permite acreditar conocimientos demostrables en asignaturas de tu pensum.</li>
                            <li>Límite reglamentario: Máximo <strong>2 asignaturas por año lectivo</strong> al inicio de cada trayecto.</li>
                            <li><strong>Inscripción Automática:</strong> Si el examen de suficiencia es reprobado (&lt; 12 pts), el sistema te inscribirá automáticamente en dicha materia en el período regular correspondiente.</li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <span class="mr-2 font-weight-bold text-dark">Límite anual de suficiencias:</span>
                        <span class="badge badge-<?= $info_suf['suficiencias_este_ano'] < 2 ? 'success' : 'danger' ?> px-3 py-2">
                            <?= $info_suf['suficiencias_este_ano'] ?> de 2 utilizadas este año
                        </span>
                    </div>

                    <?php if ($info_suf['puede_solicitar'] && !empty($info_suf['aptas'])): ?>
                        <form method="POST" action="solicitar_evaluaciones.php" id="formSuficiencia" onsubmit="return validarEnvioSuficiencia();">
                            <input type="hidden" name="accion_solicitar" value="1">
                            <input type="hidden" name="tipo_solicitud" value="suficiencia">

                            <div class="form-group mb-3">
                                <label for="materia_suficiencia" class="font-weight-bold text-dark">
                                    <i class="fas fa-book mr-1 text-primary"></i> Selecciona la Asignatura a Rendir por Suficiencia:
                                </label>
                                <select name="id_materia[]" id="materia_suficiencia" class="form-control" required>
                                    <?= renderizarOpcionesMateriasPorTrayecto($info_suf['aptas'], null, '-- Selecciona una asignatura de tu pensum --') ?>
                                </select>
                                <small class="form-text text-muted">Las asignaturas se muestran agrupadas según el trayecto del pensum de estudios.</small>
                            </div>

                            <div class="form-group">
                                <label for="motivo_suf" class="font-weight-bold small text-muted">
                                    <i class="fas fa-pen-alt mr-1"></i> Exposición de Motivos / Justificación de Competencias:
                                </label>
                                <textarea name="motivo" id="motivo_suf" class="form-control" rows="3" placeholder="Indica cursos previos, certificaciones, experiencia laboral o justificación de conocimientos adquiridos..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary font-weight-bold px-4 py-2 shadow-sm">
                                <i class="fas fa-paper-plane mr-1"></i> Enviar Solicitud de Suficiencia
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted bg-light rounded">
                            <i class="fas fa-ban fa-3x text-warning mb-2"></i>
                            <h6 class="font-weight-bold text-dark">Límite anual alcanzado o sin asignaturas disponibles</h6>
                            <p class="small mb-0">Ya has alcanzado el límite reglamentario de 2 evaluaciones de suficiencia en este año lectivo. Para casos especiales con certificaciones, consulta con la Jefatura del Departamento.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Historial de Solicitudes Realizadas -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h6 class="font-weight-bold m-0"><i class="fas fa-history mr-2"></i>Mis Solicitudes de Evaluaciones Especiales</h6>
            <span class="badge badge-light"><?= count($mis_solicitudes) ?> registrada(s)</span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($mis_solicitudes)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped m-0 align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th>Folio</th>
                                <th>Modalidad</th>
                                <th>Asignatura(s) Solicitada(s)</th>
                                <th>Período</th>
                                <th>Fecha</th>
                                <th>Estatus</th>
                                <th>Sección Asignada</th>
                                <th>Docente Asignado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mis_solicitudes as $ms): 
                                $mod_badge = 'badge-secondary';
                                $mod_nom = strtoupper($ms['tipo_solicitud']);
                                if ($ms['tipo_solicitud'] === 'per') $mod_badge = 'badge-warning text-dark';
                                elseif ($ms['tipo_solicitud'] === 'intensivo') $mod_badge = 'badge-info';
                                elseif ($ms['tipo_solicitud'] === 'extraordinario') $mod_badge = 'badge-danger';
                                elseif ($ms['tipo_solicitud'] === 'suficiencia') $mod_badge = 'badge-primary';

                                $st_badge = 'badge-warning';
                                $st_text = 'En Revisión';
                                if ($ms['status'] === 'aprobada') {
                                    $st_badge = 'badge-success';
                                    $st_text = 'Aprobada';
                                } elseif ($ms['status'] === 'rechazada') {
                                    $st_badge = 'badge-danger';
                                    $st_text = 'Rechazada';
                                }
                            ?>
                            <tr>
                                <td><strong>#<?= $ms['id'] ?></strong></td>
                                <td><span class="badge <?= $mod_badge ?> px-2 py-1"><?= $mod_nom ?></span></td>
                                <td>
                                    <?php 
                                    if (!empty($ms['materias_parsed'])) {
                                        foreach ($ms['materias_parsed'] as $mp) {
                                            echo '<div><strong>[' . htmlspecialchars($mp['cod_materia'] ?? '') . ']</strong> ' . htmlspecialchars($mp['nombre_materia'] ?? '') . '</div>';
                                        }
                                    } else {
                                        echo '<span class="text-muted">Sin especificar</span>';
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($ms['nombre_periodo'] ?? '-') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($ms['fecha_solicitud'])) ?></td>
                                <td>
                                    <span class="badge <?= $st_badge ?> px-2 py-1"><?= $st_text ?></span>
                                    <?php if (!empty($ms['motivo_rechazo'])): ?>
                                        <div class="small text-danger mt-1"><strong>Motivo:</strong> <?= htmlspecialchars($ms['motivo_rechazo']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($ms['seccion_asignada_codigo'])): ?>
                                        <a href="mis_secciones.php" class="badge badge-success px-2 py-1" title="Ver mi sección">
                                            <i class="fas fa-door-open mr-1"></i><?= htmlspecialchars($ms['seccion_asignada_codigo']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Por asignar</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($ms['nombre_docente_asignado'])): ?>
                                        <span class="text-dark small font-weight-bold">
                                            <i class="fas fa-chalkboard-teacher mr-1 text-primary"></i><?= htmlspecialchars($ms['nombre_docente_asignado']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">Por asignar</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-inbox fa-3x mb-2 text-secondary"></i>
                    <p class="mb-0">No has registrado ninguna solicitud de evaluación especial todavía.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function validarEnvioSuficiencia() {
    const select = document.getElementById('materia_suficiencia');
    if (!select || !select.value) {
        alert('Por favor selecciona una asignatura para el Examen de Suficiencia.');
        return false;
    }
    const motivo = document.getElementById('motivo_suf');
    if (!motivo || !motivo.value.trim()) {
        alert('Por favor indica la exposición de motivos o justificación de tus conocimientos.');
        if (motivo) motivo.focus();
        return false;
    }
    return confirm('¿Confirmas que deseas enviar la solicitud de Examen de Suficiencia? Recuerda que si no apruebas (nota < 12) se te inscribirá de forma regular automáticamente.');
}
</script>

<?php include("includes/footer.php"); ?>
