<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Consultar Notas Aprobadas por Profesor";
include('../funciones/functions.php');

// CARGAR PERMISOS
cargarPermisosUsuario();
verificarPermiso('consultar_notas_pasadas');

// Verificar autenticación
if (!isLoggedIn()) {
    $_SESSION['msg'] = "Debes iniciar sesión para acceder";
    header('location: ../login.php');
    exit();
}

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

// Obtener parámetros de filtro
$filtro_profesor = $_GET['profesor'] ?? '';
$filtro_materia = $_GET['materia'] ?? '';
$filtro_periodo = $_GET['periodo'] ?? '';
$filtro_seccion = $_GET['seccion'] ?? '';
$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';

// Obtener información del profesor seleccionado
$profesor_seleccionado = null;
if (!empty($filtro_profesor)) {
    $profesor_seleccionado = obtenerProfesorPorId($filtro_profesor);
}

// Obtener listas para filtros
$profesores = obtenerProfesores();
$materias = obtenerTodasLasMaterias();
$periodos = obtenerPeriodosAcademicos($db);
$secciones = obtenerTodasLasSecciones();

// Obtener grupos de notas aprobadas
$grupos_notas = obtenerGruposNotasAprobadas($filtro_profesor, $filtro_materia, $filtro_periodo, $filtro_seccion, $filtro_fecha_desde, $filtro_fecha_hasta);

include("includes/head.php");
?>

<style>
.badge-aprobada {
    background-color: #28a745 !important;
    color: white !important;
}
.filter-badge {
    background-color: #e9ecef;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
}
</style>

<div class="container-fluid">
    <h2 class="my-4">Consultar Notas Aprobadas por Profesor</h2>
    
    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['msg'] ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>
    
    <!-- Card de Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="buscar_profesor" class="form-label">Profesor:</label>
                        <input type="text" 
                               class="form-control" 
                               id="buscar_profesor" 
                               placeholder="Escriba nombre o cédula..."
                               autocomplete="off"
                               value="<?= $profesor_seleccionado ? htmlspecialchars($profesor_seleccionado['nombre']) : '' ?>">
                        <input type="hidden" name="profesor" id="profesor_id" value="<?= htmlspecialchars($filtro_profesor) ?>">
                        <div id="sugerencias_profesores" class="list-group mt-1" style="display: none; max-height: 200px; overflow-y: auto; position: absolute; z-index: 1000; width: calc(100% - 30px); background: white; border: 1px solid #ccc; border-radius: 4px;"></div>
                        
                        <?php if ($profesor_seleccionado): ?>
                            <div class="mt-2">
                                <small class="text-success">
                                    <i class="fas fa-check"></i> 
                                    Profesor: <strong><?= htmlspecialchars($profesor_seleccionado['nombre']) ?> (<?= htmlspecialchars($profesor_seleccionado['idusuario']) ?>)</strong>
                                    <button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="limpiarProfesor()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="materia" class="form-label">Materia:</label>
                        <select name="materia" id="materia" class="form-control">
                            <option value="">Todas las materias</option>
                            <?php foreach ($materias as $materia): ?>
                            <option value="<?= $materia['id'] ?>" <?= ($filtro_materia == $materia['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($materia['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="periodo" class="form-label">Periodo Académico:</label>
                        <select name="periodo" id="periodo" class="form-control">
                            <option value="">Todos los periodos</option>
                            <?php foreach ($periodos as $periodo): ?>
                            <option value="<?= $periodo['id_periodo'] ?>" <?= ($filtro_periodo == $periodo['id_periodo']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($periodo['nombre_periodo']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="seccion" class="form-label">Sección:</label>
                        <select name="seccion" id="seccion" class="form-control">
                            <option value="">Todas las secciones</option>
                            <?php foreach ($secciones as $seccion): ?>
                            <option value="<?= $seccion['id_seccion'] ?>" <?= ($filtro_seccion == $seccion['id_seccion']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($seccion['codigo_seccion']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="fecha_desde" class="form-label">Fecha Desde:</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" 
                               value="<?= htmlspecialchars($filtro_fecha_desde) ?>">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="fecha_hasta" class="form-label">Fecha Hasta:</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" 
                               value="<?= htmlspecialchars($filtro_fecha_hasta) ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="btn-group" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Aplicar Filtros
                            </button>
                            <a href="notas_pasadas.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Mostrar filtros activos -->
            <?php if (!empty($filtro_profesor) || !empty($filtro_materia) || !empty($filtro_periodo) || !empty($filtro_seccion) || !empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): ?>
                <div class="mt-3">
                    <strong>Filtros aplicados:</strong>
                    <?php 
                    if (!empty($filtro_profesor) && $profesor_seleccionado) {
                        echo '<span class="filter-badge ml-1"><i class="fas fa-chalkboard-teacher"></i> ' . htmlspecialchars($profesor_seleccionado['nombre']) . '</span>';
                    }
                    if (!empty($filtro_materia)) {
                        $materia_nombre = '';
                        foreach ($materias as $m) {
                            if ($m['id'] == $filtro_materia) { $materia_nombre = $m['nombre']; break; }
                        }
                        echo '<span class="filter-badge ml-1"><i class="fas fa-book"></i> ' . htmlspecialchars($materia_nombre) . '</span>';
                    }
                    if (!empty($filtro_periodo)) {
                        $periodo_nombre = '';
                        foreach ($periodos as $p) {
                            if ($p['id_periodo'] == $filtro_periodo) { $periodo_nombre = $p['nombre_periodo']; break; }
                        }
                        echo '<span class="filter-badge ml-1"><i class="fas fa-calendar"></i> ' . htmlspecialchars($periodo_nombre) . '</span>';
                    }
                    if (!empty($filtro_seccion)) {
                        $seccion_codigo = '';
                        foreach ($secciones as $s) {
                            if ($s['id_seccion'] == $filtro_seccion) { $seccion_codigo = $s['codigo_seccion']; break; }
                        }
                        echo '<span class="filter-badge ml-1"><i class="fas fa-users"></i> Sección ' . htmlspecialchars($seccion_codigo) . '</span>';
                    }
                    if (!empty($filtro_fecha_desde)) {
                        echo '<span class="filter-badge ml-1"><i class="fas fa-calendar-alt"></i> Desde: ' . date('d/m/Y', strtotime($filtro_fecha_desde)) . '</span>';
                    }
                    if (!empty($filtro_fecha_hasta)) {
                        echo '<span class="filter-badge ml-1"><i class="fas fa-calendar-alt"></i> Hasta: ' . date('d/m/Y', strtotime($filtro_fecha_hasta)) . '</span>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Notas Aprobadas por Docente</h5>
            <span class="badge badge-light badge-pill">
                Total: <?= $grupos_notas->num_rows ?> grupo(s)
            </span>
        </div>
        <div class="card-body">
            <?php if ($grupos_notas->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Docente</th>
                                <th>Cédula</th>
                                <th>Materia</th>
                                <th>Periodo</th>
                                <th>Sección</th>
                                <th>Carrera</th>
                                <th># Estudiantes</th>
                                <th>Última Actualización</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($grupo = $grupos_notas->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($grupo['nombre_docente']) ?></td>
                                    <td><?= htmlspecialchars($grupo['cedula_docente']) ?></td>
                                    <td><?= htmlspecialchars($grupo['nombre_materia']) ?></td>
                                    <td><?= htmlspecialchars($grupo['nombre_periodo']) ?></td>
                                    <td><?= htmlspecialchars($grupo['codigo_seccion']) ?></td>
                                    <td><?= htmlspecialchars($grupo['nombre_carrera']) ?></td>
                                    <td>
                                        <span class="badge badge-info badge-pill">
                                            <?= $grupo['total_estudiantes'] ?>
                                        </span>
                                    </div>
                                    <td><?= date('d/m/Y H:i', strtotime($grupo['ultima_fecha'])) ?></div>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-info btn-detalles" 
                                                    data-toggle="modal" data-target="#modalDetalles"
                                                    data-docente-id="<?= $grupo['id_docente'] ?>"
                                                    data-materia-id="<?= $grupo['id_materia'] ?>"
                                                    data-periodo-id="<?= $grupo['id_periodo'] ?>"
                                                    data-docente="<?= htmlspecialchars($grupo['nombre_docente']) ?>"
                                                    data-materia="<?= htmlspecialchars($grupo['nombre_materia']) ?>"
                                                    data-periodo="<?= htmlspecialchars($grupo['nombre_periodo']) ?>"
                                                    data-seccion="<?= htmlspecialchars($grupo['codigo_seccion']) ?>"
                                                    data-carrera="<?= htmlspecialchars($grupo['nombre_carrera']) ?>">
                                                <i class="fas fa-eye"></i> Ver
                                            </button>
                                            
                                            <button type="button" class="btn btn-danger btn-pdf" 
                                                    data-docente-id="<?= $grupo['id_docente'] ?>"
                                                    data-materia-id="<?= $grupo['id_materia'] ?>"
                                                    data-periodo-id="<?= $grupo['id_periodo'] ?>"
                                                    data-docente="<?= htmlspecialchars($grupo['nombre_docente']) ?>"
                                                    data-materia="<?= htmlspecialchars($grupo['nombre_materia']) ?>"
                                                    data-periodo="<?= htmlspecialchars($grupo['nombre_periodo']) ?>"
                                                    data-seccion="<?= htmlspecialchars($grupo['codigo_seccion']) ?>"
                                                    data-carrera="<?= htmlspecialchars($grupo['nombre_carrera']) ?>">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </button>
                                        </div>
                                    </div>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>
                        <?php if (!empty($filtro_profesor) || !empty($filtro_materia) || !empty($filtro_periodo) || !empty($filtro_seccion) || !empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): ?>
                            No se encontraron resultados con los filtros aplicados.
                        <?php else: ?>
                            No hay notas aprobadas registradas en el sistema.
                        <?php endif; ?>
                    </h5>
                    <?php if (!empty($filtro_profesor) || !empty($filtro_materia) || !empty($filtro_periodo) || !empty($filtro_seccion) || !empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): ?>
                        <a href="notas_pasadas.php" class="btn btn-primary mt-2">
                            <i class="fas fa-times"></i> Limpiar Filtros
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de notas -->
<div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="modalDetallesTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalDetallesTitle">
                    Notas Aprobadas - <span id="tituloGrupo"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Sidebar de navegación -->
                    <div class="col-md-3">
                        <div class="list-group" id="sidebarDetalles">
                            <a href="#lista-estudiantes" class="list-group-item list-group-item-action active" data-toggle="tab">
                                <i class="fas fa-users"></i> Lista de Estudiantes
                            </a>
                            <a href="#resumen" class="list-group-item list-group-item-action" data-toggle="tab">
                                <i class="fas fa-chart-bar"></i> Resumen
                            </a>
                            <a href="#soporte" class="list-group-item list-group-item-action" data-toggle="tab">
                                <i class="fas fa-paperclip"></i> Soporte
                            </a>
                        </div>
                    </div>
                    
                    <!-- Contenido de las pestañas -->
                    <div class="col-md-9">
                        <div class="tab-content" id="contenidoDetalles">
                            <div class="tab-pane fade show active" id="lista-estudiantes">
                                <div class="text-center">
                                    <div class="spinner-border text-primary"></div>
                                    <p>Cargando estudiantes...</p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="resumen">
                                <div class="text-center">
                                    <div class="spinner-border text-primary"></div>
                                    <p>Cargando resumen...</p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="soporte">
                                <div class="text-center">
                                    <div class="spinner-border text-primary"></div>
                                    <p>Cargando soporte...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger btn-modal-pdf" id="btnModalPDF">
                    <i class="fas fa-file-pdf"></i> Generar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Variables globales para el PDF
    let currentDocenteId = null;
    let currentMateriaId = null;
    let currentPeriodoId = null;
    let currentDocente = '';
    let currentMateria = '';
    let currentPeriodo = '';
    let currentSeccion = '';
    let currentCarrera = '';

    // Búsqueda de profesores con autocompletado
    $('#buscar_profesor').on('input', function() {
        const termino = $(this).val().trim();
        
        if (termino.length < 2) {
            $('#sugerencias_profesores').hide().empty();
            return;
        }
        
        $.ajax({
            url: 'ajax_buscar_profesores.php',
            type: 'GET',
            data: { termino: termino },
            dataType: 'json',
            success: function(data) {
                const sugerencias = $('#sugerencias_profesores');
                sugerencias.empty();
                
                if (data.length > 0) {
                    data.forEach(function(profesor) {
                        const item = $('<a href="#" class="list-group-item list-group-item-action"></a>');
                        item.html(`
                            <div class="d-flex justify-content-between">
                                <strong>${profesor.nombre}</strong>
                                <small class="text-muted">${profesor.idusuario}</small>
                            </div>
                        `);
                        item.click(function(e) {
                            e.preventDefault();
                            $('#buscar_profesor').val(profesor.nombre);
                            $('#profesor_id').val(profesor.id);
                            sugerencias.hide();
                            
                            // Eliminar selector anterior si existe
                            $('.profesor-seleccionado').remove();
                            
                            // Mostrar información del profesor seleccionado
                            const selectorHtml = `
                                <div class="mt-2 profesor-seleccionado">
                                    <small class="text-success">
                                        <i class="fas fa-check"></i> 
                                        Profesor: <strong>${profesor.nombre} (${profesor.idusuario})</strong>
                                        <button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="limpiarProfesor()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </small>
                                </div>
                            `;
                            $('#buscar_profesor').after(selectorHtml);
                        });
                        sugerencias.append(item);
                    });
                    sugerencias.show();
                } else {
                    sugerencias.hide();
                }
            },
            error: function() {
                $('#sugerencias_profesores').hide().empty();
            }
        });
    });
    
    // Ocultar sugerencias al hacer clic fuera
    $(document).click(function(e) {
        if (!$(e.target).closest('#buscar_profesor, #sugerencias_profesores').length) {
            $('#sugerencias_profesores').hide();
        }
    });

    // Cargar detalles del grupo via AJAX
    $('.btn-detalles').click(function() {
        currentDocenteId = $(this).data('docente-id');
        currentMateriaId = $(this).data('materia-id');
        currentPeriodoId = $(this).data('periodo-id');
        currentDocente = $(this).data('docente');
        currentMateria = $(this).data('materia');
        currentPeriodo = $(this).data('periodo');
        currentSeccion = $(this).data('seccion');
        currentCarrera = $(this).data('carrera');
        
        // Actualizar título del modal
        $('#tituloGrupo').text(`${currentDocente} - ${currentMateria} - ${currentPeriodo}`);
        
        // Cargar lista de estudiantes
        $.ajax({
            url: 'ajax_detalles_notas_aprobadas.php',
            type: 'POST',
            data: { 
                docente_id: currentDocenteId, 
                materia_id: currentMateriaId, 
                periodo_id: currentPeriodoId,
                seccion: 'lista-estudiantes'
            },
            success: function(data) {
                $('#lista-estudiantes').html(data);
            },
            error: function(xhr) {
                $('#lista-estudiantes').html('<div class="alert alert-danger">Error al cargar estudiantes: ' + xhr.status + '</div>');
            }
        });
        
        // Cargar resumen
        $.ajax({
            url: 'ajax_detalles_notas_aprobadas.php',
            type: 'POST',
            data: { 
                docente_id: currentDocenteId, 
                materia_id: currentMateriaId, 
                periodo_id: currentPeriodoId,
                seccion: 'resumen'
            },
            success: function(data) {
                $('#resumen').html(data);
            },
            error: function(xhr) {
                $('#resumen').html('<div class="alert alert-danger">Error al cargar resumen: ' + xhr.status + '</div>');
            }
        });
        
        // Cargar soporte
        $.ajax({
            url: 'ajax_detalles_notas_aprobadas.php',
            type: 'POST',
            data: { 
                docente_id: currentDocenteId, 
                materia_id: currentMateriaId, 
                periodo_id: currentPeriodoId,
                seccion: 'soporte'
            },
            success: function(data) {
                $('#soporte').html(data);
            },
            error: function(xhr) {
                $('#soporte').html('<div class="alert alert-danger">Error al cargar soporte: ' + xhr.status + '</div>');
            }
        });
    });
    
    // Función reutilizable para abrir/descargar PDF de notas aprobadas vía POST
    function generarPdfNotasAprobadasPost(docenteId, materiaId, periodoId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'generar_pdf_notas_aprobadas.php';
        form.target = '_blank';
        form.style.display = 'none';

        const campos = {
            docente_id: docenteId,
            materia_id: materiaId,
            periodo_id: periodoId
        };

        for (const clave in campos) {
            if (campos.hasOwnProperty(clave)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = clave;
                input.value = campos[clave];
                form.appendChild(input);
            }
        }

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    // Botón PDF del modal
    $('#btnModalPDF').click(function() {
        if (currentDocenteId && currentMateriaId && currentPeriodoId) {
            generarPdfNotasAprobadasPost(currentDocenteId, currentMateriaId, currentPeriodoId);
        } else {
            alert('No se pudo generar el PDF. Faltan datos.');
        }
    });
    
    // Botones PDF en la tabla
    $('.btn-pdf').click(function() {
        const docenteId = $(this).data('docente-id');
        const materiaId = $(this).data('materia-id');
        const periodoId = $(this).data('periodo-id');
        
        // Mostrar loading
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>');
        $btn.prop('disabled', true);
        
        generarPdfNotasAprobadasPost(docenteId, materiaId, periodoId);
        
        setTimeout(() => {
            $btn.html(originalHtml);
            $btn.prop('disabled', false);
        }, 2000);
    });

    // Validación de fechas
    $('#fecha_desde, #fecha_hasta').change(function() {
        const fechaDesde = $('#fecha_desde').val();
        const fechaHasta = $('#fecha_hasta').val();
        
        if (fechaDesde && fechaHasta && fechaHasta < fechaDesde) {
            alert('La fecha "Hasta" no puede ser menor que la fecha "Desde"');
            $('#fecha_hasta').val('');
        }
    });
});

// Función para limpiar la selección del profesor
function limpiarProfesor() {
    $('#buscar_profesor').val('');
    $('#profesor_id').val('');
    $('.profesor-seleccionado').remove();
}
</script>

<?php include("includes/footer.php"); ?>