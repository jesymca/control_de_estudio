<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Inscripción de Materias por Trayecto - Individual";
include('../funciones/functions.php');

// CARGAR PERMISOS
cargarPermisosUsuario();
verificarPermiso('inscripcion_materias');

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

// Asegurar que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Manejar búsqueda en tiempo real AJAX por POST (debe ir ANTES de cualquier include de HTML)
if (isset($_POST['ajax_buscar_estudiante'])) {
    if (ob_get_length()) {
        ob_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    $cedula = trim($_POST['cedula'] ?? '');
    
    if (empty($cedula)) {
        echo json_encode(['success' => true, 'estudiantes' => []]);
        exit();
    }
    
    $query = "SELECT u.id, u.idusuario AS cedula, u.nombre, u.carrera AS id_carrera, c.nombre_carrera, u.email, u.tlf, u.cel
              FROM users u
              LEFT JOIN carreras c ON (u.carrera = c.id_carrera OR u.carrera = c.cod_carrera)
              WHERE u.estudiante = 1 
              AND (u.idusuario LIKE CONCAT('%', ?, '%') OR u.nombre LIKE CONCAT('%', ?, '%'))
              ORDER BY u.nombre ASC
              LIMIT 12";
              
    $stmt = $db->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ss", $cedula, $cedula);
        $stmt->execute();
        $res = $stmt->get_result();
        $estudiantes = [];
        while ($row = $res->fetch_assoc()) {
            $carrera_nom = !empty($row['nombre_carrera']) ? $row['nombre_carrera'] : ($row['id_carrera'] ?? 'No asignada');
            $tray = function_exists('obtenerTrayectoActual') ? obtenerTrayectoActual($row['id'], intval($row['id_carrera'])) : 0;
            $estudiantes[] = [
                'id' => intval($row['id']),
                'cedula' => $row['cedula'],
                'nombre' => $row['nombre'],
                'carrera' => $carrera_nom,
                'trayecto' => $tray,
                'contacto' => !empty($row['tlf']) ? $row['tlf'] : (!empty($row['cel']) ? $row['cel'] : $row['email'])
            ];
        }
        $stmt->close();
        echo json_encode(['success' => true, 'estudiantes' => $estudiantes]);
    } else {
        echo json_encode(['success' => false, 'error' => $db->error]);
    }
    exit();
}

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Asegurar user_id en sesión
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
        $_SESSION['user_id'] = $_SESSION['id'];
    } elseif (isset($_SESSION['idusuario']) && $_SESSION['idusuario'] > 0) {
        $_SESSION['user_id'] = $_SESSION['idusuario'];
    }
}

include("includes/head.php");

// Obtener período activo
$periodo_activo = obtenerPeriodoActivo();

// Variables de estado
$mensaje = '';
$tipo_mensaje = '';
$info_estudiante = null;
$materias_disponibles = [];
$secciones_disponibles = [];
$materias_aprobadas = [];
$materias_inscritas = [];
$historial_secciones = [];
$trayecto_actual = 0;
$trayecto_inscripcion = 0;
$estudiantes_encontrados = [];
$es_estudiante_nuevo = false;
$info_seccion_actual = null;
$verificacion_avance = null;
$puede_avanzar = false;
$trayecto_siguiente = 0;

// Procesar búsqueda por cédula
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['buscar_cedula'])) {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $mensaje = "Error de seguridad. Token inválido.";
            $tipo_mensaje = 'danger';
        } else {
            $cedula = trim($_POST['cedula'] ?? '');
            
            if (empty($cedula)) {
                $mensaje = "Por favor ingrese una cédula o nombre";
                $tipo_mensaje = 'warning';
            } else {
                try {
                    $resultados = buscarEstudiantePorCedula($cedula);
                    
                    if (is_array($resultados) && !empty($resultados)) {
                        $est_sel = isset($resultados['id']) ? $resultados : $resultados[0];
                        $info_estudiante = obtenerInfoEstudiantePorId($est_sel['id']);
                        if ($info_estudiante) {
                            $_SESSION['estudiante_seleccionado'] = $info_estudiante['id'];
                        } else {
                            $mensaje = "No se pudo cargar la información del estudiante.";
                            $tipo_mensaje = 'warning';
                        }
                    } else {
                        $mensaje = "No se encontraron estudiantes con la cédula/nombre: " . htmlspecialchars($cedula);
                        $tipo_mensaje = 'warning';
                    }
                } catch (Exception $e) {
                    $mensaje = "Error al buscar estudiante: " . $e->getMessage();
                    $tipo_mensaje = 'danger';
                }
            }
        }
    }
    
    // Procesar selección de estudiante
    if (isset($_POST['seleccionar_estudiante'])) {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $mensaje = "Error de seguridad. Token inválido.";
            $tipo_mensaje = 'danger';
        } else {
            $id_estudiante = intval($_POST['id_estudiante'] ?? 0);
            
            if ($id_estudiante > 0) {
                $info_estudiante = obtenerInfoEstudiantePorId($id_estudiante);
                if ($info_estudiante) {
                    $_SESSION['estudiante_seleccionado'] = $info_estudiante['id'];
                } else {
                    $mensaje = "Estudiante no encontrado";
                    $tipo_mensaje = 'danger';
                }
            } else {
                unset($_SESSION['estudiante_seleccionado']);
                $info_estudiante = null;
                $mensaje = "Selección de estudiante limpiada.";
                $tipo_mensaje = 'info';
            }
        }
    }
    
    // Procesar inscripción de materias
    if (isset($_POST['inscribir_materias'])) {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $mensaje = "Error de seguridad. Token inválido.";
            $tipo_mensaje = 'danger';
        } else {
            $id_estudiante = intval($_POST['id_estudiante'] ?? 0);
            $id_seccion = intval($_POST['id_seccion'] ?? 0);
            $materias_ids = isset($_POST['materias']) ? array_map('intval', $_POST['materias']) : [];
            
            if ($id_estudiante > 0 && !empty($materias_ids)) {
                $resultado = inscribirMateriasEstudiante($id_estudiante, $id_seccion, $materias_ids);
                
                if ($resultado) {
                    $mensaje = "✅ Materias inscritas correctamente para el estudiante.";
                    $tipo_mensaje = 'success';
                    $info_estudiante = obtenerInfoEstudiantePorId($id_estudiante);
                } else {
                    $mensaje = "❌ Error al inscribir las materias. Por favor intente nuevamente.";
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = "⚠️ Debe seleccionar al menos una materia para inscribir.";
                $tipo_mensaje = 'warning';
            }
        }
    }
    
    // Procesar avance de estudiante individual
    if (isset($_POST['avanzar_estudiante_trayecto'])) {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $mensaje = "Error de seguridad. Token inválido.";
            $tipo_mensaje = 'danger';
        } else {
            $id_estudiante = intval($_POST['id_estudiante'] ?? 0);
            
            if ($id_estudiante > 0) {
                $id_admin = $_SESSION['user_id'] ?? 0;
                $resultado = avanzarEstudianteTrayecto($id_estudiante, $id_admin);
                
                if ($resultado['success']) {
                    $mensaje = "✅ " . $resultado['message'];
                    $tipo_mensaje = 'success';
                    $info_estudiante = obtenerInfoEstudiantePorId($id_estudiante);
                } else {
                    $mensaje = "❌ " . $resultado['message'];
                    $tipo_mensaje = 'danger';
                }
            }
        }
    }
}

// Cargar estudiante seleccionado de sesión
if (isset($_SESSION['estudiante_seleccionado']) && empty($info_estudiante)) {
    $info_estudiante = obtenerInfoEstudiantePorId($_SESSION['estudiante_seleccionado']);
}

// Función para cargar todos los datos del estudiante
function cargarDatosEstudiante($info_estudiante) {
    global $db, $periodo_activo, $materias_disponibles, $secciones_disponibles, 
           $materias_aprobadas, $materias_inscritas, $historial_secciones, 
           $trayecto_actual, $trayecto_inscripcion, $es_estudiante_nuevo, 
           $info_seccion_actual, $verificacion_avance, $puede_avanzar, $trayecto_siguiente;
    
    if (!$info_estudiante) return;
    
    $id_carrera = $info_estudiante['carrera'] ?? $info_estudiante['id_carrera'] ?? 0;
    $id_usuario = $info_estudiante['id'];
    
    // OBTENER SECCIÓN ACTUAL
    $info_seccion_actual = obtenerSeccionActualEstudiante($id_usuario);
    
    // OBTENER TRAYECTO DESDE LA SECCIÓN
    $info_trayecto = obtenerTrayectoDesdeSeccion($id_usuario);
    $trayecto_actual = $info_trayecto['trayecto'];
    
    // Si no tiene sección, usar sistema de aprobaciones
    if ($trayecto_actual == 0 && !$info_seccion_actual) {
        $trayecto_actual = obtenerTrayectoActual($id_usuario, $id_carrera);
    }
    
    // Verificar si es estudiante nuevo
    $es_estudiante_nuevo = esEstudianteNuevo($id_usuario);
    
    // VERIFICAR SI PUEDE AVANZAR (IGUAL QUE EN SECCIONES)
    $verificacion_avance = verificarAvancePorSeccion($id_usuario);
    $puede_avanzar = $verificacion_avance['puede_avanzar'];
    $trayecto_siguiente = $trayecto_actual + 1;
    
    // DETERMINAR TRAYECTO PARA INSCRIPCIÓN
    if ($puede_avanzar && $trayecto_actual < 4) {
        $trayecto_inscripcion = $trayecto_actual + 1;
    } else {
        $trayecto_inscripcion = $trayecto_actual;
    }
    
    // OBTENER MATERIAS APROBADAS
    $materias_aprobadas = obtenerMateriasAprobadasPorTrayecto($id_usuario, $trayecto_actual);
    
    // OBTENER SECCIONES DISPONIBLES
    if ($periodo_activo && $id_carrera > 0) {
        $secciones_disponibles = obtenerSeccionesTrayecto($id_carrera, $trayecto_inscripcion, $periodo_activo['id_periodo']);
    }
    
    // OBTENER MATERIAS PARA INSCRIPCIÓN
    if ($id_carrera > 0) {
        $materias_disponibles = obtenerMateriasDisponiblesIndividual($id_usuario, $trayecto_inscripcion, $id_carrera);
    }
    
    // OBTENER MATERIAS INSCRITAS
    $materias_inscritas = obtenerMateriasInscritasActuales($id_usuario);
    
    // OBTENER HISTORIAL
    $historial_secciones = obtenerHistorialSecciones($id_usuario);
}

// Cargar datos del estudiante si existe
if ($info_estudiante) {
    cargarDatosEstudiante($info_estudiante);
}

// Mostrar mensajes
if (!empty($mensaje)) {
    echo '<div class="alert alert-' . $tipo_mensaje . ' alert-dismissible fade show" role="alert">
            ' . htmlspecialchars($mensaje) . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>';
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mt-4"><?php echo $titulopag; ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active"><?php echo $titulopag; ?></li>
            </ol>
        </div>
    </div>

    <!-- Búsqueda por cédula o nombre en tiempo real -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-search mr-1"></i> Buscar Estudiante para Inscripción</span>
                    <span class="badge badge-light text-primary"><i class="fas fa-bolt text-warning mr-1"></i> Búsqueda en Tiempo Real</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="form_buscar_estudiante">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="row">
                            <div class="col-md-9 position-relative">
                                <div class="form-group mb-2 mb-md-0 position-relative">
                                    <label for="cedula" class="font-weight-bold">Cédula o Nombre del Estudiante</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white"><i class="fas fa-id-card text-primary"></i></span>
                                        </div>
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               id="cedula" 
                                               name="cedula" 
                                               autocomplete="off"
                                               placeholder="Escriba la cédula o nombre del estudiante..." 
                                               value="<?php echo isset($_POST['cedula']) ? htmlspecialchars($_POST['cedula']) : ''; ?>"
                                               required>
                                        <div class="input-group-append" id="spinner_busqueda" style="display: none;">
                                            <span class="input-group-text bg-white text-primary">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Las sugerencias aparecerán automáticamente mientras escribe.</small>
                                    
                                    <!-- Contenedor flotante de sugerencias en tiempo real -->
                                    <div id="sugerencias_estudiantes" class="list-group shadow position-absolute w-100 mt-1" 
                                         style="z-index: 9999; display: none; max-height: 350px; overflow-y: auto; left: 0; right: 0; background: #ffffff; border: 1px solid #ced4da; border-radius: 4px;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-group mb-0 w-100">
                                    <button type="submit" name="buscar_cedula" class="btn btn-primary btn-lg btn-block shadow-sm">
                                        <i class="fas fa-search mr-1"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Formulario oculto para selección directa por POST -->
                    <form method="POST" action="" id="form_seleccion_directa" style="display: none;">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="id_estudiante" id="id_estudiante_directo" value="0">
                        <input type="hidden" name="seleccionar_estudiante" value="1">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if ($info_estudiante): ?>
    <!-- Información del estudiante seleccionado -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-user-graduate mr-1"></i>
                    Estudiante Seleccionado
                    <?php if ($es_estudiante_nuevo): ?>
                        <span class="badge badge-warning badge-pill float-right">ESTUDIANTE NUEVO</span>
                    <?php endif; ?>
                    <form method="POST" action="" class="float-right mr-2" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="id_estudiante" value="0">
                        <button type="submit" name="seleccionar_estudiante" class="btn btn-sm btn-light">
                            <i class="fas fa-sync-alt mr-1"></i> Cambiar Estudiante
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>Cédula:</strong> <?php echo htmlspecialchars($info_estudiante['idusuario']); ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($info_estudiante['nombre']); ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Carrera:</strong> <?php echo htmlspecialchars($info_estudiante['nombre_carrera'] ?? 'No asignada'); ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Trayecto Actual:</strong> 
                                <span class="badge badge-info"><?php echo $trayecto_actual; ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Estado:</strong> 
                                <?php if ($es_estudiante_nuevo): ?>
                                    <span class="badge badge-primary">PRIMERA VEZ</span>
                                <?php else: ?>
                                    <span class="badge badge-info">EN PROCESO</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Período Activo:</strong> 
                                <?php echo $periodo_activo ? htmlspecialchars($periodo_activo['nombre_periodo']) : 'No hay período activo'; ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Trayecto Inscripción:</strong> 
                                <span class="badge badge-primary"><?php echo $trayecto_inscripcion; ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Sección Actual -->
    <?php if ($info_seccion_actual): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-users mr-1"></i>
                    Sección Actual del Estudiante
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>Código Sección:</strong> <?php echo htmlspecialchars($info_seccion_actual['codigo_seccion']); ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Trayecto:</strong> <span class="badge badge-info"><?php echo $info_seccion_actual['numero_trayecto']; ?></span></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Período:</strong> <?php echo htmlspecialchars($info_seccion_actual['nombre_periodo'] ?? 'No definido'); ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Estado:</strong> <span class="badge badge-success">ACTIVA</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ESTADO ACADÉMICO (IGUAL QUE EN SECCIONES) -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-chart-line mr-1"></i>
                    Estado Académico - Trayecto <?php echo $trayecto_actual; ?>
                    <?php if ($es_estudiante_nuevo): ?>
                        <span class="badge badge-light float-right">ESTUDIANTE NUEVO</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if ($trayecto_actual == 0 && $es_estudiante_nuevo): ?>
                        <div class="alert alert-primary">
                            <h5 class="alert-heading"><i class="fas fa-user-plus"></i> Estudiante Nuevo</h5>
                            <p>Este estudiante no tiene historial académico. Se inscribirá en el <strong>Trayecto 0</strong> por primera vez.</p>
                            <p class="mb-0"><strong>Condición para avanzar al Trayecto 1:</strong> Aprobar al menos el 50% de las materias del trayecto 0.</p>
                        </div>
                    <?php else: ?>
                        <div class="alert <?php echo ($puede_avanzar && $trayecto_actual < 4) ? 'alert-success' : 'alert-warning'; ?>">
                            <h5 class="alert-heading">
                                <i class="fas fa-<?php echo ($puede_avanzar && $trayecto_actual < 4) ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                                <?php if ($puede_avanzar && $trayecto_actual < 4): ?>
                                    ¡Cumple requisitos para avanzar al Trayecto <?php echo $trayecto_siguiente; ?>!
                                <?php elseif ($trayecto_actual >= 4): ?>
                                    ¡El estudiante ha completado todos los trayectos!
                                <?php else: ?>
                                    No cumple requisitos para avanzar al siguiente trayecto
                                <?php endif; ?>
                            </h5>
                            <p class="mb-0">
                                <strong>Condición para avanzar:</strong><br>
                                <?php echo $verificacion_avance['detalles'] ?? 'Verificando requisitos...'; ?>
                            </p>
                            
                            <!-- BOTÓN DE AVANZAR (IGUAL QUE EN SECCIONES) -->
                            <?php if ($puede_avanzar && $trayecto_actual < 4): ?>
                                <hr>
                                <form method="POST" action="" class="mt-3">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="id_estudiante" value="<?php echo $info_estudiante['id']; ?>">
                                    
                                    <div class="form-group">
                                        <label for="motivo_aprobacion"><small>Motivo de aprobación (opcional):</small></label>
                                        <textarea class="form-control form-control-sm" 
                                                  id="motivo_aprobacion" 
                                                  name="motivo_aprobacion" 
                                                  rows="2" 
                                                  placeholder="Ej: Cumple todos los requisitos académicos"></textarea>
                                    </div>
                                    
                                    <button type="submit" name="avanzar_estudiante_trayecto" class="btn btn-success btn-lg">
                                        <i class="fas fa-arrow-right mr-1"></i> AVANZAR AL TRAYECTO <?php echo $trayecto_siguiente; ?>
                                    </button>
                                    
                                    <small class="d-block text-muted mt-1">
                                        <i class="fas fa-info-circle"></i> Esta acción creará una nueva sección e inscribirá automáticamente las materias del Trayecto <?php echo $trayecto_siguiente; ?>.
                                    </small>
                                </form>
                            <?php elseif ($trayecto_actual >= 4): ?>
                                <hr>
                                <div class="alert alert-success">
                                    <i class="fas fa-graduation-cap"></i> 
                                    <strong>¡Felicidades!</strong> El estudiante ha completado todos los trayectos de la carrera.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Mostrar progreso -->
                    <?php if (isset($verificacion_avance['total_materias']) && $verificacion_avance['total_materias'] > 0): ?>
                    <div class="mt-3">
                        <strong>Progreso del Trayecto <?php echo $trayecto_actual; ?>:</strong>
                        <?php 
                        $total = $verificacion_avance['total_materias'];
                        $aprobadas = $verificacion_avance['total_aprobadas'];
                        $porcentaje = ($total > 0) ? ($aprobadas / $total) * 100 : 0;
                        ?>
                        <div class="progress mt-2" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?php echo $porcentaje; ?>%" 
                                 aria-valuenow="<?php echo $porcentaje; ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <?php echo number_format($porcentaje, 1); ?>%
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">
                                <?php echo $aprobadas; ?> de <?php echo $total; ?> materias aprobadas
                            </small>
                            <?php if (isset($verificacion_avance['minimo_requerido']) && $verificacion_avance['minimo_requerido'] > 0): ?>
                            <small class="text-muted">
                                Mínimo requerido: <?php echo $verificacion_avance['minimo_requerido']; ?> materias
                            </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Materias aprobadas -->
    <?php if (!empty($materias_aprobadas)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-check-circle mr-1"></i>
                    Materias Aprobadas - Trayecto <?php echo $trayecto_actual; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Materia</th>
                                    <th>Créditos</th>
                                    <th>Período</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materias_aprobadas as $materia): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($materia['cod_materia']); ?></td>
                                    <td><?php echo htmlspecialchars($materia['nombre_materia']); ?></td>
                                    <td><?php echo $materia['creditos']; ?></td>
                                    <td><?php echo htmlspecialchars($materia['nombre_periodo'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($materia['es_proyecto']): ?>
                                            <span class="badge badge-warning">PROYECTO</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">NORMAL</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Materias inscritas actualmente -->
    <?php if (!empty($materias_inscritas) && $periodo_activo): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <i class="fas fa-clipboard-check mr-1"></i>
                    Materias Inscritas en el Período Actual (<?php echo htmlspecialchars($periodo_activo['nombre_periodo']); ?>)
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Trayecto</th>
                                    <th>Créditos</th>
                                    <th>Nota Mínima</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materias_inscritas as $materia): 
                                    $nota_minima = obtenerNotaMinimaMateria($materia['id_materia']);
                                    $es_proyecto = $materia['es_proyecto'] ?? false;
                                    $nota_actual = obtenerNotaMateriaActualPeriodo($info_estudiante['id'], $materia['id_materia']);
                                    $estado_nota = ($nota_actual === null) ? 'Sin calificar' : 
                                        ((($es_proyecto && $nota_actual >= 16) || (!$es_proyecto && $nota_actual >= 12)) 
                                            ? 'Aprobada' 
                                            : 'Reprobada');
                                    $badge_color = ($estado_nota == 'Aprobada') ? 'success' : 
                                                 (($estado_nota == 'Reprobada') ? 'danger' : 'secondary');
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($materia['cod_materia']); ?></td>
                                    <td><?php echo htmlspecialchars($materia['nombre_materia']); ?></td>
                                    <td><?php echo $materia['trayecto']; ?></td>
                                    <td><?php echo $materia['creditos']; ?></td>
                                    <td><?php echo $nota_minima; ?></td>
                                    <td>
                                        <?php if ($es_proyecto): ?>
                                            <span class="badge badge-warning">PROYECTO</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">NORMAL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $badge_color; ?>">
                                            <?php echo $estado_nota; ?>
                                            <?php if ($nota_actual !== null): ?>
                                                (<?php echo $nota_actual; ?>)
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Estas materias ya están inscritas en el período actual. 
                        Aparecerán aquí hasta que se cierre el período o se aprueben.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Historial de secciones -->
    <?php if (!empty($historial_secciones)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-history mr-1"></i>
                    Historial de Secciones del Estudiante
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Período</th>
                                    <th>Sección</th>
                                    <th>Trayecto</th>
                                    <th>Fecha Inscripción</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial_secciones as $historial): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($historial['nombre_periodo'] ?? 'Desconocido'); ?></td>
                                    <td><?php echo htmlspecialchars($historial['codigo_seccion'] ?? 'Sin sección'); ?></td>
                                    <td><?php echo $historial['numero_trayecto'] ?? '0'; ?></td>
                                    <td><?php echo isset($historial['fecha_inscripcion']) ? date('d/m/Y', strtotime($historial['fecha_inscripcion'])) : 'Sin fecha'; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo ($historial['estatus'] ?? '') == 'activo' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($historial['estatus'] ?? 'Desconocido'); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Formulario de Inscripción -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-clipboard-list mr-1"></i>
                    Inscripción de Materias - Trayecto <?php echo $trayecto_inscripcion; ?>
                    <?php if ($trayecto_inscripcion == 0 && $es_estudiante_nuevo): ?>
                        <span class="badge badge-warning float-right">INICIO</span>
                    <?php elseif ($puede_avanzar && $trayecto_actual < 4): ?>
                        <span class="badge badge-success float-right">AVANCE APROBADO</span>
                    <?php else: ?>
                        <span class="badge badge-info float-right">REINSCRIPCIÓN</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!$periodo_activo): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> No hay un período académico activo. Contacte al administrador.
                        </div>
                    <?php elseif ($info_estudiante['carrera'] <= 0): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> El estudiante no tiene una carrera asignada.
                        </div>
                    <?php else: ?>
                        <?php if ($puede_avanzar && $trayecto_actual < 4): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> 
                                <strong>¡El estudiante puede inscribirse en el Trayecto <?php echo $trayecto_siguiente; ?>!</strong>
                                <p class="mb-0">Cumple con los requisitos para avanzar. Ahora puede inscribir materias del siguiente trayecto.</p>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="form_inscribir_materias">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="id_estudiante" value="<?php echo $info_estudiante['id']; ?>">
                            <input type="hidden" name="inscribir_materias" value="1">
                            
                            <div class="form-group">
                                <label for="id_seccion">Seleccionar Sección (Trayecto <?php echo $trayecto_inscripcion; ?>) <small class="text-muted">(Opcional)</small></label>
                                <select class="form-control" id="id_seccion" name="id_seccion">
                                    <option value="0">Sin sección (inscripción general)</option>
                                    <?php foreach ($secciones_disponibles as $seccion): ?>
                                    <option value="<?php echo $seccion['id_seccion']; ?>">
                                        <?php echo htmlspecialchars($seccion['codigo_seccion']); ?> - 
                                        Trayecto <?php echo $seccion['numero_trayecto']; ?>
                                        (Cupo: <?php echo $seccion['inscritos']; ?>/<?php echo $seccion['capacidad_maxima']; ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">
                                    Secciones disponibles para el Trayecto <?php echo $trayecto_inscripcion; ?> (opcional).
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold"><i class="fas fa-book mr-1 text-primary"></i> Materias Disponibles para Inscripción</label>
                                <div class="border rounded p-3 bg-light" style="max-height: 480px; overflow-y: auto;">
                                    <?php if (empty($materias_disponibles)): ?>
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle"></i>
                                            <?php 
                                            if ($trayecto_inscripcion == $trayecto_actual && !$es_estudiante_nuevo) {
                                                echo "¡Excelente! El estudiante ya está inscrito o tiene aprobadas todas las materias disponibles para este trayecto.";
                                            } elseif ($es_estudiante_nuevo) {
                                                echo "Se mostrarán todas las materias del Trayecto 0 para inscripción inicial.";
                                            } else {
                                                echo "No hay materias disponibles para inscribir en este trayecto.";
                                            }
                                            ?>
                                        </div>
                                    <?php else: 
                                        $cnt_nuevas = 0;
                                        $cnt_repitientes = 0;
                                        $cnt_arrastres = 0;
                                        $cnt_ya_inscritas = 0;
                                        foreach ($materias_disponibles as $m) {
                                            if (!empty($m['ya_inscrita'])) $cnt_ya_inscritas++;
                                            elseif (!empty($m['es_repitiente'])) $cnt_repitientes++;
                                            elseif (!empty($m['es_arrastre'])) $cnt_arrastres++;
                                            else $cnt_nuevas++;
                                        }
                                    ?>
                                        <!-- Barra de Resumen de Disponibilidad -->
                                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                            <div>
                                                <span class="badge badge-primary mr-1"><i class="fas fa-list"></i> Total: <?php echo count($materias_disponibles); ?></span>
                                                <?php if ($cnt_nuevas > 0): ?>
                                                    <span class="badge badge-success mr-1"><i class="fas fa-star"></i> <?php echo $cnt_nuevas; ?> Nuevas</span>
                                                <?php endif; ?>
                                                <?php if ($cnt_repitientes > 0): ?>
                                                    <span class="badge badge-danger mr-1"><i class="fas fa-redo"></i> <?php echo $cnt_repitientes; ?> Reinscripciones</span>
                                                <?php endif; ?>
                                                <?php if ($cnt_arrastres > 0): ?>
                                                    <span class="badge badge-warning text-dark mr-1"><i class="fas fa-level-down-alt"></i> <?php echo $cnt_arrastres; ?> Arrastres</span>
                                                <?php endif; ?>
                                                <?php if ($cnt_ya_inscritas > 0): ?>
                                                    <span class="badge badge-secondary mr-1"><i class="fas fa-check"></i> <?php echo $cnt_ya_inscritas; ?> En Curso</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mt-2 mt-sm-0">
                                                <span class="text-muted small">Créditos seleccionados: <strong id="total_creditos_display" class="text-primary">0</strong> UC</span>
                                            </div>
                                        </div>

                                        <div class="custom-control custom-checkbox mb-3 pb-2 border-bottom">
                                            <input class="custom-control-input" type="checkbox" id="select_all">
                                            <label class="custom-control-label font-weight-bold text-dark" for="select_all">
                                                <i class="fas fa-check-double text-primary mr-1"></i> Seleccionar todas las materias disponibles
                                            </label>
                                        </div>

                                        <div class="list-group">
                                        <?php foreach ($materias_disponibles as $materia): 
                                            $nota_minima = $materia['nota_minima'] ?? obtenerNotaMinimaMateria($materia['id_materia']);
                                            $es_proyecto = !empty($materia['es_proyecto']);
                                            $ya_inscrita = !empty($materia['ya_inscrita']);
                                            $es_repitiente = !empty($materia['es_repitiente']);
                                            $es_arrastre = !empty($materia['es_arrastre']);
                                            $nota_anterior = $materia['nota_anterior'] ?? null;
                                            $periodo_ant = $materia['periodo_anterior'] ?? '';
                                            $creditos = intval($materia['creditos'] ?? 0);
                                        ?>
                                            <div class="list-group-item list-group-item-action d-flex align-items-start p-3 mb-2 rounded border <?php echo $ya_inscrita ? 'bg-light text-muted opacity-75' : ($es_repitiente ? 'border-danger' : ($es_arrastre ? 'border-warning' : '')); ?>">
                                                <div class="custom-control custom-checkbox mr-3 mt-1">
                                                    <input class="custom-control-input materia-checkbox" type="checkbox" 
                                                           name="materias[]" 
                                                           value="<?php echo $materia['id_materia']; ?>" 
                                                           data-creditos="<?php echo $creditos; ?>"
                                                           id="materia_<?php echo $materia['id_materia']; ?>"
                                                           <?php echo $ya_inscrita ? 'disabled checked' : ''; ?>>
                                                    <label class="custom-control-label" for="materia_<?php echo $materia['id_materia']; ?>"></label>
                                                </div>
                                                <div class="w-100">
                                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                                                        <h6 class="mb-0 font-weight-bold <?php echo $ya_inscrita ? 'text-muted' : 'text-dark'; ?>">
                                                            <span class="text-primary"><?php echo htmlspecialchars($materia['cod_materia']); ?></span> - 
                                                            <?php echo htmlspecialchars($materia['nombre_materia']); ?>
                                                        </h6>
                                                        <div class="mt-1 mt-md-0">
                                                            <?php if ($es_proyecto): ?>
                                                                <span class="badge badge-info mr-1"><i class="fas fa-project-diagram"></i> PROYECTO</span>
                                                            <?php endif; ?>
                                                            
                                                            <?php if ($ya_inscrita): ?>
                                                                <span class="badge badge-secondary"><i class="fas fa-check-circle"></i> YA INSCRITA (EN CURSO)</span>
                                                            <?php elseif ($es_repitiente): ?>
                                                                <span class="badge badge-danger"><i class="fas fa-redo-alt"></i> REINSCRIPCIÓN (REPITIENTE)</span>
                                                            <?php elseif ($es_arrastre): ?>
                                                                <span class="badge badge-warning text-dark"><i class="fas fa-level-down-alt"></i> ARRASTRE (TRAYECTO <?php echo $materia['trayecto']; ?>)</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-success"><i class="fas fa-star"></i> NUEVA</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="small text-muted d-flex flex-wrap align-items-center">
                                                        <span class="mr-3"><i class="fas fa-layer-group text-secondary"></i> Trayecto: <strong><?php echo $materia['trayecto']; ?></strong></span>
                                                        <span class="mr-3"><i class="fas fa-award text-secondary"></i> Créditos: <strong><?php echo $creditos; ?> UC</strong></span>
                                                        <span class="mr-3"><i class="fas fa-check-double text-secondary"></i> Nota Mínima: <strong><?php echo $nota_minima; ?> pts</strong></span>
                                                        
                                                        <?php if ($es_repitiente && $nota_anterior !== null): ?>
                                                            <span class="badge badge-pill badge-danger text-white ml-auto">
                                                                <i class="fas fa-exclamation-triangle"></i> Nota anterior: <strong><?php echo number_format($nota_anterior, 1); ?> pts</strong>
                                                                <?php if (!empty($periodo_ant)) echo " (" . htmlspecialchars($periodo_ant) . ")"; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <button type="submit" name="inscribir_materias" class="btn btn-success btn-lg shadow-sm" 
                                    <?php echo (empty($materias_disponibles)) ? 'disabled' : ''; ?>>
                                <i class="fas fa-save mr-1"></i> Inscribir Materias Seleccionadas
                            </button>
                            
                            <?php if (!empty($materias_inscritas)): ?>
                            <div class="alert alert-info mt-3 shadow-sm">
                                <i class="fas fa-info-circle mr-1"></i> 
                                <strong>Nota:</strong> Las materias que ya se encuentran inscritas en el período actual aparecen marcadas e inhabilitadas para evitar duplicidad de matrícula.
                            </div>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Reglas -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-info-circle mr-1"></i>
                    Resumen de Reglas de Inscripción
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-arrow-right text-primary"></i> Condiciones para Avanzar:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><strong>Trayecto 0 → Trayecto 1:</strong> Aprobar el 50% de las materias del trayecto 0</li>
                                <li class="mb-2"><strong>Trayecto 1 → Trayecto 2:</strong> Aprobar Proyecto Socio Integrador (nota ≥ 16)</li>
                                <li class="mb-2"><strong>Trayecto 2 → Trayecto 3:</strong> Aprobar todas las materias y obtener primer título</li>
                                <li class="mb-2"><strong>Trayecto 3 → Trayecto 4:</strong> Aprobar Proyecto Socio Integrador (nota ≥ 16)</li>
                                <li class="mb-2"><strong>Trayecto 4:</strong> Último trayecto, no puede avanzar más</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-clipboard-check text-success"></i> Reglas de Inscripción:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><strong>Nota mínima aprobatoria:</strong> 12 puntos</li>
                                <li class="mb-2"><strong>Nota mínima para proyectos:</strong> 16 puntos</li>
                                <li class="mb-2"><strong>Reinscripción:</strong> Solo se inscriben materias NO inscritas actualmente</li>
                                <li class="mb-2"><strong>Ya inscritas:</strong> No se pueden volver a inscribir en mismo período</li>
                                <li class="mb-2"><strong>Nuevos estudiantes:</strong> Pueden inscribir todas las materias del Trayecto 0</li>
                                <li class="mb-2"><strong>Sección:</strong> El trayecto se determina por la sección activa del estudiante</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select_all');
    const materiaCheckboxes = document.querySelectorAll('.materia-checkbox:not(:disabled)');
    
    if (selectAllCheckbox && materiaCheckboxes.length > 0) {
        selectAllCheckbox.addEventListener('change', function() {
            materiaCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            actualizarEstadoBoton();
        });
        
        materiaCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(materiaCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && Array.from(materiaCheckboxes).some(cb => cb.checked);
                actualizarEstadoBoton();
            });
        });
        
        function actualizarEstadoBoton() {
            const inscribirBtn = document.querySelector('button[name="inscribir_materias"]');
            const totalCreditosDisplay = document.getElementById('total_creditos_display');
            let sumaCreditos = 0;
            let seleccionadas = 0;
            
            materiaCheckboxes.forEach(cb => {
                if (cb.checked) {
                    sumaCreditos += parseInt(cb.getAttribute('data-creditos') || 0, 10);
                    seleccionadas++;
                }
            });
            
            if (totalCreditosDisplay) {
                totalCreditosDisplay.textContent = sumaCreditos;
            }
            
            if (inscribirBtn) {
                inscribirBtn.disabled = (seleccionadas === 0);
            }
        }
        actualizarEstadoBoton();
    }
    
    // Búsqueda en tiempo real con debounce
    const cedulaInput = document.getElementById('cedula');
    const sugerenciasContainer = document.getElementById('sugerencias_estudiantes');
    const spinnerBusqueda = document.getElementById('spinner_busqueda');
    const formSeleccionDirecta = document.getElementById('form_seleccion_directa');
    const idEstudianteDirecto = document.getElementById('id_estudiante_directo');
    let searchDebounceTimer = null;

    function ejecutarBusquedaAjax(valor) {
        valor = (valor || '').trim();
        clearTimeout(searchDebounceTimer);

        if (valor.length === 0) {
            sugerenciasContainer.style.display = 'none';
            sugerenciasContainer.innerHTML = '';
            if (spinnerBusqueda) spinnerBusqueda.style.display = 'none';
            return;
        }

        if (spinnerBusqueda) spinnerBusqueda.style.display = 'flex';

        searchDebounceTimer = setTimeout(() => {
            const formData = new FormData();
            formData.append('ajax_buscar_estudiante', '1');
            formData.append('cedula', valor);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error('Error de red: ' + res.status);
                return res.json();
            })
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
                            <div class="pr-2">
                                <div class="font-weight-bold text-dark">
                                    <i class="fas fa-user-graduate text-primary mr-1"></i> ${escapeHtml(est.nombre)}
                                </div>
                                <small class="text-muted d-block">
                                    <i class="fas fa-id-card text-secondary"></i> <strong>${escapeHtml(est.cedula)}</strong> | 
                                    <i class="fas fa-graduation-cap text-secondary"></i> ${escapeHtml(est.carrera)}
                                </small>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="badge badge-info mr-1">Trayecto ${est.trayecto}</span>
                                <span class="btn btn-sm btn-success py-1 px-2"><i class="fas fa-check mr-1"></i> Seleccionar</span>
                            </div>
                        `;

                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            if (idEstudianteDirecto && formSeleccionDirecta) {
                                idEstudianteDirecto.value = est.id;
                                formSeleccionDirecta.submit();
                            }
                        });

                        sugerenciasContainer.appendChild(item);
                    });
                    sugerenciasContainer.style.display = 'block';
                } else {
                    sugerenciasContainer.innerHTML = `
                        <div class="list-group-item p-3 text-center text-muted">
                            <i class="fas fa-search-minus mr-1"></i> No se encontraron estudiantes con "<strong>${escapeHtml(valor)}</strong>".
                        </div>
                    `;
                    sugerenciasContainer.style.display = 'block';
                }
            })
            .catch(err => {
                if (spinnerBusqueda) spinnerBusqueda.style.display = 'none';
                console.error("Error en búsqueda en tiempo real:", err);
            });
        }, 200);
    }

    if (cedulaInput && sugerenciasContainer) {
        cedulaInput.addEventListener('input', function() {
            ejecutarBusquedaAjax(this.value);
        });

        cedulaInput.addEventListener('focus', function() {
            if (this.value.trim().length > 0) {
                ejecutarBusquedaAjax(this.value);
            }
        });

        // Ocultar sugerencias al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!cedulaInput.contains(e.target) && !sugerenciasContainer.contains(e.target)) {
                sugerenciasContainer.style.display = 'none';
            }
        });

        // Ocultar con tecla Escape
        cedulaInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                sugerenciasContainer.style.display = 'none';
            }
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }
});

// Función para confirmar acciones
function showConfirm(options) {
    const title = options.title || 'Confirmar acción';
    const message = options.message || '¿Está seguro?';
    const confirmText = options.confirmText || 'Confirmar';
    
    const modalHtml = `
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">${message}</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirmBtn">${confirmText}</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const existingModal = document.getElementById('confirmModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    const modal = document.getElementById('confirmModal');
    const confirmBtn = document.getElementById('confirmBtn');
    
    $(modal).modal('show');
    
    return new Promise((resolve) => {
        confirmBtn.onclick = function() {
            $(modal).modal('hide');
            resolve(true);
        };
        
        $(modal).on('hidden.bs.modal', function() {
            setTimeout(() => {
                modal.remove();
                resolve(false);
            }, 300);
        });
    });
}

// Manejar botón de avanzar
document.querySelectorAll('button[name="avanzar_estudiante_trayecto"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const form = this.closest('form');
        const trayectoActual = <?php echo $trayecto_actual; ?>;
        const siguiente = trayectoActual + 1;
        
        showConfirm({
            title: 'Confirmar Avance',
            message: `¿Está seguro de avanzar al estudiante al Trayecto ${siguiente}?<br><br>Esta acción creará una nueva sección para el estudiante en el Trayecto ${siguiente} e inscribirá automáticamente sus materias.`,
            confirmText: 'Avanzar'
        }).then(confirmed => {
            if (confirmed) {
                if (!form.querySelector('input[name="avanzar_estudiante_trayecto"]')) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'avanzar_estudiante_trayecto';
                    hidden.value = '1';
                    form.appendChild(hidden);
                }
                form.submit();
            }
        });
    });
});

// Manejar botón de inscripción
document.querySelectorAll('button[name="inscribir_materias"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const form = this.closest('form');
        const checkboxes = document.querySelectorAll('input[name="materias[]"]:checked:not(:disabled)');
        
        if (checkboxes.length === 0) {
            showConfirm({
                title: 'Atención',
                message: '⚠️ Por favor seleccione al menos una materia.',
                confirmText: 'OK'
            });
            return;
        }
        
        showConfirm({
            title: 'Confirmar Inscripción',
            message: `¿Está seguro de inscribir ${checkboxes.length} materia(s)?`,
            confirmText: 'Inscribir'
        }).then(confirmed => {
            if (confirmed) {
                if (!form.querySelector('input[name="inscribir_materias"]')) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'inscribir_materias';
                    hidden.value = '1';
                    form.appendChild(hidden);
                }
                form.submit();
            }
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>