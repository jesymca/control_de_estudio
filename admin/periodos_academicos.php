<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Gestión de Periodos Académicos";
require_once('../funciones/functions.php');

// CARGAR PERMISOS
cargarPermisosUsuario();
verificarPermiso('periodos_academicos');

$permiso_gestion = tienePermiso('gestion_periodo_academico');

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

// Función auxiliar para renderizar fila de periodo
function renderizarFilaPeriodo($periodo, $permiso_gestion) {
    $inicio = !empty($periodo['fecha_inicio']) ? date('d/m/Y', strtotime($periodo['fecha_inicio'])) : '-';
    $fin = !empty($periodo['fecha_fin']) ? date('d/m/Y', strtotime($periodo['fecha_fin'])) : '-';
    $es_activo = ($periodo['activo'] == 1);
    
    ob_start();
    ?>
    <tr class="<?= $es_activo ? 'table-success font-weight-normal' : '' ?>">
        <td class="font-weight-bold text-center"><?= htmlspecialchars($periodo['id_periodo'] ?? '') ?></td>
        <td>
            <span class="font-weight-bold text-dark"><?= htmlspecialchars($periodo['nombre_periodo'] ?? '') ?></span>
            <?php if (!empty($periodo['codigo_lapso']) && $periodo['codigo_lapso'] !== $periodo['nombre_periodo']): ?>
                <small class="text-muted ml-1">(<?= htmlspecialchars($periodo['codigo_lapso']) ?>)</small>
            <?php endif; ?>
            <?php if ($es_activo): ?>
                <span class="badge badge-success ml-2"><i class="fas fa-check-circle mr-1"></i>En curso</span>
            <?php endif; ?>
        </td>
        <td><i class="far fa-calendar-alt text-primary mr-1"></i><?= $inicio ?></td>
        <td><i class="far fa-calendar-check text-info mr-1"></i><?= $fin ?></td>
        <td class="text-center">
            <span class="badge <?= $es_activo ? 'badge-success' : 'badge-secondary' ?> px-2 py-1">
                <i class="fas <?= $es_activo ? 'fa-check-circle' : 'fa-pause-circle' ?> mr-1"></i>
                <?= $es_activo ? 'Activo' : 'Inactivo' ?>
            </span>
        </td>
        <td class="text-center">
            <?php if ($permiso_gestion): ?>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary editar-periodo" 
                        data-id="<?= htmlspecialchars($periodo['id_periodo'] ?? '') ?>"
                        data-nombre="<?= htmlspecialchars($periodo['nombre_periodo'] ?? '') ?>"
                        data-inicio="<?= htmlspecialchars($periodo['fecha_inicio'] ?? '') ?>"
                        data-fin="<?= htmlspecialchars($periodo['fecha_fin'] ?? '') ?>"
                        title="Editar período">
                    <i class="fas fa-edit"></i>
                </button>
                <?php if ($es_activo): ?>
                    <button type="button" class="btn btn-outline-danger desactivar-periodo" 
                            data-id="<?= htmlspecialchars($periodo['id_periodo'] ?? '') ?>"
                            data-nombre="<?= htmlspecialchars($periodo['nombre_periodo'] ?? '') ?>"
                            title="Desactivar período">
                        <i class="fas fa-power-off"></i>
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-success activar-periodo" 
                            data-id="<?= htmlspecialchars($periodo['id_periodo'] ?? '') ?>"
                            data-nombre="<?= htmlspecialchars($periodo['nombre_periodo'] ?? '') ?>"
                            title="Activar período">
                        <i class="fas fa-check"></i>
                    </button>
                <?php endif; ?>
            </div>
            <?php else: ?>
                <span class="text-muted small">Solo lectura</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php
    return ob_get_clean();
}

// -------------------------------------------------------------
// MANEJADORES AJAX (Respuesta rápida en JSON antes de incluir HTML)
// -------------------------------------------------------------

// 1. Cargar / filtrar periodos paginados
if (isset($_POST['ajax_periodos']) || isset($_GET['ajax_periodos'])) {
    header('Content-Type: application/json');
    $buscar = trim($_REQUEST['buscar'] ?? '');
    $activo = isset($_REQUEST['activo']) ? trim($_REQUEST['activo']) : '';
    $pagina = isset($_REQUEST['pagina']) ? (int)$_REQUEST['pagina'] : 1;
    $registros_por_pagina = isset($_REQUEST['registros_por_pagina']) ? (int)$_REQUEST['registros_por_pagina'] : 10;
    
    $filtros = ['buscar' => $buscar, 'activo' => $activo];
    $datos = obtener_periodos_academicos_paginados($filtros, $pagina, $registros_por_pagina);
    
    $html_tabla = '';
    if (!empty($datos['resultados'])) {
        foreach ($datos['resultados'] as $p) {
            $html_tabla .= renderizarFilaPeriodo($p, $permiso_gestion);
        }
    } else {
        $html_tabla = '<tr><td colspan="6" class="text-center font-weight-bold text-muted p-4"><i class="fas fa-exclamation-circle text-warning mr-2"></i> NO SE ENCONTRARON PERIODOS ACADÉMICOS CON LOS FILTROS SELECCIONADOS</td></tr>';
    }
    
    $html_info = generar_html_info_registros($datos['total_registros'], $datos['pagina_actual'], $datos['registros_por_pagina'], 'PERIODOS ACADÉMICOS');
    $html_paginacion = generar_html_paginacion_ajax($datos['total_paginas'], $datos['pagina_actual'], 'cargarPeriodos');
    $metricas = obtener_metricas_periodos_academicos();
    
    echo json_encode([
        'status' => 'success',
        'html' => $html_tabla,
        'html_info' => $html_info,
        'html_paginacion' => $html_paginacion,
        'total_registros' => $datos['total_registros'],
        'total_paginas' => $datos['total_paginas'],
        'pagina_actual' => $datos['pagina_actual'],
        'metricas' => $metricas
    ]);
    exit();
}

// 2. Crear periodo por AJAX
if (isset($_POST['ajax_crear_periodo'])) {
    header('Content-Type: application/json');
    if (!$permiso_gestion) {
        echo json_encode(['status' => 'danger', 'message' => 'No tiene permisos para crear periodos']);
        exit();
    }
    $nombre = trim($_POST['nombre_periodo'] ?? '');
    $inicio = trim($_POST['fecha_inicio'] ?? '');
    $fin = trim($_POST['fecha_fin'] ?? '');
    $resultado = crearPeriodoAcademico($db, $nombre, $inicio, $fin);
    $ok = is_array($resultado) ? !empty($resultado['success']) : (bool)$resultado;
    echo json_encode([
        'status' => $ok ? 'success' : 'danger',
        'message' => $resultado['message'] ?? ($ok ? 'Periodo creado correctamente' : 'Error al crear el periodo')
    ]);
    exit();
}

// 3. Editar periodo por AJAX
if (isset($_POST['ajax_editar_periodo'])) {
    header('Content-Type: application/json');
    if (!$permiso_gestion) {
        echo json_encode(['status' => 'danger', 'message' => 'No tiene permisos para editar periodos']);
        exit();
    }
    $id = intval($_POST['id_periodo'] ?? 0);
    $nombre = trim($_POST['nombre_periodo'] ?? '');
    $inicio = trim($_POST['fecha_inicio'] ?? '');
    $fin = trim($_POST['fecha_fin'] ?? '');
    $resultado = actualizarPeriodoAcademico($db, $id, $nombre, $inicio, $fin);
    $ok = is_array($resultado) ? !empty($resultado['success']) : (bool)$resultado;
    echo json_encode([
        'status' => $ok ? 'success' : 'danger',
        'message' => $resultado['message'] ?? ($ok ? 'Periodo actualizado correctamente' : 'Error al actualizar el periodo')
    ]);
    exit();
}

// 4. Cambiar estado por AJAX
if (isset($_POST['ajax_cambiar_estado'])) {
    header('Content-Type: application/json');
    if (!$permiso_gestion) {
        echo json_encode(['status' => 'danger', 'message' => 'No tiene permisos para cambiar estado de periodos']);
        exit();
    }
    $id = intval($_POST['id_periodo'] ?? 0);
    $nuevo_estado = intval($_POST['nuevo_estado'] ?? 0);
    $resultado = cambiarEstadoPeriodo($db, $id, $nuevo_estado);
    $ok = is_array($resultado) ? !empty($resultado['success']) : (bool)$resultado;
    echo json_encode([
        'status' => $ok ? 'success' : 'danger',
        'message' => $resultado['message'] ?? ($ok ? 'Estado del periodo cambiado correctamente.' : 'Error al cambiar el estado del periodo')
    ]);
    exit();
}

// -------------------------------------------------------------
// PROCESAMIENTO TRADICIONAL POST (Fallback en caso de que JS no esté activo)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST['ajax_crear_periodo']) && empty($_POST['ajax_editar_periodo']) && empty($_POST['ajax_cambiar_estado'])) {
    if (isset($_POST['crear_periodo'])) {
        $creado = crearPeriodoAcademico($db, $_POST['nombre_periodo'], $_POST['fecha_inicio'], $_POST['fecha_fin']);
        $ok = is_array($creado) ? !empty($creado['success']) : (bool)$creado;
        $_SESSION['mensaje'] = [
            'tipo' => $ok ? 'success' : 'danger',
            'texto' => $creado['message'] ?? ($ok ? 'Periodo creado correctamente' : 'Error al crear el periodo')
        ];
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['editar_periodo'])) {
        $actualizado = actualizarPeriodoAcademico($db, $_POST['id_periodo'], $_POST['nombre_periodo'], $_POST['fecha_inicio'], $_POST['fecha_fin']);
        $ok = is_array($actualizado) ? !empty($actualizado['success']) : (bool)$actualizado;
        $_SESSION['mensaje'] = [
            'tipo' => $ok ? 'success' : 'danger',
            'texto' => $actualizado['message'] ?? ($ok ? 'Periodo actualizado correctamente' : 'Error al actualizar el periodo')
        ];
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['cambiar_estado'])) {
        $resultado = cambiarEstadoPeriodo($db, $_POST['id_periodo'], $_POST['nuevo_estado']);
        $ok = is_array($resultado) ? !empty($resultado['success']) : (bool)$resultado;
        $_SESSION['mensaje'] = [
            'tipo' => $ok ? 'success' : 'danger',
            'texto' => $resultado['message'] ?? ($ok ? 'Estado del periodo cambiado correctamente.' : 'Error al cambiar el estado del periodo')
        ];
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Carga inicial rápida con paginación
$metricas = obtener_metricas_periodos_academicos();
$registros_por_pagina = 10;
$pagina_actual = 1;
$datos_iniciales = obtener_periodos_academicos_paginados([], $pagina_actual, $registros_por_pagina);
$periodos = $datos_iniciales['resultados'];
$total_registros = $datos_iniciales['total_registros'];
$total_paginas = $datos_iniciales['total_paginas'];

include("includes/head.php");
?>

<div class="container-fluid py-3">
    <!-- Encabezado y Botón Nuevo Periodo -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-calendar-alt text-primary mr-2"></i>Gestión de Periodos Académicos
            </h1>
            <p class="text-muted small mb-0">Administra lapsos académicos, fechas de inicio y culminación, y estados de activación institucional.</p>
        </div>
        <?php if ($permiso_gestion): ?>
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#crearPeriodoModal">
            <i class="fas fa-plus mr-1"></i> Crear Nuevo Periodo
        </button>
        <?php endif; ?>
    </div>

    <!-- Alertas Flash -->
    <div id="alertaAjaxContainer">
        <?php 
        if (isset($_SESSION['mensaje'])): 
            $msg_tipo = 'info';
            $msg_texto = '';
            if (is_array($_SESSION['mensaje'])) {
                $msg_tipo = $_SESSION['mensaje']['tipo'] ?? 'info';
                $msg_texto = $_SESSION['mensaje']['texto'] ?? '';
            } else {
                $msg_tipo = $_SESSION['tipo_mensaje'] ?? 'info';
                $msg_texto = strval($_SESSION['mensaje']);
            }
            if (!empty($msg_texto)):
        ?>
        <div class="alert alert-<?= htmlspecialchars($msg_tipo) ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= htmlspecialchars($msg_texto) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php 
            endif;
            unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); 
        endif; 
        ?>
    </div>

    <!-- TARJETAS DE MÉTRICAS KPI -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3 mb-xl-0">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Periodos Registrados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpiTotalPeriodos"><?= $metricas['total'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3 mb-xl-0">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Periodos Activos (En Curso)</div>
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="h5 mb-0 font-weight-bold text-gray-800 mr-2" id="kpiActivosPeriodos"><?= $metricas['activos'] ?></span>
                                <span class="badge badge-success" id="kpiNombresActivos">
                                    <?= !empty($metricas['nombres_activos']) ? htmlspecialchars(implode(', ', $metricas['nombres_activos'])) : 'Ninguno' ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3 mb-xl-0">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Periodos Inactivos / Históricos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpiInactivosPeriodos"><?= $metricas['inactivos'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TARJETA PRINCIPAL CON BUSCADOR Y TABLA OPTIMIZADA -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-list-ul mr-2"></i>Listado de Periodos Académicos</h6>
        </div>
        <div class="card-body">
            <!-- Barra de Filtros y Búsqueda en Tiempo Real -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-6 col-lg-5 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light"><i class="fas fa-search text-primary"></i></span>
                        </div>
                        <input type="text" id="buscadorPeriodo" class="form-control" placeholder="Buscar por Nombre (ej. 2025, 2026-1)..." autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="btnLimpiarBusqueda" title="Limpiar búsqueda">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 mb-2 mb-md-0">
                    <select id="filtroEstadoPeriodo" class="form-control font-weight-bold">
                        <option value="">Todos los Estados</option>
                        <option value="1">Solo Activos</option>
                        <option value="0">Solo Inactivos</option>
                    </select>
                </div>
                <div class="col-md-3 col-lg-4 text-md-right">
                    <label class="mr-2 font-weight-bold text-uppercase text-muted mb-0"><small>Mostrar:</small></label>
                    <select class="form-control form-control-sm d-inline-block w-auto font-weight-bold" id="registrosPorPaginaPeriodo">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <small class="text-muted font-weight-bold ml-1">por página</small>
                </div>
            </div>

            <!-- Contador de Registros -->
            <div class="row mb-2">
                <div class="col-12" id="infoPeriodosContainer">
                    <?= generar_html_info_registros($total_registros, $pagina_actual, $registros_por_pagina, 'PERIODOS ACADÉMICOS') ?>
                </div>
            </div>

            <!-- Tabla de Registros -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="tablaPeriodos">
                    <thead class="thead-dark">
                        <tr>
                            <th width="70" class="text-center">ID</th>
                            <th>Nombre del Periodo</th>
                            <th width="140">Fecha Inicio</th>
                            <th width="140">Fecha Fin</th>
                            <th width="120" class="text-center">Estado</th>
                            <th width="130" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPeriodos">
                        <?php if (!empty($periodos)): ?>
                            <?php foreach ($periodos as $periodo): ?>
                                <?= renderizarFilaPeriodo($periodo, $permiso_gestion) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center font-weight-bold text-muted p-4">
                                    <i class="fas fa-exclamation-circle text-warning mr-2"></i> NO SE ENCONTRARON PERIODOS ACADÉMICOS
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación dinámica -->
            <div id="paginacionPeriodosContainer" class="mt-3">
                <?= generar_html_paginacion_ajax($total_paginas, $pagina_actual, 'cargarPeriodos') ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear nuevo periodo -->
<div class="modal fade" id="crearPeriodoModal" tabindex="-1" role="dialog" aria-labelledby="crearPeriodoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="crearPeriodoModalLabel">
                    <i class="fas fa-calendar-plus mr-2"></i>Crear Nuevo Periodo Académico
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCrearPeriodo" method="POST" action="">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label for="nombre_periodo" class="font-weight-bold small text-muted">Nombre del Periodo:</label>
                        <input type="text" class="form-control" id="nombre_periodo" name="nombre_periodo" placeholder="Ej: 2026-1" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="fecha_inicio" class="font-weight-bold small text-muted">Fecha de Inicio:</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin" class="font-weight-bold small text-muted">Fecha de Fin:</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="crear_periodo" class="btn btn-primary font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Guardar Periodo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar periodo -->
<div class="modal fade" id="editarPeriodoModal" tabindex="-1" role="dialog" aria-labelledby="editarPeriodoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="editarPeriodoModalLabel">
                    <i class="fas fa-edit mr-2"></i>Editar Periodo Académico
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditarPeriodo" method="POST" action="">
                <input type="hidden" id="id_periodo_edit" name="id_periodo">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label for="nombre_periodo_edit" class="font-weight-bold small text-muted">Nombre del Periodo:</label>
                        <input type="text" class="form-control" id="nombre_periodo_edit" name="nombre_periodo" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="fecha_inicio_edit" class="font-weight-bold small text-muted">Fecha de Inicio:</label>
                        <input type="date" class="form-control" id="fecha_inicio_edit" name="fecha_inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin_edit" class="font-weight-bold small text-muted">Fecha de Fin:</label>
                        <input type="date" class="form-control" id="fecha_fin_edit" name="fecha_fin" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="editar_periodo" class="btn btn-primary font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para confirmar cambio de estado -->
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1" role="dialog" aria-labelledby="cambiarEstadoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-warning text-dark font-weight-bold" id="headerEstadoModal">
                <h5 class="modal-title font-weight-bold" id="cambiarEstadoModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Cambiar Estado del Periodo
                </h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCambiarEstado" method="POST" action="">
                <input type="hidden" id="id_periodo_estado" name="id_periodo">
                <input type="hidden" id="nuevo_estado" name="nuevo_estado">
                <div class="modal-body p-4">
                    <p id="mensaje_estado" class="lead mb-0 font-weight-bold">¿Está seguro que desea cambiar el estado de este periodo académico?</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="cambiar_estado" id="btnConfirmarEstado" class="btn btn-primary font-weight-bold">
                        <i class="fas fa-check mr-1"></i> Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>

<script>
$(document).ready(function() {
    let paginaPeriodosActual = 1;
    let timerBusquedaPeriodo = null;

    // Función principal para cargar datos vía AJAX
    window.cargarPeriodos = function(pagina = 1) {
        paginaPeriodosActual = pagina;
        const buscar = $('#buscadorPeriodo').val() || '';
        const activo = $('#filtroEstadoPeriodo').val();
        const porPagina = $('#registrosPorPaginaPeriodo').val() || 10;

        $('#tbodyPeriodos').css('opacity', '0.5');

        $.ajax({
            url: 'periodos_academicos.php',
            type: 'POST',
            data: {
                ajax_periodos: 1,
                buscar: buscar,
                activo: activo,
                pagina: paginaPeriodosActual,
                registros_por_pagina: porPagina
            },
            dataType: 'json',
            success: function(res) {
                $('#tbodyPeriodos').css('opacity', '1');
                if (res && res.status === 'success') {
                    $('#tbodyPeriodos').html(res.html);
                    $('#infoPeriodosContainer').html(res.html_info);
                    $('#paginacionPeriodosContainer').html(res.html_paginacion);

                    // Actualizar métricas KPI si están presentes
                    if (res.metricas) {
                        $('#kpiTotalPeriodos').text(res.metricas.total);
                        $('#kpiActivosPeriodos').text(res.metricas.activos);
                        $('#kpiInactivosPeriodos').text(res.metricas.inactivos);
                        const nombresActivos = res.metricas.nombres_activos && res.metricas.nombres_activos.length > 0 
                            ? res.metricas.nombres_activos.join(', ') 
                            : 'Ninguno';
                        $('#kpiNombresActivos').text(nombresActivos);
                    }
                }
            },
            error: function(xhr, status, error) {
                $('#tbodyPeriodos').css('opacity', '1');
                console.error('Error al cargar periodos:', error);
            }
        });
    };

    // Búsqueda en tiempo real con debounce de 250ms
    $('#buscadorPeriodo').on('input', function() {
        clearTimeout(timerBusquedaPeriodo);
        timerBusquedaPeriodo = setTimeout(function() {
            cargarPeriodos(1);
        }, 250);
    });

    $('#btnLimpiarBusqueda').on('click', function() {
        $('#buscadorPeriodo').val('');
        $('#filtroEstadoPeriodo').val('');
        cargarPeriodos(1);
    });

    $('#filtroEstadoPeriodo').on('change', function() {
        cargarPeriodos(1);
    });

    $('#registrosPorPaginaPeriodo').on('change', function() {
        cargarPeriodos(1);
    });

    // Delegación de eventos para Abrir modal de Editar
    $(document).on('click', '.editar-periodo', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        var inicio = $(this).data('inicio');
        var fin = $(this).data('fin');
        
        $('#id_periodo_edit').val(id);
        $('#nombre_periodo_edit').val(nombre);
        $('#fecha_inicio_edit').val(inicio);
        $('#fecha_fin_edit').val(fin);
        
        $('#editarPeriodoModal').modal('show');
    });
    
    // Delegación de eventos para Desactivar Periodo
    $(document).on('click', '.desactivar-periodo', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre') || '';
        $('#id_periodo_estado').val(id);
        $('#nuevo_estado').val(0);
        $('#headerEstadoModal').removeClass('bg-success text-white').addClass('bg-danger text-white');
        $('#btnConfirmarEstado').removeClass('btn-success').addClass('btn-danger');
        $('#mensaje_estado').html('¿Está seguro que desea <strong class="text-danger">DESACTIVAR</strong> el periodo académico <u>' + nombre + '</u>?');
        $('#cambiarEstadoModal').modal('show');
    });
    
    // Delegación de eventos para Activar Periodo
    $(document).on('click', '.activar-periodo', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre') || '';
        $('#id_periodo_estado').val(id);
        $('#nuevo_estado').val(1);
        $('#headerEstadoModal').removeClass('bg-danger text-white').addClass('bg-success text-white');
        $('#btnConfirmarEstado').removeClass('btn-danger').addClass('btn-success');
        $('#mensaje_estado').html('¿Está seguro que desea <strong class="text-success">ACTIVAR</strong> el periodo académico <u>' + nombre + '</u>?');
        $('#cambiarEstadoModal').modal('show');
    });

    // Helper para mostrar alerta flotante
    function mostrarAlerta(tipo, texto) {
        var alertHtml = `
            <div class="alert alert-${tipo} alert-dismissible fade show shadow-sm" role="alert">
                ${texto}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>`;
        $('#alertaAjaxContainer').html(alertHtml);
        setTimeout(function() {
            $('#alertaAjaxContainer .alert').alert('close');
        }, 5000);
    }

    // Submit asíncrono para Crear Periodo
    $('#formCrearPeriodo').on('submit', function(e) {
        e.preventDefault();
        var inicio = $('#fecha_inicio').val();
        var fin = $('#fecha_fin').val();
        if (inicio && fin && inicio >= fin) {
            alert('La fecha de fin debe ser posterior a la fecha de inicio');
            return;
        }

        var formData = $(this).serialize() + '&ajax_crear_periodo=1';
        $.ajax({
            url: 'periodos_academicos.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                $('#crearPeriodoModal').modal('hide');
                $('#formCrearPeriodo')[0].reset();
                mostrarAlerta(res.status, res.message);
                cargarPeriodos(1);
            },
            error: function() {
                $('#crearPeriodoModal').modal('hide');
                mostrarAlerta('danger', 'Error al procesar la solicitud.');
            }
        });
    });

    // Submit asíncrono para Editar Periodo
    $('#formEditarPeriodo').on('submit', function(e) {
        e.preventDefault();
        var inicio = $('#fecha_inicio_edit').val();
        var fin = $('#fecha_fin_edit').val();
        if (inicio && fin && inicio >= fin) {
            alert('La fecha de fin debe ser posterior a la fecha de inicio');
            return;
        }

        var formData = $(this).serialize() + '&ajax_editar_periodo=1';
        $.ajax({
            url: 'periodos_academicos.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                $('#editarPeriodoModal').modal('hide');
                mostrarAlerta(res.status, res.message);
                cargarPeriodos(paginaPeriodosActual);
            },
            error: function() {
                $('#editarPeriodoModal').modal('hide');
                mostrarAlerta('danger', 'Error al procesar la solicitud.');
            }
        });
    });

    // Submit asíncrono para Cambiar Estado
    $('#formCambiarEstado').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize() + '&ajax_cambiar_estado=1';
        $.ajax({
            url: 'periodos_academicos.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                $('#cambiarEstadoModal').modal('hide');
                mostrarAlerta(res.status, res.message);
                cargarPeriodos(paginaPeriodosActual);
            },
            error: function() {
                $('#cambiarEstadoModal').modal('hide');
                mostrarAlerta('danger', 'Error al procesar la solicitud.');
            }
        });
    });
});
</script>