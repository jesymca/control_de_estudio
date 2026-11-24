<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$titulopag = "Gestión de Solicitudes Especiales (PER, Intensivo, Suficiencia, Extraordinario)";
require_once('../funciones/functions.php');

cargarPermisosUsuario();
if (!isLoggedIn()) {
    header('location: ../login.php');
    exit();
}

$admin_id = obtenerIdUsuario();

// AJAX: Generador de código de sección especial compatible con el sistema viejo
if (isset($_POST['ajax_generar_codigo_especial'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    $id_c = intval($_POST['id_carrera'] ?? 0);
    $tray = intval($_POST['trayecto'] ?? 1);
    $mod = trim($_POST['modalidad'] ?? 'per');
    $turno = trim($_POST['turno'] ?? 'Diurno');
    $cod = function_exists('generarCodigoSeccionEspecial') ? generarCodigoSeccionEspecial($id_c, $tray, $mod, $turno) : (strtoupper($mod) . '-' . $tray . '-71');
    echo json_encode(['success' => true, 'codigo' => $cod]);
    exit();
}

$mensaje = '';
$tipo_mensaje = '';

// Procesar Acciones POST
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    // 1. Crear sección especial y asignar docente
    if (isset($_POST['accion_crear_seccion'])) {
        $solicitud_ids = $_POST['solicitud_ids'] ?? [];
        if (!is_array($solicitud_ids)) {
            $solicitud_ids = !empty($solicitud_ids) ? [$solicitud_ids] : [];
        }
        $id_materia = intval($_POST['id_materia'] ?? 0);
        $id_docente = intval($_POST['id_docente'] ?? 0);
        $codigo_seccion = trim($_POST['codigo_seccion'] ?? '');
        $id_periodo = intval($_POST['id_periodo'] ?? 0);
        $turno = trim($_POST['turno'] ?? 'Diurno');
        $aula = trim($_POST['aula'] ?? 'Aula Asignada');

        $res = crearSeccionEspecialYAsignarDocente($solicitud_ids, $id_materia, $id_docente, $codigo_seccion, $id_periodo, $turno, $aula, $admin_id);
        if ($res['success']) {
            $mensaje = $res['mensaje'];
            $tipo_mensaje = 'success';
        } else {
            $mensaje = $res['mensaje'];
            $tipo_mensaje = 'danger';
        }
    }

    // 2. Rechazar solicitud
    if (isset($_POST['accion_rechazar'])) {
        $solicitud_id = intval($_POST['solicitud_id'] ?? 0);
        $motivo_rechazo = trim($_POST['motivo_rechazo'] ?? '');

        $res = rechazarSolicitudEvaluacionEspecial($solicitud_id, $motivo_rechazo, $admin_id);
        if ($res['success']) {
            $mensaje = $res['mensaje'];
            $tipo_mensaje = 'warning';
        } else {
            $mensaje = $res['mensaje'];
            $tipo_mensaje = 'danger';
        }
    }
}

// Filtros GET
$filtro_carrera = intval($_GET['carrera'] ?? 0);
$filtro_periodo = intval($_GET['periodo'] ?? 0);
$filtro_modalidad = trim($_GET['modalidad'] ?? '');
$filtro_status = trim($_GET['status'] ?? '');

// Cargar listas para filtros y modales
$carreras_res = $db->query("SELECT id_carrera, nombre_carrera, cod_carrera FROM carreras WHERE id_carrera > 0 ORDER BY nombre_carrera ASC");
$periodos_res = $db->query("SELECT id_periodo, nombre_periodo, activo FROM periodos_academicos ORDER BY fecha_inicio DESC");
$docentes_lista = obtenerDocentesDisponibles();

// Cargar solicitudes filtradas
$solicitudes = obtenerSolicitudesEvaluacionesEspeciales($filtro_carrera, $filtro_periodo, $filtro_modalidad, $filtro_status);

// Conteo de métricas
$total_solicitudes = count($solicitudes);
$pendientes_count = 0;
$aprobadas_count = 0;
$rechazadas_count = 0;
foreach ($solicitudes as $s) {
    if ($s['status'] === 'pendiente') $pendientes_count++;
    elseif ($s['status'] === 'aprobada') $aprobadas_count++;
    elseif ($s['status'] === 'rechazada') $rechazadas_count++;
}

include("includes/head.php");
?>

<div class="container-fluid px-3 px-md-4 mt-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <div>
            <h1 class="h3 font-weight-bold text-dark mb-1">
                <i class="fas fa-layer-group text-primary mr-2"></i><?= htmlspecialchars($titulopag) ?>
            </h1>
            <p class="text-muted small mb-0">Revisa solicitudes de estudiantes, genera secciones exclusivas para PER, Intensivo, Suficiencia y Extraordinario, y asigna docentes.</p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="consulta_notas.php" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fas fa-search mr-1"></i> Ir a Consulta de Notas
            </a>
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

    <!-- Contadores Estadísticos -->
    <div class="row mb-3">
        <div class="col-6 col-md-3 mb-2">
            <div class="card border-0 shadow-sm bg-white p-3 text-center border-left-primary" style="border-left: 4px solid #007bff !important;">
                <span class="text-muted small font-weight-bold text-uppercase">Total Filtradas</span>
                <h3 class="font-weight-bold text-dark m-0"><?= $total_solicitudes ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card border-0 shadow-sm bg-white p-3 text-center" style="border-left: 4px solid #ffc107 !important;">
                <span class="text-warning small font-weight-bold text-uppercase">Pendientes</span>
                <h3 class="font-weight-bold text-warning m-0"><?= $pendientes_count ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card border-0 shadow-sm bg-white p-3 text-center" style="border-left: 4px solid #28a745 !important;">
                <span class="text-success small font-weight-bold text-uppercase">Sección Asignada</span>
                <h3 class="font-weight-bold text-success m-0"><?= $aprobadas_count ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card border-0 shadow-sm bg-white p-3 text-center" style="border-left: 4px solid #dc3545 !important;">
                <span class="text-danger small font-weight-bold text-uppercase">Rechazadas</span>
                <h3 class="font-weight-bold text-danger m-0"><?= $rechazadas_count ?></h3>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros Requeridos: Carrera, Período, Modalidad, Estatus -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="font-weight-bold text-dark m-0">
                <i class="fas fa-filter text-primary mr-1"></i> Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body p-3">
            <form method="GET" action="solicitudes_evaluaciones.php" class="row align-items-end">
                <!-- Filtro Carrera -->
                <div class="col-md-3 mb-2">
                    <label for="filtro_carrera" class="small font-weight-bold text-muted">Carrera / Programa:</label>
                    <select name="carrera" id="filtro_carrera" class="form-control form-control-sm">
                        <option value="0">-- Todas las Carreras --</option>
                        <?php 
                        if ($carreras_res) {
                            $carreras_res->data_seek(0);
                            while ($c = $carreras_res->fetch_assoc()) {
                                $sel = ($filtro_carrera == $c['id_carrera']) ? 'selected' : '';
                                echo '<option value="' . $c['id_carrera'] . '" ' . $sel . '>' . htmlspecialchars($c['nombre_carrera']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Filtro Período Académico -->
                <div class="col-md-3 mb-2">
                    <label for="filtro_periodo" class="small font-weight-bold text-muted">Período Académico:</label>
                    <select name="periodo" id="filtro_periodo" class="form-control form-control-sm">
                        <option value="0">-- Todos los Períodos --</option>
                        <?php 
                        if ($periodos_res) {
                            $periodos_res->data_seek(0);
                            while ($p = $periodos_res->fetch_assoc()) {
                                $sel = ($filtro_periodo == $p['id_periodo']) ? 'selected' : '';
                                $act = ($p['activo'] == 1) ? ' (Activo)' : '';
                                echo '<option value="' . $p['id_periodo'] . '" ' . $sel . '>' . htmlspecialchars($p['nombre_periodo']) . $act . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Filtro Modalidad -->
                <div class="col-md-2 mb-2">
                    <label for="filtro_modalidad" class="small font-weight-bold text-muted">Modalidad:</label>
                    <select name="modalidad" id="filtro_modalidad" class="form-control form-control-sm">
                        <option value="">-- Todas --</option>
                        <option value="per" <?= $filtro_modalidad === 'per' ? 'selected' : '' ?>>PER (Plan Especial)</option>
                        <option value="intensivo" <?= $filtro_modalidad === 'intensivo' ? 'selected' : '' ?>>Intensivo (T)</option>
                        <option value="suficiencia" <?= $filtro_modalidad === 'suficiencia' ? 'selected' : '' ?>>Suficiencia (S)</option>
                        <option value="extraordinario" <?= $filtro_modalidad === 'extraordinario' ? 'selected' : '' ?>>Extraordinario (E)</option>
                    </select>
                </div>

                <!-- Filtro Estatus -->
                <div class="col-md-2 mb-2">
                    <label for="filtro_status" class="small font-weight-bold text-muted">Estatus:</label>
                    <select name="status" id="filtro_status" class="form-control form-control-sm">
                        <option value="">-- Todos --</option>
                        <option value="pendiente" <?= $filtro_status === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="aprobada" <?= $filtro_status === 'aprobada' ? 'selected' : '' ?>>Aprobada / Sección Creada</option>
                        <option value="rechazada" <?= $filtro_status === 'rechazada' ? 'selected' : '' ?>>Rechazada</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="col-md-2 mb-2 d-flex">
                    <button type="submit" class="btn btn-primary btn-sm btn-block mr-1">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="solicitudes_evaluaciones.php" class="btn btn-light btn-sm" title="Limpiar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Barra de Acciones Masivas -->
    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <div>
            <button type="button" id="btnCrearSeccionGrupal" class="btn btn-success btn-sm font-weight-bold shadow-sm" disabled>
                <i class="fas fa-plus-circle mr-1"></i> Crear Sección para Seleccionados (<span id="countSeleccionados">0</span>)
            </button>
            <small class="text-muted ml-2">Selecciona solicitudes pendientes de la misma asignatura para agruparlas en una misma sección.</small>
        </div>
        <div>
            <span class="badge badge-light border"><?= $total_solicitudes ?> registro(s)</span>
        </div>
    </div>

    <!-- Tabla Principal de Solicitudes -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <?php if (!empty($solicitudes)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped m-0 align-middle font-size-sm" id="tablaSolicitudes">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <input type="checkbox" id="checkAllSolicitudes" title="Seleccionar Todas las pendientes">
                                </th>
                                <th>Folio</th>
                                <th>Estudiante</th>
                                <th>Carrera</th>
                                <th>Modalidad</th>
                                <th>Asignatura(s) Solicitada(s)</th>
                                <th>Período</th>
                                <th>Estatus</th>
                                <th>Sección / Docente</th>
                                <th class="text-center" style="min-width: 140px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $sol): 
                                $mod_badge = 'badge-secondary';
                                $mod_nom = strtoupper($sol['tipo_solicitud']);
                                if ($sol['tipo_solicitud'] === 'per') $mod_badge = 'badge-warning text-dark';
                                elseif ($sol['tipo_solicitud'] === 'intensivo') $mod_badge = 'badge-info';
                                elseif ($sol['tipo_solicitud'] === 'extraordinario') $mod_badge = 'badge-danger';
                                elseif ($sol['tipo_solicitud'] === 'suficiencia') $mod_badge = 'badge-primary';

                                $st_badge = 'badge-warning';
                                $st_text = 'Pendiente';
                                if ($sol['status'] === 'aprobada') {
                                    $st_badge = 'badge-success';
                                    $st_text = 'Aprobada';
                                } elseif ($sol['status'] === 'rechazada') {
                                    $st_badge = 'badge-danger';
                                    $st_text = 'Rechazada';
                                }

                                $primera_materia = $sol['materias_parsed'][0] ?? null;
                                $id_materia_first = $primera_materia['id_materia'] ?? 0;
                                $nombre_materia_first = $primera_materia['nombre_materia'] ?? '';
                                $cod_materia_first = $primera_materia['cod_materia'] ?? '';
                                $trayecto_first = $primera_materia['trayecto'] ?? 1;
                            ?>
                            <tr data-materia-id="<?= $id_materia_first ?>" data-modalidad="<?= htmlspecialchars($sol['tipo_solicitud']) ?>" data-periodo-id="<?= $sol['id_periodo'] ?>">
                                <td class="text-center">
                                    <?php if ($sol['status'] === 'pendiente'): ?>
                                        <input type="checkbox" class="check-solicitud" 
                                               value="<?= $sol['id'] ?>" 
                                               data-estudiante="<?= htmlspecialchars($sol['nombre_estudiante']) ?>"
                                               data-cedula="<?= htmlspecialchars($sol['cedula_estudiante']) ?>"
                                               data-materia-id="<?= $id_materia_first ?>"
                                               data-materia-nombre="<?= htmlspecialchars($nombre_materia_first) ?>"
                                               data-materia-cod="<?= htmlspecialchars($cod_materia_first) ?>"
                                               data-carrera-id="<?= $sol['id_carrera'] ?>"
                                               data-carrera-nombre="<?= htmlspecialchars($sol['nombre_carrera']) ?>"
                                               data-periodo-id="<?= $sol['id_periodo'] ?>"
                                               data-periodo-nombre="<?= htmlspecialchars($sol['nombre_periodo']) ?>"
                                               data-modalidad="<?= htmlspecialchars($sol['tipo_solicitud']) ?>"
                                               data-trayecto="<?= $trayecto_first ?>">
                                    <?php else: ?>
                                        <i class="fas fa-check-circle text-muted"></i>
                                    <?php endif; ?>
                                </td>
                                <td><strong>#<?= $sol['id'] ?></strong></td>
                                <td>
                                    <strong><?= htmlspecialchars($sol['nombre_estudiante']) ?></strong><br>
                                    <small class="text-muted">C.I: <?= htmlspecialchars($sol['cedula_estudiante']) ?></small>
                                </td>
                                <td>
                                    <span class="small font-weight-bold text-dark"><?= htmlspecialchars($sol['nombre_carrera']) ?></span>
                                </td>
                                <td>
                                    <span class="badge <?= $mod_badge ?> px-2 py-1"><?= $mod_nom ?></span>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($sol['materias_parsed'])) {
                                        foreach ($sol['materias_parsed'] as $mp) {
                                            echo '<div><strong>[' . htmlspecialchars($mp['cod_materia'] ?? '') . ']</strong> ' . htmlspecialchars($mp['nombre_materia'] ?? '') . '</div>';
                                        }
                                    } else {
                                        echo '<span class="text-muted">Sin especificar</span>';
                                    }
                                    ?>
                                    <small class="text-muted d-block font-italic mt-1">"<?= htmlspecialchars($sol['motivo']) ?>"</small>
                                </td>
                                <td><?= htmlspecialchars($sol['nombre_periodo'] ?? '-') ?></td>
                                <td>
                                    <span class="badge <?= $st_badge ?> px-2 py-1"><?= $st_text ?></span>
                                    <?php if (!empty($sol['motivo_rechazo'])): ?>
                                        <div class="small text-danger mt-1"><strong>Rechazo:</strong> <?= htmlspecialchars($sol['motivo_rechazo']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($sol['seccion_asignada_codigo'])): ?>
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-door-open mr-1"></i><?= htmlspecialchars($sol['seccion_asignada_codigo']) ?>
                                        </span>
                                        <?php if (!empty($sol['nombre_docente_asignado'])): ?>
                                            <div class="small text-dark font-weight-bold mt-1">
                                                <i class="fas fa-chalkboard-teacher text-primary mr-1"></i><?= htmlspecialchars($sol['nombre_docente_asignado']) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">Sin Asignar</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($sol['status'] === 'pendiente'): ?>
                                        <button type="button" class="btn btn-sm btn-success btn-abrir-seccion-individual mb-1" 
                                                data-solicitud-id="<?= $sol['id'] ?>"
                                                data-estudiante="<?= htmlspecialchars($sol['nombre_estudiante']) ?>"
                                                data-cedula="<?= htmlspecialchars($sol['cedula_estudiante']) ?>"
                                                data-materia-id="<?= $id_materia_first ?>"
                                                data-materia-nombre="<?= htmlspecialchars($nombre_materia_first) ?>"
                                                data-materia-cod="<?= htmlspecialchars($cod_materia_first) ?>"
                                                data-carrera-id="<?= $sol['id_carrera'] ?>"
                                                data-carrera-nombre="<?= htmlspecialchars($sol['nombre_carrera']) ?>"
                                                data-periodo-id="<?= $sol['id_periodo'] ?>"
                                                data-periodo-nombre="<?= htmlspecialchars($sol['nombre_periodo']) ?>"
                                                data-modalidad="<?= htmlspecialchars($sol['tipo_solicitud']) ?>"
                                                data-trayecto="<?= $trayecto_first ?>"
                                                title="Crear sección exclusiva">
                                            <i class="fas fa-plus"></i> Sección
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-abrir-rechazo mb-1"
                                                data-solicitud-id="<?= $sol['id'] ?>"
                                                data-estudiante="<?= htmlspecialchars($sol['nombre_estudiante']) ?>"
                                                title="Rechazar solicitud">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    <?php endif; ?>
                                    <a href="consulta_notas.php?cedula=<?= urlencode($sol['cedula_estudiante']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mb-1" title="Ver Historial / Calificar en Consulta">
                                        <i class="fas fa-user-graduate"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 text-secondary"></i>
                    <h5 class="font-weight-bold text-dark">No se encontraron solicitudes</h5>
                    <p class="mb-0">No hay solicitudes registradas con los filtros seleccionados.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================================================== -->
<!-- MODAL: CREAR SECCIÓN ESPECIAL Y ASIGNAR PROFESOR                               -->
<!-- ============================================================================== -->
<div class="modal fade" id="modalCrearSeccion" tabindex="-1" role="dialog" aria-labelledby="modalCrearSeccionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="solicitudes_evaluaciones.php" id="formCrearSeccionEspecial">
            <input type="hidden" name="accion_crear_seccion" value="1">
            <input type="hidden" name="id_materia" id="modal_id_materia" value="">
            <input type="hidden" name="id_periodo" id="modal_id_periodo" value="">
            <div id="hiddenSolicitudesIds"></div>

            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="modalCrearSeccionLabel">
                        <i class="fas fa-door-open mr-2"></i>Crear Sección Especial y Asignar Docente
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Modalidad:</span>
                                <strong id="modal_badge_modalidad" class="badge badge-warning px-2 py-1 text-uppercase">PER</strong>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Período Académico:</span>
                                <strong id="modal_txt_periodo" class="text-dark">-</strong>
                            </div>
                            <div class="col-md-6 mt-2">
                                <span class="text-muted small d-block">Carrera:</span>
                                <strong id="modal_txt_carrera" class="text-dark">-</strong>
                            </div>
                            <div class="col-md-6 mt-2">
                                <span class="text-muted small d-block">Asignatura a impartir:</span>
                                <strong id="modal_txt_materia" class="text-primary">-</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Código de Sección Sugerido / Editable -->
                        <div class="col-md-6 form-group">
                            <label for="modal_codigo_seccion" class="font-weight-bold text-dark">
                                Código de Sección Especial: <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="codigo_seccion" id="modal_codigo_seccion" class="form-control font-weight-bold text-primary" required>
                            <small class="text-muted">Generado automáticamente. Puedes personalizarlo si lo requieres.</small>
                        </div>

                        <!-- Turno -->
                        <div class="col-md-6 form-group">
                            <label for="modal_turno" class="font-weight-bold text-dark">Turno:</label>
                            <select name="turno" id="modal_turno" class="form-control">
                                <option value="Diurno" selected>Diurno</option>
                                <option value="Nocturno">Nocturno</option>
                                <option value="Sabatino">Sabatino</option>
                                <option value="Especial">Especial</option>
                            </select>
                        </div>

                        <!-- Asignación del Profesor (Docente) -->
                        <div class="col-md-8 form-group">
                            <label for="modal_id_docente" class="font-weight-bold text-dark">
                                <i class="fas fa-chalkboard-teacher text-primary mr-1"></i> Asignar Profesor (Docente): <span class="text-danger">*</span>
                            </label>
                            <select name="id_docente" id="modal_id_docente" class="form-control" required>
                                <option value="">-- Seleccionar Docente --</option>
                                <?php foreach ($docentes_lista as $doc): ?>
                                    <option value="<?= $doc['id'] ?>">
                                        <?= htmlspecialchars($doc['nombre']) ?> (C.I: <?= htmlspecialchars($doc['idusuario']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">El profesor verá la sección y podrá asentar notas de esta materia.</small>
                        </div>

                        <!-- Aula / Ubicación -->
                        <div class="col-md-4 form-group">
                            <label for="modal_aula" class="font-weight-bold text-dark">Aula / Espacio:</label>
                            <input type="text" name="aula" id="modal_aula" class="form-control" placeholder="Ej: Aula 102 / Lab 2" value="Aula Asignada">
                        </div>
                    </div>

                    <!-- Lista de Estudiantes a Inscribir -->
                    <div class="form-group mt-2">
                        <label class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-user-check text-success mr-1"></i> Estudiantes que quedarán inscritos automáticamente:
                        </label>
                        <div id="modal_lista_estudiantes" class="p-2 border rounded bg-light" style="max-height: 140px; overflow-y: auto;">
                            <!-- Inyectado vía JS -->
                        </div>
                        <small class="text-muted">Al confirmar, el sistema inscribirá a los estudiantes en esta sección para ver <strong>únicamente la clase de la materia que les falta</strong>.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4">
                        <i class="fas fa-check mr-1"></i> Generar Sección e Inscribir Estudiantes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================================== -->
<!-- MODAL: RECHAZAR SOLICITUD                                                      -->
<!-- ============================================================================== -->
<div class="modal fade" id="modalRechazarSolicitud" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="solicitudes_evaluaciones.php">
            <input type="hidden" name="accion_rechazar" value="1">
            <input type="hidden" name="solicitud_id" id="modal_rechazo_solicitud_id" value="">

            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-ban mr-2"></i>Rechazar Solicitud
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-dark">¿Estás seguro de que deseas rechazar la solicitud del estudiante <strong id="modal_rechazo_estudiante">-</strong>?</p>
                    <div class="form-group">
                        <label for="motivo_rechazo" class="font-weight-bold small text-muted">Indica el motivo del rechazo: <span class="text-danger">*</span></label>
                        <textarea name="motivo_rechazo" id="motivo_rechazo" class="form-control" rows="3" required placeholder="Explica la causa del rechazo (ej: No cumple nota mínima de 6 pts, cupo lleno, etc.)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4">
                        <i class="fas fa-times mr-1"></i> Confirmar Rechazo
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Manejo de Selección de Solicitudes Masivas
    function actualizarBotonGrupal() {
        var checks = $('.check-solicitud:checked');
        var count = checks.length;
        $('#countSeleccionados').text(count);

        if (count > 0) {
            // Validar que todas las seleccionadas pertenezcan a la misma materia y período
            var primerMateria = checks.first().data('materia-id');
            var mismaMateria = true;
            checks.each(function() {
                if ($(this).data('materia-id') != primerMateria) {
                    mismaMateria = false;
                }
            });

            if (!mismaMateria) {
                $('#btnCrearSeccionGrupal').prop('disabled', true).addClass('btn-secondary').removeClass('btn-success');
                $('#btnCrearSeccionGrupal').attr('title', 'Debes seleccionar solicitudes de la MISMA asignatura para crear una sección común');
            } else {
                $('#btnCrearSeccionGrupal').prop('disabled', false).addClass('btn-success').removeClass('btn-secondary');
                $('#btnCrearSeccionGrupal').removeAttr('title');
            }
        } else {
            $('#btnCrearSeccionGrupal').prop('disabled', true);
        }
    }

    $('.check-solicitud').on('change', function() {
        actualizarBotonGrupal();
    });

    $('#checkAllSolicitudes').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.check-solicitud').prop('checked', isChecked);
        actualizarBotonGrupal();
    });

    // Abrir modal desde acción individual
    $('.btn-abrir-seccion-individual').on('click', function() {
        var btn = $(this);
        var solId = btn.data('solicitud-id');
        var estudiante = btn.data('estudiante');
        var cedula = btn.data('cedula');
        var matId = btn.data('materia-id');
        var matNombre = btn.data('materia-nombre');
        var matCod = btn.data('materia-cod');
        var carId = btn.data('carrera-id');
        var carNombre = btn.data('carrera-nombre');
        var perId = btn.data('periodo-id');
        var perNombre = btn.data('periodo-nombre');
        var mod = btn.data('modalidad');
        var tray = btn.data('trayecto');

        abrirModalSeccion([solId], [{nombre: estudiante, cedula: cedula}], matId, matNombre, matCod, carId, carNombre, perId, perNombre, mod, tray);
    });

    // Abrir modal desde acción grupal
    $('#btnCrearSeccionGrupal').on('click', function() {
        var checks = $('.check-solicitud:checked');
        if (checks.length === 0) return;

        var solIds = [];
        var estudiantes = [];
        var first = checks.first();
        var matId = first.data('materia-id');
        var matNombre = first.data('materia-nombre');
        var matCod = first.data('materia-cod');
        var carId = first.data('carrera-id');
        var carNombre = first.data('carrera-nombre');
        var perId = first.data('periodo-id');
        var perNombre = first.data('periodo-nombre');
        var mod = first.data('modalidad');
        var tray = first.data('trayecto');

        checks.each(function() {
            solIds.push($(this).val());
            estudiantes.push({
                nombre: $(this).data('estudiante'),
                cedula: $(this).data('cedula')
            });
        });

        abrirModalSeccion(solIds, estudiantes, matId, matNombre, matCod, carId, carNombre, perId, perNombre, mod, tray);
    });

    function abrirModalSeccion(solIds, estudiantes, matId, matNombre, matCod, carId, carNombre, perId, perNombre, mod, tray) {
        $('#modal_id_materia').val(matId);
        $('#modal_id_periodo').val(perId);

        // Generar inputs ocultos de IDs
        var hiddenContainer = $('#hiddenSolicitudesIds');
        hiddenContainer.empty();
        solIds.forEach(function(id) {
            hiddenContainer.append('<input type="hidden" name="solicitud_ids[]" value="' + id + '">');
        });

        // Textos descriptivos
        $('#modal_badge_modalidad').text(mod.toUpperCase());
        $('#modal_txt_periodo').text(perNombre);
        $('#modal_txt_carrera').text(carNombre);
        $('#modal_txt_materia').text('[' + matCod + '] ' + matNombre);

        // Generar código de sección sugerido compatible con el formato institucional del sistema viejo: [MOD]-[TRAYECTO]-[NUM]
        var prefijo = 'PER';
        if (mod === 'intensivo') prefijo = 'INT';
        else if (mod === 'suficiencia') prefijo = 'SUF';
        else if (mod === 'extraordinario') prefijo = 'EXT';

        var trayectoStr = tray ? tray : '1';
        $('#modal_codigo_seccion').val(prefijo + '-' + trayectoStr + '-71');

        $.ajax({
            url: 'solicitudes_evaluaciones.php',
            type: 'POST',
            data: {
                ajax_generar_codigo_especial: 1,
                id_carrera: carId,
                trayecto: trayectoStr,
                modalidad: mod,
                turno: $('#modal_turno').val()
            },
            dataType: 'json',
            success: function(resp) {
                if (resp && resp.success && resp.codigo) {
                    $('#modal_codigo_seccion').val(resp.codigo);
                }
            }
        });

        // Renderizar lista de estudiantes en el modal
        var listContainer = $('#modal_lista_estudiantes');
        listContainer.empty();
        estudiantes.forEach(function(est) {
            listContainer.append('<div class="d-flex justify-content-between border-bottom py-1 small"><span><i class="fas fa-user-graduate mr-1 text-muted"></i><strong>' + est.nombre + '</strong></span><span class="badge badge-light">C.I: ' + est.cedula + '</span></div>');
        });

        $('#modalCrearSeccion').modal('show');
    }

    // Modal Rechazo
    $('.btn-abrir-rechazo').on('click', function() {
        var solId = $(this).data('solicitud-id');
        var estudiante = $(this).data('estudiante');

        $('#modal_rechazo_solicitud_id').val(solId);
        $('#modal_rechazo_estudiante').text(estudiante);
        $('#modalRechazarSolicitud').modal('show');
    });
});
</script>

<?php include("includes/footer.php"); ?>
