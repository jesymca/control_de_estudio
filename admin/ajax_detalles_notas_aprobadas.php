<?php
require_once('../funciones/functions.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acceso no permitido');
}

if (!isset($_POST['docente_id']) || !isset($_POST['materia_id']) || !isset($_POST['periodo_id']) || !isset($_POST['seccion'])) {
    die('Parámetros incompletos');
}

$docente_id = (int)$_POST['docente_id'];
$materia_id = (int)$_POST['materia_id'];
$periodo_id = (int)$_POST['periodo_id'];
$seccion = $_POST['seccion'];

switch ($seccion) {
    case 'lista-estudiantes':
        $query = "SELECT 
                    u.id as id_usuario,
                    u.idusuario as cedula,
                    u.nombre as nombre_estudiante,
                    MAX(CASE WHEN nt.trimestre_num = 1 THEN nt.nota END) as trimestre_1,
                    MAX(CASE WHEN nt.trimestre_num = 2 THEN nt.nota END) as trimestre_2,
                    MAX(CASE WHEN nt.trimestre_num = 3 THEN nt.nota END) as trimestre_3,
                    nt.estado
                  FROM estudiante_seccion es
                  INNER JOIN users u ON es.id_usuario = u.id
                  INNER JOIN docente_seccion ds ON es.id_seccion = ds.id_seccion
                  LEFT JOIN notas_trimestres nt ON u.id = nt.id_usuario 
                      AND nt.id_materia = $materia_id 
                      AND nt.id_periodo = $periodo_id
                      AND nt.estado = 'aprobada'
                  WHERE ds.id_usuario = $docente_id 
                  AND ds.id_materia = $materia_id
                  AND u.estudiante = 1
                  GROUP BY u.id, u.idusuario, u.nombre, nt.estado
                  ORDER BY u.nombre ASC";
        
        $result = $db->query($query);
        ?>
        <h4>Lista de Estudiantes</h4>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> 
            <strong>Notas Aprobadas:</strong> Estas notas ya han sido validadas por el administrador.
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Cédula</th>
                        <th>Estudiante</th>
                        <th class="text-center">Trimestre 1</th>
                        <th class="text-center">Trimestre 2</th>
                        <th class="text-center">Trimestre 3</th>
                        <th class="text-center">Nota Final</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $aprobados = 0;
                    $reprobados = 0;
                    while ($row = $result->fetch_assoc()):
                        $t1 = $row['trimestre_1'];
                        $t2 = $row['trimestre_2'];
                        $t3 = $row['trimestre_3'];
                        
                        $suma = 0;
                        $count = 0;
                        if ($t1 !== null) { $suma += $t1; $count++; }
                        if ($t2 !== null) { $suma += $t2; $count++; }
                        if ($t3 !== null) { $suma += $t3; $count++; }
                        $nota_final = $count > 0 ? round($suma / $count, 1) : null;
                        
                        if ($nota_final !== null && $nota_final >= 12) {
                            $aprobados++;
                        } elseif ($nota_final !== null) {
                            $reprobados++;
                        }
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['cedula']) ?></div>
                            <td><?= htmlspecialchars($row['nombre_estudiante']) ?></div>
                            <td class="text-center"><?= $t1 !== null ? '<span class="badge bg-success p-2">'.number_format($t1, 1).'</span>' : '<span class="text-muted">-</span>' ?></div>
                            <td class="text-center"><?= $t2 !== null ? '<span class="badge bg-success p-2">'.number_format($t2, 1).'</span>' : '<span class="text-muted">-</span>' ?></div>
                            <td class="text-center"><?= $t3 !== null ? '<span class="badge bg-success p-2">'.number_format($t3, 1).'</span>' : '<span class="text-muted">-</span>' ?></div>
                            <td class="text-center"><?= $nota_final !== null ? '<span class="badge ' . ($nota_final >= 12 ? 'bg-success' : 'bg-danger') . ' p-2" style="font-size:1rem;">'.number_format($nota_final, 1).'</span>' : '<span class="text-muted">-</span>' ?></div>
                            <td class="text-center">
                                <?php if ($nota_final !== null): ?>
                                    <span class="badge badge-<?= $nota_final >= 12 ? 'success' : 'danger' ?>">
                                        <?= $nota_final >= 12 ? 'Aprobado' : 'Reprobado' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Sin nota</span>
                                <?php endif; ?>
                            </div>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3 alert alert-info">
            <strong>Resumen:</strong> Aprobados: <?= $aprobados ?> | Reprobados: <?= $reprobados ?>
        </div>
        <?php
        break;
        
    case 'resumen':
        $query_estadisticas = "SELECT 
                                COUNT(DISTINCT u.id) as total_estudiantes,
                                SUM(CASE WHEN (COALESCE(nt_t1.nota,0) + COALESCE(nt_t2.nota,0) + COALESCE(nt_t3.nota,0)) / 
                                    (CASE WHEN nt_t1.nota IS NOT NULL THEN 1 ELSE 0 END +
                                     CASE WHEN nt_t2.nota IS NOT NULL THEN 1 ELSE 0 END +
                                     CASE WHEN nt_t3.nota IS NOT NULL THEN 1 ELSE 0 END) >= 12 THEN 1 ELSE 0 END) as aprobados
                              FROM estudiante_seccion es
                              INNER JOIN users u ON es.id_usuario = u.id
                              INNER JOIN docente_seccion ds ON es.id_seccion = ds.id_seccion
                              LEFT JOIN notas_trimestres nt_t1 ON u.id = nt_t1.id_usuario AND nt_t1.id_materia = $materia_id AND nt_t1.id_periodo = $periodo_id AND nt_t1.trimestre_num = 1 AND nt_t1.estado = 'aprobada'
                              LEFT JOIN notas_trimestres nt_t2 ON u.id = nt_t2.id_usuario AND nt_t2.id_materia = $materia_id AND nt_t2.id_periodo = $periodo_id AND nt_t2.trimestre_num = 2 AND nt_t2.estado = 'aprobada'
                              LEFT JOIN notas_trimestres nt_t3 ON u.id = nt_t3.id_usuario AND nt_t3.id_materia = $materia_id AND nt_t3.id_periodo = $periodo_id AND nt_t3.trimestre_num = 3 AND nt_t3.estado = 'aprobada'
                              WHERE ds.id_usuario = $docente_id AND ds.id_materia = $materia_id AND u.estudiante = 1";
        
        $result_est = $db->query($query_estadisticas);
        $stats = $result_est->fetch_assoc();
        ?>
        <h4>Resumen del Grupo</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-light">Estadísticas</div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h3 class="text-primary"><?= $stats['total_estudiantes'] ?? 0 ?></h3>
                                <small>Total Estudiantes</small>
                            </div>
                            <div class="col-6">
                                <h3 class="text-success"><?= $stats['aprobados'] ?? 0 ?></h3>
                                <small>Aprobados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
        
    case 'soporte':
        $query_soporte = "SELECT soporte, tipo_archivo, fecha_registro 
                          FROM notas_definitivas 
                          WHERE id_docente = $docente_id 
                          AND id_materia = $materia_id 
                          AND id_periodo = $periodo_id 
                          LIMIT 1";
        $result_soporte = $db->query($query_soporte);
        $soporte = $result_soporte->fetch_assoc();
        ?>
        <h4>Archivo de Soporte</h4>
        <?php if ($soporte && !empty($soporte['soporte'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> 
                <strong>Archivo de soporte disponible</strong>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <?php if (in_array($soporte['tipo_archivo'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                        <img src="../soportes/<?= htmlspecialchars($soporte['soporte']) ?>" 
                             alt="Soporte" class="img-fluid rounded border" style="max-height: 300px;">
                    <?php else: ?>
                        <i class="fas fa-file-pdf fa-4x text-danger"></i>
                        <p class="mt-2">Archivo PDF</p>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a href="../soportes/<?= htmlspecialchars($soporte['soporte']) ?>" 
                           class="btn btn-primary" target="_blank">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                    </div>
                    <p class="text-muted mt-2">
                        <small>Subido: <?= date('d/m/Y H:i', strtotime($soporte['fecha_registro'])) ?></small>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                No hay archivo de soporte disponible para este grupo.
            </div>
        <?php endif; ?>
        <?php
        break;
}
?>