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

// Obtener información básica del grupo
$info_query = "SELECT 
                u.nombre as nombre_docente,
                u.idusuario as cedula_docente,
                m.nombre_materia,
                pa.nombre_periodo,
                s.codigo_seccion,
                c.nombre_carrera
              FROM docente_seccion ds
              INNER JOIN users u ON ds.id_usuario = u.id
              INNER JOIN materias m ON ds.id_materia = m.id_materia
              INNER JOIN periodos_academicos pa ON pa.id_periodo = $periodo_id
              INNER JOIN secciones s ON ds.id_seccion = s.id_seccion
              INNER JOIN carreras c ON s.id_carrera = c.id_carrera
              WHERE ds.id_usuario = $docente_id 
              AND ds.id_materia = $materia_id
              LIMIT 1";

$info_result = $db->query($info_query);
$info = $info_result->fetch_assoc();

switch ($seccion) {
    case 'lista-estudiantes':
        // Obtener estudiantes con sus 3 notas trimestrales en UNA SOLA FILA
        $query = "SELECT 
                    u.id as id_usuario,
                    u.idusuario as cedula,
                    u.nombre as nombre_estudiante,
                    MAX(CASE WHEN nt.trimestre_num = 1 THEN nt.nota END) as trimestre_1,
                    MAX(CASE WHEN nt.trimestre_num = 2 THEN nt.nota END) as trimestre_2,
                    MAX(CASE WHEN nt.trimestre_num = 3 THEN nt.nota END) as trimestre_3,
                    MAX(CASE WHEN nt.trimestre_num = 1 THEN nt.estado END) as estado_1,
                    MAX(CASE WHEN nt.trimestre_num = 2 THEN nt.estado END) as estado_2,
                    MAX(CASE WHEN nt.trimestre_num = 3 THEN nt.estado END) as estado_3,
                    MAX(CASE WHEN nt.trimestre_num = 1 THEN nt.id END) as id_nota_1,
                    MAX(CASE WHEN nt.trimestre_num = 2 THEN nt.id END) as id_nota_2,
                    MAX(CASE WHEN nt.trimestre_num = 3 THEN nt.id END) as id_nota_3
                  FROM estudiante_seccion es
                  INNER JOIN users u ON es.id_usuario = u.id
                  INNER JOIN docente_seccion ds ON es.id_seccion = ds.id_seccion
                  LEFT JOIN notas_trimestres nt ON u.id = nt.id_usuario 
                      AND nt.id_materia = $materia_id 
                      AND nt.id_periodo = $periodo_id
                  WHERE ds.id_usuario = $docente_id 
                  AND ds.id_materia = $materia_id
                  AND u.estudiante = 1
                  GROUP BY u.id, u.idusuario, u.nombre
                  ORDER BY u.nombre ASC";
        
        $result = $db->query($query);
        ?>
        <h4>Lista de Estudiantes</h4>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Información del Grupo:</strong> <?= htmlspecialchars($info['nombre_docente']) ?> - 
            <?= htmlspecialchars($info['nombre_materia']) ?> - 
            Sección <?= htmlspecialchars($info['codigo_seccion']) ?>
            <br>
            <strong>Notas en Revisión:</strong> Las notas que aparecen aquí están pendientes de aprobación por el administrador.
        </div>
        
        <!-- Botones Aprobar/Rechazar Todo -->
        <div id="botonesGrupo" class="mb-3">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Acciones Grupales:</strong> Estas acciones afectarán a TODOS los estudiantes del grupo.
            </div>
            <button type="button" class="btn btn-success btn-sm" onclick="accionGrupo('aprobar')">
                <i class="fas fa-check-circle"></i> Aprobar Todo el Grupo
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="accionGrupo('rechazar')">
                <i class="fas fa-times-circle"></i> Rechazar Todo el Grupo
            </button>
        </div>
        
        <!-- Botones Aprobar/Rechazar Seleccionados -->
        <div id="botonesSeleccion" class="mb-3" style="display: none;">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Acciones sobre selección:</strong> Estas acciones afectarán solo a los estudiantes seleccionados.
            </div>
            <button type="button" class="btn btn-success btn-sm" onclick="aplicarAccion('aprobar')">
                <i class="fas fa-check-circle"></i> Aprobar Seleccionados
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="aplicarAccion('rechazar')">
                <i class="fas fa-times-circle"></i> Rechazar Seleccionados
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarSeleccion()">
                <i class="fas fa-times"></i> Limpiar Selección
            </button>
            <span id="contadorSeleccion" class="badge badge-primary ml-2"></span>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th width="40"><input type="checkbox" id="selectAllEstudiantes"></th>
                        <th>Cédula</th>
                        <th>Estudiante</th>
                        <th class="text-center" width="80">Trimestre 1</th>
                        <th class="text-center" width="80">Trimestre 2</th>
                        <th class="text-center" width="80">Trimestre 3</th>
                        <th class="text-center" width="90">Nota Final</th>
                        <th class="text-center" width="100">Estado</th>
                        <th width="180">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): 
                        $t1 = $row['trimestre_1'];
                        $t2 = $row['trimestre_2'];
                        $t3 = $row['trimestre_3'];
                        
                        $suma = 0;
                        $count = 0;
                        if ($t1 !== null) { $suma += $t1; $count++; }
                        if ($t2 !== null) { $suma += $t2; $count++; }
                        if ($t3 !== null) { $suma += $t3; $count++; }
                        $nota_final = $count > 0 ? round($suma / $count, 1) : null;
                        
                        // Determinar el estado general
                        $estados = [];
                        if ($row['estado_1']) $estados[] = $row['estado_1'];
                        if ($row['estado_2']) $estados[] = $row['estado_2'];
                        if ($row['estado_3']) $estados[] = $row['estado_3'];
                        
                        $badge_class = 'secondary';
                        $badge_text = 'Pendiente';
                        
                        if (in_array('en_revision', $estados)) {
                            $badge_class = 'warning';
                            $badge_text = 'En Revisión';
                        } elseif (in_array('rechazada', $estados)) {
                            $badge_class = 'danger';
                            $badge_text = 'Rechazada';
                        } elseif (in_array('aprobada', $estados)) {
                            $badge_class = 'success';
                            $badge_text = 'Aprobada';
                        }
                        
                        $id_nota_1 = $row['id_nota_1'];
                        $id_nota_2 = $row['id_nota_2'];
                        $id_nota_3 = $row['id_nota_3'];
                    ?>
                        <tr data-estudiante-id="<?= $row['id_usuario'] ?>">
                            <td>
                                <input type="checkbox" name="estudiantes_ids[]" 
                                       value="<?= $row['id_usuario'] ?>" 
                                       class="estudiante-checkbox"
                                       data-estudiante-nombre="<?= htmlspecialchars($row['nombre_estudiante']) ?>"
                                       data-nota-1="<?= $id_nota_1 ?>"
                                       data-nota-2="<?= $id_nota_2 ?>"
                                       data-nota-3="<?= $id_nota_3 ?>">
                            </div>
                            <td><?= htmlspecialchars($row['cedula']) ?></div>
                            <td><?= htmlspecialchars($row['nombre_estudiante']) ?></div>
                            
                            <td class="text-center">
                                <?php if ($t1 !== null): ?>
                                    <span class="badge <?= $t1 >= 12 ? 'bg-success' : 'bg-danger' ?> p-2">
                                        <?= number_format($t1, 1) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </div>
                            
                            <td class="text-center">
                                <?php if ($t2 !== null): ?>
                                    <span class="badge <?= $t2 >= 12 ? 'bg-success' : 'bg-danger' ?> p-2">
                                        <?= number_format($t2, 1) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </div>
                            
                            <td class="text-center">
                                <?php if ($t3 !== null): ?>
                                    <span class="badge <?= $t3 >= 12 ? 'bg-success' : 'bg-danger' ?> p-2">
                                        <?= number_format($t3, 1) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </div>
                            
                            <td class="text-center">
                                <?php if ($nota_final !== null): ?>
                                    <span class="badge <?= $nota_final >= 12 ? 'bg-success' : 'bg-danger' ?> p-2" style="font-size: 1rem;">
                                        <?= number_format($nota_final, 1) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </div>
                            
                            <td class="text-center">
                                <span class="badge badge-<?= $badge_class ?> px-3 py-2"><?= $badge_text ?></span>
                            </div>
                            
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-success accion-individual" 
                                            data-accion="aprobar"
                                            data-estudiante-id="<?= $row['id_usuario'] ?>"
                                            data-nota-1="<?= $id_nota_1 ?>"
                                            data-nota-2="<?= $id_nota_2 ?>"
                                            data-nota-3="<?= $id_nota_3 ?>"
                                            data-estudiante-nombre="<?= htmlspecialchars($row['nombre_estudiante']) ?>">
                                        <i class="fas fa-check"></i> Aprobar
                                    </button>
                                    <button type="button" class="btn btn-danger accion-individual" 
                                            data-accion="rechazar"
                                            data-estudiante-id="<?= $row['id_usuario'] ?>"
                                            data-nota-1="<?= $id_nota_1 ?>"
                                            data-nota-2="<?= $id_nota_2 ?>"
                                            data-nota-3="<?= $id_nota_3 ?>"
                                            data-estudiante-nombre="<?= htmlspecialchars($row['nombre_estudiante']) ?>">
                                        <i class="fas fa-times"></i> Rechazar
                                    </button>
                                </div>
                            </div>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($result->num_rows == 0): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                No hay estudiantes en esta sección
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <script>
        function actualizarBotones() {
            const selected = $('.estudiante-checkbox:checked');
            if (selected.length === 0) {
                $('#botonesGrupo').show();
                $('#botonesSeleccion').hide();
            } else {
                $('#botonesGrupo').hide();
                $('#botonesSeleccion').show();
                $('#contadorSeleccion').text(selected.length === 1 ? '1 estudiante' : selected.length + ' estudiantes');
            }
        }
        
        $('#selectAllEstudiantes').change(function() {
            $('.estudiante-checkbox').prop('checked', this.checked);
            actualizarBotones();
        });
        
        $('.estudiante-checkbox').change(actualizarBotones);
        
        $('.accion-individual').click(function() {
            const accion = $(this).data('accion');
            const estudianteId = $(this).data('estudiante-id');
            const estudianteNombre = $(this).data('estudiante-nombre');
            const nota1 = $(this).data('nota-1');
            const nota2 = $(this).data('nota-2');
            const nota3 = $(this).data('nota-3');
            
            // Recolectar IDs de notas no nulas
            const notasIds = [];
            if (nota1) notasIds.push(nota1);
            if (nota2) notasIds.push(nota2);
            if (nota3) notasIds.push(nota3);
            
            window.accionPendiente = accion;
            window.notasIdsPendientes = notasIds;
            window.esAccionGrupal = false;
            
            if (accion === 'rechazar') {
                $('#estudiantesRechazados').text(estudianteNombre);
                $('#mensajeRechazoTexto').val('');
                $('#modalMensajeRechazo').modal('show');
            } else {
                $('#estudiantesAprobados').text(estudianteNombre);
                $('#mensajeAprobacionTexto').val('');
                $('#modalMensajeAprobacion').modal('show');
            }
            
            setTimeout(function() {
                if (accion === 'rechazar') {
                    $('#mensajeRechazoTexto').focus();
                } else {
                    $('#mensajeAprobacionTexto').focus();
                }
            }, 500);
        });
        
        actualizarBotones();
        </script>
        <?php
        break;
        
    case 'resumen':
        // Obtener estadísticas
        $query_stats = "SELECT 
                        COUNT(DISTINCT u.id) as total_estudiantes,
                        SUM(CASE WHEN nt.estado = 'en_revision' THEN 1 ELSE 0 END) as en_revision,
                        SUM(CASE WHEN nt.estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                        SUM(CASE WHEN nt.estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas
                      FROM estudiante_seccion es
                      INNER JOIN users u ON es.id_usuario = u.id
                      INNER JOIN docente_seccion ds ON es.id_seccion = ds.id_seccion
                      LEFT JOIN notas_trimestres nt ON u.id = nt.id_usuario 
                          AND nt.id_materia = $materia_id 
                          AND nt.id_periodo = $periodo_id
                      WHERE ds.id_usuario = $docente_id 
                      AND ds.id_materia = $materia_id
                      AND u.estudiante = 1";
        
        $result_stats = $db->query($query_stats);
        $stats = $result_stats->fetch_assoc();
        ?>
        <h4>Resumen del Grupo</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-light">Información del Grupo</div>
                    <div class="card-body">
                        <p><strong>Docente:</strong> <?= htmlspecialchars($info['nombre_docente'] ?? 'No disponible') ?> (<?= htmlspecialchars($info['cedula_docente'] ?? '') ?>)</p>
                        <p><strong>Materia:</strong> <?= htmlspecialchars($info['nombre_materia'] ?? 'No disponible') ?></p>
                        <p><strong>Periodo:</strong> <?= htmlspecialchars($info['nombre_periodo'] ?? 'No disponible') ?></p>
                        <p><strong>Sección:</strong> <?= htmlspecialchars($info['codigo_seccion'] ?? 'No disponible') ?></p>
                        <p><strong>Carrera:</strong> <?= htmlspecialchars($info['nombre_carrera'] ?? 'No disponible') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-light">Estadísticas de Notas</div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h3 class="text-primary"><?= $stats['total_estudiantes'] ?? 0 ?></h3>
                                <small>Total Estudiantes</small>
                            </div>
                            <div class="col-6">
                                <h3 class="text-warning"><?= $stats['en_revision'] ?? 0 ?></h3>
                                <small>En Revisión</small>
                            </div>
                            <div class="col-6 mt-2">
                                <h3 class="text-success"><?= $stats['aprobadas'] ?? 0 ?></h3>
                                <small>Aprobadas</small>
                            </div>
                            <div class="col-6 mt-2">
                                <h3 class="text-danger"><?= $stats['rechazadas'] ?? 0 ?></h3>
                                <small>Rechazadas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
        
    case 'soporte':
        // Obtener soporte
        $query_soporte = "SELECT soporte, tipo_archivo, fecha_subida 
                          FROM notas_pendientes 
                          WHERE id_docente = $docente_id 
                          AND id_materia = $materia_id 
                          AND id_periodo = $periodo_id 
                          LIMIT 1";
        $result_soporte = $db->query($query_soporte);
        $soporte = $result_soporte->fetch_assoc();
        ?>
        <h4>Archivo de Soporte</h4>
        <div class="alert alert-info">
            <strong>Grupo:</strong> <?= htmlspecialchars($info['nombre_docente']) ?> - 
            <?= htmlspecialchars($info['nombre_materia']) ?> - 
            Sección <?= htmlspecialchars($info['codigo_seccion']) ?>
        </div>
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
                        <small>Subido: <?= date('d/m/Y H:i', strtotime($soporte['fecha_subida'])) ?></small>
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