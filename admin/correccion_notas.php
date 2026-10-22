<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Corrección de Notas";
include('../funciones/functions.php');

// Verificar permisos y sesión
cargarPermisosUsuario();
verificarPermiso('editar_nota');

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

// ==========================================
// CONTROLADOR AJAX PARA BÚSQUEDA EN TIEMPO REAL
// (Usa la función centralizada buscarEstudiantesPagosAjax de functions.php)
// ==========================================
if (isset($_POST['ajax_buscar_estudiantes'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    $termino = trim($_POST['termino'] ?? '');
    $estudiantes = function_exists('buscarEstudiantesPagosAjax') ? buscarEstudiantesPagosAjax($termino, 15) : [];
    
    echo json_encode(['success' => true, 'estudiantes' => $estudiantes]);
    exit();
}

// Procesar formularios
$mensaje = '';
$tipo_mensaje = '';
$estudiante = null;
$carreras = [];
$materias = [];
$notas = [];
$historial_completo = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'buscar_estudiante':
                $id_usuario = intval($_POST['id_usuario'] ?? 0);
                $cedula = trim($_POST['cedula'] ?? '');
                
                if ($id_usuario > 0) {
                    $estudiante = obtenerEstudiantePorId($id_usuario);
                    if (isset($estudiante['error'])) $estudiante = null;
                } elseif (!empty($cedula)) {
                    // 1. Búsqueda directa por cédula con auditoría estándar
                    $estudiante = buscarEstudiantePorCedulaConsulta($cedula);
                    
                    // 2. Fallback a búsqueda general por cédula
                    if (!$estudiante) {
                        $res_est = buscarEstudiantePorCedula($cedula);
                        if (is_array($res_est) && !empty($res_est)) {
                            if (isset($res_est['id'])) {
                                $estudiante = $res_est;
                            } else if (isset($res_est[0]['id'])) {
                                $estudiante = $res_est[0];
                            }
                        }
                    }
                    
                    // 3. Si no se encontró y quizás ingresó un nombre, buscar con la función ajax
                    if (!$estudiante && function_exists('buscarEstudiantesPagosAjax')) {
                        $res_ajax = buscarEstudiantesPagosAjax($cedula, 2);
                        if (is_array($res_ajax) && count($res_ajax) === 1 && isset($res_ajax[0]['id'])) {
                            $estudiante = obtenerEstudiantePorId($res_ajax[0]['id']);
                            if (isset($estudiante['error'])) $estudiante = null;
                        }
                    }
                }
                
                if ($estudiante && is_array($estudiante) && isset($estudiante['id'])) {
                    $carreras = obtenerCarrerasEstudiante($estudiante['id']);
                    $historial_completo = obtenerHistorialCambiosNotasEstudiante($estudiante['id']);
                } else {
                    $estudiante = null;
                    $mensaje = 'No se encontró ningún estudiante con esa cédula o búsqueda';
                    $tipo_mensaje = 'warning';
                }
                break;
                
            case 'seleccionar_carrera':
                $estudiante_id = $_POST['id_usuario'] ?? '';
                $id_carrera = $_POST['id_carrera'] ?? '';
                if (!empty($estudiante_id) && !empty($id_carrera)) {
                    $estudiante = obtenerEstudiantePorId($estudiante_id);
                    $carreras = obtenerCarrerasEstudiante($estudiante_id);
                    $materias = obtenerMateriasInscritasPorEstudiante($estudiante_id, $id_carrera);
                    $historial_completo = obtenerHistorialCambiosNotasEstudiante($estudiante_id);
                }
                break;
                
            case 'seleccionar_materia':
                $estudiante_id = $_POST['id_usuario'] ?? '';
                $id_carrera = $_POST['id_carrera'] ?? '';
                $id_materia = $_POST['id_materia'] ?? '';
                if (!empty($estudiante_id) && !empty($id_materia)) {
                    $estudiante = obtenerEstudiantePorId($estudiante_id);
                    $carreras = obtenerCarrerasEstudiante($estudiante_id);
                    $materias = obtenerMateriasInscritasPorEstudiante($estudiante_id, $id_carrera);
                    $notas = obtenerNotasTrimestresPorMateria($estudiante_id, $id_materia);
                    $historial_completo = obtenerHistorialCambiosNotasEstudiante($estudiante_id);
                }
                break;
                
            case 'editar_nota':
                $resultado = procesarEdicionNotaTrimestral();
                if ($resultado['success']) {
                    $mensaje = $resultado['message'];
                    $tipo_mensaje = 'success';
                    $estudiante_id = $_POST['id_usuario'] ?? '';
                    $id_carrera = $_POST['id_carrera'] ?? '';
                    $id_materia = $_POST['id_materia'] ?? '';
                    if (!empty($estudiante_id)) {
                        $estudiante = obtenerEstudiantePorId($estudiante_id);
                        $carreras = obtenerCarrerasEstudiante($estudiante_id);
                        $materias = obtenerMateriasInscritasPorEstudiante($estudiante_id, $id_carrera);
                        $notas = obtenerNotasTrimestresPorMateria($estudiante_id, $id_materia);
                        $historial_completo = obtenerHistorialCambiosNotasEstudiante($estudiante_id);
                    }
                } else {
                    $mensaje = $resultado['message'];
                    $tipo_mensaje = 'error';
                }
        }
    }
}

// Cédula activa para formularios y vistas
$cedula_actual = '';
if ($estudiante && is_array($estudiante)) {
    $cedula_actual = $estudiante['idusuario'] ?? $estudiante['cedula'] ?? '';
} elseif (!empty($_POST['cedula'])) {
    $cedula_actual = trim($_POST['cedula']);
}

include("includes/head.php");
?>

<style>
.justificacion-texto {
    max-height: 80px;
    overflow-y: auto;
    font-size: 0.85rem;
    padding: 5px;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.table-responsive {
    max-height: 500px;
    overflow-y: auto;
}

.badge {
    font-size: 0.8rem;
    padding: 0.4em 0.6em;
}

.btn-reporte {
    background-color: #17a2b8;
    border-color: #17a2b8;
    color: white;
}

.btn-reporte:hover {
    background-color: #138496;
    border-color: #117a8b;
    color: white;
}

.btn-group-actions {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.historial-row:hover {
    background-color: #f8f9fa;
}

.bg-danger {
    background-color: #dc3545 !important;
    color: white !important;
}

.bg-success {
    background-color: #28a745 !important;
    color: white !important;
}

.bg-secondary {
    background-color: #6c757d !important;
    color: white !important;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">Corrección de Notas</h1>
            
            <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje === 'success' ? 'success' : ($tipo_mensaje === 'warning' ? 'warning' : 'danger'); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- Paso 1: Buscar estudiante -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-search mr-1"></i> Buscar Estudiante
                    </h6>
                    <?php if ($estudiante): ?>
                    <a href="correccion_notas.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times mr-1"></i> Limpiar Búsqueda
                    </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="form_buscar_estudiante">
                        <input type="hidden" name="accion" value="buscar_estudiante">
                        <input type="hidden" name="id_usuario" id="input_sel_id_usuario" value="">
                        
                        <div class="position-relative">
                            <label for="buscador_estudiante" class="font-weight-bold text-gray-800 mb-1">
                                <i class="fas fa-user-graduate mr-1 text-primary"></i> Cédula o Nombre del Estudiante:
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" 
                                       id="buscador_estudiante" 
                                       name="cedula" 
                                       class="form-control" 
                                       placeholder="<?php echo $estudiante ? 'Buscar otro estudiante por nombre o cédula...' : 'Escriba la cédula o nombre del estudiante...'; ?>" 
                                       value="<?php echo htmlspecialchars($cedula_actual); ?>" 
                                       autocomplete="off"
                                       <?php echo $estudiante ? '' : 'autofocus'; ?>>
                                <div class="input-group-append" id="spinner_busqueda_est" style="display: none;">
                                    <span class="input-group-text bg-white text-primary">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i> Buscar
                                    </button>
                                </div>
                                <?php if ($estudiante): ?>
                                <div class="input-group-append">
                                    <a href="correccion_notas.php" class="btn btn-outline-secondary" title="Limpiar y buscar otro">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <small class="form-text text-muted">Escriba para ver sugerencias en tiempo real o ingrese la cédula y presione Buscar.</small>

                            <!-- Lista flotante de sugerencias -->
                            <div id="sugerencias_estudiantes" class="list-group shadow position-absolute w-100 mt-1" 
                                 style="z-index: 9999; display: none; max-height: 320px; overflow-y: auto; left: 0; right: 0; background: #ffffff; border: 1px solid #007bff; border-radius: 6px;">
                            </div>
                        </div>
                    </form>
                    
                    <?php if ($estudiante && is_array($estudiante) && isset($estudiante['id'])): ?>
                    <div class="mt-3 p-3 bg-light rounded border-left-primary shadow-sm">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-user mr-1 text-primary"></i> Nombre:</strong> <?php echo htmlspecialchars($estudiante['nombre'] ?? ''); ?></p>
                                <p class="mb-1"><strong><i class="fas fa-id-card mr-1 text-primary"></i> Cédula:</strong> <?php echo htmlspecialchars($estudiante['idusuario'] ?? $estudiante['cedula'] ?? ''); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-hashtag mr-1 text-primary"></i> ID Estudiante:</strong> <?php echo htmlspecialchars((string)($estudiante['id'] ?? '')); ?></p>
                                <p class="mb-0"><strong><i class="fas fa-history mr-1 text-info"></i> Total de cambios:</strong> <span class="badge badge-info"><?php echo is_array($historial_completo) ? count($historial_completo) : 0; ?></span></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Paso 2: Seleccionar Carrera -->
            <?php if ($estudiante && is_array($estudiante) && isset($estudiante['id']) && !empty($carreras)): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Seleccionar Carrera</h6>
                    <small>Se encontraron <?php echo count($carreras); ?> carrera(s)</small>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="accion" value="seleccionar_carrera">
                        <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars((string)($estudiante['id'] ?? '')); ?>">
                        <input type="hidden" name="cedula" value="<?php echo htmlspecialchars($cedula_actual); ?>">
                        
                        <div class="form-group">
                            <label for="id_carrera">Seleccione la Carrera:</label>
                            <select name="id_carrera" id="id_carrera" class="form-control" required onchange="this.form.submit()">
                                <option value="">Seleccionar Carrera</option>
                                <?php foreach ($carreras as $carrera): ?>
                                <option value="<?php echo $carrera['id_carrera']; ?>" 
                                    <?php echo (isset($_POST['id_carrera']) && $_POST['id_carrera'] == $carrera['id_carrera']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($carrera['nombre_carrera']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Paso 3: Seleccionar Materia -->
            <?php if ($estudiante && !empty($materias) && $materias->num_rows > 0): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Seleccionar Materia</h6>
                    <small>Se encontraron <?php echo $materias->num_rows; ?> materia(s) inscritas</small>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="accion" value="seleccionar_materia">
                        <input type="hidden" name="id_usuario" value="<?php echo $estudiante['id']; ?>">
                        <input type="hidden" name="id_carrera" value="<?php echo htmlspecialchars($_POST['id_carrera'] ?? ''); ?>">
                        <input type="hidden" name="cedula" value="<?php echo htmlspecialchars($cedula_actual); ?>">
                        
                        <div class="form-group">
                            <label for="id_materia">Seleccione la Materia:</label>
                            <select name="id_materia" id="id_materia" class="form-control" required onchange="this.form.submit()">
                                <option value="">Seleccionar Materia</option>
                                <?php 
                                $materias->data_seek(0);
                                while ($materia = $materias->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $materia['id_materia']; ?>" 
                                    <?php echo (isset($_POST['id_materia']) && $_POST['id_materia'] == $materia['id_materia']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($materia['nombre_materia']); ?> 
                                    - Trayecto <?php echo $materia['trayecto']; ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Paso 4: Mostrar y Editar Notas -->
            <?php if ($estudiante && isset($_POST['id_materia']) && !empty($notas)): ?>
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Notas del Estudiante - Trimestres
                    </h6>
                    <small>Se encontraron <?php echo count($notas); ?> periodo(s) académico(s)</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Periodo</th>
                                    <th class="text-center">Trimestre 1</th>
                                    <th class="text-center">Trimestre 2</th>
                                    <th class="text-center">Trimestre 3</th>
                                    <th class="text-center">Nota Final</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notas as $nota): 
                                    $t1 = $nota['trimestre_1'];
                                    $t2 = $nota['trimestre_2'];
                                    $t3 = $nota['trimestre_3'];
                                    
                                    $suma = 0;
                                    $count = 0;
                                    if ($t1 !== null) { $suma += $t1; $count++; }
                                    if ($t2 !== null) { $suma += $t2; $count++; }
                                    if ($t3 !== null) { $suma += $t3; $count++; }
                                    $nota_final = $count > 0 ? round($suma / $count, 1) : null;
                                    
                                    $estado = $nota['estado'];
                                    $badge_class = 'secondary';
                                    $badge_text = 'Pendiente';
                                    
                                    if ($estado === 'aprobada') {
                                        $badge_class = 'success';
                                        $badge_text = 'Aprobada';
                                    } elseif ($estado === 'rechazada') {
                                        $badge_class = 'danger';
                                        $badge_text = 'Rechazada';
                                    } elseif ($estado === 'en_revision') {
                                        $badge_class = 'warning';
                                        $badge_text = 'En Revisión';
                                    }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($nota['nombre_periodo'] ?? 'Sin periodo'); ?></td>
                                    <td class="text-center"><span class="badge <?php echo ($t1 === null) ? 'bg-secondary' : ($t1 >= 12 ? 'bg-success' : 'bg-danger'); ?> p-2"><?php echo ($t1 !== null) ? number_format($t1, 1) : 'N/A'; ?></span></td>
                                    <td class="text-center"><span class="badge <?php echo ($t2 === null) ? 'bg-secondary' : ($t2 >= 12 ? 'bg-success' : 'bg-danger'); ?> p-2"><?php echo ($t2 !== null) ? number_format($t2, 1) : 'N/A'; ?></span></td>
                                    <td class="text-center"><span class="badge <?php echo ($t3 === null) ? 'bg-secondary' : ($t3 >= 12 ? 'bg-success' : 'bg-danger'); ?> p-2"><?php echo ($t3 !== null) ? number_format($t3, 1) : 'N/A'; ?></span></td>
                                    <td class="text-center"><span class="badge <?php echo ($nota_final === null) ? 'bg-secondary' : ($nota_final >= 12 ? 'bg-success' : 'bg-danger'); ?> p-2"><?php echo ($nota_final !== null) ? number_format($nota_final, 1) : 'N/A'; ?></span></td>
                                    <td class="text-center"><span class="badge badge-<?php echo $badge_class; ?>"><?php echo $badge_text; ?></span></td>
                                    <td class="text-center"><?php echo !empty($nota['fecha_registro']) ? date('d/m/Y', strtotime($nota['fecha_registro'])) : '-'; ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEditarNota<?php echo $nota['id']; ?>">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                    </div>
                                </tr>

                                <!-- MODAL EDITAR NOTA -->
                                <div class="modal fade" id="modalEditarNota<?php echo $nota['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Editar Nota - <?php echo htmlspecialchars($nota['nombre_periodo'] ?? 'Sin periodo'); ?></h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="accion" value="editar_nota">
                                                    <input type="hidden" name="id_usuario" value="<?php echo $estudiante['id']; ?>">
                                                    <input type="hidden" name="id_materia" value="<?php echo htmlspecialchars($_POST['id_materia'] ?? ''); ?>">
                                                    <input type="hidden" name="id_periodo" value="<?php echo $nota['id_periodo']; ?>">
                                                    <input type="hidden" name="cedula" value="<?php echo htmlspecialchars($cedula_actual); ?>">
                                                    
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle"></i> 
                                                        <strong>Información:</strong> Modifique los trimestres que desee. Los campos vacíos mantendrán su valor actual.
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Trimestre 1:</label>
                                                                <input type="number" name="trimestre_1" class="form-control" step="1" min="1" max="20" value="<?php echo $t1; ?>">
                                                                <small class="text-muted">Actual: <?php echo $t1 !== null ? number_format($t1, 1) : 'No registrado'; ?></small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Trimestre 2:</label>
                                                                <input type="number" name="trimestre_2" class="form-control" step="1" min="1" max="20" value="<?php echo $t2; ?>">
                                                                <small class="text-muted">Actual: <?php echo $t2 !== null ? number_format($t2, 1) : 'No registrado'; ?></small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Trimestre 3:</label>
                                                                <input type="number" name="trimestre_3" class="form-control" step="1" min="1" max="20" value="<?php echo $t3; ?>">
                                                                <small class="text-muted">Actual: <?php echo $t3 !== null ? number_format($t3, 1) : 'No registrado'; ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group mt-3">
                                                        <label>Estado de la Nota:</label>
                                                        <select name="estado" class="form-control">
                                                            <option value="pendiente" <?php echo $estado === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                                            <option value="aprobada" <?php echo $estado === 'aprobada' ? 'selected' : ''; ?>>Aprobada</option>
                                                            <option value="rechazada" <?php echo $estado === 'rechazada' ? 'selected' : ''; ?>>Rechazada</option>
                                                            <option value="en_revision" <?php echo $estado === 'en_revision' ? 'selected' : ''; ?>>En Revisión</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label>Justificación del Cambio:</label>
                                                        <textarea name="justificacion" class="form-control" rows="3" required placeholder="Explique detalladamente por qué se realiza este cambio..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- HISTORIAL DE CAMBIOS -->
            <?php if ($estudiante && is_array($estudiante) && isset($estudiante['id'])): ?>
            <div class="card shadow mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-history"></i> Historial de Cambios de Notas
                    </h6>
                    <small>Todos los cambios realizados a las notas de <?php echo htmlspecialchars($estudiante['nombre'] ?? ''); ?></small>
                </div>
                <div class="card-body">
                    <?php if (!empty($historial_completo)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Materia</th>
                                    <th>Periodo</th>
                                    <th>Cambios realizados</th>
                                    <th>Administrador</th>
                                    <th>Justificación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial_completo as $cambio): ?>
                                <tr class="historial-row">
                                    <td><?php echo date('d/m/Y H:i', strtotime($cambio['fecha_cambio'])); ?></td>
                                    <td><?php echo htmlspecialchars($cambio['nombre_materia']); ?></td>
                                    <td><?php echo htmlspecialchars($cambio['nombre_periodo']); ?></td>
                                    <td>
                                        <?php 
                                        $cambios_texto = [];
                                        if (isset($cambio['trimestre_1_anterior']) && isset($cambio['trimestre_1_nuevo']) && $cambio['trimestre_1_anterior'] != $cambio['trimestre_1_nuevo']) {
                                            $cambios_texto[] = "T1: " . ($cambio['trimestre_1_anterior'] ?? 'N/A') . " → " . ($cambio['trimestre_1_nuevo'] ?? 'N/A');
                                        }
                                        if (isset($cambio['trimestre_2_anterior']) && isset($cambio['trimestre_2_nuevo']) && $cambio['trimestre_2_anterior'] != $cambio['trimestre_2_nuevo']) {
                                            $cambios_texto[] = "T2: " . ($cambio['trimestre_2_anterior'] ?? 'N/A') . " → " . ($cambio['trimestre_2_nuevo'] ?? 'N/A');
                                        }
                                        if (isset($cambio['trimestre_3_anterior']) && isset($cambio['trimestre_3_nuevo']) && $cambio['trimestre_3_anterior'] != $cambio['trimestre_3_nuevo']) {
                                            $cambios_texto[] = "T3: " . ($cambio['trimestre_3_anterior'] ?? 'N/A') . " → " . ($cambio['trimestre_3_nuevo'] ?? 'N/A');
                                        }
                                        echo !empty($cambios_texto) ? implode('<br>', $cambios_texto) : 'Sin cambios registrados';
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($cambio['nombre_admin'] ?? 'Desconocido'); ?></td>
                                    <td style="max-width: 250px;"><?php echo nl2br(htmlspecialchars($cambio['justificacion'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-light text-center mb-0">
                        <i class="fas fa-info-circle"></i> No hay registros de cambios de notas para este estudiante.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBuscador = document.getElementById('buscador_estudiante');
    const sugerenciasContainer = document.getElementById('sugerencias_estudiantes');
    const spinnerBusqueda = document.getElementById('spinner_busqueda_est');
    const formBuscar = document.getElementById('form_buscar_estudiante');
    const inputSelId = document.getElementById('input_sel_id_usuario');
    let debounceTimer = null;

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

    function buscarEstudiantes() {
        if (!inputBuscador || !sugerenciasContainer) return;
        const termino = inputBuscador.value.trim();
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
            fd.append('ajax_buscar_estudiantes', '1');
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
                                        <span>${escapeHtml(est.nombre_carrera || 'Sin Carrera')}</span>
                                    </div>
                                </div>
                                <span class="btn btn-sm btn-success py-1 px-2"><i class="fas fa-check mr-1"></i> Seleccionar</span>
                            `;

                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                if (inputSelId && formBuscar && inputBuscador) {
                                    inputSelId.value = est.id;
                                    inputBuscador.value = est.cedula || est.idusuario;
                                    formBuscar.submit();
                                }
                            });

                            sugerenciasContainer.appendChild(item);
                        });
                        sugerenciasContainer.style.display = 'block';
                    } else {
                        sugerenciasContainer.innerHTML = `
                            <div class="list-group-item p-3 text-center text-muted">
                                <i class="fas fa-user-slash mr-1"></i> No se encontraron estudiantes activos con "<strong>${escapeHtml(termino)}</strong>".
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

    if (inputBuscador) {
        inputBuscador.addEventListener('input', buscarEstudiantes);
        inputBuscador.addEventListener('focus', function() {
            if (this.value.trim().length > 0) {
                buscarEstudiantes();
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (inputBuscador && sugerenciasContainer && !inputBuscador.contains(e.target) && !sugerenciasContainer.contains(e.target)) {
            sugerenciasContainer.style.display = 'none';
        }
    });
});
</script>

<?php include("includes/footer.php"); ?>