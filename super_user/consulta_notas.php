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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cedula'])) {
    $cedula = trim($_POST['cedula']);
    
    if (!empty($cedula)) {
        $estudiante = buscarEstudiantePorCedulaConsulta($cedula);
        
        if ($estudiante) {
            // Obtener información de la carrera
            $carrera = obtenerCarreraEstudiante($estudiante['id']);
            
            if ($carrera) {
                // Obtener todas las materias de la carrera
                $materias_carrera = obtenerMateriasCarrera($carrera['id_carrera']);
                
                // Obtener notas del estudiante (si existen)
                $notas_estudiante = obtenerNotasEstudianteConsulta($estudiante['id']);
                
                // Determinar si es apto para grado
                $info_apto = esAptoParaGradoConsulta($estudiante['id'], $carrera['id_carrera']);
            }
        } else {
            $mensaje_error = "No se encontró ningún estudiante con la cédula: " . htmlspecialchars($cedula);
        }
    } else {
        $mensaje_error = "Por favor, ingrese una cédula para buscar.";
    }
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
                           placeholder="Ej: V12345678" value="<?= isset($_POST['cedula']) ? htmlspecialchars($_POST['cedula']) : '' ?>" required>
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
    
    <?php if ($estudiante && $carrera): ?>
<div class="card mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Información del Estudiante</h5>
        <div class="btn-group" role="group">
            <!-- Botón 1: Historial TSU -->
            <a href="historial_desglozado_tsu.php?estudiante_id=<?= $estudiante['id'] ?>&cedula=<?= urlencode($estudiante['idusuario']) ?>&nombre=<?= urlencode($estudiante['nombre']) ?>&carrera=<?= urlencode($carrera['nombre_carrera']) ?>" 
               class="btn btn-warning btn-sm mr-2" target="_blank">
                <i class="fas fa-file-pdf"></i> Historial TSU
            </a>
            
            <!-- Botón 2: Historial Ingeniería -->
            <a href="historial_desglozado_ingenieria.php?estudiante_id=<?= $estudiante['id'] ?>&cedula=<?= urlencode($estudiante['idusuario']) ?>&nombre=<?= urlencode($estudiante['nombre']) ?>&carrera=<?= urlencode($carrera['nombre_carrera']) ?>" 
               class="btn btn-info btn-sm mr-2" target="_blank">
                <i class="fas fa-file-pdf"></i> Historial Ingeniería
            </a>
            
            <!-- Botón 3: Historial Completo -->
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
                    <p><strong>Total de Materias:</strong> <span class="badge badge-primary"><?= $materias_carrera->num_rows ?></span></p>
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
    
    <?php if ($materias_carrera->num_rows > 0): ?>
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5>Plan de Estudios y Notas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th width="100">Trayecto</th>
                            <th>Materia</th>
                            <th width="100">Cod Materia</th>
                            <th width="80">Nota</th>
                            <th width="90">Estado</th>
                            <th width="120">Periodo</th>
                            <th width="100">Fecha</th>
                            <th width="120">Aprobado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $materias_aprobadas = 0;
                        $materias_reprobadas = 0;
                        $materias_sin_notas = 0;
                        $suma_promedios = 0;
                        $materias_con_notas = 0;
                        
                        // Reiniciar el puntero del resultado
                        $materias_carrera->data_seek(0);
                        
                        while ($materia = $materias_carrera->fetch_assoc()): 
                            $nota = isset($notas_estudiante[$materia['id_materia']]) ? $notas_estudiante[$materia['id_materia']] : null;
                            
                            // Obtener información del trayecto de la materia
                            $numero_trayecto_materia = (int)$materia['trayecto'];
                            $info_trayecto = obtenerInfoTrayecto($numero_trayecto_materia);
                            $nombre_trayecto = $info_trayecto['nombre_trayecto'];
                            
                            // Obtener la nota específica del trayecto correspondiente
                            $nota_trayecto = null;
                            $tiene_nota = false;
                            
                            if ($nota) {
                                $campo_trayecto = 'trayecto_' . $numero_trayecto_materia;
                                if (isset($nota[$campo_trayecto]) && $nota[$campo_trayecto] !== null) {
                                    $nota_trayecto = (float)$nota[$campo_trayecto];
                                    $tiene_nota = true;
                                }
                            }
                            
                            // Determinar estado
                            $estado = 'Sin notas';
                            $color_estado = 'secondary';
                            $badge_estado = 'secondary';
                            
                            if ($tiene_nota) {
                                if ($nota_trayecto >= 12) {
                                    $estado = 'Aprobado';
                                    $color_estado = 'success';
                                    $badge_estado = 'success';
                                    $materias_aprobadas++;
                                } else {
                                    $estado = 'Reprobado';
                                    $color_estado = 'danger';
                                    $badge_estado = 'danger';
                                    $materias_reprobadas++;
                                }
                                $suma_promedios += $nota_trayecto;
                                $materias_con_notas++;
                            } else {
                                $materias_sin_notas++;
                            }
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($nombre_trayecto) ?></td>
                                <td><?= htmlspecialchars($materia['nombre_materia']) ?></td>
                                <td><?= htmlspecialchars($materia['cod_materia']) ?></td>
                                
                                <td class="text-center">
                                    <?php if ($tiene_nota): ?>
                                        <div class="nota-display">
                                            <span class="nota-valor <?= $nota_trayecto >= 12 ? 'nota-aprobada' : 'nota-reprobada' ?>">
                                                <?= $nota_trayecto ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge badge-<?= $badge_estado ?>">
                                        <?= $estado ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <?php if ($nota): ?>
                                        <?= htmlspecialchars($nota['nombre_periodo']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <?php if ($nota && $nota['fecha_registro']): ?>
                                        <?= date('d/m/Y', strtotime($nota['fecha_registro'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <?php if ($nota && !empty($nota['nombre_admin'])): ?>
                                        <?= htmlspecialchars($nota['nombre_admin']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Resumen estadístico -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6>Resumen Académico</h6>
                        </div>
                        <div class="card-body">
                            <?php
                            $total_materias = $materias_carrera->num_rows;
                            $promedio_general = $materias_con_notas > 0 ? round($suma_promedios / $materias_con_notas, 1) : 0;
                            $porcentaje_aprobadas = $materias_con_notas > 0 ? round(($materias_aprobadas / $materias_con_notas) * 100, 1) : 0;
                            $porcentaje_completado = $total_materias > 0 ? round(($materias_con_notas / $total_materias) * 100, 1) : 0;
                            ?>
                            
                            <div class="stats-container">
                                <div class="stat-item">
                                    <div class="stat-value h4 text-primary"><?= $total_materias ?></div>
                                    <div class="stat-label">Total Materias</div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-value h4 <?= $promedio_general >= 12 ? 'text-success' : ($promedio_general > 0 ? 'text-warning' : 'text-secondary') ?>">
                                        <?= $promedio_general > 0 ? $promedio_general : 'N/A' ?>
                                    </div>
                                    <div class="stat-label">Promedio</div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-value h4 text-success"><?= $materias_aprobadas ?></div>
                                    <div class="stat-label">Aprobadas</div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-value h4 text-danger"><?= $materias_reprobadas ?></div>
                                    <div class="stat-label">Reprobadas</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <p><strong>Progreso:</strong> 
                                    <span class="badge badge-<?= $porcentaje_completado >= 100 ? 'success' : ($porcentaje_completado >= 50 ? 'info' : 'warning') ?>">
                                        <?= $porcentaje_completado ?>% completado
                                    </span>
                                </p>
                                <p><strong>Efectividad:</strong> 
                                    <span class="badge badge-<?= $porcentaje_aprobadas >= 80 ? 'success' : ($porcentaje_aprobadas >= 50 ? 'warning' : 'danger') ?>">
                                        <?= $porcentaje_aprobadas ?>% de aprobación
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6>Progreso de la Carrera</h6>
                        </div>
                        <div class="card-body">
                            <?php if ($total_materias > 0): 
                            // Calcular porcentajes para las metas
                            $porcentaje_meta_tsu = 0;
                            
                            // Contar materias por trayecto
                            $materias_por_trayecto = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0];
                            $materias_carrera->data_seek(0); // Reiniciar el puntero
                            while ($materia = $materias_carrera->fetch_assoc()) {
                                $trayecto = (int)$materia['trayecto'];
                                if (isset($materias_por_trayecto[$trayecto])) {
                                    $materias_por_trayecto[$trayecto]++;
                                }
                            }
                            
                            // Calcular porcentaje para la meta de TSU (hasta trayecto 2)
                            $materias_tsu = $materias_por_trayecto[0] + $materias_por_trayecto[1] + $materias_por_trayecto[2];
                            $porcentaje_meta_tsu = ($materias_tsu / $total_materias) * 100;
                            ?>
                            
                            <!-- Barra de progreso principal con metas -->
                            <div class="progress mb-3" style="height: 25px; position: relative; background-color: #f8f9fa;">
                                <div class="progress-bar bg-success" 
                                     style="width: <?= $porcentaje_completado ?>%"
                                     title="<?= $porcentaje_completado ?>% completado">
                                    <span class="progress-text" style="color: #000; font-weight: bold;"><?= $porcentaje_completado ?>%</span>
                                </div>
                                
                                <!-- Línea de meta para TSU -->
                                <?php if ($porcentaje_meta_tsu > 0 && $porcentaje_meta_tsu < 100): ?>
                                <div style="position: absolute; left: <?= $porcentaje_meta_tsu ?>%; top: -5px; bottom: -5px; width: 3px; background-color: #ff6b00; z-index: 10;" 
                                     title="Meta: TSU (<?= round($porcentaje_meta_tsu, 1) ?>%)"></div>
                                <div style="position: absolute; left: <?= $porcentaje_meta_tsu ?>%; top: -20px; transform: translateX(-50%);">
                                    <i class="fas fa-flag" style="color: #ff6b00; font-size: 16px;"></i>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Icono de meta final (fin de carrera) -->
                                <div style="position: absolute; right: 0; top: -20px;">
                                    <i class="fas fa-graduation-cap" style="color: #28a745; font-size: 18px;"></i>
                                </div>
                            </div>
                            
                            <!-- Leyenda de las metas -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <small>
                                        <i class="fas fa-flag text-warning"></i>
                                        <strong> Meta TSU:</strong> <?= round($porcentaje_meta_tsu, 1) ?>%
                                    </small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small>
                                        <i class="fas fa-graduation-cap text-success"></i>
                                        <strong> Fin de Carrera:</strong> 100%
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Barra de progreso por estados -->
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar bg-success" 
                                     style="width: <?= ($materias_aprobadas / $total_materias) * 100 ?>%">
                                    <span class="progress-text" style="color: #000; font-weight: bold;"><?= $materias_aprobadas ?></span>
                                </div>
                                <div class="progress-bar bg-danger" 
                                     style="width: <?= ($materias_reprobadas / $total_materias) * 100 ?>%">
                                    <span class="progress-text" style="color: #000; font-weight: bold;"><?= $materias_reprobadas ?></span>
                                </div>
                                <div class="progress-bar bg-secondary" 
                                     style="width: <?= ($materias_sin_notas / $total_materias) * 100 ?>%">
                                    <span class="progress-text" style="color: #000; font-weight: bold;"><?= $materias_sin_notas ?></span>
                                </div>
                            </div>
                            
                            <!-- Información adicional sobre los trayectos -->
                            <div class="mt-3">
                                <h6>Distribución por Trayectos:</h6>
                                <div class="row">
                                    <?php for ($i = 0; $i <= 4; $i++): 
                                        if ($materias_por_trayecto[$i] > 0): ?>
                                        <div class="col-md-2 col-sm-4 col-4 mb-2">
                                            <small>
                                                <strong>T<?= $i ?>:</strong> <?= $materias_por_trayecto[$i] ?> mat.
                                            </small>
                                        </div>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <?php else: ?>
                                <p class="text-muted">No hay materias en esta carrera</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-warning">
            No se encontraron materias para la carrera: <?= htmlspecialchars($carrera['nombre_carrera']) ?>
        </div>
    <?php endif; ?>
    
    <?php endif; ?>
</div>

<style>
.nota-display {
    text-align: center;
    padding: 2px;
}

.nota-valor {
    display: inline-block;
    font-size: 1rem;
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 6px;
    min-width: 40px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    color: #000000 !important; /* Texto negro */
    font-weight: 900; /* Texto más grueso */
}

.nota-aprobada {
    background: #90EE90; /* Verde claro */
    border: 1px solid #28a745;
}

.nota-reprobada {
    background: #FFB6C1; /* Rojo claro */
    border: 1px solid #dc3545;
}

.stats-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.stat-item {
    text-align: center;
    padding: 8px;
    border-radius: 6px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}

.stat-value {
    font-weight: bold;
    margin-bottom: 2px;
    color: #000 !important; /* Texto negro en estadísticas */
    font-weight: 700;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
}

.progress-text {
    font-weight: bold;
    text-shadow: 1px 1px 1px rgba(255,255,255,0.5);
}

.table-sm td, .table-sm th {
    padding: 0.5rem;
}

/* Estilos para el botón de reporte */
.btn-danger {
    color: white;
}
</style>

<?php include("includes/footer.php"); ?>