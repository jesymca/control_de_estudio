<?php
/**
 * ==============================================================================
 * PROSECUCIÓN ACADÉMICA - ESTUDIANTES UPTPC
 * Módulo de inscripción para prosecución de estudios de T.S.U. a Ingeniería/Licenciatura
 * ==============================================================================
 */

require_once('../funciones/functions.php');

// Verificar sesión de estudiante
if (!isLoggedIn() || !isEstudiante()) {
    $_SESSION['msg'] = "Debes iniciar sesión como estudiante para acceder a Prosecución.";
    header('location: ../login.php');
    exit();
}

$id_usuario = (int)($_SESSION['user']['id'] ?? 0);
$success_message = '';
$error_message = '';
$nueva_solicitud_id = null;

// Obtener servicio de prosecución
global $db, $prosecucionService;
if (!$prosecucionService && isset($db) && ($db instanceof mysqli)) {
    $prosecucionService = new ProsecucionService($db);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['accion_prosecucion'])) {
    if (empty($_POST['confirmar_declaracion'])) {
        $error_message = "Debe aceptar la declaración jurada de cumplimiento de requisitos.";
    } else {
        $resultado = registrarSolicitudProsecucion($id_usuario, $_POST);
        if (!empty($resultado['success'])) {
            $success_message = $resultado['message'];
            $nueva_solicitud_id = $resultado['id'];
        } else {
            $error_message = $resultado['message'] ?? 'Ocurrió un error al procesar su solicitud.';
            if (!empty($resultado['id'])) {
                $nueva_solicitud_id = $resultado['id'];
            }
        }
    }
}

// 2. Evaluar elegibilidad académica del estudiante
$elegibilidad = verificarElegibilidadProsecucion($id_usuario);
$estudiante = $prosecucionService ? $prosecucionService->obtenerDatosEstudiante($id_usuario) : null;
$solicitud_previa = $elegibilidad['solicitud'] ?? null;

$es_apto = !empty($elegibilidad['es_apto']);
$motivo_elegibilidad = $elegibilidad['motivo'] ?? '';
$titulo_obtenido = $elegibilidad['titulo_obtenido'] ?? ($estudiante['titulo_otorga'] ?? 'T.S.U.');
$titulo_destino = $elegibilidad['titulo_destino'] ?? ($estudiante['otro_titulo'] ?? 'Ingeniería / Licenciatura');
$nombre_carrera = $estudiante['nombre_carrera'] ?? 'Carrera no especificada';

$titulopag = "Inscripción de Prosecución de Estudios | UPTPC";
include('includes/head.php');
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded">
            <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-graduation-cap"></i> Prosecución de Estudios</li>
        </ol>
    </nav>

    <!-- Encabezado de la página -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 bg-white rounded">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="font-weight-bold text-primary mb-1">
                        <i class="fas fa-graduation-cap mr-2"></i>Inscripción de Prosecución Académica
                    </h3>
                    <p class="text-muted mb-0">
                        Programa de Continuación de Estudios de Técnico Superior Universitario (T.S.U.) al grado de <strong>Ingeniería o Licenciatura</strong>.
                    </p>
                </div>
                <div class="col-md-4 text-md-right mt-3 mt-md-0">
                    <?php if ($es_apto): ?>
                        <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 0.95rem;">
                            <i class="fas fa-check-circle mr-1"></i> APTO PARA PROSECUCIÓN
                        </span>
                    <?php else: ?>
                        <span class="badge badge-danger px-3 py-2 font-weight-bold" style="font-size: 0.95rem;">
                            <i class="fas fa-times-circle mr-1"></i> NO APTO PARA PROSECUCIÓN
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes de alerta -->
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm p-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x mr-3 text-success"></i>
                <div>
                    <h5 class="alert-heading font-weight-bold mb-1">¡Registro Exitoso!</h5>
                    <p class="mb-0"><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            </div>
            <?php if (!empty($nueva_solicitud_id)): ?>
                <hr>
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <span class="font-weight-bold">
                        <i class="fas fa-download mr-1"></i> ¿No se descargó la planilla automáticamente?
                    </span>
                    <a href="generar_planilla_prosecucion.php?id=<?php echo (int)$nueva_solicitud_id; ?>" target="_blank" class="btn btn-primary btn-sm font-weight-bold mt-2 mt-sm-0">
                        <i class="fas fa-file-pdf mr-1"></i> Descargar Planilla de Prosecución (PDF)
                    </a>
                </div>
                <script>
                    setTimeout(function() {
                        window.open("generar_planilla_prosecucion.php?id=<?php echo (int)$nueva_solicitud_id; ?>", "_blank");
                    }, 600);
                </script>
            <?php endif; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm p-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x mr-3 text-danger"></i>
                <div>
                    <h5 class="alert-heading font-weight-bold mb-1">Atención</h5>
                    <p class="mb-0"><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- CASO 1: ESTUDIANTE NO ES APTO -->
    <?php if (!$es_apto): ?>
        <div class="card border-warning shadow-sm mb-4">
            <div class="card-header bg-warning text-dark font-weight-bold py-3">
                <i class="fas fa-exclamation-triangle mr-2"></i>Condición Académica Actual: No Apto
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h5 class="text-dark font-weight-bold mb-2">No cumple con los requisitos institucionales para Prosecución</h5>
                        <p class="text-muted mb-3">
                            <?php echo htmlspecialchars($motivo_elegibilidad); ?>
                        </p>
                        
                        <div class="alert alert-light border p-3 rounded small">
                            <h6 class="font-weight-bold text-primary mb-2">
                                <i class="fas fa-info-circle mr-1"></i> Requisitos Académicos para Cursar Prosecución (UPTPC):
                            </h6>
                            <ul class="mb-0 pl-3">
                                <li>Haber aprobado la totalidad de unidades curriculares correspondientes al ciclo de <strong>Técnico Superior Universitario</strong> (Trayecto I y Trayecto II).</li>
                                <li>Tener aprobado el <strong>Proyecto Socio-Integrador</strong> con nota mínima de 16 puntos.</li>
                                <li>Contar con estatus de egresado registrado o autorización formal de avance de trayecto emitida por Secretaría / Control de Estudios.</li>
                                <li>El Programa Nacional de Formación (PNF) cursado debe disponer de continuación a segundo título (Ingeniería o Licenciatura).</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center mt-3 mt-lg-0">
                        <div class="p-3 bg-light rounded border">
                            <i class="fas fa-user-clock fa-4x text-warning mb-3"></i>
                            <h6 class="font-weight-bold mb-1"><?php echo htmlspecialchars($estudiante['nombre'] ?? 'Estudiante'); ?></h6>
                            <p class="small text-muted mb-3">C.I. <?php echo htmlspecialchars($estudiante['idusuario'] ?? ''); ?><br><?php echo htmlspecialchars($nombre_carrera); ?></p>
                            <button type="button" class="btn btn-outline-primary btn-sm btn-block font-weight-bold" data-toggle="modal" data-target="#modalRequisitosDetallados">
                                <i class="fas fa-file-alt mr-1"></i> Ver Normativa Institucional
                            </button>
                            <a href="index.php" class="btn btn-secondary btn-sm btn-block mt-2 font-weight-bold">
                                <i class="fas fa-arrow-left mr-1"></i> Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- CASO 2: ESTUDIANTE YA TIENE SOLICITUD REGISTRADA -->
    <?php elseif (!empty($solicitud_previa)): ?>
        <div class="card border-info shadow-sm mb-4">
            <div class="card-header bg-primary text-white font-weight-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clipboard-check mr-2"></i>Solicitud de Prosecución Registrada</span>
                <span class="badge badge-light text-primary font-weight-bold text-uppercase px-3 py-1">
                    <?php echo htmlspecialchars($solicitud_previa['estatus'] ?? 'PENDIENTE'); ?>
                </span>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info border-info p-3 mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    Usted ya posee un registro formal de prosecución para continuar sus estudios hacia <strong><?php echo htmlspecialchars($solicitud_previa['titulo_solicitado'] ?? $titulo_destino); ?></strong>.
                </div>

                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <div class="bg-light p-3 rounded border h-100">
                            <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-user mr-2"></i>Datos del Aspirante</h6>
                            <p class="mb-2"><strong>Nombre:</strong> <?php echo htmlspecialchars($estudiante['nombre'] ?? ''); ?></p>
                            <p class="mb-2"><strong>Cédula:</strong> <?php echo htmlspecialchars($estudiante['idusuario'] ?? ''); ?></p>
                            <p class="mb-2"><strong>Correo:</strong> <?php echo htmlspecialchars($solicitud_previa['email_contacto'] ?? $estudiante['email']); ?></p>
                            <p class="mb-0"><strong>Teléfono:</strong> <?php echo htmlspecialchars($solicitud_previa['telefono_contacto'] ?? $estudiante['tlf']); ?></p>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="bg-light p-3 rounded border h-100">
                            <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-graduation-cap mr-2"></i>Datos Académicos</h6>
                            <p class="mb-2"><strong>N° Registro:</strong> <span class="badge badge-secondary"><?php echo sprintf("PROS-%05d", $solicitud_previa['id']); ?></span></p>
                            <p class="mb-2"><strong>Programa de Origen:</strong> <?php echo htmlspecialchars($solicitud_previa['nombre_carrera_origen'] ?? $nombre_carrera); ?> (<?php echo htmlspecialchars($solicitud_previa['titulo_obtenido'] ?? $titulo_obtenido); ?>)</p>
                            <p class="mb-2"><strong>Programa a Cursar:</strong> <strong class="text-success"><?php echo htmlspecialchars($solicitud_previa['titulo_solicitado'] ?? $titulo_destino); ?></strong></p>
                            <p class="mb-2"><strong>Turno / Sede:</strong> <?php echo htmlspecialchars($solicitud_previa['turno'] ?? 'Diurno'); ?> / <?php echo htmlspecialchars($solicitud_previa['sede'] ?? 'Sede Principal'); ?></p>
                            <p class="mb-0"><strong>Fecha de Registro:</strong> <?php echo date('d/m/Y h:i A', strtotime($solicitud_previa['fecha_solicitud'])); ?></p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3 pt-3 border-top">
                    <a href="generar_planilla_prosecucion.php?id=<?php echo (int)$solicitud_previa['id']; ?>" target="_blank" class="btn btn-primary btn-lg font-weight-bold px-4 shadow-sm mr-2">
                        <i class="fas fa-file-pdf mr-2"></i>Descargar Planilla Oficial (PDF)
                    </a>
                    <button type="button" class="btn btn-outline-info btn-lg font-weight-bold px-4" data-toggle="modal" data-target="#modalDetalleSolicitud">
                        <i class="fas fa-eye mr-2"></i>Ver Detalles de Solicitud
                    </button>
                </div>
            </div>
        </div>

    <!-- CASO 3: ESTUDIANTE ES APTO Y PUEDE INSCRIBIRSE -->
    <?php else: ?>
        <!-- Flujo Visual de Continuación de Estudios -->
        <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white" style="background: linear-gradient(135deg, #003366 0%, #00509e 100%);">
            <div class="card-body p-4">
                <div class="row align-items-center text-center text-md-left">
                    <div class="col-md-5 mb-3 mb-md-0">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="mr-3 bg-white text-primary rounded-circle p-3 shadow-sm">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                            <div>
                                <small class="text-warning text-uppercase font-weight-bold">Grado Culminado</small>
                                <h5 class="mb-0 font-weight-bold"><?php echo htmlspecialchars($titulo_obtenido); ?></h5>
                                <small class="text-light"><?php echo htmlspecialchars($nombre_carrera); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 text-center my-2 my-md-0">
                        <i class="fas fa-arrow-right fa-2x text-warning d-none d-md-inline"></i>
                        <i class="fas fa-arrow-down fa-2x text-warning d-inline d-md-none"></i>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="mr-3 bg-warning text-dark rounded-circle p-3 shadow-sm">
                                <i class="fas fa-award fa-2x"></i>
                            </div>
                            <div>
                                <small class="text-warning text-uppercase font-weight-bold">Prosecución a Cursar</small>
                                <h5 class="mb-0 font-weight-bold text-white"><?php echo htmlspecialchars($titulo_destino); ?></h5>
                                <small class="text-light">Trayectos III y IV (Ciclo Profesional)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Prosecución -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="font-weight-bold text-primary mb-0">
                    <i class="fas fa-edit mr-2"></i>Formulario de Inscripción para Prosecución
                </h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">
                    Sus datos personales y académicos han sido cargados automáticamente desde su expediente institucional. Por favor verifique la información, seleccione su sede y turno de preferencia y envíe su postulación.
                </p>

                <form id="formProsecucion" method="POST" action="prosecucion.php">
                    <input type="hidden" name="accion_prosecucion" value="1">

                    <!-- Sección 1: Datos de Identificación (Pre-llenados) -->
                    <h6 class="font-weight-bold text-secondary mb-3 text-uppercase border-bottom pb-2">
                        <i class="fas fa-id-card mr-1"></i> 1. Identificación del Aspirante
                    </h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold text-muted small">Cédula de Identidad</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($estudiante['idusuario'] ?? ''); ?>" readonly>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label font-weight-bold text-muted small">Apellidos y Nombres</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($estudiante['nombre'] ?? ''); ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold text-muted small">Género</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($estudiante['genero'] ?? 'No especificado'); ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold text-muted small">Estado Civil</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($estudiante['edo_civil'] ?? 'Soltero(a)'); ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold text-muted small">Fecha de Nacimiento</label>
                            <input type="text" class="form-control bg-light" value="<?php echo (!empty($estudiante['fecha_nac']) && $estudiante['fecha_nac'] !== '0000-00-00') ? date('d/m/Y', strtotime($estudiante['fecha_nac'])) : 'No especificada'; ?>" readonly>
                        </div>
                    </div>

                    <!-- Sección 2: Información de Contacto y Ubicación -->
                    <h6 class="font-weight-bold text-secondary mb-3 mt-3 text-uppercase border-bottom pb-2">
                        <i class="fas fa-map-marker-alt mr-1"></i> 2. Contacto y Habitación
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email_contacto" class="form-label font-weight-bold small required">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email_contacto" name="email_contacto" value="<?php echo htmlspecialchars($_POST['email_contacto'] ?? ($estudiante['email'] ?? '')); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono_contacto" class="form-label font-weight-bold small required">Teléfono Principal de Contacto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="telefono_contacto" name="telefono_contacto" value="<?php echo htmlspecialchars($_POST['telefono_contacto'] ?? ($estudiante['tlf'] ?: $estudiante['cel'] ?: '')); ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="direccion_actual" class="form-label font-weight-bold small">Dirección de Habitación Actual</label>
                            <textarea class="form-control" id="direccion_actual" name="direccion_actual" rows="2"><?php echo htmlspecialchars($_POST['direccion_actual'] ?? ($estudiante['direccion'] ?? '')); ?></textarea>
                        </div>
                    </div>

                    <!-- Sección 3: Datos de Prosecución y Preferencias -->
                    <h6 class="font-weight-bold text-secondary mb-3 mt-3 text-uppercase border-bottom pb-2">
                        <i class="fas fa-graduation-cap mr-1"></i> 3. Preferencias de Prosecución
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-muted small">Programa a Cursar</label>
                            <input type="text" class="form-control bg-light font-weight-bold text-primary" value="<?php echo htmlspecialchars($titulo_destino); ?>" readonly>
                            <small class="form-text text-muted">Correspondiente a la prosecución de <?php echo htmlspecialchars($nombre_carrera); ?>.</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="turno" class="form-label font-weight-bold small required">Turno Preferido <span class="text-danger">*</span></label>
                            <select class="custom-select" id="turno" name="turno" required>
                                <option value="Diurno" <?php echo (($_POST['turno'] ?? '') === 'Diurno') ? 'selected' : ''; ?>>Diurno</option>
                                <option value="Nocturno" <?php echo (($_POST['turno'] ?? '') === 'Nocturno') ? 'selected' : ''; ?>>Nocturno</option>
                                <option value="Fines de Semana" <?php echo (($_POST['turno'] ?? '') === 'Fines de Semana') ? 'selected' : ''; ?>>Fines de Semana</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="sede" class="form-label font-weight-bold small required">Sede <span class="text-danger">*</span></label>
                            <select class="custom-select" id="sede" name="sede" required>
                                <option value="Sede Principal" <?php echo (($_POST['sede'] ?? '') === 'Sede Principal') ? 'selected' : ''; ?>>Sede Principal</option>
                                <option value="Complejo Educativo COEF" <?php echo (($_POST['sede'] ?? '') === 'Complejo Educativo COEF') ? 'selected' : ''; ?>>Complejo Educativo COEF</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="observaciones" class="form-label font-weight-bold small">Observaciones o Consideraciones Adicionales</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Indique cualquier detalle adicional relevante para Control de Estudios..."><?php echo htmlspecialchars($_POST['observaciones'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Declaración Jurada y Conformidad -->
                    <div class="card bg-light border-0 my-4">
                        <div class="card-body p-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirmar_declaracion" name="confirmar_declaracion" value="1" required>
                                <label class="custom-control-label font-weight-bold text-dark small" for="confirmar_declaracion">
                                    Declaro bajo fe de juramento que he culminado con éxito los requisitos académicos del ciclo de Técnico Superior Universitario (T.S.U.) y solicito formalmente mi prosecución de estudios a Ingeniería o Licenciatura en la UPTPC.
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="text-right">
                        <a href="index.php" class="btn btn-secondary font-weight-bold px-4 mr-2">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </a>
                        <button type="button" class="btn btn-primary btn-lg font-weight-bold px-5 shadow-sm" onclick="abrirModalConfirmacion()">
                            <i class="fas fa-paper-plane mr-2"></i> Registrar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ============================================================================== -->
<!-- 🔹 MODALES INSTITUCIONALES -->
<!-- ============================================================================== -->

<!-- 1. MODAL: CONFIRMACIÓN DE SOLICITUD DE PROSECUCIÓN -->
<div class="modal fade" id="modalConfirmarProsecucion" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold" id="modalConfirmarLabel">
                    <i class="fas fa-check-circle mr-2"></i>Confirmar Inscripción de Prosecución
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-3">
                    Está a punto de registrar formalmente su inscripción para prosecución académica con los siguientes parámetros:
                </p>
                <div class="bg-light p-3 rounded border small mb-3">
                    <p class="mb-2"><strong>Aspirante:</strong> <?php echo htmlspecialchars($estudiante['nombre'] ?? ''); ?> (<?php echo htmlspecialchars($estudiante['idusuario'] ?? ''); ?>)</p>
                    <p class="mb-2"><strong>Título Culminado:</strong> <?php echo htmlspecialchars($titulo_obtenido); ?></p>
                    <p class="mb-2"><strong>Título Solicitado:</strong> <span class="text-primary font-weight-bold"><?php echo htmlspecialchars($titulo_destino); ?></span></p>
                    <p class="mb-2"><strong>Turno Seleccionado:</strong> <span id="resumenTurno" class="font-weight-bold text-dark">-</span></p>
                    <p class="mb-0"><strong>Sede:</strong> <span id="resumenSede" class="font-weight-bold text-dark">-</span></p>
                </div>
                <div class="alert alert-warning small mb-0">
                    <i class="fas fa-info-circle mr-1"></i> Al confirmar, se generará su <strong>Planilla Oficial en PDF</strong> que deberá presentar en Control de Estudios para formalizar la carga académica.
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal">
                    <i class="fas fa-undo mr-1"></i> Modificar
                </button>
                <button type="button" class="btn btn-success font-weight-bold px-4 shadow-sm" onclick="enviarFormularioProsecucion()">
                    <i class="fas fa-check mr-1"></i> Sí, Enviar Inscripción
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 2. MODAL: NORMATIVA Y REQUISITOS INSTITUCIONALES -->
<div class="modal fade" id="modalRequisitosDetallados" tabindex="-1" role="dialog" aria-labelledby="modalRequisitosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold" id="modalRequisitosLabel">
                    <i class="fas fa-book mr-2"></i>Normativa Institucional para Prosecución de Estudios
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-primary mb-3">
                    <i class="fas fa-university mr-2"></i> <strong>Universidad Territorial Politécnica de Puerto Cabello (UPTPC)</strong><br>
                    Reglamento de Evaluación del Desempeño Estudiantil y Continuación de Estudios en PNF.
                </div>

                <h6 class="font-weight-bold text-dark">Artículo 13: Prosecución hacia Ingeniería / Licenciatura</h6>
                <p class="text-muted small">
                    La prosecución de estudios es el derecho que adquieren los egresados de Técnico Superior Universitario (T.S.U.) debidamente certificados para continuar su formación académica en los Trayectos III y IV correspondientes al título de Ingeniero o Licenciado de su respectivo PNF.
                </p>

                <h6 class="font-weight-bold text-dark mt-3">Requisitos de Ingreso al Ciclo de Prosecución:</h6>
                <div class="table-responsive small mt-2">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>Requisito</th>
                                <th>Condición Mínima</th>
                                <th>Verificación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Aprobación de Trayectos I y II</td>
                                <td>100% de materias aprobadas</td>
                                <td>Historial de Calificaciones</td>
                            </tr>
                            <tr>
                                <td>Proyecto Socio-Integrador</td>
                                <td>Calificación mínima: 16 puntos</td>
                                <td>Acta de Evaluación Definitiva</td>
                            </tr>
                            <tr>
                                <td>Servicio Comunitario</td>
                                <td>Aprobado y Certificado</td>
                                <td>Constancia Institucional</td>
                            </tr>
                            <tr>
                                <td>Registro de Grado / Avance</td>
                                <td>Estado graduado o avance formal</td>
                                <td>Dirección de Control de Estudios</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info small mt-3 mb-0">
                    <i class="fas fa-headset mr-1"></i> Para resolver discrepancias o solicitar una revisión de avance de trayecto, acuda a la <strong>Dirección de Control de Estudios</strong> o al <strong>Director de Carrera</strong>.
                </div>
            </div>
            <div class="modal-footer bg-light py-2 justify-content-end">
                <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 3. MODAL: DETALLES DE LA SOLICITUD PREVIA REGISTRADA -->
<?php if (!empty($solicitud_previa)): ?>
<div class="modal fade" id="modalDetalleSolicitud" tabindex="-1" role="dialog" aria-labelledby="modalDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title font-weight-bold" id="modalDetalleLabel">
                    <i class="fas fa-file-invoice mr-2"></i>Detalle de su Registro de Prosecución
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 small">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">N° de Trámite:</span>
                        <span class="font-weight-bold"><?php echo sprintf("PROS-%05d", $solicitud_previa['id']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Fecha de Solicitud:</span>
                        <span class="font-weight-bold"><?php echo date('d/m/Y h:i A', strtotime($solicitud_previa['fecha_solicitud'])); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Estado del Trámite:</span>
                        <span class="badge badge-success text-uppercase"><?php echo htmlspecialchars($solicitud_previa['estatus'] ?? 'Pendiente'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Programa Actual (TSU):</span>
                        <span class="font-weight-bold"><?php echo htmlspecialchars($solicitud_previa['nombre_carrera_origen'] ?? $nombre_carrera); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Prosecución Solicitada:</span>
                        <span class="font-weight-bold text-primary"><?php echo htmlspecialchars($solicitud_previa['titulo_solicitado'] ?? $titulo_destino); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Turno / Sede:</span>
                        <span class="font-weight-bold"><?php echo htmlspecialchars($solicitud_previa['turno'] ?? 'Diurno'); ?> / <?php echo htmlspecialchars($solicitud_previa['sede'] ?? 'Sede Principal'); ?></span>
                    </li>
                    <?php if (!empty($solicitud_previa['observaciones'])): ?>
                    <li class="list-group-item px-0">
                        <span class="text-muted d-block mb-1">Observaciones registradas:</span>
                        <span class="font-italic bg-light p-2 d-block rounded"><?php echo htmlspecialchars($solicitud_previa['observaciones']); ?></span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="modal-footer bg-light py-2">
                <a href="generar_planilla_prosecucion.php?id=<?php echo (int)$solicitud_previa['id']; ?>" target="_blank" class="btn btn-primary font-weight-bold btn-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Descargar Planilla PDF
                </a>
                <button type="button" class="btn btn-secondary font-weight-bold btn-sm" data-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Scripts para Validación y Modales -->
<script>
function abrirModalConfirmacion() {
    const form = document.getElementById('formProsecucion');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const turnoSelect = document.getElementById('turno');
    const sedeSelect = document.getElementById('sede');
    
    document.getElementById('resumenTurno').textContent = turnoSelect.options[turnoSelect.selectedIndex].text;
    document.getElementById('resumenSede').textContent = sedeSelect.options[sedeSelect.selectedIndex].text;

    $('#modalConfirmarProsecucion').modal('show');
}

function enviarFormularioProsecucion() {
    $('#modalConfirmarProsecucion').modal('hide');
    document.getElementById('formProsecucion').submit();
}
</script>

<?php include('includes/footer.php'); ?>
