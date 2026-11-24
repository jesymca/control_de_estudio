<?php
// MOSTRAR ERRORES (eliminar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('../funciones/functions.php');

// CARGAR PERMISOS
cargarPermisosUsuario();
verificarPermiso('pagos');

// Verificar autenticación y rol
if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['msg'] = "Debes iniciar sesión como administrador para acceder";
    header('location: ../login.php');
    exit();
}

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

$current_user_id = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0;

// ==========================================
// CONTROLADOR AJAX PARA BÚSQUEDA EN TIEMPO REAL
// ==========================================
if (isset($_POST['ajax_buscar_estudiantes_pagos'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    $termino = trim($_POST['termino'] ?? '');
    $estudiantes = buscarEstudiantesPagosAjax($termino, 15);
    
    echo json_encode(['success' => true, 'estudiantes' => $estudiantes]);
    exit();
}

// Asegurar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje_error = '';
$mensaje_exito = '';
$estudiante = null;

// ==========================================
// PROCESAR ACCIONES POST
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $mensaje_error = "Error de seguridad CSRF.";
    }
    // 1. Registro manual de pago
    elseif (isset($_POST['registrar_pago'])) {
        $estudiante_id     = intval($_POST['estudiante_id'] ?? 0);
        $tipo_pago         = trim($_POST['tipo_pago'] ?? '');
        $otro_concepto     = trim($_POST['otro_concepto'] ?? '');
        $monto             = (float)($_POST['monto'] ?? 0);
        $metodo_pago       = trim($_POST['metodo_pago'] ?? 'Transferencia');
        $banco_destino_id  = intval($_POST['banco_destino_id'] ?? 0);
        $banco_origen      = trim($_POST['banco_origen'] ?? '');
        $fecha_transaccion = trim($_POST['fecha_transaccion'] ?? date('Y-m-d'));
        $referencia        = trim($_POST['referencia'] ?? '');
        $observaciones     = trim($_POST['observaciones'] ?? '');
        
        // Manejo opcional de comprobante adjuntado por admin
        $comprobante_rel_path = '';
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $ext = strtolower(pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $dir = __DIR__ . '/../uploads/comprobantes_pagos/';
                if (!is_dir($dir)) @mkdir($dir, 0777, true);
                $uname = 'pago_admin_' . $estudiante_id . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $dir . $uname)) {
                    $comprobante_rel_path = 'uploads/comprobantes_pagos/' . $uname;
                }
            }
        }
        
        if ($estudiante_id > 0 && $monto > 0) {
            $pago_id = registrarPago(
                $estudiante_id, $tipo_pago, $otro_concepto, $monto, $observaciones,
                $current_user_id, $metodo_pago, $banco_destino_id, $banco_origen,
                $fecha_transaccion, $referencia, $comprobante_rel_path, 'aprobado'
            );
            if ($pago_id) {
                $mensaje_exito = "Pago registrado y aprobado exitosamente.";
                $estudiante = null;
            } else {
                $mensaje_error = "Error al registrar el pago en la base de datos.";
            }
        } else {
            $mensaje_error = "Datos inválidos. El monto debe ser mayor a cero y debe seleccionar un estudiante válido.";
        }
    }
    // 2. Aprobar pago declarado por estudiante
    elseif (isset($_POST['aprobar_pago'])) {
        $pago_id = intval($_POST['pago_id']);
        if (cambiarEstadoPagoAdmin($pago_id, 'aprobado', '', $current_user_id)) {
            $mensaje_exito = "Pago #{$pago_id} aprobado exitosamente.";
        } else {
            $mensaje_error = "Error al aprobar el pago.";
        }
    }
    // 3. Rechazar pago declarado por estudiante
    elseif (isset($_POST['rechazar_pago'])) {
        $pago_id = intval($_POST['pago_id']);
        $motivo  = trim($_POST['motivo_rechazo'] ?? 'Comprobante no válido o datos no coincidentes');
        if (cambiarEstadoPagoAdmin($pago_id, 'rechazado', $motivo, $current_user_id)) {
            $mensaje_exito = "Pago #{$pago_id} rechazado.";
        } else {
            $mensaje_error = "Error al rechazar el pago.";
        }
    }
    // 4. Eliminar pago
    elseif (isset($_POST['eliminar_pago_id'])) {
        $pago_id = intval($_POST['eliminar_pago_id']);
        if (eliminarPago($pago_id)) {
            $mensaje_exito = "Pago eliminado exitosamente.";
        } else {
            $mensaje_error = "Error al eliminar el pago.";
        }
    }
}

// Manejo de filtros por fecha, estado, concepto y método
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['limpiar_filtros_pagos'])) {
        unset($_SESSION['filtro_pagos']);
        unset($_SESSION['filtro_pagos_status']);
    } elseif (isset($_POST['filtrar_pagos'])) {
        $_SESSION['filtro_pagos'] = [
            'fecha_desde'   => trim($_POST['fecha_desde'] ?? ''),
            'fecha_hasta'   => trim($_POST['fecha_hasta'] ?? ''),
            'filtro_status' => trim($_POST['filtro_status'] ?? ''),
            'filtro_tipo'   => trim($_POST['filtro_tipo'] ?? ''),
            'filtro_metodo' => trim($_POST['filtro_metodo'] ?? '')
        ];
    } elseif (isset($_POST['filtro_status_rapido'])) {
        if (!isset($_SESSION['filtro_pagos'])) $_SESSION['filtro_pagos'] = [];
        $_SESSION['filtro_pagos']['filtro_status'] = trim($_POST['filtro_status_rapido']);
    }
} elseif (isset($_GET['status'])) {
    if (!isset($_SESSION['filtro_pagos'])) $_SESSION['filtro_pagos'] = [];
    $_SESSION['filtro_pagos']['filtro_status'] = trim($_GET['status']);
}

// Fechas por defecto: todo el año actual hasta hoy para abarcar las operaciones
$filtro_fecha_desde = $_SESSION['filtro_pagos']['fecha_desde'] ?? date('Y-01-01');
$filtro_fecha_hasta = $_SESSION['filtro_pagos']['fecha_hasta'] ?? date('Y-m-d');
$filtro_status      = $_SESSION['filtro_pagos']['filtro_status'] ?? '';
$filtro_tipo        = $_SESSION['filtro_pagos']['filtro_tipo'] ?? '';
$filtro_metodo      = $_SESSION['filtro_pagos']['filtro_metodo'] ?? '';

// Obtener pagos según rango y filtros
$todos_los_pagos = obtenerPagosPorRangoFechas(
    $filtro_fecha_desde, 
    $filtro_fecha_hasta, 
    $filtro_status, 
    $filtro_tipo, 
    $filtro_metodo, 
    'DESC'
);

$stats_periodo = obtenerEstadisticasPagosRango(
    $filtro_fecha_desde, 
    $filtro_fecha_hasta, 
    $filtro_status, 
    $filtro_tipo, 
    $filtro_metodo
);

$tipos_pago           = obtenerTiposPago(true);
$metodos_pago_activos = obtenerMetodosPago(true);
$bancos_pm            = obtenerBancos(true, 'pago_movil');
$bancos_trans         = obtenerBancos(true, 'transferencia');
$bancos_todos         = obtenerBancos(true);
$total_pagos_hoy      = obtenerTotalPagosDelDia();

$titulopag = "Registro y Control de Pagos";
include("includes/head.php");
?>

<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h2 class="mb-0" style="font-size: 1.4rem;">
            <i class="fas fa-cash-register text-primary mr-2"></i>Registro y Control de Pagos
        </h2>
    </div>
    
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= htmlspecialchars($mensaje_exito) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($mensaje_error) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Formulario de búsqueda y registro -->
        <div class="col-12 col-lg-5 mb-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-search mr-1"></i> Buscar Estudiante y Registrar Pago</h5>
                </div>
                <div class="card-body p-3">
                    <!-- Buscador en tiempo real -->
                    <div class="form-group position-relative mb-3">
                        <label for="buscador_estudiante_pago" class="font-weight-bold">
                            <i class="fas fa-id-card text-secondary mr-1"></i> Cédula o Nombre del Estudiante:
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="buscador_estudiante_pago" 
                                   placeholder="Escriba cédula o nombre del estudiante..." autocomplete="off">
                            <div class="input-group-append" id="spinner_busqueda_est" style="display: none;">
                                <span class="input-group-text bg-white"><i class="fas fa-spinner fa-spin text-primary"></i></span>
                            </div>
                        </div>
                        
                        <!-- Lista flotante de sugerencias -->
                        <div id="sugerencias_estudiantes_pago" class="list-group shadow position-absolute w-100" 
                             style="z-index: 9999; display: none; max-height: 280px; overflow-y: auto; left: 0; right: 0; background: #ffffff; border: 1px solid #ced4da; border-radius: 4px;">
                        </div>
                    </div>
                    
                    <!-- Tarjeta de estudiante seleccionado -->
                    <div id="card_estudiante_seleccionado" class="alert alert-info p-3 mb-3" style="<?= $estudiante ? '' : 'display: none;' ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="font-weight-bold mb-1 text-primary">
                                    <i class="fas fa-user-graduate mr-1"></i> <span id="lbl_est_nombre"><?= $estudiante ? htmlspecialchars($estudiante['nombre']) : '' ?></span>
                                </h6>
                                <div class="small text-dark">
                                    <strong>Cédula:</strong> <span id="lbl_est_cedula"><?= $estudiante ? htmlspecialchars($estudiante['cedula']) : '' ?></span>
                                </div>
                                <div class="small text-muted">
                                    <strong>Carrera:</strong> <span id="lbl_est_carrera"><?= $estudiante ? htmlspecialchars($estudiante['nombre_carrera']) : '' ?></span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="btn_limpiar_estudiante" title="Cambiar estudiante">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Formulario de Registro de Pago -->
                    <form method="POST" action="" enctype="multipart/form-data" id="formRegistroPago" style="<?= $estudiante ? '' : 'display: none;' ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="estudiante_id" id="estudiante_id" value="<?= $estudiante ? $estudiante['id'] : '' ?>" required>
                        
                        <div class="form-group">
                            <label for="tipo_pago" class="font-weight-bold">Concepto / Arancel: <span class="text-danger">*</span></label>
                            <select class="custom-select" id="tipo_pago" name="tipo_pago" required>
                                <option value="" data-precio="">-- Seleccionar Concepto --</option>
                                <?php foreach ($tipos_pago as $tipo): ?>
                                    <option value="<?= $tipo['id'] ?>" data-precio="<?= htmlspecialchars($tipo['precio'] ?? '0.00') ?>">
                                        <?= htmlspecialchars($tipo['tipopago']) ?> <?php if (isset($tipo['precio']) && (float)$tipo['precio'] > 0): ?>(Bs <?= number_format($tipo['precio'], 2, ',', '.') ?>)<?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="otro" data-precio="0.00">Otro concepto personalizado</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="otro_concepto_group" style="display: none;">
                            <label for="otro_concepto" class="font-weight-bold">Especificar Concepto:</label>
                            <input type="text" class="form-control" id="otro_concepto" name="otro_concepto" 
                                   placeholder="Especifique el motivo o arancel">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="metodo_pago" class="font-weight-bold">Método de Pago: <span class="text-danger">*</span></label>
                                <select class="custom-select" id="metodo_pago" name="metodo_pago" required>
                                    <?php if (!empty($metodos_pago_activos)): ?>
                                        <?php foreach ($metodos_pago_activos as $idx => $mp): 
                                            $disabled = false;
                                            $extra_msg = '';
                                            if ($mp['codigo'] === 'pago_movil' && empty($bancos_pm)) {
                                                $disabled = true;
                                                $extra_msg = ' (Sin cuentas activas)';
                                            } elseif ($mp['codigo'] === 'transferencia' && empty($bancos_trans)) {
                                                $disabled = true;
                                                $extra_msg = ' (Sin cuentas activas)';
                                            }
                                        ?>
                                            <option value="<?= htmlspecialchars($mp['nombre']) ?>" 
                                                    data-codigo="<?= htmlspecialchars($mp['codigo']) ?>" 
                                                    data-requiere-banco="<?= $mp['requiere_banco'] ?>" 
                                                    <?= $disabled ? 'disabled' : '' ?>
                                                    <?= ($idx === 0 && !$disabled) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($mp['nombre']) . $extra_msg ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled selected>(No hay métodos de pago habilitados)</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="banco_destino_id" class="font-weight-bold">Banco Receptor (UPTPC):</label>
                                <select class="custom-select" id="banco_destino_id" name="banco_destino_id">
                                    <option value="">-- Seleccionar Banco --</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="banco_origen" class="font-weight-bold">Banco Emisor (Banco del Estudiante):</label>
                                <select class="custom-select" id="banco_origen" name="banco_origen">
                                    <option value="">-- Seleccionar Banco Emisor --</option>
                                    <?php if (!empty($bancos_todos)): ?>
                                        <?php foreach ($bancos_todos as $b): 
                                            $codigo_banc = !empty($b['codigo_banco']) ? $b['codigo_banco'] . ' - ' : '';
                                        ?>
                                            <option value="<?= htmlspecialchars($b['nombre_banco']) ?>">
                                                <?= htmlspecialchars($codigo_banc . $b['nombre_banco']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="Otro">Otro Banco / Entidad</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="fecha_transaccion" class="font-weight-bold">Fecha de Pago:</label>
                                <input type="date" class="form-control" id="fecha_transaccion" name="fecha_transaccion" 
                                       value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="referencia" class="font-weight-bold">N° de Referencia:</label>
                            <input type="text" class="form-control" id="referencia" name="referencia" 
                                   placeholder="Referencia o número de transacción">
                        </div>
                        
                        <div class="form-group">
                            <label for="monto" class="font-weight-bold">Monto a Registrar (Bs): <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Bs</span>
                                </div>
                                <input type="number" class="form-control font-weight-bold" id="monto" name="monto" 
                                       step="0.01" min="0.01" required placeholder="0.00">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Comprobante / Capture (Opcional):</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="comprobante" name="comprobante" accept="image/jpeg,image/png,application/pdf">
                                <label class="custom-file-label" for="comprobante" id="lbl_comprobante">Seleccionar archivo...</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="observaciones" class="font-weight-bold">Observaciones / Notas:</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" 
                                      rows="2" placeholder="Notas internas o referencia bancaria..."></textarea>
                        </div>
                        
                        <button type="submit" name="registrar_pago" class="btn btn-success btn-block shadow-sm py-2 font-weight-bold">
                            <i class="fas fa-check-circle mr-1"></i> Confirmar y Registrar Pago
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Lista de Pagos con Filtros y Agrupación Fecha por Fecha -->
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white d-flex flex-wrap justify-content-between align-items-center py-2">
                    <h5 class="mb-0 font-weight-bold" style="font-size: 1.1rem;">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Reporte y Control de Pagos (Fecha por Fecha)
                    </h5>
                    <span class="badge badge-light p-2 font-weight-bold text-dark">
                        Total Aprobado Hoy: Bs <?= number_format($total_pagos_hoy, 2, ',', '.') ?>
                    </span>
                </div>
                
                <!-- Barra de Filtros Avanzada (Rango de Fechas, Estado, Concepto, Método) -->
                <div class="card-body p-3 bg-light border-bottom">
                    <form method="POST" action="" id="formFiltroPagos" class="mb-0">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <!-- Rango de Fechas y Botones Rápidos -->
                        <div class="row align-items-end mb-2">
                            <div class="col-6 col-sm-3 mb-2">
                                <label class="font-weight-bold small text-muted mb-1"><i class="fas fa-calendar-alt mr-1"></i> Desde:</label>
                                <input type="date" name="fecha_desde" id="filtro_fecha_desde" class="form-control form-control-sm font-weight-bold" value="<?= htmlspecialchars($filtro_fecha_desde) ?>" required>
                            </div>
                            <div class="col-6 col-sm-3 mb-2">
                                <label class="font-weight-bold small text-muted mb-1"><i class="fas fa-calendar-alt mr-1"></i> Hasta:</label>
                                <input type="date" name="fecha_hasta" id="filtro_fecha_hasta" class="form-control form-control-sm font-weight-bold" value="<?= htmlspecialchars($filtro_fecha_hasta) ?>" required>
                            </div>
                            <div class="col-12 col-sm-6 mb-2">
                                <label class="font-weight-bold small text-muted mb-1"><i class="fas fa-bolt mr-1"></i> Rango Rápido:</label>
                                <div class="btn-group btn-group-sm w-100" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetFecha('hoy')">Hoy</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetFecha('semana')">Semana</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetFecha('mes')">Mes</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetFecha('anio')">Año</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetFecha('todo')">Todo</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filtros Adicionales: Estado, Concepto, Método -->
                        <div class="row align-items-end mb-3">
                            <div class="col-12 col-sm-4 mb-2">
                                <label class="font-weight-bold small text-muted mb-1">Estado de Pago:</label>
                                <select name="filtro_status" class="form-control form-control-sm">
                                    <option value="" <?= empty($filtro_status) ? 'selected' : '' ?>>Todos los Estados</option>
                                    <option value="aprobado" <?= $filtro_status === 'aprobado' ? 'selected' : '' ?>>Aprobados (Verificados)</option>
                                    <option value="pendiente" <?= $filtro_status === 'pendiente' ? 'selected' : '' ?>>Pendientes de Verificación</option>
                                    <option value="rechazado" <?= $filtro_status === 'rechazado' ? 'selected' : '' ?>>Rechazados</option>
                                </select>
                            </div>
                            <div class="col-6 col-sm-4 mb-2">
                                <label class="font-weight-bold small text-muted mb-1">Concepto / Arancel:</label>
                                <select name="filtro_tipo" class="form-control form-control-sm">
                                    <option value="">Todos los Conceptos</option>
                                    <?php foreach ($tipos_pago as $tp): ?>
                                        <option value="<?= $tp['id'] ?>" <?= ($filtro_tipo == $tp['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tp['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-sm-4 mb-2">
                                <label class="font-weight-bold small text-muted mb-1">Forma de Pago:</label>
                                <select name="filtro_metodo" class="form-control form-control-sm">
                                    <option value="">Todas las Formas</option>
                                    <?php foreach ($metodos_pago_activos as $m): ?>
                                        <option value="<?= $m['codigo'] ?>" <?= ($filtro_metodo === $m['codigo']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($m['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Botones de Acción y Generación de Reportes -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm mb-2">
                                <button type="submit" name="filtrar_pagos" class="btn btn-primary font-weight-bold px-3">
                                    <i class="fas fa-filter mr-1"></i> Filtrar Pagos
                                </button>
                                <button type="submit" name="limpiar_filtros_pagos" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo mr-1"></i> Limpiar
                                </button>
                            </div>
                            <div class="btn-group btn-group-sm mb-2">
                                <button type="submit" formaction="reporte_pagos_pdf.php" formmethod="POST" formtarget="_blank" class="btn btn-danger font-weight-bold shadow-sm">
                                    <i class="fas fa-file-pdf mr-1"></i> Reporte PDF Fecha por Fecha
                                </button>
                                <button type="submit" formaction="reporte_pagos_excel.php" formmethod="POST" class="btn btn-success font-weight-bold shadow-sm">
                                    <i class="fas fa-file-excel mr-1"></i> Reporte Excel (.xlsx) Fecha por Fecha
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Resumen de Métricas del Período Filtrado -->
                <div class="p-3 bg-white border-bottom">
                    <div class="row text-center">
                        <div class="col-6 col-md-3 mb-2 mb-md-0 border-right">
                            <span class="text-muted small d-block font-weight-bold">PERÍODO:</span>
                            <span class="badge badge-light border text-dark font-weight-bold px-2 py-1">
                                <?= date('d/m/Y', strtotime($filtro_fecha_desde)) ?> - <?= date('d/m/Y', strtotime($filtro_fecha_hasta)) ?>
                            </span>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0 border-right">
                            <span class="text-muted small d-block font-weight-bold">APROBADO (RECAUDADO):</span>
                            <span class="text-success font-weight-bold" style="font-size: 1.05rem;">
                                Bs <?= number_format($stats_periodo['aprobados_monto'], 2, ',', '.') ?>
                            </span>
                            <small class="text-muted d-block">(<?= $stats_periodo['aprobados_count'] ?> pagos)</small>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0 border-right">
                            <span class="text-muted small d-block font-weight-bold">PENDIENTE:</span>
                            <span class="text-warning font-weight-bold" style="font-size: 1.05rem;">
                                Bs <?= number_format($stats_periodo['pendientes_monto'], 2, ',', '.') ?>
                            </span>
                            <small class="text-muted d-block">(<?= $stats_periodo['pendientes_count'] ?> pagos)</small>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="text-muted small d-block font-weight-bold">TOTAL OPERACIONES:</span>
                            <span class="text-primary font-weight-bold" style="font-size: 1.05rem;">
                                <?= $stats_periodo['total_transacciones'] ?>
                            </span>
                            <small class="text-muted d-block">Rechazados: <?= $stats_periodo['rechazados_count'] ?></small>
                        </div>
                    </div>
                </div>
                
                <?php
                // Agrupación de pagos fecha por fecha
                $dias_semana = [
                    'Sunday' => 'Domingo', 'Monday' => 'Lunes', 'Tuesday' => 'Martes', 
                    'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado'
                ];
                $pagos_por_fecha = [];
                if (!empty($todos_los_pagos)) {
                    foreach ($todos_los_pagos as $p) {
                        $f_efectiva = !empty($p['fecha_transaccion']) && $p['fecha_transaccion'] !== '0000-00-00'
                            ? $p['fecha_transaccion'] 
                            : date('Y-m-d', strtotime($p['fecha_pago']));
                        $pagos_por_fecha[$f_efectiva][] = $p;
                    }
                }
                ?>
                
                <div class="card-body p-2 p-sm-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>Fecha / Hora</th>
                                    <th>Estudiante</th>
                                    <th>Concepto</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pagos_por_fecha)): ?>
                                    <?php foreach ($pagos_por_fecha as $fecha_grupo => $pagos_del_dia): 
                                        $timestamp = strtotime($fecha_grupo);
                                        $dia_ingles = date('l', $timestamp);
                                        $nombre_dia = $dias_semana[$dia_ingles] ?? $dia_ingles;
                                        $subtotal_dia_aprobado = 0;
                                        $subtotal_dia_pendiente = 0;
                                        $count_dia_aprobado = 0;
                                        $count_dia_pendiente = 0;
                                        foreach ($pagos_del_dia as $pd) {
                                            if ($pd['status_pago'] === 'aprobado') {
                                                $subtotal_dia_aprobado += (float)$pd['monto'];
                                                $count_dia_aprobado++;
                                            } elseif ($pd['status_pago'] === 'pendiente') {
                                                $subtotal_dia_pendiente += (float)$pd['monto'];
                                                $count_dia_pendiente++;
                                            }
                                        }
                                    ?>
                                        <!-- Encabezado de la Fecha -->
                                        <tr class="table-primary font-weight-bold">
                                            <td colspan="6" class="py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fas fa-calendar-day mr-2 text-primary"></i>
                                                        FECHA: <?= strtoupper($nombre_dia) ?>, <?= date('d/m/Y', $timestamp) ?>
                                                    </span>
                                                    <span class="badge badge-light text-dark font-weight-bold px-2 py-1">
                                                        <?= count($pagos_del_dia) ?> <?= count($pagos_del_dia) === 1 ? 'operación' : 'operaciones' ?>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Filas de pagos correspondientes a esta fecha -->
                                        <?php foreach ($pagos_del_dia as $pago): ?>
                                            <tr>
                                                <td class="small text-center align-middle">
                                                    <div><?= date('d/m/Y', strtotime($pago['fecha_transaccion'] ?: $pago['fecha_pago'])) ?></div>
                                                    <small class="text-muted"><?= date('h:i A', strtotime($pago['fecha_pago'])) ?></small>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold"><?= htmlspecialchars($pago['nombre_estudiante']) ?></div>
                                                    <small class="text-muted">C.I: <?= htmlspecialchars($pago['cedula']) ?></small>
                                                </td>
                                                <td class="align-middle">
                                                    <strong><?= htmlspecialchars($pago['nombre_tipo_pago']) ?></strong>
                                                    <div class="small text-muted">
                                                        <?= htmlspecialchars($pago['metodo_pago'] ?: 'Pago') ?>
                                                        <?php if (!empty($pago['referencia'])): ?>
                                                            | Ref: <strong><?= htmlspecialchars($pago['referencia']) ?></strong>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if (!empty($pago['nombre_banco_destino'])): ?>
                                                        <small class="text-info d-block"><i class="fas fa-landmark mr-1"></i><?= htmlspecialchars($pago['nombre_banco_destino']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right font-weight-bold text-success align-middle">
                                                    Bs <?= number_format($pago['monto'], 2, ',', '.') ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php if ($pago['status_pago'] === 'aprobado'): ?>
                                                        <span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i>Aprobado</span>
                                                    <?php elseif ($pago['status_pago'] === 'rechazado'): ?>
                                                        <span class="badge badge-danger p-2" title="<?= htmlspecialchars($pago['motivo_rechazo'] ?? '') ?>">
                                                            <i class="fas fa-times-circle mr-1"></i>Rechazado
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning p-2 text-dark"><i class="fas fa-clock mr-1"></i>Pendiente</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group btn-group-sm">
                                                        <!-- Ver Detalles Completos -->
                                                        <button type="button" class="btn btn-outline-primary btn-ver-detalles-pago" 
                                                                data-pago='<?= htmlspecialchars(json_encode($pago), ENT_QUOTES, 'UTF-8') ?>'
                                                                title="Ver Detalles Completos del Pago">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        
                                                        <!-- Ver Comprobante directo si existe -->
                                                        <?php if (!empty($pago['comprobante'])): ?>
                                                            <button type="button" class="btn btn-outline-info btn-ver-capture" 
                                                                    data-url="../<?= htmlspecialchars($pago['comprobante']) ?>" 
                                                                    data-titulo="Capture: <?= htmlspecialchars($pago['nombre_estudiante']) ?> (Ref: <?= htmlspecialchars($pago['referencia']) ?>)"
                                                                    title="Ver Capture">
                                                                <i class="fas fa-image"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        
                                                        <!-- Acciones rápidas para pagos pendientes -->
                                                        <?php if ($pago['status_pago'] === 'pendiente'): ?>
                                                            <form method="POST" action="" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                <input type="hidden" name="pago_id" value="<?= $pago['id'] ?>">
                                                                <button type="submit" name="aprobar_pago" class="btn btn-outline-success" title="Aprobar Pago">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                            
                                                            <button type="button" class="btn btn-outline-warning btn-abrir-rechazo-pago" 
                                                                    data-id="<?= $pago['id'] ?>"
                                                                    data-estudiante="<?= htmlspecialchars($pago['nombre_estudiante']) ?>"
                                                                    title="Rechazar Pago">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        
                                                        <!-- Eliminar con Modal -->
                                                        <button type="button" class="btn btn-outline-danger btn-abrir-eliminar-pago" 
                                                                data-id="<?= $pago['id'] ?>" 
                                                                data-estudiante="<?= htmlspecialchars($pago['nombre_estudiante']) ?>" 
                                                                data-monto="Bs <?= number_format($pago['monto'], 2, ',', '.') ?>" 
                                                                title="Eliminar">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <!-- Subtotal de la fecha -->
                                        <tr class="bg-light font-weight-bold">
                                            <td colspan="3" class="text-right text-muted small py-2">
                                                Subtotal del Día (<?= date('d/m/Y', $timestamp) ?>):
                                            </td>
                                            <td class="text-right text-dark py-2">
                                                Bs <?= number_format($subtotal_dia_aprobado, 2, ',', '.') ?>
                                            </td>
                                            <td colspan="2" class="small py-2 text-muted">
                                                <span class="text-success"><i class="fas fa-check-circle mr-1"></i>Bs <?= number_format($subtotal_dia_aprobado, 2, ',', '.') ?> (<?= $count_dia_aprobado ?> apr.)</span>
                                                <?php if ($count_dia_pendiente > 0): ?>
                                                    | <span class="text-warning"><i class="fas fa-clock mr-1"></i>Bs <?= number_format($subtotal_dia_pendiente, 2, ',', '.') ?> (<?= $count_dia_pendiente ?> pend.)</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                                            No se encontraron pagos con los filtros seleccionados.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="thead-dark font-weight-bold text-center">
                                <tr>
                                    <td colspan="3" class="text-right text-white py-2">
                                        TOTAL GENERAL RECAUDADO (APROBADO):
                                    </td>
                                    <td class="text-right text-warning py-2" style="font-size: 1.05rem;">
                                        Bs <?= number_format($stats_periodo['aprobados_monto'], 2, ',', '.') ?>
                                    </td>
                                    <td colspan="2" class="text-white small text-center py-2">
                                        <?= $stats_periodo['aprobados_count'] ?> aprobados / <?= $stats_periodo['total_transacciones'] ?> operaciones
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Modal Completo de Detalles del Pago -->
<div class="modal fade" id="modalDetallesPago" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="modal-title mb-0" id="detallesPagoModalTitulo">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Detalles del Pago #<span id="det_pago_id"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-3 p-md-4 bg-light">
                <!-- Tarjeta de Estado y Monto -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center bg-white rounded">
                        <div>
                            <span class="text-muted small d-block font-weight-bold">ESTADO DE LA DECLARACIÓN:</span>
                            <span id="det_badge_status" class="badge badge-warning p-2 font-weight-bold" style="font-size: 0.95rem;">Pendiente</span>
                        </div>
                        <div class="mt-2 mt-sm-0 text-sm-right">
                            <span class="text-muted small d-block font-weight-bold">MONTO REGISTRADO:</span>
                            <span id="det_monto" class="text-success font-weight-bold" style="font-size: 1.35rem;">Bs 0,00</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Datos del Estudiante -->
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white font-weight-bold text-dark py-2 border-bottom">
                                <i class="fas fa-user-graduate text-primary mr-1"></i> Información del Estudiante
                            </div>
                            <div class="card-body p-3 small">
                                <p class="mb-2"><strong>Nombre Completo:</strong> <span id="det_est_nombre" class="text-dark"></span></p>
                                <p class="mb-2"><strong>Cédula / Usuario:</strong> <span id="det_est_cedula" class="badge badge-light border text-dark"></span></p>
                                <p class="mb-2"><strong>Carrera / PNF:</strong> <span id="det_est_carrera" class="text-muted"></span></p>
                                <p class="mb-0"><strong>Correo Electrónico:</strong> <span id="det_est_email" class="text-muted"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Datos de la Transacción -->
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white font-weight-bold text-dark py-2 border-bottom">
                                <i class="fas fa-receipt text-success mr-1"></i> Datos de la Transacción
                            </div>
                            <div class="card-body p-3 small">
                                <p class="mb-2"><strong>Concepto / Arancel:</strong> <span id="det_concepto" class="font-weight-bold text-primary"></span></p>
                                <p class="mb-2"><strong>Forma de Pago:</strong> <span id="det_metodo" class="badge badge-info"></span></p>
                                <p class="mb-2"><strong>N° de Referencia:</strong> <span id="det_referencia" class="font-weight-bold text-dark" style="font-family: monospace; font-size: 1rem;"></span></p>
                                <p class="mb-2"><strong>Fecha de Pago:</strong> <span id="det_fecha_trans"></span></p>
                                <p class="mb-0"><strong>Fecha de Registro:</strong> <span id="det_fecha_registro" class="text-muted"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Bancos Origen y Destino -->
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white font-weight-bold text-dark py-2 border-bottom">
                                <i class="fas fa-university text-info mr-1"></i> Entidades Bancarias
                            </div>
                            <div class="card-body p-3 small">
                                <p class="mb-2"><strong>Banco Emisor (Origen):</strong> <span id="det_banco_origen" class="text-dark font-weight-bold"></span></p>
                                <p class="mb-2"><strong>Banco Receptor (UPTPC):</strong> <span id="det_banco_destino" class="text-dark font-weight-bold"></span></p>
                                <div id="det_banco_destino_datos" class="p-2 bg-light rounded border text-muted small mb-0"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Auditoría y Notas -->
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white font-weight-bold text-dark py-2 border-bottom">
                                <i class="fas fa-clipboard-check text-secondary mr-1"></i> Auditoría y Notas
                            </div>
                            <div class="card-body p-3 small">
                                <p class="mb-2"><strong>Registrado Por:</strong> <span id="det_registrador" class="text-muted"></span></p>
                                <div id="det_motivo_rechazo_box" style="display: none;" class="alert alert-danger p-2 mb-2">
                                    <strong>Motivo de Rechazo:</strong> <span id="det_motivo_rechazo"></span>
                                </div>
                                <p class="mb-0"><strong>Observaciones / Notas:</strong> <span id="det_observaciones" class="text-muted"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Comprobante / Capture -->
                <div class="card border-0 shadow-sm" id="det_capture_card" style="display: none;">
                    <div class="card-header bg-white font-weight-bold text-dark py-2 border-bottom d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-camera text-primary mr-1"></i> Comprobante / Capture Adjunto</span>
                        <a href="#" id="det_btn_abrir_capture" target="_blank" class="btn btn-sm btn-outline-primary font-weight-bold">
                            <i class="fas fa-external-link-alt mr-1"></i> Abrir en Pantalla Completa
                        </a>
                    </div>
                    <div class="card-body p-3 text-center bg-white">
                        <img id="det_img_capture" src="" class="img-fluid rounded border shadow-sm" alt="Comprobante" style="max-height: 450px; display: none;">
                        <iframe id="det_pdf_capture" src="" style="width: 100%; height: 450px; border: none; display: none;"></iframe>
                    </div>
                </div>
            </div>

            <!-- Footer con acciones condicionales -->
            <div class="modal-footer bg-white d-flex justify-content-between align-items-center">
                <div id="det_acciones_pendientes" style="display: none;">
                    <form method="POST" action="" class="d-inline mr-2">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="pago_id" id="det_form_aprobar_id" value="0">
                        <button type="submit" name="aprobar_pago" class="btn btn-success font-weight-bold">
                            <i class="fas fa-check-circle mr-1"></i> Aprobar Pago
                        </button>
                    </form>
                    <button type="button" class="btn btn-warning text-dark font-weight-bold" id="det_btn_rechazar_modal">
                        <i class="fas fa-times-circle mr-1"></i> Rechazar Pago
                    </button>
                </div>
                <div class="ml-auto">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Centralizado para Rechazar Pago -->
<div class="modal fade" id="modalRechazarPagoCentral" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="pago_id" id="rechazar_pago_id_input" value="0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle mr-1"></i> Rechazar Declaración de Pago</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body text-left p-4">
                    <p class="mb-2">Indique el motivo por el cual se rechaza el pago del estudiante <strong id="rechazar_pago_estudiante_nombre"></strong>:</p>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Motivo del Rechazo: <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="motivo_rechazo" id="rechazar_motivo_textarea" rows="3" required placeholder="Ej: Comprobante ilegible, monto no recibido en cuenta institucional, referencia duplicada..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="rechazar_pago" class="btn btn-danger font-weight-bold">
                        <i class="fas fa-times-circle mr-1"></i> Confirmar Rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Ver Capture Simple -->
<div class="modal fade" id="modalVerCaptureAdmin" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCaptureTituloAdmin"><i class="fas fa-image mr-2"></i>Comprobante de Pago</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-3 text-center">
                <img id="imgCaptureModalAdmin" src="" class="img-fluid rounded" alt="Capture de pago" style="max-height: 75vh; display: none;">
                <iframe id="pdfCaptureModalAdmin" src="" style="width: 100%; height: 75vh; border: none; display: none;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <a href="#" id="btnDescargarCaptureAdmin" target="_blank" class="btn btn-primary"><i class="fas fa-download mr-1"></i> Abrir / Descargar</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para selección rápida de rangos de fechas
function setPresetFecha(tipo) {
    const ahora = new Date();
    const pad = (n) => n.toString().padStart(2, '0');
    const toISO = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    
    let desde = '';
    let hasta = toISO(ahora);
    
    if (tipo === 'hoy') {
        desde = toISO(ahora);
        hasta = toISO(ahora);
    } else if (tipo === 'semana') {
        const d = new Date(ahora);
        const day = d.getDay(); // 0 domingo
        const diff = d.getDate() - day + (day === 0 ? -6 : 1); // lunes
        d.setDate(diff);
        desde = toISO(d);
        hasta = toISO(ahora);
    } else if (tipo === 'mes') {
        const firstDay = new Date(ahora.getFullYear(), ahora.getMonth(), 1);
        desde = toISO(firstDay);
        hasta = toISO(ahora);
    } else if (tipo === 'anio') {
        desde = `${ahora.getFullYear()}-01-01`;
        hasta = toISO(ahora);
    } else if (tipo === 'todo') {
        desde = '2020-01-01';
        hasta = toISO(ahora);
    }
    
    const inputDesde = document.getElementById('filtro_fecha_desde');
    const inputHasta = document.getElementById('filtro_fecha_hasta');
    if (inputDesde) inputDesde.value = desde;
    if (inputHasta) inputHasta.value = hasta;
}

document.addEventListener('DOMContentLoaded', function() {
    const inputBuscador = document.getElementById('buscador_estudiante_pago');
    const sugerenciasContainer = document.getElementById('sugerencias_estudiantes_pago');
    const spinnerBusqueda = document.getElementById('spinner_busqueda_est');
    const cardEstudiante = document.getElementById('card_estudiante_seleccionado');
    const formPago = document.getElementById('formRegistroPago');
    const inputEstudianteId = document.getElementById('estudiante_id');
    const lblNombre = document.getElementById('lbl_est_nombre');
    const lblCedula = document.getElementById('lbl_est_cedula');
    const lblCarrera = document.getElementById('lbl_est_carrera');
    const btnLimpiar = document.getElementById('btn_limpiar_estudiante');
    const selectTipoPago = document.getElementById('tipo_pago');
    const otroGroup = document.getElementById('otro_concepto_group');
    const montoInput = document.getElementById('monto');
    const fileComprobante = document.getElementById('comprobante');
    const lblFile = document.getElementById('lbl_comprobante');
    let debounceTimer = null;

    function escapeHtml(text) {
        if (!text) return '';
        const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    if (fileComprobante && lblFile) {
        fileComprobante.addEventListener('change', function() {
            if (this.files[0]) {
                lblFile.textContent = this.files[0].name;
            } else {
                lblFile.textContent = 'Seleccionar archivo...';
            }
        });
    }

    // Búsqueda AJAX reactiva con debounce
    function buscarEstudiantes() {
        const termino = (inputBuscador.value || '').trim();
        clearTimeout(debounceTimer);

        if (termino.length < 1) {
            sugerenciasContainer.style.display = 'none';
            sugerenciasContainer.innerHTML = '';
            if (spinnerBusqueda) spinnerBusqueda.style.display = 'none';
            return;
        }

        if (spinnerBusqueda) spinnerBusqueda.style.display = 'flex';

        debounceTimer = setTimeout(() => {
            const fd = new FormData();
            fd.append('ajax_buscar_estudiantes_pagos', '1');
            fd.append('termino', termino);

            fetch('', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (spinnerBusqueda) spinnerBusqueda.style.display = 'none';
                    sugerenciasContainer.innerHTML = '';

                    if (data.success && Array.isArray(data.estudiantes) && data.estudiantes.length > 0) {
                        data.estudiantes.forEach(est => {
                            const item = document.createElement('a');
                            item.href = 'javascript:void(0);';
                            item.className = 'list-group-item list-group-item-action p-2 d-flex justify-content-between align-items-center border-bottom text-decoration-none';
                            item.style.cursor = 'pointer';
                            item.innerHTML = `
                                <div>
                                    <strong class="text-dark"><i class="fas fa-user-graduate mr-1 text-primary"></i> ${escapeHtml(est.nombre)}</strong>
                                    <div class="small text-muted">
                                        <span>C.I: ${escapeHtml(est.cedula || est.idusuario)}</span> | 
                                        <span>${escapeHtml(est.nombre_carrera)}</span>
                                    </div>
                                </div>
                                <span class="btn btn-sm btn-success py-1 px-2"><i class="fas fa-check mr-1"></i>Elegir</span>
                            `;

                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                seleccionarEstudiante(est);
                            });

                            sugerenciasContainer.appendChild(item);
                        });
                        sugerenciasContainer.style.display = 'block';
                    } else {
                        sugerenciasContainer.innerHTML = `
                            <div class="list-group-item p-3 text-center text-muted">
                                <i class="fas fa-user-slash mr-1"></i> No se encontraron estudiantes activos.
                            </div>
                        `;
                        sugerenciasContainer.style.display = 'block';
                    }
                })
                .catch(err => {
                    if (spinnerBusqueda) spinnerBusqueda.style.display = 'none';
                    console.error("Error buscando estudiante:", err);
                });
        }, 200);
    }

    function seleccionarEstudiante(est) {
        inputEstudianteId.value = est.id;
        lblNombre.textContent = est.nombre;
        lblCedula.textContent = est.cedula || est.idusuario;
        lblCarrera.textContent = est.nombre_carrera;
        
        cardEstudiante.style.display = 'block';
        formPago.style.display = 'block';
        sugerenciasContainer.style.display = 'none';
        sugerenciasContainer.innerHTML = '';
        inputBuscador.value = '';
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function() {
            inputEstudianteId.value = '';
            cardEstudiante.style.display = 'none';
            formPago.style.display = 'none';
            inputBuscador.value = '';
            inputBuscador.focus();
        });
    }

    if (inputBuscador) {
        inputBuscador.addEventListener('input', buscarEstudiantes);
        inputBuscador.addEventListener('focus', buscarEstudiantes);
    }

    document.addEventListener('click', function(e) {
        if (inputBuscador && !inputBuscador.contains(e.target) && !sugerenciasContainer.contains(e.target)) {
            sugerenciasContainer.style.display = 'none';
        }
    });

    if (selectTipoPago) {
        selectTipoPago.addEventListener('change', function() {
            if (this.value === 'otro' || this.value === '0') {
                if (otroGroup) otroGroup.style.display = 'block';
            } else {
                if (otroGroup) otroGroup.style.display = 'none';
            }

            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption) {
                const precio = selectedOption.getAttribute('data-precio');
                if (precio !== null && precio !== undefined && precio !== '' && parseFloat(precio) > 0) {
                    if (montoInput) montoInput.value = parseFloat(precio).toFixed(2);
                }
            }
        });
    }

    // Datos de bancos activos por método
    const bancosPagoMovil     = <?= json_encode(array_values($bancos_pm)) ?>;
    const bancosTransferencia = <?= json_encode(array_values($bancos_trans)) ?>;
    const bancosTodos         = <?= json_encode(array_values($bancos_todos)) ?>;
    const selectMetodoPago    = document.getElementById('metodo_pago');
    const selectBancoDestino  = document.getElementById('banco_destino_id');

    function actualizarBancosDisponibles() {
        if (!selectMetodoPago || !selectBancoDestino) return;
        const selectedOpt = selectMetodoPago.options[selectMetodoPago.selectedIndex];
        const codigo = selectedOpt ? selectedOpt.getAttribute('data-codigo') : '';
        const requiereBanco = selectedOpt ? selectedOpt.getAttribute('data-requiere-banco') : '1';
        
        selectBancoDestino.innerHTML = '<option value="">-- Seleccionar Banco Receptor --</option>';

        if (requiereBanco === '0') {
            const optDirecto = document.createElement('option');
            optDirecto.value = "0";
            optDirecto.textContent = "Directo / No requiere cuenta bancaria institucional";
            optDirecto.selected = true;
            selectBancoDestino.appendChild(optDirecto);
            return;
        }

        let bancosMostrar = [];
        if (codigo === 'pago_movil') {
            bancosMostrar = bancosPagoMovil;
        } else if (codigo === 'transferencia') {
            bancosMostrar = bancosTransferencia;
        } else {
            bancosMostrar = bancosTodos;
        }

        if (bancosMostrar.length === 0) {
            const optVacio = document.createElement('option');
            optVacio.value = "";
            optVacio.textContent = "(No hay cuentas habilitadas para este método)";
            optVacio.disabled = true;
            optVacio.selected = true;
            selectBancoDestino.appendChild(optVacio);
        } else {
            bancosMostrar.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                let detalle = '';
                if (codigo === 'pago_movil' && b.telefono_pago_movil) {
                    detalle = ` (PM: ${b.telefono_pago_movil})`;
                } else if (codigo === 'transferencia' && b.numero_cuenta) {
                    detalle = ` (Cta: ${b.numero_cuenta.substring(0, 10)}...)`;
                }
                opt.textContent = `${b.nombre_banco}${detalle}`;
                selectBancoDestino.appendChild(opt);
            });
        }
    }

    if (selectMetodoPago) {
        selectMetodoPago.addEventListener('change', actualizarBancosDisponibles);
        actualizarBancosDisponibles();
    }

    // Modal visor de comprobantes
    document.querySelectorAll('.btn-ver-capture').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const titulo = this.getAttribute('data-titulo');
            
            $('#modalCaptureTituloAdmin').text(titulo);
            $('#btnDescargarCaptureAdmin').attr('href', url);

            if (url.toLowerCase().endsWith('.pdf')) {
                $('#imgCaptureModalAdmin').hide();
                $('#pdfCaptureModalAdmin').attr('src', url).show();
            } else {
                $('#pdfCaptureModalAdmin').hide();
                $('#imgCaptureModalAdmin').attr('src', url).show();
            }

            $('#modalVerCaptureAdmin').modal('show');
        });
    });

    // Modal de Detalles Completos del Pago
    document.querySelectorAll('.btn-ver-detalles-pago').forEach(btn => {
        btn.addEventListener('click', function() {
            const rawData = this.getAttribute('data-pago');
            if (!rawData) return;
            
            try {
                const p = JSON.parse(rawData);
                
                // ID y Título
                $('#det_pago_id').text(p.id);
                
                // Status Badge
                const badgeStatus = $('#det_badge_status');
                badgeStatus.removeClass('badge-warning badge-success badge-danger text-dark');
                if (p.status_pago === 'aprobado') {
                    badgeStatus.addClass('badge-success').html('<i class="fas fa-check-circle mr-1"></i> Aprobado');
                } else if (p.status_pago === 'rechazado') {
                    badgeStatus.addClass('badge-danger').html('<i class="fas fa-times-circle mr-1"></i> Rechazado');
                } else {
                    badgeStatus.addClass('badge-warning text-dark').html('<i class="fas fa-clock mr-1"></i> Pendiente');
                }
                
                // Monto
                const montoNum = parseFloat(p.monto || 0);
                $('#det_monto').text('Bs ' + montoNum.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                
                // Estudiante
                $('#det_est_nombre').text(p.nombre_estudiante || 'N/A');
                $('#det_est_cedula').text('C.I: ' + (p.cedula || 'N/A'));
                $('#det_est_carrera').text(p.nombre_carrera || 'Sin Carrera');
                $('#det_est_email').text(p.email_estudiante || 'No registrado');
                
                // Transacción
                $('#det_concepto').text(p.nombre_tipo_pago || 'Arancel');
                $('#det_metodo').text(p.metodo_pago || 'No especificado');
                $('#det_referencia').text(p.referencia || '(Sin Referencia)');
                $('#det_fecha_trans').text(p.fecha_transaccion ? p.fecha_transaccion : 'N/A');
                $('#det_fecha_registro').text(p.fecha_pago || 'N/A');
                
                // Bancos
                $('#det_banco_origen').text(p.banco_origen || '(No especificado)');
                $('#det_banco_destino').text(p.nombre_banco_destino || 'Directo / Institucional');
                
                let bancoInfoHtml = '';
                if (p.cuenta_banco_destino) {
                    bancoInfoHtml += '<div><strong>Cuenta UPTPC:</strong> ' + escapeHtml(p.cuenta_banco_destino) + '</div>';
                }
                if (p.telefono_banco_destino) {
                    bancoInfoHtml += '<div><strong>Teléfono Pago Móvil:</strong> ' + escapeHtml(p.telefono_banco_destino) + '</div>';
                }
                if (p.titular_banco_destino) {
                    bancoInfoHtml += '<div><strong>Titular:</strong> ' + escapeHtml(p.titular_banco_destino) + '</div>';
                }
                if (p.rif_banco_destino) {
                    bancoInfoHtml += '<div><strong>RIF:</strong> ' + escapeHtml(p.rif_banco_destino) + '</div>';
                }
                $('#det_banco_destino_datos').html(bancoInfoHtml || '<em>Información estándar institucional</em>');
                
                // Auditoría
                $('#det_registrador').text(p.nombre_registrador || 'Estudiante / Portal Web');
                $('#det_observaciones').text(p.observaciones || '(Ninguna)');
                
                if (p.status_pago === 'rechazado' && p.motivo_rechazo) {
                    $('#det_motivo_rechazo').text(p.motivo_rechazo);
                    $('#det_motivo_rechazo_box').show();
                } else {
                    $('#det_motivo_rechazo_box').hide();
                }
                
                // Capture / Comprobante
                if (p.comprobante) {
                    const compUrl = '../' + p.comprobante;
                    $('#det_btn_abrir_capture').attr('href', compUrl);
                    if (compUrl.toLowerCase().endsWith('.pdf')) {
                        $('#det_img_capture').hide();
                        $('#det_pdf_capture').attr('src', compUrl).show();
                    } else {
                        $('#det_pdf_capture').hide();
                        $('#det_img_capture').attr('src', compUrl).show();
                    }
                    $('#det_capture_card').show();
                } else {
                    $('#det_capture_card').hide();
                }
                
                // Acciones pendientes dentro del modal
                if (p.status_pago === 'pendiente') {
                    $('#det_form_aprobar_id').val(p.id);
                    $('#det_acciones_pendientes').show();
                    
                    $('#det_btn_rechazar_modal').off('click').on('click', function() {
                        $('#modalDetallesPago').modal('hide');
                        $('#rechazar_pago_id_input').val(p.id);
                        $('#rechazar_pago_estudiante_nombre').text(p.nombre_estudiante);
                        $('#rechazar_motivo_textarea').val('');
                        $('#modalRechazarPagoCentral').modal('show');
                    });
                } else {
                    $('#det_acciones_pendientes').hide();
                }
                
                $('#modalDetallesPago').modal('show');
            } catch(e) {
                console.error("Error al parsear datos del pago", e);
            }
        });
    });

    // Botón rechazar individual desde tabla
    document.querySelectorAll('.btn-abrir-rechazo-pago').forEach(btn => {
        btn.addEventListener('click', function() {
            const pagoId = this.getAttribute('data-id');
            const estNombre = this.getAttribute('data-estudiante');
            
            $('#rechazar_pago_id_input').val(pagoId);
            $('#rechazar_pago_estudiante_nombre').text(estNombre);
            $('#rechazar_motivo_textarea').val('');
            $('#modalRechazarPagoCentral').modal('show');
        });
    });

    // Modal confirmación de eliminación de pago
    document.querySelectorAll('.btn-abrir-eliminar-pago').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('eliminar_pago_id_input').value = this.getAttribute('data-id');
            document.getElementById('eliminar_pago_info_estudiante').textContent = this.getAttribute('data-estudiante');
            document.getElementById('eliminar_pago_info_monto').textContent = this.getAttribute('data-monto');
            $('#modalEliminarPago').modal('show');
        });
    });
});
</script>

<!-- Modal Confirmación Eliminación de Pago -->
<div class="modal fade" id="modalEliminarPago" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="eliminar_pago_id" id="eliminar_pago_id_input" value="0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-trash-alt mr-2"></i>Eliminar Registro de Pago</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <p class="mb-2">¿Está seguro de que desea eliminar este registro de pago del sistema?</p>
                    <div class="alert alert-warning text-left mb-2">
                        <div><strong>Estudiante:</strong> <span id="eliminar_pago_info_estudiante"></span></div>
                        <div><strong>Monto:</strong> <span id="eliminar_pago_info_monto" class="text-success font-weight-bold"></span></div>
                    </div>
                    <small class="text-muted d-block">Esta acción eliminará el registro y no se puede deshacer.</small>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger font-weight-bold">
                        <i class="fas fa-trash-alt mr-1"></i> Sí, Eliminar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>