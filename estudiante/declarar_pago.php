<?php
require_once('../funciones/functions.php');

// Verificar autenticación y rol de estudiante
if (!isLoggedIn() || !isEstudiante()) {
    $_SESSION['msg'] = "Debes iniciar sesión como estudiante para acceder";
    header('location: ../login.php');
    exit();
}

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

$current_user_id = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0;

// Asegurar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje_exito = '';
$mensaje_error = '';

// Procesar Formulario de Declaración de Pago
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['declarar_pago'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $mensaje_error = "Error de validación de seguridad (CSRF). Por favor recargue la página.";
    } else {
        $tipo_pago        = trim($_POST['tipo_pago'] ?? '');
        $otro_concepto    = trim($_POST['otro_concepto'] ?? '');
        $monto            = floatval($_POST['monto'] ?? 0);
        $metodo_pago      = trim($_POST['metodo_pago'] ?? 'Pago Movil');
        $banco_destino_id = intval($_POST['banco_destino_id'] ?? 0);
        $banco_origen     = trim($_POST['banco_origen'] ?? '');
        if ($banco_origen === 'Otro' && !empty($_POST['otro_banco_origen'])) {
            $banco_origen = trim($_POST['otro_banco_origen']);
        }
        $fecha_transaccion = trim($_POST['fecha_transaccion'] ?? date('Y-m-d'));
        $referencia       = trim($_POST['referencia'] ?? '');
        $observaciones    = trim($_POST['observaciones'] ?? '');
        $archivo_capture  = $_FILES['comprobante'] ?? null;
        
        $res = declararPagoEstudiante(
            $current_user_id, $tipo_pago, $otro_concepto, $monto, $metodo_pago,
            $banco_destino_id, $banco_origen, $fecha_transaccion, $referencia,
            $archivo_capture, $observaciones
        );
        
        if ($res['success']) {
            $mensaje_exito = $res['message'];
        } else {
            $mensaje_error = $res['message'];
        }
    }
}

// Consultas para la vista
$tipos_pago_activos   = obtenerTiposPago(true);
$metodos_pago_activos = obtenerMetodosPago(true);
$bancos_activos       = obtenerBancos(true);
$mis_pagos            = obtenerPagosEstudiante($current_user_id);

$bancos_pm    = obtenerBancos(true, 'pago_movil');
$bancos_trans = obtenerBancos(true, 'transferencia');

$titulopag = "Declarar Pagos y Aranceles";
include("includes/head.php");
?>

<style>
.bank-info-box {
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
    overflow: hidden;
}
.bank-info-box:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}
.copy-pill {
    cursor: pointer;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
    padding: 5px 10px;
    transition: background 0.15s ease;
}
.copy-pill:hover {
    background: #e2e8f0;
}
</style>

<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 text-dark font-weight-bold" style="font-size: 1.5rem;">
                <i class="fas fa-money-bill-wave text-success mr-2"></i>Declaración de Pagos y Aranceles
            </h2>
            <p class="text-muted small mb-0">Consulte las cuentas oficiales para pagar y reporte sus comprobantes para su oportuna validación.</p>
        </div>
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
    
    <!-- Sección de Cuentas Institucionales con Pestañas -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <h6 class="mb-2 mb-md-0 font-weight-bold text-primary">
                    <i class="fas fa-university mr-2"></i>Cuentas Bancarias Oficiales de la UPTPC (Para Realizar Pagos)
                </h6>
                <ul class="nav nav-pills" id="pills-bank-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active py-1 px-3 small font-weight-bold" id="pills-trans-tab" data-toggle="pill" href="#pills-trans" role="tab">
                            <i class="fas fa-university mr-1 text-primary"></i> Transferencias Bancarias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-1 px-3 small font-weight-bold" id="pills-pm-tab" data-toggle="pill" href="#pills-pm" role="tab">
                            <i class="fas fa-mobile-alt mr-1 text-success"></i> Pago Móvil
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body p-3 bg-light">
            <div class="tab-content" id="pills-bank-tabContent">
                <!-- Pestaña Transferencias (Predeterminada) -->
                <div class="tab-pane fade show active" id="pills-trans" role="tabpanel">
                    <div class="row">
                        <?php if (!empty($bancos_trans)): ?>
                            <?php foreach ($bancos_trans as $b): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="bank-info-box p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="font-weight-bold text-primary"><i class="fas fa-university mr-1"></i><?= htmlspecialchars($b['nombre_banco']) ?></span>
                                            <span class="badge badge-info"><?= htmlspecialchars($b['tipo_cuenta'] ?: 'Corriente') ?></span>
                                        </div>
                                        <div class="copy-pill d-flex justify-content-between align-items-center mb-2" onclick="copiarTexto('<?= htmlspecialchars($b['numero_cuenta']) ?>', this)">
                                            <span class="font-weight-bold text-primary small" style="letter-spacing: 0.5px;"><?= htmlspecialchars($b['numero_cuenta']) ?></span>
                                            <i class="fas fa-copy text-muted small" title="Copiar cuenta"></i>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="copy-pill d-flex justify-content-between align-items-center" onclick="copiarTexto('<?= htmlspecialchars($b['rif_cedula'] ?: 'G-20005608-8') ?>', this)">
                                                    <span class="small text-dark"><strong>RIF:</strong> <?= htmlspecialchars($b['rif_cedula'] ?: 'G-20005608-8') ?></span>
                                                    <i class="fas fa-copy text-muted small"></i>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-1 small text-truncate" title="<?= htmlspecialchars($b['titular'] ?: 'UPTPC') ?>">
                                                    <strong>Titular:</strong> <?= htmlspecialchars($b['titular'] ?: 'UPTPC') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center text-muted py-2 small">No hay cuentas bancarias para transferencias disponibles en este momento.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pestaña Pago Móvil -->
                <div class="tab-pane fade" id="pills-pm" role="tabpanel">
                    <div class="row">
                        <?php if (!empty($bancos_pm)): ?>
                            <?php foreach ($bancos_pm as $b): ?>
                                <div class="col-md-4 mb-2">
                                    <div class="bank-info-box p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="font-weight-bold text-primary"><i class="fas fa-landmark mr-1"></i><?= htmlspecialchars($b['nombre_banco']) ?></span>
                                            <span class="badge badge-success">Pago Móvil</span>
                                        </div>
                                        <div class="copy-pill d-flex justify-content-between align-items-center mb-2" onclick="copiarTexto('<?= htmlspecialchars($b['telefono_pago_movil']) ?>', this)">
                                            <span class="small font-weight-bold text-success"><i class="fas fa-mobile-alt mr-1"></i><?= htmlspecialchars($b['telefono_pago_movil']) ?></span>
                                            <i class="fas fa-copy text-muted small"></i>
                                        </div>
                                        <div class="copy-pill d-flex justify-content-between align-items-center" onclick="copiarTexto('<?= htmlspecialchars($b['rif_cedula'] ?: 'G-20005608-8') ?>', this)">
                                            <span class="small text-dark"><strong>RIF:</strong> <?= htmlspecialchars($b['rif_cedula'] ?: 'G-20005608-8') ?></span>
                                            <i class="fas fa-copy text-muted small"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center text-muted py-2 small">No hay datos de Pago Móvil disponibles en este momento.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Formulario para declarar pago -->
        <div class="col-12 col-lg-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-file-invoice-dollar mr-1"></i> Reportar Nuevo Pago</h5>
                </div>
                <div class="card-body p-3">
                    <form method="POST" action="" enctype="multipart/form-data" id="formDeclararPago">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <!-- Concepto de Pago -->
                        <div class="form-group">
                            <label for="tipo_pago" class="font-weight-bold">Concepto / Arancel: <span class="text-danger">*</span></label>
                            <select class="custom-select" id="tipo_pago" name="tipo_pago" required>
                                <option value="" data-precio="">-- Seleccione el Arancel --</option>
                                <?php foreach ($tipos_pago_activos as $tp): ?>
                                    <option value="<?= $tp['id'] ?>" data-precio="<?= htmlspecialchars($tp['precio'] ?? '0.00') ?>">
                                        <?= htmlspecialchars($tp['tipopago']) ?> <?php if (isset($tp['precio']) && (float)$tp['precio'] > 0): ?>(Bs <?= number_format($tp['precio'], 2, ',', '.') ?>)<?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="otro" data-precio="0.00">Otro concepto específico</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="otro_concepto_group" style="display: none;">
                            <label for="otro_concepto" class="font-weight-bold">Especifique el Concepto:</label>
                            <input type="text" class="form-control" id="otro_concepto" name="otro_concepto" 
                                   placeholder="Ej: Reimpresión de carnet, constancia especial...">
                        </div>
                        
                        <!-- Método de Pago -->
                        <div class="form-group">
                            <label for="metodo_pago" class="font-weight-bold">Método de Pago: <span class="text-danger">*</span></label>
                            <select class="custom-select" id="metodo_pago" name="metodo_pago" required>
                                <?php if (!empty($metodos_pago_activos)): 
                                    $default_codigo = (!empty($bancos_trans)) ? 'transferencia' : 'pago_movil';
                                ?>
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
                                        $is_selected = (!$disabled && $mp['codigo'] === $default_codigo);
                                    ?>
                                        <option value="<?= htmlspecialchars($mp['nombre']) ?>" 
                                                data-codigo="<?= htmlspecialchars($mp['codigo']) ?>" 
                                                data-requiere-banco="<?= $mp['requiere_banco'] ?>" 
                                                <?= $disabled ? 'disabled' : '' ?>
                                                <?= $is_selected ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($mp['nombre']) . $extra_msg ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled selected>(No hay métodos de pago habilitados)</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <!-- Banco Destino Institucional -->
                        <div class="form-group">
                            <label for="banco_destino_id" class="font-weight-bold">Banco Receptor (UPTPC): <span class="text-danger">*</span></label>
                            <select class="custom-select" id="banco_destino_id" name="banco_destino_id" required>
                                <option value="">-- Seleccione el Banco al que Transfirió --</option>
                                <?php foreach ($bancos_activos as $b): ?>
                                    <option value="<?= $b['id'] ?>">
                                        <?= htmlspecialchars($b['nombre_banco']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Banco de Origen (Emisor) -->
                        <div class="form-group">
                            <label for="banco_origen" class="font-weight-bold">Banco Emisor (Banco desde donde pagó): <span class="text-danger">*</span></label>
                            <select class="custom-select" id="banco_origen" name="banco_origen" required>
                                <option value="">-- Seleccione su Banco Emisor --</option>
                                <?php if (!empty($bancos_activos)): ?>
                                    <?php foreach ($bancos_activos as $b): 
                                        $codigo_banc = !empty($b['codigo_banco']) ? $b['codigo_banco'] . ' - ' : '';
                                    ?>
                                        <option value="<?= htmlspecialchars($b['nombre_banco']) ?>">
                                            <?= htmlspecialchars($codigo_banc . $b['nombre_banco']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <option value="Otro">Otro Banco / Entidad Financiera</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="otro_banco_origen_group" style="display: none;">
                            <label for="otro_banco_origen" class="font-weight-bold">Especifique el Banco Emisor:</label>
                            <input type="text" class="form-control" id="otro_banco_origen" name="otro_banco_origen" 
                                   placeholder="Nombre del banco o billetera">
                        </div>
                        
                        <div class="form-row">
                            <!-- Fecha de Transacción -->
                            <div class="form-group col-md-6">
                                <label for="fecha_transaccion" class="font-weight-bold">Fecha del Pago: <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_transaccion" name="fecha_transaccion" 
                                       value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                            </div>
                            
                            <!-- Número de Referencia -->
                            <div class="form-group col-md-6">
                                <label for="referencia" class="font-weight-bold">N° de Referencia: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="referencia" name="referencia" 
                                       placeholder="Últimos 4 a 8 dígitos" required>
                            </div>
                        </div>
                        
                        <!-- Monto -->
                        <div class="form-group">
                            <label for="monto" class="font-weight-bold">Monto Transferido (Bs): <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Bs</span>
                                </div>
                                <input type="number" class="form-control font-weight-bold" id="monto" name="monto" 
                                       step="0.01" min="0.01" required placeholder="0.00">
                            </div>
                        </div>
                        
                        <!-- Capture / Comprobante Obligatorio con Previsualización -->
                        <div class="form-group">
                            <label class="font-weight-bold">
                                <i class="fas fa-camera text-primary mr-1"></i> Capture / Comprobante de Pago: <span class="text-danger">*</span>
                            </label>
                            <div class="custom-file mb-2">
                                <input type="file" class="custom-file-input" id="comprobante" name="comprobante" 
                                       accept="image/jpeg,image/png,application/pdf" required>
                                <label class="custom-file-label" for="comprobante" id="lbl_comprobante">Seleccionar imagen o PDF...</label>
                            </div>
                            <small class="form-text text-muted">Formatos permitidos: JPG, PNG, PDF (Máx. 5MB).</small>
                            
                            <!-- Contenedor de Previsualización Interactiva -->
                            <div id="preview_container" class="mt-2 text-center p-2 border rounded bg-light" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold text-success"><i class="fas fa-check-circle mr-1"></i>Archivo cargado:</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="btn_cancelar_preview">
                                        <i class="fas fa-times mr-1"></i>Quitar
                                    </button>
                                </div>
                                <img id="preview_image" src="#" alt="Previsualización" class="img-fluid rounded shadow-sm" style="max-height: 220px; display: none;">
                                <div id="preview_pdf" class="alert alert-danger py-3 mb-0" style="display: none;">
                                    <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                    <div class="font-weight-bold" id="pdf_file_name">documento.pdf</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Observaciones Opcionales -->
                        <div class="form-group">
                            <label for="observaciones" class="font-weight-bold">Observaciones Adicionales:</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" 
                                      rows="2" placeholder="Detalles adicionales sobre su pago..."></textarea>
                        </div>
                        
                        <button type="submit" name="declarar_pago" class="btn btn-success btn-block py-2 font-weight-bold shadow-sm">
                            <i class="fas fa-paper-plane mr-1"></i> Declarar Pago para Verificación
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Historial de Pagos Declarados -->
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-history mr-1"></i> Mis Pagos Declarados</h5>
                </div>
                <div class="card-body p-2 p-sm-3">
                    <?php if (!empty($mis_pagos)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped table-sm">
                                <thead class="thead-dark text-center">
                                    <tr>
                                        <th>Fecha Transacción</th>
                                        <th>Concepto</th>
                                        <th>Método / Ref</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Comprobante</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mis_pagos as $p): ?>
                                        <tr>
                                            <td class="small text-center align-middle">
                                                <?= date('d/m/Y', strtotime($p['fecha_transaccion'] ?: $p['fecha_pago'])) ?>
                                            </td>
                                            <td class="align-middle">
                                                <strong><?= htmlspecialchars($p['nombre_tipo_pago']) ?></strong>
                                                <?php if (!empty($p['nombre_banco_destino'])): ?>
                                                    <small class="text-muted d-block"><i class="fas fa-landmark mr-1"></i><?= htmlspecialchars($p['nombre_banco_destino']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle small">
                                                <span class="badge badge-secondary"><?= htmlspecialchars($p['metodo_pago']) ?></span>
                                                <div>Ref: <strong><?= htmlspecialchars($p['referencia'] ?: $p['observaciones']) ?></strong></div>
                                            </td>
                                            <td class="text-right font-weight-bold text-success align-middle">
                                                Bs <?= number_format($p['monto'], 2, ',', '.') ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php if ($p['status_pago'] === 'aprobado'): ?>
                                                    <span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i>Aprobado</span>
                                                <?php elseif ($p['status_pago'] === 'rechazado'): ?>
                                                    <span class="badge badge-danger p-2" title="<?= htmlspecialchars($p['motivo_rechazo'] ?? '') ?>">
                                                        <i class="fas fa-times-circle mr-1"></i>Rechazado
                                                    </span>
                                                    <?php if (!empty($p['motivo_rechazo'])): ?>
                                                        <small class="text-danger d-block mt-1"><?= htmlspecialchars($p['motivo_rechazo']) ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge badge-warning p-2 text-dark"><i class="fas fa-clock mr-1"></i>Pendiente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php if (!empty($p['comprobante'])): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-info btn-ver-capture" 
                                                            data-url="../<?= htmlspecialchars($p['comprobante']) ?>" 
                                                            data-titulo="Comprobante Ref: <?= htmlspecialchars($p['referencia']) ?>">
                                                        <i class="fas fa-eye mr-1"></i> Ver
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted small">Sin adjunto</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 text-muted"></i>
                            <h5>No has declarado ningún pago todavía.</h5>
                            <p class="small">Utiliza el formulario de la izquierda para reportar tus pagos arancelarios.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Comprobante -->
<div class="modal fade" id="modalVerCapture" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCaptureTitulo"><i class="fas fa-image mr-2"></i>Comprobante de Pago</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-3 text-center" id="modalCaptureBody">
                <img id="imgCaptureModal" src="" class="img-fluid rounded" alt="Capture de pago" style="max-height: 75vh; display: none;">
                <iframe id="pdfCaptureModal" src="" style="width: 100%; height: 75vh; border: none; display: none;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <a href="#" id="btnDescargarCapture" target="_blank" class="btn btn-primary"><i class="fas fa-download mr-1"></i> Abrir / Descargar</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function copiarTexto(texto, el) {
    if (!texto) return;
    navigator.clipboard.writeText(texto).then(() => {
        const icon = el.querySelector('i');
        if (icon) {
            const originalClass = icon.className;
            icon.className = 'fas fa-check text-success small';
            setTimeout(() => {
                icon.className = originalClass;
            }, 1500);
        }
    }).catch(err => {
        console.error('Error al copiar:', err);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const selectTipoPago = document.getElementById('tipo_pago');
    const otroGroup      = document.getElementById('otro_concepto_group');
    const montoInput     = document.getElementById('monto');
    const fileInput      = document.getElementById('comprobante');
    const lblFile        = document.getElementById('lbl_comprobante');
    const previewBox     = document.getElementById('preview_container');
    const previewImg     = document.getElementById('preview_image');
    const previewPdf     = document.getElementById('preview_pdf');
    const pdfFileName    = document.getElementById('pdf_file_name');
    const btnCancelPrev  = document.getElementById('btn_cancelar_preview');

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

    // Filtrar bancos disponibles para el estudiante según método
    const bancosEstPagoMovil     = <?= json_encode(array_values($bancos_pm)) ?>;
    const bancosEstTransferencia = <?= json_encode(array_values($bancos_trans)) ?>;
    const bancosEstTodos         = <?= json_encode(array_values($bancos_activos)) ?>;
    const selectEstMetodo        = document.getElementById('metodo_pago');
    const selectEstBancoDestino  = document.getElementById('banco_destino_id');

    function actualizarBancosEstudiante() {
        if (!selectEstMetodo || !selectEstBancoDestino) return;
        const selectedOpt = selectEstMetodo.options[selectEstMetodo.selectedIndex];
        const codigo = selectedOpt ? selectedOpt.getAttribute('data-codigo') : '';
        const requiereBanco = selectedOpt ? selectedOpt.getAttribute('data-requiere-banco') : '1';
        
        selectEstBancoDestino.innerHTML = '<option value="">-- Seleccione el Banco al que Realizó el Pago --</option>';

        if (requiereBanco === '0') {
            const optDirecto = document.createElement('option');
            optDirecto.value = "0";
            optDirecto.textContent = "Directo / No requiere cuenta bancaria institucional";
            optDirecto.selected = true;
            selectEstBancoDestino.appendChild(optDirecto);
            return;
        }

        let bancosList = [];
        if (codigo === 'pago_movil') {
            bancosList = bancosEstPagoMovil;
        } else if (codigo === 'transferencia') {
            bancosList = bancosEstTransferencia;
        } else {
            bancosList = bancosEstTodos;
        }

        if (bancosList.length === 0) {
            const optVacio = document.createElement('option');
            optVacio.value = "";
            optVacio.textContent = "(No hay cuentas disponibles para este método)";
            optVacio.disabled = true;
            optVacio.selected = true;
            selectEstBancoDestino.appendChild(optVacio);
        } else {
            bancosList.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                let detalle = '';
                if (codigo === 'pago_movil' && b.telefono_pago_movil) {
                    detalle = ` (Tel: ${b.telefono_pago_movil})`;
                } else if (codigo === 'transferencia' && b.numero_cuenta) {
                    detalle = ` (Cta: ${b.numero_cuenta.substring(0, 10)}...)`;
                }
                opt.textContent = `${b.nombre_banco}${detalle}`;
                selectEstBancoDestino.appendChild(opt);
            });
        }
    }

    if (selectEstMetodo) {
        selectEstMetodo.addEventListener('change', actualizarBancosEstudiante);
        actualizarBancosEstudiante();
    }

    // Toggle para campo 'Otro Banco Emisor'
    const selectBancoOrigen     = document.getElementById('banco_origen');
    const otroBancoOrigenGroup  = document.getElementById('otro_banco_origen_group');
    const inputOtroBancoOrigen  = document.getElementById('otro_banco_origen');

    if (selectBancoOrigen) {
        selectBancoOrigen.addEventListener('change', function() {
            if (this.value === 'Otro') {
                if (otroBancoOrigenGroup) otroBancoOrigenGroup.style.display = 'block';
                if (inputOtroBancoOrigen) inputOtroBancoOrigen.setAttribute('required', 'required');
            } else {
                if (otroBancoOrigenGroup) otroBancoOrigenGroup.style.display = 'none';
                if (inputOtroBancoOrigen) inputOtroBancoOrigen.removeAttribute('required');
            }
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (!file) {
                previewBox.style.display = 'none';
                lblFile.textContent = 'Seleccionar imagen o PDF...';
                return;
            }

            lblFile.textContent = file.name;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    previewImg.src = evt.target.result;
                    previewImg.style.display = 'block';
                    previewPdf.style.display = 'none';
                    previewBox.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                pdfFileName.textContent = file.name;
                previewImg.style.display = 'none';
                previewPdf.style.display = 'block';
                previewBox.style.display = 'block';
            } else {
                alert('Formato no compatible. Por favor seleccione una imagen o PDF.');
                fileInput.value = '';
                previewBox.style.display = 'none';
                lblFile.textContent = 'Seleccionar imagen o PDF...';
            }
        });
    }

    if (btnCancelPrev) {
        btnCancelPrev.addEventListener('click', function() {
            fileInput.value = '';
            lblFile.textContent = 'Seleccionar imagen o PDF...';
            previewBox.style.display = 'none';
            previewImg.src = '#';
        });
    }

    document.querySelectorAll('.btn-ver-capture').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const titulo = this.getAttribute('data-titulo');
            
            $('#modalCaptureTitulo').text(titulo);
            $('#btnDescargarCapture').attr('href', url);

            if (url.toLowerCase().endsWith('.pdf')) {
                $('#imgCaptureModal').hide();
                $('#pdfCaptureModal').attr('src', url).show();
            } else {
                $('#pdfCaptureModal').hide();
                $('#imgCaptureModal').attr('src', url).show();
            }

            $('#modalVerCapture').modal('show');
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>
