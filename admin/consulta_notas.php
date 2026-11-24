<?php
require_once('../funciones/functions.php');

//CARGAR PERMISOS
cargarPermisosUsuario();
verificarPermiso('consultar_notas');

if (!isLoggedIn()) {
    header('location: ../login.php');
    exit();
}

// ==========================================
// CONTROLADORES AJAX (Deben ir ANTES de enviar HTML)
// ==========================================

// 1. Búsqueda en tiempo real de estudiantes
if (isset($_POST['ajax_buscar_estudiantes'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    $termino = trim($_POST['termino'] ?? '');
    $estudiantes = function_exists('buscarEstudiantesPagosAjax') ? buscarEstudiantesPagosAjax($termino, 12) : [];
    echo json_encode(['success' => true, 'estudiantes' => $estudiantes]);
    exit();
}

// 2. Verificación de condiciones de modalidad en tiempo real
if (isset($_POST['ajax_verificar_modalidad'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    $estudiante_id = intval($_POST['estudiante_id'] ?? 0);
    $id_materia = intval($_POST['id_materia'] ?? 0);
    $tipo_examen = strtoupper(trim($_POST['tipo_examen'] ?? 'R'));
    $caso_especial = !empty($_POST['caso_especial_jefa']);
    
    global $db;
    $q_m = $db->query("SELECT * FROM materias WHERE id_materia = $id_materia");
    $m = $q_m ? $q_m->fetch_assoc() : null;
    $trayecto = intval($m['trayecto'] ?? 0);
    $es_proy = (!empty($m['es_proyecto_socio']) && $m['es_proyecto_socio'] == 1);
    
    $historial = obtenerHistorialCompletoNotasEstudiante($estudiante_id);
    $intentos = $historial[$id_materia] ?? [];
    $ultimo = !empty($intentos) ? end($intentos) : null;
    $nota_actual = $ultimo['nota_final'] ?? null;
    
    $resultado = [
        'apto' => true,
        'mensaje' => '',
        'nota_max' => 20.0,
        'requiere_desbloqueo' => false,
        'tipo' => $tipo_examen
    ];
    
    if ($tipo_examen === 'P') { // Plan Especial de Recuperación (PER)
        $resultado['nota_max'] = 12.0;
        if ($trayecto === 0) {
            $resultado['apto'] = false;
            $resultado['mensaje'] = 'El Trayecto Inicial (T0) NO hace Plan Especial de Recuperación (PER).';
        } elseif ($es_proy) {
            $resultado['apto'] = false;
            $resultado['mensaje'] = 'Los Proyectos Sociotecnológicos / Sociointegradores NO aplican para PER.';
        } elseif ($nota_actual === null || $nota_actual < 6 || $nota_actual >= 12) {
            $resultado['apto'] = false;
            $resultado['mensaje'] = 'Solo aplica para materias reprobadas con nota previa entre 6 y 11 puntos (Calificación registrada: ' . ($nota_actual !== null ? number_format($nota_actual, 1) : 'Sin nota') . ').';
        } else {
            $resultado['apto'] = true;
            $resultado['mensaje'] = 'Materia apta para PER (Reprobada con ' . number_format($nota_actual, 1) . ' pts). La calificación máxima admisible es 12.0 puntos.';
        }
    } elseif ($tipo_examen === 'E') { // Examen Extraordinario
        $val_e = esAptoExtraordinario($estudiante_id, $id_materia);
        $resultado['apto'] = $val_e['apto'];
        $resultado['mensaje'] = $val_e['mensaje'];
    } elseif ($tipo_examen === 'T') { // Curso Intensivo
        if (empty($intentos)) {
            $resultado['apto'] = false;
            $resultado['mensaje'] = 'Para cursar intensivo, el estudiante debe haber visto previamente la materia.';
        } else {
            $resultado['apto'] = true;
            $resultado['mensaje'] = 'Materia cursada previamente. La calificación de intensivo reemplazará la nota activa como definitiva sin borrar el intento anterior.';
        }
    } elseif ($tipo_examen === 'S') { // Examen de Suficiencia
        $val_s = validarSuficiencia($estudiante_id, $id_materia, $caso_especial);
        $resultado['apto'] = $val_s['permitido'];
        $resultado['mensaje'] = $val_s['mensaje'];
        $resultado['requiere_desbloqueo'] = !empty($val_s['requiere_desbloqueo']);
        $resultado['cantidad_anio'] = $val_s['cantidad_anio'] ?? 0;
    } else {
        $resultado['apto'] = true;
        $resultado['mensaje'] = 'Calificación ordinaria bajo modalidad regular.';
    }
    
    echo json_encode($resultado);
    exit();
}

// 3. Registro / Modificación de nota y modalidad vía AJAX
if (isset($_POST['ajax_registrar_nota_modalidad'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    $estudiante_id = intval($_POST['estudiante_id'] ?? 0);
    $id_materia = intval($_POST['id_materia'] ?? 0);
    $id_periodo = intval($_POST['id_periodo'] ?? 0);
    $id_docente = intval($_POST['id_docente'] ?? 0);
    $nota = floatval($_POST['nota'] ?? 0);
    $tipo_examen = strtoupper(trim($_POST['tipo_examen'] ?? 'R'));
    $caso_especial = !empty($_POST['caso_especial_jefa']);
    
    if ($estudiante_id <= 0 || $id_materia <= 0 || $id_periodo <= 0) {
        echo json_encode(['success' => false, 'message' => 'Por favor complete todos los datos obligatorios.']);
        exit();
    }
    
    if ($nota < 0 || $nota > 20) {
        echo json_encode(['success' => false, 'message' => 'La nota debe estar entre 0 y 20 puntos.']);
        exit();
    }
    
    if ($tipo_examen === 'S') {
        $val_suf = validarSuficiencia($estudiante_id, $id_materia, $caso_especial);
        if (!$val_suf['permitido']) {
            echo json_encode(['success' => false, 'message' => $val_suf['mensaje']]);
            exit();
        }
    }
    
    $admin_id = $_SESSION['user']['id'] ?? 1;
    $res = registrarNotaModalidadEspecial($estudiante_id, $id_materia, $id_periodo, $id_docente, $nota, $tipo_examen, $admin_id);
    echo json_encode($res);
    exit();
}

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

$titulopag = "Consulta de Notas por Cédula";
include("includes/head.php");

// Procesar búsqueda del estudiante
$estudiante = null;
$carrera = null;
$materias_carrera = [];
$notas_estudiante = [];
$mensaje_error = '';
$info_apto = null;
$aptas_per_lista = [];
$apto_extra_info = null;
$materias_pensum_todas = [];

// Períodos y docentes para el modal interactivo
$periodos_todos = [];
$res_p = $db->query("SELECT id_periodo, nombre_periodo, activo FROM periodos_academicos ORDER BY id_periodo DESC");
if ($res_p) {
    while ($rp = $res_p->fetch_assoc()) $periodos_todos[] = $rp;
}

$docentes_todos = [];
$res_d = $db->query("SELECT id, nombre, idusuario FROM users WHERE user_type = 'docente' OR docente = 1 ORDER BY nombre ASC");
if ($res_d) {
    while ($rd = $res_d->fetch_assoc()) $docentes_todos[] = $rd;
}

// Aceptar búsqueda tanto por POST como por GET
$cedula = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cedula'])) {
    $cedula = trim($_POST['cedula']);
} elseif (isset($_GET['cedula']) && !empty($_GET['cedula'])) {
    $cedula = trim($_GET['cedula']);
}

if (!empty($cedula)) {
    $estudiante = buscarEstudiantePorCedulaConsulta($cedula);
    
    if ($estudiante) {
        $carrera = obtenerCarreraEstudiante($estudiante['id']);
        
        if ($carrera) {
            $materias_carrera = obtenerMateriasEstudianteConsultaNotas($estudiante['id'], $carrera['id_carrera']);
            $historial_notas = obtenerHistorialCompletoNotasEstudiante($estudiante['id']);
            $info_apto = esAptoParaGradoConsulta($estudiante['id'], $carrera['id_carrera']);
            
            // Diagnóstico de aptitud para PER y Extraordinario
            $aptas_per_lista = obtenerMateriasAptasPER($estudiante['id']);
            $apto_extra_info = esAptoExtraordinario($estudiante['id']);
            
            // Todas las materias de la carrera para selector del modal
            $res_pen = obtenerMateriasCarrera($carrera['id_carrera']);
            if ($res_pen) {
                while ($rpen = $res_pen->fetch_assoc()) $materias_pensum_todas[] = $rpen;
            }
        }
    } else {
        $mensaje_error = "No se encontró ningún estudiante con la cédula o criterio: " . htmlspecialchars($cedula);
    }
} else if (isset($_GET['cedula']) && empty($_GET['cedula'])) {
    $mensaje_error = "Por favor, ingrese una cédula válida para buscar.";
}
?>

<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center my-3">
        <h2 class="mb-2 mb-md-0 font-weight-bold text-dark">
            <i class="fas fa-search text-primary mr-2"></i>Consulta y Gestión de Calificaciones
        </h2>
        <div>
            <a href="solicitudes_evaluaciones.php" class="btn btn-outline-info font-weight-bold shadow-sm">
                <i class="fas fa-tasks mr-1"></i> Solicitudes Especiales (PER/INT/SUF/EXT)
            </a>
        </div>
    </div>
    
    <!-- TARJETA DE BÚSQUEDA CON SUGERENCIAS EN TIEMPO REAL -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-primary text-white py-2">
            <h6 class="mb-0 font-weight-bold"><i class="fas fa-user-graduate mr-1"></i> Buscar Estudiante</h6>
        </div>
        <div class="card-body py-3">
            <form method="POST" class="form-inline position-relative" id="formBuscarEstudiante">
                <div class="form-group mr-2 mb-2 position-relative" style="min-width: 340px; max-width: 100%;">
                    <label for="cedula" class="mr-2 font-weight-bold text-dark">Cédula o Nombre:</label>
                    <div class="input-group w-100">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light"><i class="fas fa-id-card text-muted"></i></span>
                        </div>
                        <input type="text" class="form-control" id="cedula" name="cedula" autocomplete="off"
                               placeholder="Ej: 12345678 o nombre del estudiante..." 
                               value="<?= htmlspecialchars($cedula) ?>" required>
                        <div class="input-group-append" id="spinnerBusqueda" style="display: none;">
                            <span class="input-group-text bg-white text-primary"><i class="fas fa-spinner fa-spin"></i></span>
                        </div>
                    </div>
                    <!-- Lista flotante de sugerencias AJAX -->
                    <div id="sugerenciasEstudiantes" class="list-group shadow position-absolute w-100" 
                         style="z-index: 9999; display: none; max-height: 280px; overflow-y: auto; top: 100%; left: 0; right: 0;"></div>
                </div>
                <button type="submit" class="btn btn-success mb-2 font-weight-bold shadow-sm">
                    <i class="fas fa-search mr-1"></i> Consultar
                </button>
            </form>
            
            <?php if (!empty($mensaje_error)): ?>
                <div class="alert alert-danger mt-3 mb-0"><?= $mensaje_error ?></div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($estudiante && $carrera && $materias_carrera && $materias_carrera->num_rows > 0): ?>
    
    <!-- NOTIFICACIONES INTELIGENTES DE MODALIDADES ESPECIALES (PER / EXTRAORDINARIO) -->
    <?php if (!empty($aptas_per_lista)): ?>
        <div class="alert alert-warning border-warning shadow-sm d-flex justify-content-between align-items-center mb-3">
            <div>
                <i class="fas fa-sync-alt fa-lg text-warning mr-2"></i>
                <strong>Candidato a Plan Especial de Recuperación (PER):</strong> 
                Tiene <strong><?= count($aptas_per_lista) ?> asignatura(s)</strong> reprobada(s) con nota entre 6 y 11 pts aptas para solicitar PER:
                <?php foreach ($aptas_per_lista as $ap): ?>
                    <span class="badge badge-dark ml-1 font-weight-bold">
                        <?= htmlspecialchars($ap['nombre_materia']) ?> (<?= number_format($ap['nota_reprobada'], 1) ?> pts)
                    </span>
                <?php endforeach; ?>
            </div>
            <div>
                <span class="badge badge-warning text-dark p-2">Tope de nota: 12 pts</span>
                <a href="solicitudes_evaluaciones.php?modalidad=per" class="btn btn-sm btn-dark font-weight-bold shadow-sm ml-2">
                    <i class="fas fa-tasks mr-1"></i> Ver Solicitudes PER
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($apto_extra_info && $apto_extra_info['apto']): ?>
        <div class="alert alert-info border-info shadow-sm d-flex justify-content-between align-items-center mb-3">
            <div>
                <i class="fas fa-star fa-lg text-primary mr-2"></i>
                <strong>Candidato a Examen Extraordinario (E):</strong> 
                Cumple con las condiciones de ley: le falta únicamente <strong>1 asignatura</strong> para culminar su plan (<strong><?= htmlspecialchars($apto_extra_info['materia_faltante']['nombre_materia'] ?? '') ?></strong>) y ya la cursó previamente.
            </div>
            <span class="badge badge-primary p-2">Única Materia Faltante</span>
        </div>
    <?php endif; ?>

    <!-- FICHA DEL ESTUDIANTE Y ACCIONES DE REPORTES -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-info text-white d-flex flex-wrap justify-content-between align-items-center py-2">
            <h5 class="mb-0 font-weight-bold"><i class="fas fa-user-check mr-1"></i> Expediente del Estudiante</h5>
            
            <!-- 4 REPORTES PDF FORMULARIO POST -->
            <div class="d-inline-flex flex-wrap align-items-center mt-2 mt-md-0">
                <form method="POST" action="historial_desglozado_tsu.php" target="_blank" class="d-inline mr-2 mb-1">
                    <input type="hidden" name="estudiante_id" value="<?= htmlspecialchars($estudiante['id']) ?>">
                    <input type="hidden" name="cedula" value="<?= htmlspecialchars($estudiante['idusuario']) ?>">
                    <input type="hidden" name="nombre" value="<?= htmlspecialchars($estudiante['nombre']) ?>">
                    <input type="hidden" name="carrera" value="<?= htmlspecialchars($carrera['nombre_carrera']) ?>">
                    <button type="submit" class="btn btn-warning btn-sm shadow-sm font-weight-bold">
                        <i class="fas fa-file-pdf mr-1"></i> Historial TSU
                    </button>
                </form>
                <form method="POST" action="historial_desglozado_ingenieria.php" target="_blank" class="d-inline mr-2 mb-1">
                    <input type="hidden" name="estudiante_id" value="<?= htmlspecialchars($estudiante['id']) ?>">
                    <input type="hidden" name="cedula" value="<?= htmlspecialchars($estudiante['idusuario']) ?>">
                    <input type="hidden" name="nombre" value="<?= htmlspecialchars($estudiante['nombre']) ?>">
                    <input type="hidden" name="carrera" value="<?= htmlspecialchars($carrera['nombre_carrera']) ?>">
                    <button type="submit" class="btn btn-info btn-sm shadow-sm font-weight-bold">
                        <i class="fas fa-file-pdf mr-1"></i> Historial Ingeniería
                    </button>
                </form>
                <form method="POST" action="historial_graduando.php" target="_blank" class="d-inline mr-2 mb-1">
                    <input type="hidden" name="estudiante_id" value="<?= htmlspecialchars($estudiante['id']) ?>">
                    <input type="hidden" name="cedula" value="<?= htmlspecialchars($estudiante['idusuario']) ?>">
                    <input type="hidden" name="nombre" value="<?= htmlspecialchars($estudiante['nombre']) ?>">
                    <input type="hidden" name="carrera" value="<?= htmlspecialchars($carrera['nombre_carrera']) ?>">
                    <button type="submit" class="btn btn-success btn-sm shadow-sm font-weight-bold">
                        <i class="fas fa-user-graduate mr-1"></i> Historial Graduando
                    </button>
                </form>
                <form method="POST" action="historial_desglosado.php" target="_blank" class="d-inline mb-1">
                    <input type="hidden" name="estudiante_id" value="<?= htmlspecialchars($estudiante['id']) ?>">
                    <input type="hidden" name="cedula" value="<?= htmlspecialchars($estudiante['idusuario']) ?>">
                    <input type="hidden" name="nombre" value="<?= htmlspecialchars($estudiante['nombre']) ?>">
                    <input type="hidden" name="carrera" value="<?= htmlspecialchars($carrera['nombre_carrera']) ?>">
                    <button type="submit" class="btn btn-dark btn-sm shadow-sm font-weight-bold">
                        <i class="fas fa-list-alt mr-1"></i> Historial Desglosado
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Cédula:</strong> <?= htmlspecialchars($estudiante['idusuario']) ?></p>
                    <p class="mb-1"><strong>Nombre Completo:</strong> <?= htmlspecialchars($estudiante['nombre']) ?></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Carrera:</strong> <?= htmlspecialchars($carrera['nombre_carrera']) ?> (<?= htmlspecialchars($carrera['cod_carrera']) ?>)</p>
                    <p class="mb-1"><strong>Materias en Historial:</strong> <span class="badge badge-primary"><?= $materias_carrera->num_rows ?></span></p>
                    <?php if ($info_apto): ?>
                    <p class="mb-0"><strong>Condición Académica:</strong> 
                        <?= obtenerBadgeEstadoConsulta($info_apto) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($info_apto): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert <?= ($info_apto['apto_grado_completo'] || $info_apto['apto_tsu']) ? 'alert-success' : 'alert-warning' ?> mb-0 py-2">
                        <h6 class="mb-2 font-weight-bold"><i class="fas fa-graduation-cap"></i> Auditoría de Requisitos de Grado:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nivel TSU (Trayectos 0-2):</strong><br>
                                <?= $info_apto['materias_aprobadas_tsu'] ?>/<?= $info_apto['total_materias_tsu'] ?> materias aprobadas<br>
                                <span class="badge badge-<?= $info_apto['porcentaje_tsu'] >= 90 ? 'success' : 'warning' ?>">
                                    <?= $info_apto['porcentaje_tsu'] ?>% completado
                                </span>
                                <?php if ($info_apto['apto_tsu']): ?>
                                    <span class="badge badge-success ml-2">APTO TSU</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary ml-2">Pendiente</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Grado Completo (Ingeniería / Licenciatura):</strong><br>
                                <?= $info_apto['materias_aprobadas_completo'] ?>/<?= $info_apto['total_materias_carrera'] ?> materias aprobadas<br>
                                <span class="badge badge-<?= $info_apto['porcentaje_completo'] >= 100 ? 'success' : 'info' ?>">
                                    <?= $info_apto['porcentaje_completo'] ?>% completado
                                </span>
                                <?php if ($info_apto['apto_grado_completo']): ?>
                                    <span class="badge badge-success ml-2">APTO GRADO COMPLETO</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary ml-2">Pendiente</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (!empty($info_apto['solo_trayecto_0'])): ?>
                        <div class="mt-2 pt-2 border-top small text-dark">
                            <i class="fas fa-info-circle text-primary mr-1"></i>
                            <strong>Estudiante en Trayecto Inicial:</strong> Solo tiene completadas las materias del Trayecto 0 (<?= $info_apto['aprobadas_t0'] ?>/<?= $info_apto['total_t0'] ?>). <strong>No es apto para grado</strong> porque le faltan las materias de los Trayectos 1, 2, 3 y 4 del plan de estudios.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- TABLA DE ASIGNATURAS Y NOTAS CON ACCIONES DIRECTAS -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-2">
            <h5 class="mb-0 font-weight-bold">
                <i class="fas fa-table mr-1"></i> Calificaciones por Asignatura
            </h5>
            <button type="button" class="btn btn-light btn-sm font-weight-bold text-success shadow-sm" id="btnCalificarPensum">
                <i class="fas fa-plus-circle mr-1"></i> Calificar Asignatura del Pensum
            </button>
        </div>
        <div class="card-body p-0">
            <!-- Aclaratoria sobre calificaciones sin cortes trimestrales -->
            <div class="px-3 pt-3 pb-2 bg-light border-bottom">
                <div class="d-flex align-items-center text-muted small">
                    <i class="fas fa-info-circle text-info fa-lg mr-2"></i>
                    <div>
                        <strong>Aclaratoria sobre evaluaciones:</strong> Las asignaturas asentadas en acta oficial (sistema anterior, convalidaciones o equivalencias) cuentan directamente con su <strong>Nota Final</strong> oficial. Cuando no apliquen evaluaciones trimestrales continuas, se muestran identificadas como <span class="badge badge-light border text-muted px-2 py-1"><i class="fas fa-archive text-secondary mr-1"></i> Carga Directa (Histórica)</span>.
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Trayecto</th>
                            <th>Materia</th>
                            <th>Código</th>
                            <th class="text-center" title="Corte evaluativo continuo del Trimestre 1">Trim. 1</th>
                            <th class="text-center" title="Corte evaluativo continuo del Trimestre 2">Trim. 2</th>
                            <th class="text-center" title="Corte evaluativo continuo del Trimestre 3">Trim. 3</th>
                            <th class="text-center">Nota Final</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Estado</th>
                            <th>Docente</th>
                            <th>Aprobado por</th>
                            <th>Periodo</th>
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $materias_aprobadas = 0;
                        $materias_reprobadas = 0;
                        $materias_sin_notas = 0;
                        $materias_con_notas = 0;
                        $todas_las_evaluaciones = [];
                        $total_intentos_repitientes_evaluados = 0;
                        
                        if ($materias_carrera) {
                            $materias_carrera->data_seek(0);
                        }
                        
                        while ($materia = $materias_carrera->fetch_assoc()): 
                            $mid = (int)$materia['id_materia'];
                            $intentos_materia = $historial_notas[$mid] ?? [];
                            $total_intentos = count($intentos_materia);
                            
                            if ($total_intentos > 0) {
                                $intento_actual = $intentos_materia[$total_intentos - 1];
                                $intentos_previos = array_slice($intentos_materia, 0, $total_intentos - 1);
                            } else {
                                $intento_actual = null;
                                $intentos_previos = [];
                            }
                            
                            $numero_trayecto_materia = (int)$materia['trayecto'];
                            $info_trayecto = obtenerInfoTrayecto($numero_trayecto_materia);
                            $nombre_trayecto = $info_trayecto['nombre_trayecto'];
                            $nota_min = (!empty($materia['es_proyecto_socio']) && $materia['es_proyecto_socio'] == 1) ? 16 : 12;
                            
                            $t1 = $intento_actual['trimestre_1'] ?? null;
                            $t2 = $intento_actual['trimestre_2'] ?? null;
                            $t3 = $intento_actual['trimestre_3'] ?? null;
                            $nota_final = $intento_actual['nota_final'] ?? null;
                            $t_exam = !empty($intento_actual['tipo_examen']) ? strtoupper($intento_actual['tipo_examen']) : 'R';
                            
                            if ($nota_final !== null) {
                                if ($nota_final >= $nota_min) {
                                    $estado = 'Aprobado';
                                    $badge_estado = 'success';
                                    $materias_aprobadas++;
                                } else {
                                    $estado = 'Reprobado';
                                    $badge_estado = 'danger';
                                    $materias_reprobadas++;
                                }
                                $materias_con_notas++;
                                $todas_las_evaluaciones[] = ['materia' => $materia['nombre_materia'], 'nota' => $nota_final];
                            } else {
                                $estado = 'Sin nota';
                                $badge_estado = 'secondary';
                                $materias_sin_notas++;
                            }

                            // Contar notas previas para el promedio general
                            if (!empty($intentos_previos)) {
                                foreach ($intentos_previos as $prev) {
                                    if ($prev['nota_final'] !== null) {
                                        $todas_las_evaluaciones[] = [
                                            'materia' => $materia['nombre_materia'] . ' (Repitiente)',
                                            'nota' => $prev['nota_final']
                                        ];
                                        $total_intentos_repitientes_evaluados++;
                                    }
                                }
                            }
                            
                            $ult_nota_prev = null;
                            if (!empty($intentos_previos)) {
                                $ultimo_prev = end($intentos_previos);
                                $ult_nota_prev = $ultimo_prev['nota_final'] ?? null;
                            }
                            
                            $t_badge_class = 'badge-secondary';
                            if ($t_exam === 'E') $t_badge_class = 'badge-primary';
                            elseif ($t_exam === 'P') $t_badge_class = 'badge-warning text-dark';
                            elseif ($t_exam === 'T') $t_badge_class = 'badge-success';
                            elseif ($t_exam === 'S') $t_badge_class = 'badge-info';
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($nombre_trayecto) ?></td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="font-weight-bold text-dark"><?= htmlspecialchars($materia['nombre_materia']) ?></span>
                                        <?php if (!empty($intentos_previos)): ?>
                                            <button class="btn btn-sm btn-outline-info ml-2 py-0 px-2 text-nowrap" 
                                                    type="button" 
                                                    data-toggle="collapse" 
                                                    data-target="#hist_materia_<?= $mid ?>" 
                                                    aria-expanded="false" 
                                                    aria-controls="hist_materia_<?= $mid ?>"
                                                    title="Haga clic para ver notas anteriores de repetición">
                                                <i class="fas fa-chevron-down flecha-toggle"></i> 
                                                <span class="badge badge-warning text-dark ml-1">
                                                    <?= $ult_nota_prev !== null ? 'Nota anterior: ' . number_format($ult_nota_prev, 1) : 'Repitiente (' . count($intentos_previos) . ')' ?>
                                                </span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($materia['cod_materia']) ?></td>
                                
                                <?php 
                                $tiene_desglose_trimestral = ($t1 !== null || $t2 !== null || $t3 !== null);
                                if ($tiene_desglose_trimestral): 
                                ?>
                                    <td class="text-center">
                                        <?php if ($t1 !== null): ?>
                                            <span class="badge badge-<?= $t1 >= $nota_min ? 'success' : 'danger' ?> p-2">
                                                <?= number_format($t1, 1) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted" title="Sin corte trimestral">-</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php if ($t2 !== null): ?>
                                            <span class="badge badge-<?= $t2 >= $nota_min ? 'success' : 'danger' ?> p-2">
                                                <?= number_format($t2, 1) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted" title="Sin corte trimestral">-</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php if ($t3 !== null): ?>
                                            <span class="badge badge-<?= $t3 >= $nota_min ? 'success' : 'danger' ?> p-2">
                                                <?= number_format($t3, 1) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted" title="Sin corte trimestral">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php elseif ($nota_final !== null): ?>
                                    <td colspan="3" class="text-center bg-light align-middle">
                                        <span class="badge badge-light border text-muted px-2 py-1 font-weight-normal" 
                                              style="font-size: 0.82rem;" 
                                              title="Calificación asentada directamente en acta oficial sin cortes trimestrales (Histórico / Equivalencia)">
                                            <i class="fas fa-archive text-secondary mr-1"></i> Carga Directa (Histórica)
                                        </span>
                                    </td>
                                <?php else: ?>
                                    <td colspan="3" class="text-center text-muted align-middle small font-italic">
                                        Sin cortes trimestrales
                                    </td>
                                <?php endif; ?>
                                
                                <td class="text-center">
                                    <?php if ($nota_final !== null): ?>
                                        <span class="badge badge-<?= $nota_final >= $nota_min ? 'success' : 'danger' ?> p-2" style="font-size: 0.95rem;">
                                            <?= number_format($nota_final, 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">En curso</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge <?= $t_badge_class ?> font-weight-bold px-2 py-1" title="Modalidad: <?= $t_exam ?>">
                                        <?= $t_exam ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge badge-<?= $badge_estado ?>"><?= $estado ?></span>
                                </td>
                                
                                <td>
                                    <?php if (!empty($intento_actual['nombre_docente'])): ?>
                                        <span class="font-weight-bold text-dark"><i class="fas fa-chalkboard-teacher text-primary mr-1"></i><?= htmlspecialchars($intento_actual['nombre_docente']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">No asignado</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <?php if (!empty($intento_actual['nombre_admin'])): ?>
                                        <span class="badge badge-light border text-dark" title="Aprobado por <?= htmlspecialchars($intento_actual['nombre_admin']) ?>">
                                            <i class="fas fa-user-check text-success mr-1"></i><?= htmlspecialchars($intento_actual['nombre_admin']) ?>
                                        </span>
                                    <?php elseif ($nota_final !== null): ?>
                                        <span class="badge badge-info text-dark small"><i class="fas fa-check mr-1"></i> Registrada</span>
                                    <?php else: ?>
                                        <span class="text-muted small">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td><?= htmlspecialchars($intento_actual['nombre_periodo'] ?? '-') ?></td>
                                <td class="text-center"><?= !empty($intento_actual['fecha_registro']) ? date('d/m/Y', strtotime($intento_actual['fecha_registro'])) : '-' ?></td>
                                
                                <!-- BOTÓN DE ACCIÓN CALIFICAR DIRECTO -->
                                <td class="text-center text-nowrap">
                                    <button type="button" 
                                            class="btn btn-sm btn-primary btn-abrir-calificar py-1 px-2 font-weight-bold shadow-sm"
                                            data-materia-id="<?= $mid ?>"
                                            data-materia-codigo="<?= htmlspecialchars($materia['cod_materia']) ?>"
                                            data-materia-nombre="<?= htmlspecialchars($materia['nombre_materia']) ?>"
                                            data-trayecto="<?= $numero_trayecto_materia ?>"
                                            data-es-proyecto="<?= (!empty($materia['es_proyecto_socio']) && $materia['es_proyecto_socio'] == 1) ? '1' : '0' ?>"
                                            data-nota-actual="<?= ($nota_final !== null) ? $nota_final : '' ?>"
                                            data-tipo-actual="<?= $t_exam ?>"
                                            data-periodo-id="<?= !empty($intento_actual['id_periodo']) ? $intento_actual['id_periodo'] : '' ?>"
                                            data-docente-id="<?= !empty($intento_actual['id_docente']) ? $intento_actual['id_docente'] : '' ?>"
                                            title="Calificar o Asignar Modalidad Especial (R, E, T, P, S)">
                                        <i class="fas fa-edit mr-1"></i> Calificar
                                    </button>
                                </td>
                            </tr>
                            
                            <?php if (!empty($intentos_previos)): ?>
                            <tr class="collapse bg-light" id="hist_materia_<?= $mid ?>">
                                <td colspan="14" class="p-3">
                                    <div class="card border-info shadow-sm mb-0">
                                        <div class="card-header bg-info text-white py-1 px-3 d-flex justify-content-between align-items-center">
                                            <small class="font-weight-bold mb-0">
                                                <i class="fas fa-history mr-1"></i> Historial de Intentos Anteriores (Repitiente) — <?= htmlspecialchars($materia['nombre_materia']) ?>
                                            </small>
                                            <span class="badge badge-light text-info"><?= count($intentos_previos) ?> intento(s) previo(s)</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped mb-0 text-center" style="font-size: 0.85rem;">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th class="text-left pl-3">Período Académico</th>
                                                            <th>Trimestre 1</th>
                                                            <th>Trimestre 2</th>
                                                            <th>Trimestre 3</th>
                                                            <th>Nota Final</th>
                                                            <th>Modalidad</th>
                                                            <th>Estado</th>
                                                            <th>Docente</th>
                                                            <th>Aprobado por</th>
                                                            <th>Fecha Registro</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($intentos_previos as $prev): 
                                                            $pt1 = $prev['trimestre_1'];
                                                            $pt2 = $prev['trimestre_2'];
                                                            $pt3 = $prev['trimestre_3'];
                                                            $pfinal = $prev['nota_final'];
                                                            $p_aprobado = ($pfinal !== null && $pfinal >= $nota_min);
                                                            $ptipo = !empty($prev['tipo_examen']) ? strtoupper($prev['tipo_examen']) : 'R';
                                                        ?>
                                                        <tr>
                                                            <td class="text-left pl-3 font-weight-bold">
                                                                <i class="fas fa-calendar-alt text-muted mr-1"></i> <?= htmlspecialchars($prev['nombre_periodo'] ?? '-') ?>
                                                            </td>
                                                            <?php 
                                                            $p_tiene_trimestres = ($pt1 !== null || $pt2 !== null || $pt3 !== null);
                                                            if ($p_tiene_trimestres): 
                                                            ?>
                                                                <td>
                                                                    <?php if ($pt1 !== null): ?>
                                                                        <span class="badge badge-<?= $pt1 >= $nota_min ? 'success' : 'danger' ?> px-2 py-1"><?= number_format($pt1, 1) ?></span>
                                                                    <?php else: ?>
                                                                        <span class="text-muted" title="Sin corte trimestral">-</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($pt2 !== null): ?>
                                                                        <span class="badge badge-<?= $pt2 >= $nota_min ? 'success' : 'danger' ?> px-2 py-1"><?= number_format($pt2, 1) ?></span>
                                                                    <?php else: ?>
                                                                        <span class="text-muted" title="Sin corte trimestral">-</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($pt3 !== null): ?>
                                                                        <span class="badge badge-<?= $pt3 >= $nota_min ? 'success' : 'danger' ?> px-2 py-1"><?= number_format($pt3, 1) ?></span>
                                                                    <?php else: ?>
                                                                        <span class="text-muted" title="Sin corte trimestral">-</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            <?php elseif ($pfinal !== null): ?>
                                                                <td colspan="3" class="text-center align-middle">
                                                                    <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.75rem;" title="Nota oficial asentada directamente en acta">
                                                                        <i class="fas fa-archive text-secondary mr-1"></i> Carga Directa (Histórica)
                                                                    </span>
                                                                </td>
                                                            <?php else: ?>
                                                                <td colspan="3" class="text-center text-muted small font-italic">
                                                                    Sin cortes trimestrales
                                                                </td>
                                                            <?php endif; ?>
                                                            <td>
                                                                <?php if ($pfinal !== null): ?>
                                                                    <span class="badge badge-<?= $p_aprobado ? 'success' : 'danger' ?> px-2 py-1 font-weight-bold" style="font-size: 0.9rem;">
                                                                        <?= number_format($pfinal, 1) ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-dark"><?= $ptipo ?></span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-<?= $p_aprobado ? 'success' : 'danger' ?>">
                                                                    <?= $p_aprobado ? 'Aprobado' : 'Reprobado' ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-left">
                                                                <?= !empty($prev['nombre_docente']) ? htmlspecialchars($prev['nombre_docente']) : '<span class="text-muted small">No asignado</span>' ?>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($prev['nombre_admin'])): ?>
                                                                    <span class="badge badge-light border text-dark"><?= htmlspecialchars($prev['nombre_admin']) ?></span>
                                                                <?php else: ?>
                                                                    <span class="text-muted small">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= !empty($prev['fecha_registro']) ? date('d/m/Y', strtotime($prev['fecha_registro'])) : '-' ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- RESUMEN ACADÉMICO CON PROMEDIO GENERAL E IRA -->
    <div class="row mt-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-chart-pie mr-1 text-primary"></i> Resumen de Rendimiento Académico</h6>
                </div>
                <div class="card-body">
                    <?php
                    $total_materias = $materias_carrera ? $materias_carrera->num_rows : 0;
                    $total_evaluaciones = count($todas_las_evaluaciones);
                    $promedio_general = $total_evaluaciones > 0 ? round(array_sum(array_column($todas_las_evaluaciones, 'nota')) / $total_evaluaciones, 2) : 0;
                    $porcentaje_completado = $total_materias > 0 ? round(($materias_con_notas / $total_materias) * 100, 1) : 0;
                    $ira_info = calcularIRAEstudiante($estudiante['id'], $carrera['id_carrera']);
                    ?>
                    
                    <div class="row">
                        <div class="col-4 text-center mb-3">
                            <h3 class="text-primary font-weight-bold"><?= $total_materias ?></h3>
                            <small class="font-weight-bold text-muted">Materias del Plan</small>
                        </div>
                        <div class="col-4 text-center mb-3">
                            <h3 class="<?= $promedio_general >= 12 ? 'text-success' : ($promedio_general > 0 ? 'text-warning' : 'text-secondary') ?> font-weight-bold">
                                <?= $promedio_general > 0 ? number_format($promedio_general, 2) : 'N/A' ?>
                            </h3>
                            <small class="font-weight-bold text-dark">Promedio General</small>
                            <div class="mt-1">
                                <span class="badge badge-info" title="Total de notas evaluadas contabilizadas"><?= $total_evaluaciones ?> notas</span>
                            </div>
                        </div>
                        <div class="col-4 text-center mb-3">
                            <h3 class="<?= ($ira_info['ira'] ?? 0) >= 12 ? 'text-success' : (($ira_info['ira'] ?? 0) > 0 ? 'text-warning' : 'text-secondary') ?> font-weight-bold">
                                <?= ($ira_info['ira'] ?? 0) > 0 ? number_format($ira_info['ira'], 3) : 'N/A' ?>
                            </h3>
                            <small class="font-weight-bold text-dark">IRA (Ponderado)</small>
                            <div class="mt-1">
                                <span class="badge badge-primary" title="Índice de Rendimiento Académico ponderado con todas las repeticiones">Ponderado UC</span>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <h3 class="text-success font-weight-bold"><?= $materias_aprobadas ?></h3>
                            <small class="font-weight-bold text-muted">Aprobadas</small>
                        </div>
                        <div class="col-6 text-center">
                            <h3 class="text-danger font-weight-bold"><?= $materias_reprobadas ?></h3>
                            <small class="font-weight-bold text-muted">Reprobadas</small>
                        </div>
                    </div>
                    
                    <div class="progress mt-3" style="height: 22px;">
                        <div class="progress-bar bg-success font-weight-bold" style="width: <?= $porcentaje_completado ?>%">
                            <?= $porcentaje_completado ?>% completado
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-info-circle mr-1 text-info"></i> Leyenda de Modalidades</h6>
                </div>
                <div class="card-body small">
                    <p class="mb-1"><span class="badge badge-secondary p-1 font-weight-bold">[R]</span> = Regular (Evaluación Ordinaria)</p>
                    <p class="mb-1"><span class="badge badge-warning text-dark p-1 font-weight-bold">[P]</span> = Plan Especial de Recuperación (PER: reprobada 6-11 pts, máx 12 pts, no T0/proy)</p>
                    <p class="mb-1"><span class="badge badge-success p-1 font-weight-bold">[T]</span> = Curso Intensivo (Previamente cursada, reemplaza definitiva sin borrar previo)</p>
                    <p class="mb-1"><span class="badge badge-primary p-1 font-weight-bold">[E]</span> = Extraordinario (Falta solo 1 materia para culminar y ya cursada)</p>
                    <p class="mb-2"><span class="badge badge-info p-1 font-weight-bold">[S]</span> = Suficiencia (Máx 2/año. Si reprueba &lt; 12 inscribe regular automático)</p>
                    <hr class="my-2">
                    <p class="mb-0 text-muted"><i class="fas fa-lightbulb text-warning mr-1"></i> Puede hacer clic en el botón <strong>"Calificar"</strong> de cualquier materia para registrar o cambiar calificaciones y modalidades con validación de requisitos en tiempo real.</p>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($estudiante && $carrera && (!$materias_carrera || $materias_carrera->num_rows == 0)): ?>
        <div class="alert alert-warning shadow-sm">
            Este estudiante no tiene materias inscritas o registradas en la carrera: <?= htmlspecialchars($carrera['nombre_carrera']) ?>
        </div>
    <?php endif; ?>

</div>

<!-- ============================================================== -->
<!-- MODAL INTERACTIVO DE CALIFICACIÓN Y MODALIDAD ESPECIAL (E, T, P, S) -->
<!-- ============================================================== -->
<div class="modal fade" id="modalCalificar" tabindex="-1" role="dialog" aria-labelledby="modalCalificarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold" id="modalCalificarLabel">
                    <i class="fas fa-award mr-2"></i>Calificar / Evaluación Especial
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCalificarModalidad">
                <input type="hidden" name="ajax_registrar_nota_modalidad" value="1">
                <input type="hidden" name="estudiante_id" id="modal_estudiante_id" value="<?= $estudiante['id'] ?? 0 ?>">
                <input type="hidden" name="id_materia" id="modal_input_id_materia" value="">
                
                <div class="modal-body p-4">
                    <!-- Ficha Resumen de la Asignatura y Estudiante -->
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h6 class="font-weight-bold text-dark mb-1" id="modal_materia_titulo">Seleccione Asignatura</h6>
                                <p class="text-muted small mb-0" id="modal_materia_subtitulo"></p>
                            </div>
                            <div class="col-md-5 text-md-right mt-2 mt-md-0">
                                <span class="text-muted small">Estudiante:</span><br>
                                <strong class="text-primary"><?= htmlspecialchars($estudiante['nombre'] ?? '') ?></strong>
                                <span class="badge badge-secondary ml-1"><?= htmlspecialchars($estudiante['idusuario'] ?? '') ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Si se abrió para calificar otra materia del pensum -->
                    <div class="form-group mb-3" id="selector_materia_pensum_box" style="display: none;">
                        <label class="font-weight-bold text-dark">
                            <i class="fas fa-book mr-1 text-primary"></i> Seleccionar Asignatura del Pensum: <span class="text-danger">*</span>
                        </label>
                        <select id="modal_select_pensum_materia" class="form-control">
                            <?= renderizarOpcionesMateriasPorTrayecto($materias_pensum_todas, null, '-- Seleccione Asignatura del Plan de Estudios --', ['data_attrs' => true]) ?>
                        </select>
                    </div>

                    <!-- Selector Visual de Modalidad / Tipo de Examen -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark d-block">
                            <i class="fas fa-sliders-h mr-1 text-primary"></i> Modalidad de Evaluación / Tipo de Examen:
                        </label>
                        <div class="btn-group btn-group-toggle w-100 flex-wrap" data-toggle="buttons" id="toggleModalidades">
                            <label class="btn btn-outline-secondary font-weight-bold active flex-fill m-1">
                                <input type="radio" name="tipo_examen" value="R" id="opt_r" checked> [R] Regular
                            </label>
                            <label class="btn btn-outline-warning text-dark font-weight-bold flex-fill m-1">
                                <input type="radio" name="tipo_examen" value="P" id="opt_p"> [P] PER
                            </label>
                            <label class="btn btn-outline-success font-weight-bold flex-fill m-1">
                                <input type="radio" name="tipo_examen" value="T" id="opt_t"> [T] Intensivo
                            </label>
                            <label class="btn btn-outline-primary font-weight-bold flex-fill m-1">
                                <input type="radio" name="tipo_examen" value="E" id="opt_e"> [E] Extraordinario
                            </label>
                            <label class="btn btn-outline-info font-weight-bold flex-fill m-1">
                                <input type="radio" name="tipo_examen" value="S" id="opt_s"> [S] Suficiencia
                            </label>
                        </div>
                    </div>

                    <!-- Caja de Feedback Dinámico de Reglas y Condiciones -->
                    <div id="modal_feedback_regla" class="mb-3">
                        <!-- Actualizado en tiempo real con JavaScript / AJAX -->
                    </div>

                    <!-- Fila de Calificación y Período -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-check-circle mr-1 text-success"></i> Calificación Obtenida: <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.1" min="0" max="20" name="nota" id="modal_input_nota" class="form-control form-control-lg font-weight-bold text-center" placeholder="0 - 20" required>
                                <div class="input-group-append">
                                    <span class="input-group-text font-weight-bold" id="modal_label_tope_nota">pts (max 20)</span>
                                </div>
                            </div>
                            <small id="modal_help_nota" class="form-text text-muted">Aprobatoria &ge; 12.0 pts (16.0 para Proyectos).</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-calendar-alt mr-1 text-info"></i> Período Académico: <span class="text-danger">*</span>
                            </label>
                            <select name="id_periodo" id="modal_select_periodo" class="form-control form-control-lg" required>
                                <?php foreach ($periodos_todos as $p): ?>
                                    <option value="<?= $p['id_periodo'] ?>" <?= (!empty($p['activo'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nombre_periodo']) ?> <?= (!empty($p['activo'])) ? '(Activo)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Docente Evaluador -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">
                            <i class="fas fa-chalkboard-teacher mr-1 text-primary"></i> Docente Evaluador / Jurado:
                        </label>
                        <select name="id_docente" id="modal_select_docente" class="form-control">
                            <option value="0">-- Sin docente específico / Asignar después --</option>
                            <?php foreach ($docentes_todos as $doc): ?>
                                <option value="<?= $doc['id'] ?>">
                                    <?= htmlspecialchars($doc['nombre']) ?> (C.I: <?= htmlspecialchars($doc['idusuario']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Checkbox de Desbloqueo Especial de Jefa de Control de Estudios -->
                    <div class="form-group mb-0 p-3 bg-light rounded border" id="modal_box_desbloqueo_jefa" style="display: none;">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="modal_caso_especial_jefa" name="caso_especial_jefa" value="1">
                            <label class="custom-control-label font-weight-bold text-dark" for="modal_caso_especial_jefa">
                                <i class="fas fa-unlock text-warning mr-1"></i> Desbloqueo Especial (Autorizado por Jefa de Control de Estudios)
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1">Marque para permitir presentar suficiencia adicional en caso de haber alcanzado el límite anual de 2 asignaturas por acreditación de conocimientos externos.</small>
                    </div>

                    <!-- Mensajes de Alerta al Guardar -->
                    <div id="modal_alerta_guardar" class="mt-3" style="display: none;"></div>
                </div>
                
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm" id="btnGuardarNotaModal">
                        <i class="fas fa-save mr-1"></i> Guardar Calificación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-success {
    background-color: #28a745 !important;
    color: white !important;
}
.bg-danger {
    background-color: #dc3545 !important;
    color: white !important;
}
.bg-secondary {
    background-color: #6c757d !important;
    color: white !important;
}
.table td, .table th {
    vertical-align: middle;
}
.flecha-toggle {
    transition: transform 0.2s ease-in-out;
}
.btn-calificar {
    font-size: 0.8rem;
}
#sugerenciasEstudiantes .list-group-item:hover {
    background-color: #e9ecef;
    cursor: pointer;
}
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.75rem;
    }
    .badge {
        font-size: 0.7rem;
    }
}
</style>

<script>
$(document).ready(function() {
    // 1. Manejo del acordeón de repitientes
    $('.collapse').on('show.bs.collapse', function() {
        $('button[data-target="#' + $(this).attr('id') + '"] .flecha-toggle')
            .removeClass('fa-chevron-down')
            .addClass('fa-chevron-up');
    }).on('hide.bs.collapse', function() {
        $('button[data-target="#' + $(this).attr('id') + '"] .flecha-toggle')
            .removeClass('fa-chevron-up')
            .addClass('fa-chevron-down');
    });

    // 2. Búsqueda en tiempo real con sugerencias AJAX
    var timerBusqueda = null;
    $('#cedula').on('input', function() {
        var query = $(this).val().trim();
        clearTimeout(timerBusqueda);
        
        if (query.length < 2) {
            $('#sugerenciasEstudiantes').hide().empty();
            $('#spinnerBusqueda').hide();
            return;
        }

        $('#spinnerBusqueda').show();
        timerBusqueda = setTimeout(function() {
            $.post('consulta_notas.php', { ajax_buscar_estudiantes: 1, termino: query }, function(data) {
                $('#spinnerBusqueda').hide();
                var $sug = $('#sugerenciasEstudiantes');
                $sug.empty();
                
                if (data.success && data.estudiantes && data.estudiantes.length > 0) {
                    data.estudiantes.forEach(function(est) {
                        var ced = est.cedula || est.idusuario;
                        var nom = est.nombre || '';
                        var car = est.nombre_carrera || est.carrera || '';
                        var item = $('<a href="#" class="list-group-item list-group-item-action py-2 px-3">' +
                                     '<strong>' + ced + '</strong> — ' + nom + ' <small class="text-muted">(' + car + ')</small>' +
                                     '</a>');
                        item.on('click', function(e) {
                            e.preventDefault();
                            $('#cedula').val(ced);
                            $sug.hide();
                            $('#formBuscarEstudiante').submit();
                        });
                        $sug.append(item);
                    });
                    $sug.show();
                } else {
                    $sug.hide();
                }
            }, 'json').fail(function() {
                $('#spinnerBusqueda').hide();
            });
        }, 250);
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#formBuscarEstudiante').length) {
            $('#sugerenciasEstudiantes').hide();
        }
    });

    // 3. Abrir Modal para materia específica desde la tabla
    $(document).on('click', '.btn-abrir-calificar', function() {
        var mid = $(this).data('materia-id');
        var cod = $(this).data('materia-codigo');
        var nom = $(this).data('materia-nombre');
        var tray = $(this).data('trayecto');
        var notaAct = $(this).data('nota-actual');
        var tipoAct = $(this).data('tipo-actual') || 'R';
        var perId = $(this).data('periodo-id');
        var docId = $(this).data('docente-id');

        $('#modal_input_id_materia').val(mid);
        $('#modal_materia_titulo').text('[' + cod + '] ' + nom);
        $('#modal_materia_subtitulo').text('Trayecto ' + tray + (notaAct ? ' — Calificación actual: ' + parseFloat(notaAct).toFixed(1) + ' pts' : ' — Sin calificación previa'));
        $('#selector_materia_pensum_box').hide();

        if (notaAct !== '' && notaAct !== null) {
            $('#modal_input_nota').val(parseFloat(notaAct).toFixed(1));
        } else {
            $('#modal_input_nota').val('');
        }

        if (perId) $('#modal_select_periodo').val(perId);
        if (docId) $('#modal_select_docente').val(docId);

        // Seleccionar radio de modalidad correspondiente
        $('#toggleModalidades label').removeClass('active');
        $('input[name="tipo_examen"]').prop('checked', false);
        $('input[name="tipo_examen"][value="' + tipoAct + '"]').prop('checked', true).closest('label').addClass('active');

        $('#modal_caso_especial_jefa').prop('checked', false);
        $('#modal_alerta_guardar').hide().empty();

        verificarReglasModal();
        $('#modalCalificar').modal('show');
    });

    // 4. Abrir Modal para seleccionar cualquier asignatura del Pensum
    $('#btnCalificarPensum').on('click', function() {
        $('#modal_input_id_materia').val('');
        $('#modal_materia_titulo').text('Seleccione una asignatura del Pensum');
        $('#modal_materia_subtitulo').text('Puede calificar cualquier materia de la carrera bajo la modalidad adecuada');
        $('#selector_materia_pensum_box').show();
        $('#modal_select_pensum_materia').val('');
        $('#modal_input_nota').val('');
        $('#modal_caso_especial_jefa').prop('checked', false);
        $('#modal_alerta_guardar').hide().empty();

        $('#toggleModalidades label').removeClass('active');
        $('input[name="tipo_examen"][value="R"]').prop('checked', true).closest('label').addClass('active');

        verificarReglasModal();
        $('#modalCalificar').modal('show');
    });

    $('#modal_select_pensum_materia').on('change', function() {
        var opt = $(this).find('option:selected');
        var mid = $(this).val();
        if (mid) {
            $('#modal_input_id_materia').val(mid);
            $('#modal_materia_titulo').text('[' + opt.data('codigo') + '] ' + opt.data('nombre'));
            $('#modal_materia_subtitulo').text('Trayecto ' + opt.data('trayecto') + ' (' + opt.data('uc') + ' UC)');
            verificarReglasModal();
        } else {
            $('#modal_input_id_materia').val('');
            $('#modal_feedback_regla').empty();
        }
    });

    // 5. Cambio en la modalidad de examen (Radio Buttons)
    $('input[name="tipo_examen"]').on('change', function() {
        verificarReglasModal();
    });

    $('#modal_caso_especial_jefa').on('change', function() {
        verificarReglasModal();
    });

    // 6. Función para verificar reglas y condiciones vía AJAX
    function verificarReglasModal() {
        var estId = $('#modal_estudiante_id').val();
        var matId = $('#modal_input_id_materia').val();
        var tipo = $('input[name="tipo_examen"]:checked').val() || 'R';
        var casoJefa = $('#modal_caso_especial_jefa').is(':checked') ? 1 : 0;
        var $fb = $('#modal_feedback_regla');
        var $tope = $('#modal_label_tope_nota');
        var $inputNota = $('#modal_input_nota');
        var $helpNota = $('#modal_help_nota');
        var $btnSubmit = $('#btnGuardarNotaModal');

        if (!matId) {
            $fb.html('<div class="alert alert-secondary py-2 small mb-0"><i class="fas fa-info-circle mr-1"></i> Seleccione una materia para verificar las condiciones.</div>');
            return;
        }

        // Ajustar visual de tope
        if (tipo === 'P') {
            $tope.text('pts (Tope PER: 12.0)');
            $inputNota.attr('max', '12');
            $helpNota.text('La nota máxima admisible para aprobar en PER es 12.0 puntos.');
            $('#modal_box_desbloqueo_jefa').hide();
        } else if (tipo === 'S') {
            $tope.text('pts (max 20)');
            $inputNota.attr('max', '20');
            $helpNota.text('Aprobatoria ≥ 12.0 pts. Si reprueba (< 12 pts), el sistema inscribe automáticamente en período regular.');
            $('#modal_box_desbloqueo_jefa').show();
        } else {
            $tope.text('pts (max 20)');
            $inputNota.attr('max', '20');
            $helpNota.text('Aprobatoria ≥ 12.0 pts (≥ 16.0 para Proyectos).');
            $('#modal_box_desbloqueo_jefa').hide();
        }

        $.post('consulta_notas.php', {
            ajax_verificar_modalidad: 1,
            estudiante_id: estId,
            id_materia: matId,
            tipo_examen: tipo,
            caso_especial_jefa: casoJefa
        }, function(res) {
            if (res.apto) {
                $fb.html('<div class="alert alert-success py-2 small mb-0 shadow-sm">' +
                         '<i class="fas fa-check-circle mr-1"></i> <strong>Cumple Condiciones:</strong> ' + res.mensaje +
                         '</div>');
                $btnSubmit.prop('disabled', false);
            } else {
                $fb.html('<div class="alert alert-danger py-2 small mb-0 shadow-sm">' +
                         '<i class="fas fa-exclamation-triangle mr-1"></i> <strong>Restricción:</strong> ' + res.mensaje +
                         '</div>');
                // No deshabilitamos completamente para permitir al admin corregir parámetros
                $btnSubmit.prop('disabled', false);
            }
        }, 'json');
    }

    // 7. Enviar formulario para registrar calificación
    $('#formCalificarModalidad').on('submit', function(e) {
        e.preventDefault();
        var matId = $('#modal_input_id_materia').val();
        if (!matId) {
            alert('Por favor seleccione una asignatura válida.');
            return;
        }

        var $btn = $('#btnGuardarNotaModal');
        var $alerta = $('#modal_alerta_guardar');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...');
        $alerta.hide().empty();

        $.post('consulta_notas.php', $(this).serialize(), function(res) {
            if (res.success) {
                $alerta.removeClass('alert-danger').addClass('alert-success')
                       .html('<i class="fas fa-check-circle mr-1"></i> ' + res.message)
                       .fadeIn();
                setTimeout(function() {
                    window.location.reload();
                }, 800);
            } else {
                $alerta.removeClass('alert-success').addClass('alert-danger')
                       .html('<i class="fas fa-times-circle mr-1"></i> ' + res.message)
                       .fadeIn();
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Guardar Calificación');
            }
        }, 'json').fail(function() {
            $alerta.removeClass('alert-success').addClass('alert-danger')
                   .html('<i class="fas fa-times-circle mr-1"></i> Error de conexión con el servidor.')
                   .fadeIn();
            $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Guardar Calificación');
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>