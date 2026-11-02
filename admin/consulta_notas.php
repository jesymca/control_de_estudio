<?php
require_once('../funciones/functions.php');

//CARGAR PERMISOS
cargarPermisosUsuario();
verificarPermiso('consultar_notas');

if (!isLoggedIn()) {
    header('location: ../login.php');
    exit();
}

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

$titulopag = "Consulta de Notas por Cédula";
include("includes/head.php");

// Procesar búsqueda
$estudiante = null;
$carrera = null;
$materias_carrera = [];
$notas_estudiante = [];
$mensaje_error = '';
$info_apto = null;

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
        // Obtener información de la carrera
        $carrera = obtenerCarreraEstudiante($estudiante['id']);
        
        if ($carrera) {
            // Obtener materias que el estudiante tiene inscritas o con notas en la carrera
            $materias_carrera = obtenerMateriasEstudianteConsultaNotas($estudiante['id'], $carrera['id_carrera']);
            
            // Obtener historial completo de notas e intentos previos (incluyendo materias repitientes)
            $historial_notas = obtenerHistorialCompletoNotasEstudiante($estudiante['id']);
            
            // Determinar si es apto para grado
            $info_apto = esAptoParaGradoConsulta($estudiante['id'], $carrera['id_carrera']);
        }
    } else {
        $mensaje_error = "No se encontró ningún estudiante con la cédula: " . htmlspecialchars($cedula);
    }
} else if (isset($_GET['cedula']) && empty($_GET['cedula'])) {
    $mensaje_error = "Por favor, ingrese una cédula válida para buscar.";
}
?>

<div class="container-fluid">
    <h2 class="my-4">Consulta de Notas por Cédula</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5>Buscar Estudiante</h5>
        </div>
        <div class="card-body">
            <form method="POST" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <label for="cedula" class="mr-2">Cédula del Estudiante:</label>
                    <input type="text" class="form-control" id="cedula" name="cedula" 
                           placeholder="Ej: V12345678" value="<?= isset($_POST['cedula']) ? htmlspecialchars($_POST['cedula']) : (isset($_GET['cedula']) ? htmlspecialchars($_GET['cedula']) : '') ?>" required>
                </div>
                <button type="submit" class="btn btn-success mb-2">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </form>
            
            <?php if (!empty($mensaje_error)): ?>
                <div class="alert alert-danger mt-3"><?= $mensaje_error ?></div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($estudiante && $carrera && $materias_carrera && $materias_carrera->num_rows > 0): ?>
<div class="card mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Información del Estudiante</h5>
        <div class="btn-group" role="group">
            <a href="historial_desglozado_tsu.php?estudiante_id=<?= $estudiante['id'] ?>&cedula=<?= urlencode($estudiante['idusuario']) ?>&nombre=<?= urlencode($estudiante['nombre']) ?>&carrera=<?= urlencode($carrera['nombre_carrera']) ?>" 
               class="btn btn-warning btn-sm mr-2" target="_blank">
                <i class="fas fa-file-pdf"></i> Historial TSU
            </a>
            <a href="historial_desglozado_ingenieria.php?estudiante_id=<?= $estudiante['id'] ?>&cedula=<?= urlencode($estudiante['idusuario']) ?>&nombre=<?= urlencode($estudiante['nombre']) ?>&carrera=<?= urlencode($carrera['nombre_carrera']) ?>" 
               class="btn btn-info btn-sm mr-2" target="_blank">
                <i class="fas fa-file-pdf"></i> Historial Ingeniería
            </a>
            <a href="generar_reporte_consulta.php?estudiante_id=<?= $estudiante['id'] ?>&cedula=<?= urlencode($estudiante['idusuario']) ?>&nombre=<?= urlencode($estudiante['nombre']) ?>&carrera=<?= urlencode($carrera['nombre_carrera']) ?>" 
               class="btn btn-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf"></i> Historial Completo
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Cédula:</strong> <?= htmlspecialchars($estudiante['idusuario']) ?></p>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($estudiante['nombre']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Carrera:</strong> <?= htmlspecialchars($carrera['nombre_carrera']) ?> (<?= htmlspecialchars($carrera['cod_carrera']) ?>)</p>
                <p><strong>Materias Inscritas:</strong> <span class="badge badge-primary"><?= $materias_carrera->num_rows ?></span></p>
                <?php if ($info_apto): ?>
                <p><strong>Estado para Grado:</strong> 
                    <?= obtenerBadgeEstadoConsulta($info_apto) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($info_apto): ?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert <?= ($info_apto['apto_grado_completo'] || $info_apto['apto_tsu']) ? 'alert-success' : 'alert-warning' ?>">
                    <h6><i class="fas fa-graduation-cap"></i> Evaluación para Grado:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>TSU (Trayectos 0-2):</strong><br>
                            <?= $info_apto['materias_aprobadas_tsu'] ?>/<?= $info_apto['total_materias_tsu'] ?> materias aprobadas de la carrera<br>
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
                            <?= $info_apto['materias_aprobadas_completo'] ?>/<?= $info_apto['total_materias_carrera'] ?> materias aprobadas de la carrera<br>
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

<div class="card">
    <div class="card-header bg-success text-white">
        <h5>Materias Inscritas y Notas por Trimestre</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Trayecto</th>
                        <th>Materia</th>
                        <th>Código</th>
                        <th class="text-center">Trimestre 1</th>
                        <th class="text-center">Trimestre 2</th>
                        <th class="text-center">Trimestre 3</th>
                        <th class="text-center">Nota Final</th>
                        <th class="text-center">Estado</th>
                        <th>Periodo</th>
                        <th>Fecha</th>
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
                            $todas_las_evaluaciones[] = [
                                'id_materia' => $mid,
                                'nombre_materia' => $materia['nombre_materia'],
                                'nota' => $nota_final,
                                'tipo' => 'actual',
                                'periodo' => $intento_actual['nombre_periodo'] ?? '-'
                            ];
                        } else {
                            $estado = 'En curso';
                            $badge_estado = 'secondary';
                            $materias_sin_notas++;
                        }
                        
                        // Recolectar notas de los intentos previos (materias repitientes) para el promedio general
                        foreach ($intentos_previos as $prev) {
                            if ($prev['nota_final'] !== null) {
                                $todas_las_evaluaciones[] = [
                                    'id_materia' => $mid,
                                    'nombre_materia' => $materia['nombre_materia'],
                                    'nota' => $prev['nota_final'],
                                    'tipo' => 'repitiente',
                                    'periodo' => $prev['nombre_periodo'] ?? '-'
                                ];
                                $total_intentos_repitientes_evaluados++;
                            }
                        }
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($nombre_trayecto) ?></td>
                            <td>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <strong><?= htmlspecialchars($materia['nombre_materia']) ?></strong>
                                        <?php if (!empty($materia['es_proyecto_socio']) && $materia['es_proyecto_socio'] == 1): ?>
                                            <span class="badge badge-info ml-1" title="Proyecto Socio Integrador (Mín. 16 pts)"><i class="fas fa-project-diagram"></i> Proyecto</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($intentos_previos)): 
                                        $ult_prev = end($intentos_previos);
                                        $ult_nota_prev = $ult_prev['nota_final'] ?? null;
                                    ?>
                                        <button class="btn btn-sm btn-outline-info ml-2 py-0 px-2 text-nowrap" 
                                                type="button" 
                                                data-toggle="collapse" 
                                                data-target="#hist_materia_<?= $mid ?>" 
                                                aria-expanded="false" 
                                                aria-controls="hist_materia_<?= $mid ?>"
                                                title="Haga clic para desplegar u ocultar la nota que tuvo antes">
                                            <i class="fas fa-chevron-down flecha-toggle"></i> 
                                            <span class="badge badge-warning text-dark ml-1">
                                                <?= $ult_nota_prev !== null ? 'Nota anterior: ' . number_format($ult_nota_prev, 1) : 'Repitiente (' . count($intentos_previos) . ')' ?>
                                            </span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($materia['cod_materia']) ?></td>
                            
                            <td class="text-center">
                                <?php if ($t1 !== null): ?>
                                    <span class="badge badge-<?= $t1 >= $nota_min ? 'success' : 'danger' ?> p-2">
                                        <?= number_format($t1, 1) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center">
                                <?php if ($t2 !== null): ?>
                                    <span class="badge badge-<?= $t2 >= $nota_min ? 'success' : 'danger' ?> p-2">
                                        <?= number_format($t2, 1) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center">
                                <?php if ($t3 !== null): ?>
                                    <span class="badge badge-<?= $t3 >= $nota_min ? 'success' : 'danger' ?> p-2">
                                        <?= number_format($t3, 1) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            
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
                                <span class="badge badge-<?= $badge_estado ?>"><?= $estado ?></span>
                            </td>
                            
                            <td><?= htmlspecialchars($intento_actual['nombre_periodo'] ?? '-') ?></td>
                            <td class="text-center"><?= !empty($intento_actual['fecha_registro']) ? date('d/m/Y', strtotime($intento_actual['fecha_registro'])) : '-' ?></td>
                        </tr>
                        <?php if (!empty($intentos_previos)): ?>
                        <tr class="collapse bg-light" id="hist_materia_<?= $mid ?>">
                            <td colspan="10" class="p-3">
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
                                                        <th>Estado</th>
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
                                                    ?>
                                                    <tr>
                                                        <td class="text-left pl-3 font-weight-bold">
                                                            <i class="fas fa-calendar-alt text-muted mr-1"></i> <?= htmlspecialchars($prev['nombre_periodo'] ?? '-') ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($pt1 !== null): ?>
                                                                <span class="badge badge-<?= $pt1 >= $nota_min ? 'success' : 'danger' ?> px-2 py-1"><?= number_format($pt1, 1) ?></span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($pt2 !== null): ?>
                                                                <span class="badge badge-<?= $pt2 >= $nota_min ? 'success' : 'danger' ?> px-2 py-1"><?= number_format($pt2, 1) ?></span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($pt3 !== null): ?>
                                                                <span class="badge badge-<?= $pt3 >= $nota_min ? 'success' : 'danger' ?> px-2 py-1"><?= number_format($pt3, 1) ?></span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($pfinal !== null): ?>
                                                                <span class="badge badge-<?= $p_aprobado ? 'success' : 'danger' ?> px-2 py-1 font-weight-bold">
                                                                    <?= number_format($pfinal, 1) ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary">Sin nota</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($pfinal !== null): ?>
                                                                <span class="badge badge-<?= $p_aprobado ? 'success' : 'danger' ?>">
                                                                    <?= $p_aprobado ? 'Aprobado' : 'Reprobado' ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary">-</span>
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
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6>Resumen Académico</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $total_materias = $materias_carrera ? $materias_carrera->num_rows : 0;
                        $total_evaluaciones = count($todas_las_evaluaciones);
                        $promedio_general = $total_evaluaciones > 0 ? round(array_sum(array_column($todas_las_evaluaciones, 'nota')) / $total_evaluaciones, 2) : 0;
                        $porcentaje_completado = $total_materias > 0 ? round(($materias_con_notas / $total_materias) * 100, 1) : 0;
                        ?>
                        
                        <div class="row">
                            <div class="col-6 text-center mb-3">
                                <h3 class="text-primary"><?= $total_materias ?></h3>
                                <small class="font-weight-bold">Materias Registradas</small>
                            </div>
                            <div class="col-6 text-center mb-3">
                                <h3 class="<?= $promedio_general >= 12 ? 'text-success' : ($promedio_general > 0 ? 'text-warning' : 'text-secondary') ?>">
                                    <?= $promedio_general > 0 ? number_format($promedio_general, 2) : 'N/A' ?>
                                </h3>
                                <small class="font-weight-bold">Promedio General</small>
                                <div class="mt-1">
                                    <span class="badge badge-info" title="Total de notas evaluadas contabilizadas en el promedio"><?= $total_evaluaciones ?> evaluadas</span>
                                    <?php if ($total_intentos_repitientes_evaluados > 0): ?>
                                        <span class="badge badge-warning text-dark ml-1" title="Notas de materias repitientes incluidas"><?= $total_intentos_repitientes_evaluados ?> repitiente(s)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <h3 class="text-success"><?= $materias_aprobadas ?></h3>
                                <small class="font-weight-bold">Aprobadas (Período actual)</small>
                            </div>
                            <div class="col-6 text-center">
                                <h3 class="text-danger"><?= $materias_reprobadas ?></h3>
                                <small class="font-weight-bold">Reprobadas (Período actual)</small>
                            </div>
                        </div>
                        
                        <div class="progress mt-3" style="height: 25px;">
                            <div class="progress-bar bg-success" style="width: <?= $porcentaje_completado ?>%">
                                <?= $porcentaje_completado ?>%
                            </div>
                        </div>
                        <p class="mt-2 text-center"><small>Progreso de materias cursadas en período actual</small></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6>Leyenda</h6>
                    </div>
                    <div class="card-body">
                        <p><span class="badge bg-success p-2">≥ 12</span> = Aprobado (≥ 16 para Proyectos Socio Integradores)</p>
                        <p><span class="badge bg-danger p-2">&lt; 12</span> = Reprobado (&lt; 16 para Proyectos Socio Integradores)</p>
                        <p><span class="badge bg-secondary p-2">-</span> = Sin nota registrada / En curso</p>
                        <p><span class="badge badge-warning text-dark p-2"><i class="fas fa-chevron-down"></i> Repitiente</span> = Materia con intentos previos (clic en la flecha para desplegar notas anteriores)</p>
                        <hr>
                        <p class="mb-1"><i class="fas fa-calculator text-primary"></i> <strong>Promedio General:</strong> Incluye todas las notas evaluadas del estudiante (tanto materias cursadas actualmente como materias repitientes de períodos anteriores).</p>
                        <p class="mb-0"><i class="fas fa-chart-line text-success"></i> <strong>Nota Final por Período:</strong> Promedio de los trimestres evaluados.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php elseif ($estudiante && $carrera && (!$materias_carrera || $materias_carrera->num_rows == 0)): ?>
    <div class="alert alert-warning">
        Este estudiante no tiene materias inscritas o registradas en la carrera: <?= htmlspecialchars($carrera['nombre_carrera']) ?>
    </div>
<?php endif; ?>

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
    $('.collapse').on('show.bs.collapse', function() {
        $('button[data-target="#' + $(this).attr('id') + '"] .flecha-toggle')
            .removeClass('fa-chevron-down')
            .addClass('fa-chevron-up');
    }).on('hide.bs.collapse', function() {
        $('button[data-target="#' + $(this).attr('id') + '"] .flecha-toggle')
            .removeClass('fa-chevron-up')
            .addClass('fa-chevron-down');
    });
});
</script>

<?php include("includes/footer.php"); ?>