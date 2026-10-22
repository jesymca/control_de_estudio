<?php
/**
 * ====================================================================================================
 * SISTEMA DE CONTROL DE ESTUDIOS - PANEL DE ESTUDIANTE VOCERO
 * ====================================================================================================
 * Archivo: estudiante/vocero.php
 * Descripción: Permite a los estudiantes designados como voceros consultar de forma segura el rendimiento
 *              académico y calificaciones de sus compañeros de sección.
 * 
 * Políticas de Seguridad Implementadas:
 * 1. Validación de Sesión: Obligatorio estar autenticado con rol de estudiante y tener el flag de vocero
 *    activo en la base de datos (evita manipulación en sesión).
 * 2. Consulta de Notas vía POST: Se eliminaron las peticiones GET por URL para proteger los datos y
 *    evitar exposición de identificadores en historial de navegación.
 * 3. Modal Interactivo: Las calificaciones se presentan en un modal dinámico sin recargar la página.
 * 4. Validador de Pertenencia Estricta: Se valida en base de datos que el estudiante solicitado pertenezca
 *    exactamente a la misma sección activa asignada al vocero en sesión (previene ataques IDOR).
 * 5. Protección CSRF: Cada petición POST valida el token de seguridad contra la sesión activa.
 * 6. Buscador en Tiempo Real: Filtrado instantáneo por nombre y cédula en tablas y tarjetas móviles.
 * ====================================================================================================
 */

// Configuración de visualización de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Cargar librerías y arranque de sesión usando ruta absoluta basada en __DIR__
require_once(__DIR__ . '/../funciones/functions.php');

// Generar o mantener token CSRF para peticiones seguras
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ====================================================================================================
// CONTROLADOR DE PETICIONES AJAX (POST): OBTENCIÓN SEGURA DE NOTAS PARA EL MODAL
// ====================================================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'obtener_notas_estudiante') {
    // Establecer cabecera de respuesta en formato JSON
    header('Content-Type: application/json; charset=utf-8');
    
    // ------------------------------------------------------------------------------------------------
    // VALIDACIÓN DE SEGURIDAD 1: Autenticación básica y rol de estudiante
    // ------------------------------------------------------------------------------------------------
    if (!isLoggedIn() || !isEstudiante()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Sesión no válida o expirada. Por favor inicie sesión nuevamente.']);
        exit();
    }
    
    // ------------------------------------------------------------------------------------------------
    // VALIDACIÓN DE SEGURIDAD 2: Identidad del usuario en sesión
    // ------------------------------------------------------------------------------------------------
    $session_uid = intval($_SESSION['user']['id'] ?? 0);
    if ($session_uid <= 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Identificador de sesión inválido.']);
        exit();
    }
    
    // ------------------------------------------------------------------------------------------------
    // VALIDACIÓN DE SEGURIDAD 3: Verificar en BD que el usuario en sesión es vocero activo
    // ------------------------------------------------------------------------------------------------
    if (!esVoceroUsuario($session_uid)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acceso denegado: Tu usuario no posee privilegios de vocero activo.']);
        exit();
    }
    
    // ------------------------------------------------------------------------------------------------
    // VALIDACIÓN DE SEGURIDAD 4: Token de protección contra CSRF
    // ------------------------------------------------------------------------------------------------
    $token_recibido = $_POST['csrf_token'] ?? '';
    if (empty($token_recibido) || !hash_equals($_SESSION['csrf_token'], $token_recibido)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Fallo de seguridad CSRF. Actualice la página e intente de nuevo.']);
        exit();
    }
    
    // ------------------------------------------------------------------------------------------------
    // VALIDACIÓN DE SEGURIDAD 5: Obtener sección activa del vocero en sesión
    // ------------------------------------------------------------------------------------------------
    $seccion_vocero = obtenerSeccionEstudiante($db, $session_uid);
    if (!$seccion_vocero || empty($seccion_vocero['id_seccion'])) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No se encontró una sección activa asignada a tu usuario.']);
        exit();
    }
    
    $id_seccion_vocero = intval($seccion_vocero['id_seccion']);
    $estudiante_id = intval($_POST['estudiante_id'] ?? 0);
    
    if ($estudiante_id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Parámetro de estudiante no válido.']);
        exit();
    }
    
    // ------------------------------------------------------------------------------------------------
    // VALIDACIÓN DE SEGURIDAD 6 (CRÍTICA): Verificar que el estudiante pertenece a la sección del vocero
    // Previene manipulación de parámetros (IDOR) para que un vocero solo consulte su propia sección
    // ------------------------------------------------------------------------------------------------
    $stmt_check = $db->prepare("
        SELECT u.id, u.idusuario AS cedula, u.nombre, u.email
        FROM users u
        INNER JOIN estudiante_seccion es ON u.id = es.id_usuario
        WHERE u.id = ? AND es.id_seccion = ? AND es.estatus = 'activo'
        LIMIT 1
    ");
    $stmt_check->bind_param("ii", $estudiante_id, $id_seccion_vocero);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    
    if ($res_check->num_rows === 0) {
        // Registrar intento de acceso indebido en auditoría de seguridad
        if (function_exists('registrarAuditoria')) {
            registrarAuditoria(
                'ACCESS_DENIED',
                'users',
                $estudiante_id,
                null,
                ['seccion_vocero' => $id_seccion_vocero],
                'Seguridad Vocero',
                'Intento de consulta no autorizada de notas de estudiante fuera de sección por vocero ID: ' . $session_uid
            );
        }
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acceso denegado: El estudiante solicitado no pertenece a tu sección académica.']);
        exit();
    }
    
    $datos_estudiante = $res_check->fetch_assoc();
    
    // ------------------------------------------------------------------------------------------------
    // CONSULTA DE RENDIMIENTO ACADÉMICO: Carrera, notas, plan de estudio y evaluación de grado
    // ------------------------------------------------------------------------------------------------
    $carrera = obtenerCarreraEstudiante($estudiante_id);
    if (!$carrera) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No se encontró la carrera asociada a este estudiante.']);
        exit();
    }
    
    $materias_carrera = obtenerMateriasCarrera($carrera['id_carrera']);
    $notas_estudiante = obtenerNotasEstudianteConsulta($estudiante_id);
    $info_apto = esAptoParaGradoConsulta($estudiante_id, $carrera['id_carrera']);
    
    // ------------------------------------------------------------------------------------------------
    // RENDERIZADO DEL CONTENIDO HTML DEL MODAL (Buffer de salida)
    // ------------------------------------------------------------------------------------------------
    ob_start();
    ?>
    <div class="container-fluid p-0">
        <!-- Tarjeta de Información General del Estudiante -->
        <div class="card mb-3 border-left-primary shadow-sm">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h5 class="font-weight-bold text-primary mb-1">
                            <i class="fas fa-user-graduate mr-2"></i><?= htmlspecialchars($datos_estudiante['nombre']) ?>
                        </h5>
                        <div class="text-muted small">
                            <span><i class="fas fa-id-card mr-1"></i> Cédula: <strong><?= htmlspecialchars($datos_estudiante['cedula']) ?></strong></span>
                            <span class="mx-2">|</span>
                            <span><i class="fas fa-graduation-cap mr-1"></i> Carrera: <strong><?= htmlspecialchars($carrera['nombre_carrera']) ?> (<?= htmlspecialchars($carrera['cod_carrera']) ?>)</strong></span>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-right mt-2 mt-md-0">
                        <span class="badge badge-info px-3 py-2 text-uppercase font-weight-bold">
                            <i class="fas fa-users mr-1"></i> Sección: <?= htmlspecialchars($seccion_vocero['codigo_seccion']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluación de Aptitud para Grado (TSU / Grado Completo) -->
        <?php if ($info_apto): ?>
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body p-3 <?= ($info_apto['apto_grado_completo'] || $info_apto['apto_tsu']) ? 'bg-light-success border border-success' : 'bg-light-warning border border-warning' ?>" style="border-radius: 8px;">
                <h6 class="font-weight-bold mb-2 <?= ($info_apto['apto_grado_completo'] || $info_apto['apto_tsu']) ? 'text-success' : 'text-warning' ?>">
                    <i class="fas fa-award mr-1"></i> Evaluación Académica para Grado:
                </h6>
                <div class="row text-center">
                    <div class="col-6 border-right">
                        <div class="small font-weight-bold text-muted">TSU (Trayectos 0 al 2)</div>
                        <div class="h6 mb-1 font-weight-bold text-dark">
                            <?= $info_apto['materias_aprobadas_tsu'] ?> / <?= $info_apto['total_materias_tsu'] ?> Aprobadas
                        </div>
                        <span class="badge badge-<?= $info_apto['porcentaje_tsu'] >= 90 ? 'success' : 'warning' ?>">
                            <?= $info_apto['porcentaje_tsu'] ?>% Completado
                        </span>
                        <?php if ($info_apto['apto_tsu']): ?>
                            <span class="badge badge-success ml-1"><i class="fas fa-check-circle"></i> APTO TSU</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                        <div class="small font-weight-bold text-muted">Grado Completo (Ingeniería / Lic.)</div>
                        <div class="h6 mb-1 font-weight-bold text-dark">
                            <?= $info_apto['materias_aprobadas_completo'] ?> / <?= $info_apto['total_materias_carrera'] ?> Aprobadas
                        </div>
                        <span class="badge badge-<?= $info_apto['porcentaje_completo'] >= 100 ? 'success' : 'info' ?>">
                            <?= $info_apto['porcentaje_completo'] ?>% Completado
                        </span>
                        <?php if ($info_apto['apto_grado_completo']): ?>
                            <span class="badge badge-success ml-1"><i class="fas fa-check-circle"></i> APTO COMPLETO</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php
        // Procesamiento estadístico de materias y calificaciones
        $materias_aprobadas = 0;
        $materias_reprobadas = 0;
        $materias_sin_notas = 0;
        $suma_promedios = 0;
        $materias_con_notas = 0;
        $lista_materias = [];
        
        if ($materias_carrera && $materias_carrera->num_rows > 0) {
            $materias_carrera->data_seek(0);
            while ($m = $materias_carrera->fetch_assoc()) {
                $lista_materias[] = $m;
            }
        }
        
        foreach ($lista_materias as $mat) {
            $id_m = $mat['id_materia'];
            $nota_reg = $notas_estudiante[$id_m] ?? null;
            $num_trayecto = (int)$mat['trayecto'];
            $campo_t = 'trayecto_' . $num_trayecto;
            
            if ($nota_reg && isset($nota_reg[$campo_t]) && $nota_reg[$campo_t] !== null) {
                $val_nota = (float)$nota_reg[$campo_t];
                $suma_promedios += $val_nota;
                $materias_con_notas++;
                if ($val_nota >= 12) {
                    $materias_aprobadas++;
                } else {
                    $materias_reprobadas++;
                }
            } else {
                $materias_sin_notas++;
            }
        }
        
        $total_materias_plan = count($lista_materias);
        $promedio_general = $materias_con_notas > 0 ? round($suma_promedios / $materias_con_notas, 1) : 0;
        $porcentaje_avance = $total_materias_plan > 0 ? round(($materias_con_notas / $total_materias_plan) * 100, 1) : 0;
        ?>

        <!-- Métricas Rápidas y Resumen Académico -->
        <div class="row text-center mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="p-2 bg-light rounded border shadow-sm">
                    <div class="h5 mb-0 font-weight-bold text-primary"><?= $total_materias_plan ?></div>
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Materias Plan</small>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="p-2 bg-light rounded border shadow-sm">
                    <div class="h5 mb-0 font-weight-bold text-success"><?= $materias_aprobadas ?></div>
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Aprobadas</small>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="p-2 bg-light rounded border shadow-sm">
                    <div class="h5 mb-0 font-weight-bold text-danger"><?= $materias_reprobadas ?></div>
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Reprobadas</small>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="p-2 bg-light rounded border shadow-sm">
                    <div class="h5 mb-0 font-weight-bold text-dark">
                        <span class="badge badge-<?= $promedio_general >= 12 ? 'success' : ($promedio_general > 0 ? 'warning' : 'secondary') ?>">
                            <?= $promedio_general > 0 ? $promedio_general : 'N/A' ?>
                        </span>
                    </div>
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Promedio</small>
                </div>
            </div>
        </div>

        <!-- Barra de Progreso del Plan de Estudio -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1 small font-weight-bold">
                <span class="text-muted"><i class="fas fa-chart-line mr-1"></i> Progreso Curricular:</span>
                <span class="text-primary"><?= $porcentaje_avance ?>% de materias cursadas</span>
            </div>
            <div class="progress" style="height: 12px; border-radius: 6px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $total_materias_plan > 0 ? ($materias_aprobadas / $total_materias_plan)*100 : 0 ?>%;" title="Aprobadas: <?= $materias_aprobadas ?>"></div>
                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $total_materias_plan > 0 ? ($materias_reprobadas / $total_materias_plan)*100 : 0 ?>%;" title="Reprobadas: <?= $materias_reprobadas ?>"></div>
                <div class="progress-bar bg-secondary" role="progressbar" style="width: <?= $total_materias_plan > 0 ? ($materias_sin_notas / $total_materias_plan)*100 : 0 ?>%;" title="Pendientes: <?= $materias_sin_notas ?>"></div>
            </div>
        </div>

        <!-- Listado Detallado de Materias y Calificaciones -->
        <h6 class="font-weight-bold text-dark mb-2">
            <i class="fas fa-book mr-1"></i> Calificaciones por Asignatura:
        </h6>

        <?php if (empty($lista_materias)): ?>
            <div class="alert alert-info text-center small">No hay materias registradas en el pensum de esta carrera.</div>
        <?php else: ?>
            <!-- Vista de Tabla para Pantallas Medianas y Grandes -->
            <div class="table-responsive d-none d-md-block" style="max-height: 420px; overflow-y: auto;">
                <table class="table table-bordered table-hover table-sm text-center mb-0">
                    <thead class="thead-light" style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width: 90px;">Trayecto</th>
                            <th class="text-left">Asignatura</th>
                            <th style="width: 100px;">Código</th>
                            <th style="width: 80px;">Nota</th>
                            <th style="width: 100px;">Estado</th>
                            <th style="width: 120px;">Período</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista_materias as $mat): 
                            $id_m = $mat['id_materia'];
                            $nota_reg = $notas_estudiante[$id_m] ?? null;
                            $num_trayecto = (int)$mat['trayecto'];
                            $info_t = obtenerInfoTrayecto($num_trayecto);
                            $nombre_t = $info_t['nombre_trayecto'] ?? ('Trayecto ' . $num_trayecto);
                            $campo_t = 'trayecto_' . $num_trayecto;
                            
                            $tiene_nota = false;
                            $nota_valor = null;
                            $estado = 'Sin notas';
                            $badge_class = 'secondary';
                            
                            if ($nota_reg && isset($nota_reg[$campo_t]) && $nota_reg[$campo_t] !== null) {
                                $nota_valor = (float)$nota_reg[$campo_t];
                                $tiene_nota = true;
                                if ($nota_valor >= 12) {
                                    $estado = 'Aprobado';
                                    $badge_class = 'success';
                                } else {
                                    $estado = 'Reprobado';
                                    $badge_class = 'danger';
                                }
                            }
                        ?>
                        <tr>
                            <td class="small font-weight-bold text-muted"><?= htmlspecialchars($nombre_t) ?></td>
                            <td class="text-left font-weight-bold text-dark small"><?= htmlspecialchars($mat['nombre_materia']) ?></td>
                            <td class="small text-muted font-monospace"><?= htmlspecialchars($mat['cod_materia']) ?></td>
                            <td>
                                <?php if ($tiene_nota): ?>
                                    <span class="badge badge-pill <?= $nota_valor >= 12 ? 'badge-success' : 'badge-danger' ?> px-2 py-1 font-weight-bold" style="font-size: 0.85rem;">
                                        <?= $nota_valor ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $badge_class ?> px-2 py-1 small">
                                    <?= $estado ?>
                                </span>
                            </td>
                            <td class="small text-muted">
                                <?= ($nota_reg && !empty($nota_reg['nombre_periodo'])) ? htmlspecialchars($nota_reg['nombre_periodo']) : '-' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Vista de Tarjetas Responsivas para Móviles -->
            <div class="d-block d-md-none" style="max-height: 400px; overflow-y: auto;">
                <?php foreach ($lista_materias as $mat): 
                    $id_m = $mat['id_materia'];
                    $nota_reg = $notas_estudiante[$id_m] ?? null;
                    $num_trayecto = (int)$mat['trayecto'];
                    $info_t = obtenerInfoTrayecto($num_trayecto);
                    $nombre_t = $info_t['nombre_trayecto'] ?? ('Trayecto ' . $num_trayecto);
                    $campo_t = 'trayecto_' . $num_trayecto;
                    
                    $tiene_nota = false;
                    $nota_valor = null;
                    $estado = 'Sin notas';
                    $badge_class = 'secondary';
                    
                    if ($nota_reg && isset($nota_reg[$campo_t]) && $nota_reg[$campo_t] !== null) {
                        $nota_valor = (float)$nota_reg[$campo_t];
                        $tiene_nota = true;
                        if ($nota_valor >= 12) {
                            $estado = 'Aprobado';
                            $badge_class = 'success';
                        } else {
                            $estado = 'Reprobado';
                            $badge_class = 'danger';
                        }
                    }
                ?>
                <div class="card mb-2 border shadow-sm">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge badge-light border text-muted small font-weight-bold"><?= htmlspecialchars($nombre_t) ?></span>
                            <span class="badge badge-<?= $badge_class ?> small"><?= $estado ?></span>
                        </div>
                        <div class="font-weight-bold text-dark small mb-1"><?= htmlspecialchars($mat['nombre_materia']) ?></div>
                        <div class="d-flex justify-content-between align-items-center small text-muted">
                            <span>Código: <code><?= htmlspecialchars($mat['cod_materia']) ?></code></span>
                            <span>Nota: 
                                <?php if ($tiene_nota): ?>
                                    <strong class="<?= $nota_valor >= 12 ? 'text-success' : 'text-danger' ?>"><?= $nota_valor ?></strong>
                                <?php else: ?>
                                    <span>-</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    $modal_html = ob_get_clean();
    
    // Retorno exitoso en formato JSON
    echo json_encode([
        'success'           => true,
        'estudiante_nombre' => $datos_estudiante['nombre'],
        'cedula'            => $datos_estudiante['cedula'],
        'seccion'           => $seccion_vocero['codigo_seccion'],
        'html'              => $modal_html
    ]);
    exit();
}

// ====================================================================================================
// VISTA PRINCIPAL (GET): PANEL DE LISTADO DE COMPAÑEROS DE SECCIÓN
// ====================================================================================================

// 1. Control de acceso: Verificar que el usuario sea estudiante
if (!isLoggedIn() || !isEstudiante()) {
    $_SESSION['msg'] = "Debes iniciar sesión como estudiante para acceder";
    header('location: ../login.php');
    exit();
}

// 2. Control de acceso: Verificar que el estudiante esté registrado como vocero
$uid = intval($_SESSION['user']['id']);
$is_vocero = esVoceroUsuario($uid);

if (!$is_vocero) {
    $_SESSION['msg'] = "Acceso denegado: Esta sección es exclusiva para voceros de sección.";
    header('location: index.php');
    exit();
}

// 3. Registrar auditoría de visita a la página
visita();

// 4. Identificar la sección asignada al vocero en la base de datos
$seccion = obtenerSeccionEstudiante($db, $uid);
$estudiantes = [];

if ($seccion && !empty($seccion['id_seccion'])) {
    $estudiantes = obtenerEstudiantesConNotasSeccion($seccion['id_seccion']);
}

// 5. Inclusión de cabecera visual del panel estudiante
$titulopag = "Panel del Vocero - Consulta de Notas";
include(__DIR__ . "/includes/head.php");
?>

<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    
    <!-- Encabezado de la página -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-1 font-weight-bold">
                <i class="fas fa-microphone-alt text-primary mr-2"></i>Panel del Vocero
            </h1>
            <p class="text-muted small mb-0">Consulta académica y seguimiento de notas de los compañeros de tu sección.</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="index.php" class="btn btn-secondary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Volver al Inicio
            </a>
        </div>
    </div>

    <!-- Si se encontró sección activa para el vocero -->
    <?php if ($seccion): ?>
        
        <!-- Banner informativo de la sección -->
        <div class="card mb-4 border-left-info shadow-sm">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="h5 mb-1 font-weight-bold text-dark">
                            <i class="fas fa-users text-info mr-2"></i>Sección: 
                            <span class="badge badge-info px-3 py-2 text-uppercase"><?= htmlspecialchars($seccion['codigo_seccion']) ?></span>
                        </div>
                        <div class="text-muted small">
                            <span><i class="fas fa-graduation-cap mr-1"></i> Carrera: <strong><?= htmlspecialchars($seccion['nombre_carrera']) ?></strong></span>
                            <span class="mx-2">|</span>
                            <span><i class="fas fa-layer-group mr-1"></i> Trayecto: <strong><?= htmlspecialchars($seccion['numero_trayecto']) ?></strong></span>
                            <span class="mx-2">|</span>
                            <span><i class="fas fa-sun mr-1"></i> Turno: <strong><?= htmlspecialchars($seccion['turno']) ?></strong></span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-right mt-2 mt-md-0">
                        <span class="badge badge-pill badge-primary px-3 py-2 font-weight-bold">
                            <i class="fas fa-user-check mr-1"></i> Total en Sección: <?= count($estudiantes) ?> estudiantes
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta Principal con Buscador en Tiempo Real y Tabla de Estudiantes -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <h5 class="mb-2 mb-md-0 font-weight-bold text-primary">
                    <i class="fas fa-list-ul mr-2"></i>Nómina de Estudiantes de la Sección
                </h5>
                <span class="badge badge-light border text-muted px-2 py-1 small" id="contadorResultados">
                    Mostrando <strong id="cantVisible"><?= count($estudiantes) ?></strong> de <?= count($estudiantes) ?>
                </span>
            </div>
            
            <div class="card-body p-3">
                
                <?php if (empty($estudiantes)): ?>
                    <div class="alert alert-info text-center py-4 mb-0">
                        <i class="fas fa-info-circle fa-2x mb-2 d-block text-info"></i>
                        <strong>No hay estudiantes registrados activamente en tu sección.</strong>
                    </div>
                <?php else: ?>
                    
                    <!-- ================================================================================= -->
                    <!-- BUSCADOR EN TIEMPO REAL CON FILTRADO INSTANTÁNEO POR NOMBRE O CÉDULA             -->
                    <!-- ================================================================================= -->
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 col-lg-5">
                            <div class="input-group shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0 text-muted">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" 
                                       id="buscadorEstudiantes" 
                                       class="form-control border-left-0 border-right-0" 
                                       placeholder="Buscar estudiante por nombre o cédula..." 
                                       autocomplete="off"
                                       aria-label="Buscar estudiante">
                                <div class="input-group-append" id="btnLimpiarContenedor" style="display: none;">
                                    <button class="btn btn-outline-secondary border-left-0 bg-white" 
                                            type="button" 
                                            id="btnLimpiarBuscador" 
                                            title="Limpiar búsqueda">
                                        <i class="fas fa-times text-danger"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted mt-1">
                                <i class="fas fa-lightbulb text-warning mr-1"></i> Escribe para filtrar al instante por nombre o número de cédula.
                            </small>
                        </div>
                    </div>

                    <!-- Mensaje para cuando la búsqueda no arroje resultados -->
                    <div id="mensajeSinResultados" class="alert alert-warning text-center py-4" style="display: none;">
                        <i class="fas fa-search-minus fa-2x mb-2 d-block text-warning"></i>
                        <strong>No se encontraron estudiantes coincidentes con la búsqueda.</strong>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-secondary font-weight-bold" onclick="limpiarFiltro()">
                                <i class="fas fa-undo mr-1"></i> Restablecer Lista Completa
                            </button>
                        </div>
                    </div>

                    <!-- ================================================================================= -->
                    <!-- VISTA PARA ESCRITORIO: TABLA DE ESTUDIANTES CON BOTONES VÍA POST                 -->
                    <!-- ================================================================================= -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover table-sm text-center mb-0" id="tablaEstudiantes">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th class="text-left">Estudiante (Nombre Completo)</th>
                                    <th style="width: 140px;">Cédula de Identidad</th>
                                    <th style="width: 150px;">Acción Segura</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $contador = 1;
                                $estudiantes_vistos = [];
                                foreach ($estudiantes as $est):
                                    if (in_array($est['id'], $estudiantes_vistos)) continue;
                                    $estudiantes_vistos[] = $est['id'];
                                    
                                    $nombre_completo = htmlspecialchars($est['nombre']);
                                    $cedula = htmlspecialchars($est['cedula']);
                                    $id_est = intval($est['id']);
                                ?>
                                    <tr class="fila-estudiante" data-nombre="<?= mb_strtolower($nombre_completo, 'UTF-8') ?>" data-cedula="<?= $cedula ?>">
                                        <td class="font-weight-bold text-muted"><?= $contador++ ?></td>
                                        <td class="text-left font-weight-bold text-dark">
                                            <i class="fas fa-user-circle text-muted mr-1"></i> <?= $nombre_completo ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-light border px-2 py-1 font-monospace font-weight-bold text-dark">
                                                <?= $cedula ?>
                                            </span>
                                        </td>
                                        <td>
                                            <!-- Botón POST / AJAX: Abre el modal sin exponer ID por GET -->
                                            <button type="button" 
                                                    class="btn btn-info btn-sm shadow-sm btn-abrir-notas font-weight-bold"
                                                    data-id="<?= $id_est ?>"
                                                    data-nombre="<?= $nombre_completo ?>"
                                                    data-cedula="<?= $cedula ?>">
                                                <i class="fas fa-eye mr-1"></i> Ver Notas
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- ================================================================================= -->
                    <!-- VISTA PARA DISPOSITIVOS MÓVILES: TARJETAS RESPONSIVAS CON BOTONES VÍA POST       -->
                    <!-- ================================================================================= -->
                    <div class="d-block d-md-none" id="contenedorTarjetasMovil">
                        <?php 
                        $contador_m = 1;
                        $estudiantes_vistos_m = [];
                        foreach ($estudiantes as $est):
                            if (in_array($est['id'], $estudiantes_vistos_m)) continue;
                            $estudiantes_vistos_m[] = $est['id'];
                            
                            $nombre_completo = htmlspecialchars($est['nombre']);
                            $cedula = htmlspecialchars($est['cedula']);
                            $id_est = intval($est['id']);
                        ?>
                            <div class="card mb-2 shadow-sm border tarjeta-estudiante-movil" data-nombre="<?= mb_strtolower($nombre_completo, 'UTF-8') ?>" data-cedula="<?= $cedula ?>">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="pr-2">
                                            <div class="font-weight-bold text-primary mb-1">
                                                <span class="badge badge-secondary mr-1"><?= $contador_m++ ?></span>
                                                <?= $nombre_completo ?>
                                            </div>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-id-card mr-1"></i> Cédula: <strong><?= $cedula ?></strong>
                                            </small>
                                        </div>
                                        <button type="button" 
                                                class="btn btn-info btn-sm font-weight-bold btn-abrir-notas shadow-sm"
                                                data-id="<?= $id_est ?>"
                                                data-nombre="<?= $nombre_completo ?>"
                                                data-cedula="<?= $cedula ?>">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>



                <?php endif; ?>

            </div>
        </div>

    <?php else: ?>
        <!-- Mensaje de advertencia si no tiene sección asignada -->
        <div class="alert alert-warning text-center py-4 shadow-sm">
            <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block text-warning"></i>
            <h5>No se pudo determinar tu sección asignada</h5>
            <p class="mb-0">Por favor acude a la oficina de Control de Estudios o con tu Director de Carrera para verificar tu asignación de sección.</p>
        </div>
    <?php endif; ?>

</div>

<!-- ==================================================================================================== -->
<!-- VENTANA MODAL PARA VER NOTAS DEL ESTUDIANTE (CONSULTA SEGURA POR POST)                              -->
<!-- ==================================================================================================== -->
<div class="modal fade" id="modalVerNotasEstudiante" tabindex="-1" role="dialog" aria-labelledby="modalVerNotasLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content shadow border-0">
            
            <!-- Cabecera del Modal -->
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold" id="modalVerNotasLabel">
                    <i class="fas fa-clipboard-list mr-2"></i>Calificaciones de: <span id="modalNombreEstudiante" class="text-warning">...</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body p-3 p-md-4">
                
                <!-- Spinner de carga mientras responde el servidor vía POST -->
                <div id="spinnerCargaNotas" class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Cargando calificaciones...</span>
                    </div>
                    <div class="mt-3 font-weight-bold text-muted">
                        Consultando calificaciones en base de datos...
                    </div>
                </div>

                <!-- Contenedor para mensajes de error en caso de fallo -->
                <div id="alertaErrorModal" class="alert alert-danger text-center" style="display: none;">
                    <i class="fas fa-exclamation-circle mr-1"></i> <span id="mensajeErrorModal">Error al cargar datos.</span>
                </div>

                <!-- Contenedor donde se inyecta el HTML retornado por el backend -->
                <div id="contenidoDinamicoNotas" style="display: none;"></div>

            </div>
            
            <!-- Pie del Modal -->
            <div class="modal-footer bg-light py-2 justify-content-end">
                <button type="button" class="btn btn-secondary font-weight-bold px-4 shadow-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ==================================================================================================== -->
<!-- ESTILOS VISUALES COMPLEMENTARIOS                                                                     -->
<!-- ==================================================================================================== -->
<style>
/* Estilos para el modal y contenido dinámico */
.bg-light-success {
    background-color: #e8f5e9 !important;
}
.bg-light-warning {
    background-color: #fffde7 !important;
}
.border-left-primary {
    border-left: 4px solid #00509e !important;
}
.border-left-info {
    border-left: 4px solid #17a2b8 !important;
}

/* Modal en pantallas móviles */
@media (max-width: 767.98px) {
    .modal-dialog {
        margin: 0.5rem;
    }
    .modal-body {
        padding: 0.75rem !important;
    }
}
</style>

<!-- ==================================================================================================== -->
<!-- JAVASCRIPT: BUSCADOR EN TIEMPO REAL Y CONSULTA DE NOTAS EN MODAL VÍA POST                           -->
<!-- ==================================================================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // ------------------------------------------------------------------------------------------------
    // 1. LÓGICA DEL BUSCADOR EN TIEMPO REAL (FILTRADO INSTANTÁNEO)
    // ------------------------------------------------------------------------------------------------
    const buscador = document.getElementById('buscadorEstudiantes');
    const btnLimpiar = document.getElementById('btnLimpiarBuscador');
    const btnLimpiarContenedor = document.getElementById('btnLimpiarContenedor');
    const filasTabla = document.querySelectorAll('.fila-estudiante');
    const tarjetasMovil = document.querySelectorAll('.tarjeta-estudiante-movil');
    const mensajeSinResultados = document.getElementById('mensajeSinResultados');
    const cantVisibleSpan = document.getElementById('cantVisible');
    const totalEstudiantes = <?= count($estudiantes) ?>;

    /**
     * Filtra los estudiantes según el texto ingresado en el buscador
     */
    function filtrarEstudiantes() {
        if (!buscador) return;
        
        const termino = buscador.value.trim().toLowerCase();
        let visibles = 0;

        // Mostrar u ocultar botón de limpiar
        if (termino.length > 0) {
            btnLimpiarContenedor.style.display = 'block';
        } else {
            btnLimpiarContenedor.style.display = 'none';
        }

        // Filtrar filas de la tabla de escritorio
        filasTabla.forEach(fila => {
            const nombre = fila.getAttribute('data-nombre') || '';
            const cedula = fila.getAttribute('data-cedula') || '';
            
            if (nombre.includes(termino) || cedula.includes(termino)) {
                fila.style.display = '';
                visibles++;
            } else {
                fila.style.display = 'none';
            }
        });

        // Filtrar tarjetas en vista móvil
        tarjetasMovil.forEach(tarjeta => {
            const nombre = tarjeta.getAttribute('data-nombre') || '';
            const cedula = tarjeta.getAttribute('data-cedula') || '';
            
            if (nombre.includes(termino) || cedula.includes(termino)) {
                tarjeta.style.display = '';
            } else {
                tarjeta.style.display = 'none';
            }
        });

        // Actualizar contador numérico
        if (cantVisibleSpan) {
            cantVisibleSpan.textContent = visibles;
        }

        // Mostrar alerta si no hay coincidencias
        if (mensajeSinResultados) {
            if (visibles === 0 && totalEstudiantes > 0) {
                mensajeSinResultados.style.display = 'block';
            } else {
                mensajeSinResultados.style.display = 'none';
            }
        }
    }

    // Escuchar eventos de escritura en el buscador
    if (buscador) {
        buscador.addEventListener('input', filtrarEstudiantes);
        buscador.addEventListener('keyup', filtrarEstudiantes);
    }

    // Botón para limpiar el buscador
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function () {
            limpiarFiltro();
        });
    }

    // Función global para restablecer el buscador
    window.limpiarFiltro = function () {
        if (buscador) {
            buscador.value = '';
            filtrarEstudiantes();
            buscador.focus();
        }
    };

    // ------------------------------------------------------------------------------------------------
    // 2. LÓGICA DE APERTURA DE MODAL Y CONSULTA DE CALIFICACIONES VÍA POST
    // ------------------------------------------------------------------------------------------------
    const csrfToken = '<?= $csrf_token ?>';
    const botonesVerNotas = document.querySelectorAll('.btn-abrir-notas');
    const modalElement = $('#modalVerNotasEstudiante');
    const modalNombre = document.getElementById('modalNombreEstudiante');
    const spinnerCarga = document.getElementById('spinnerCargaNotas');
    const alertaError = document.getElementById('alertaErrorModal');
    const mensajeError = document.getElementById('mensajeErrorModal');
    const contenedorNotas = document.getElementById('contenidoDinamicoNotas');

    botonesVerNotas.forEach(boton => {
        boton.addEventListener('click', function () {
            const estudianteId = this.getAttribute('data-id');
            const estudianteNombre = this.getAttribute('data-nombre');
            const estudianteCedula = this.getAttribute('data-cedula');

            // Actualizar título del modal
            modalNombre.textContent = estudianteNombre + ' (' + estudianteCedula + ')';

            // Restablecer estados visuales del modal
            spinnerCarga.style.display = 'block';
            alertaError.style.display = 'none';
            contenedorNotas.style.display = 'none';
            contenedorNotas.innerHTML = '';

            // Desplegar modal en pantalla
            modalElement.modal('show');

            // Preparar payload seguro para la petición POST
            const formData = new FormData();
            formData.append('action', 'obtener_notas_estudiante');
            formData.append('estudiante_id', estudianteId);
            formData.append('csrf_token', csrfToken);

            // Enviar petición POST mediante Fetch API
            fetch('vocero.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errData => {
                        throw new Error(errData.message || 'Error del servidor al procesar la solicitud.');
                    });
                }
                return response.json();
            })
            .then(data => {
                spinnerCarga.style.display = 'none';
                
                if (data.success && data.html) {
                    contenedorNotas.innerHTML = data.html;
                    contenedorNotas.style.display = 'block';
                } else {
                    mensajeError.textContent = data.message || 'No se pudieron recuperar las notas.';
                    alertaError.style.display = 'block';
                }
            })
            .catch(error => {
                spinnerCarga.style.display = 'none';
                mensajeError.textContent = error.message || 'Error de conexión con el servidor.';
                alertaError.style.display = 'block';
            });
        });
    });

});
</script>

<?php 
// Inclusión del pie de página común
include(__DIR__ . "/includes/footer.php"); 
?>
