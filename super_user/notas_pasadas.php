<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Consultar Notas Definitivas por Profesor";
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
$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';

// Obtener información del profesor seleccionado para mostrar
$profesor_seleccionado = null;
if (!empty($filtro_profesor)) {
    $profesor_seleccionado = obtenerProfesorPorId($filtro_profesor);
}

$profesores = obtenerProfesores();
$grupos_notas = obtenerGruposNotasDefinitivas($filtro_profesor, $filtro_fecha_desde, $filtro_fecha_hasta);

include("includes/head.php");
?>

<div class="container-fluid">
    <h2 class="my-4">Consultar Notas Definitivas por Profesor</h2>
    
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
                <div class="form-row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label for="buscar_profesor" class="form-label">Buscar Profesor:</label>
                        <input type="text" 
                               class="form-control" 
                               id="buscar_profesor" 
                               placeholder="Escriba nombre o cédula..."
                               autocomplete="off">
                        <input type="hidden" name="profesor" id="profesor_id" value="<?= htmlspecialchars($filtro_profesor) ?>">
                        <div id="sugerencias_profesores" class="list-group mt-1" style="display: none; max-height: 200px; overflow-y: auto; position: absolute; z-index: 1000; width: 100%;"></div>
                        
                        <!-- Mostrar profesor seleccionado -->
                        <?php if ($profesor_seleccionado): ?>
                            <div class="mt-2">
                                <small class="text-success">
                                    <i class="fas fa-check"></i> 
                                    Profesor seleccionado: <strong><?= htmlspecialchars($profesor_seleccionado['nombre']) ?> (<?= htmlspecialchars($profesor_seleccionado['idusuario']) ?>)</strong>
                                    <button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="limpiarProfesor()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
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
                    
                    <div class="col-md-2 mb-3">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Aplicar
                            </button>
                            <a href="notas_pasadas.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Mostrar filtros activos -->
            <?php if (!empty($filtro_profesor) || !empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): ?>
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Filtros aplicados:</strong>
                        <?php 
                        $filtros_activos = array();
                        
                        if (!empty($filtro_profesor) && $profesor_seleccionado) {
                            $filtros_activos[] = "Profesor: " . htmlspecialchars($profesor_seleccionado['nombre']) . " (" . htmlspecialchars($profesor_seleccionado['idusuario']) . ")";
                        }
                        
                        if (!empty($filtro_fecha_desde)) {
                            $filtros_activos[] = "Desde: " . date('d/m/Y', strtotime($filtro_fecha_desde));
                        }
                        
                        if (!empty($filtro_fecha_hasta)) {
                            $filtros_activos[] = "Hasta: " . date('d/m/Y', strtotime($filtro_fecha_hasta));
                        }
                        
                        echo implode(' | ', $filtros_activos);
                        ?>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Notas Definitivas por Docente</h5>
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
                                <th># Notas</th>
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
                                            <?= $grupo['total_notas'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($grupo['ultima_fecha'])) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info btn-detalles" 
                                                data-toggle="modal" data-target="#modalDetalles"
                                                data-docente-id="<?= $grupo['id_docente'] ?>"
                                                data-materia-id="<?= $grupo['id_materia'] ?>"
                                                data-periodo-id="<?= $grupo['id_periodo'] ?>"
                                                data-docente="<?= htmlspecialchars($grupo['nombre_docente']) ?>"
                                                data-materia="<?= htmlspecialchars($grupo['nombre_materia']) ?>"
                                                data-periodo="<?= htmlspecialchars($grupo['nombre_periodo']) ?>"
                                                data-seccion="<?= htmlspecialchars($grupo['codigo_seccion']) ?>"
                                                data-carrera="<?= htmlspecialchars($grupo['nombre_carrera']) ?>">
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </button>
                                        
                                        <!-- Botón para generar PDF -->
                                        <button type="button" class="btn btn-sm btn-danger btn-pdf" 
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
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>
                        <?php if (!empty($filtro_profesor) || !empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): ?>
                            No se encontraron resultados con los filtros aplicados.
                        <?php else: ?>
                            No hay notas definitivas registradas en el sistema.
                        <?php endif; ?>
                    </h5>
                    <?php if (!empty($filtro_profesor) || !empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): ?>
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
                    Notas Definitivas - <span id="tituloGrupo"></span>
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
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
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
                            
                            // Mostrar información del profesor seleccionado
                            $('<div class="mt-2">' +
                                '<small class="text-success">' +
                                '<i class="fas fa-check"></i> ' +
                                'Profesor seleccionado: <strong>' + profesor.nombre + ' (' + profesor.idusuario + ')</strong>' +
                                '<button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="limpiarProfesor()">' +
                                '<i class="fas fa-times"></i>' +
                                '</button>' +
                                '</small>' +
                                '</div>').insertAfter('#buscar_profesor');
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
    
    // Limpiar campo de búsqueda si está vacío
    $('#buscar_profesor').on('blur', function() {
        if ($(this).val().trim() === '' && $('#profesor_id').val() === '') {
            $(this).val('');
        }
    });

    // Cargar detalles del grupo via AJAX
    $('.btn-detalles').click(function() {
        const docenteId = $(this).data('docente-id');
        const materiaId = $(this).data('materia-id');
        const periodoId = $(this).data('periodo-id');
        const docente = $(this).data('docente');
        const materia = $(this).data('materia');
        const periodo = $(this).data('periodo');
        const seccion = $(this).data('seccion');
        const carrera = $(this).data('carrera');
        
        // Actualizar título del modal
        $('#tituloGrupo').text(`${docente} - ${materia} - ${periodo}`);
        
        // Cargar lista de estudiantes
        $.ajax({
            url: 'ajax_detalles_notas_definitivas.php',
            type: 'POST',
            data: { 
                docente_id: docenteId, 
                materia_id: materiaId, 
                periodo_id: periodoId,
                seccion: 'lista-estudiantes'
            },
            success: function(data) {
                $('#lista-estudiantes').html(data);
            }
        });
        
        // Cargar resumen
        $.ajax({
            url: 'ajax_detalles_notas_definitivas.php',
            type: 'POST',
            data: { 
                docente_id: docenteId, 
                materia_id: materiaId, 
                periodo_id: periodoId,
                seccion: 'resumen'
            },
            success: function(data) {
                $('#resumen').html(data);
            }
        });
        
        // Cargar soporte
        $.ajax({
            url: 'ajax_detalles_notas_definitivas.php',
            type: 'POST',
            data: { 
                docente_id: docenteId, 
                materia_id: materiaId, 
                periodo_id: periodoId,
                seccion: 'soporte'
            },
            success: function(data) {
                $('#soporte').html(data);
            }
        });
    });
    
    // Manejar clic en botón PDF
    $('.btn-pdf').click(function() {
        const docenteId = $(this).data('docente-id');
        const materiaId = $(this).data('materia-id');
        const periodoId = $(this).data('periodo-id');
        const docente = $(this).data('docente');
        const materia = $(this).data('materia');
        const periodo = $(this).data('periodo');
        const seccion = $(this).data('seccion');
        const carrera = $(this).data('carrera');
        
        // Mostrar loading
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Generando...');
        $btn.prop('disabled', true);
        
        // Cargar datos para el PDF
        $.ajax({
            url: 'ajax_detalles_notas_definitivas.php',
            type: 'POST',
            data: { 
                docente_id: docenteId, 
                materia_id: materiaId, 
                periodo_id: periodoId,
                accion: 'pdf'
            },
            success: function(data) {
                // Restaurar botón
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
                
                // Generar PDF
                generarPDF(data, docente, materia, periodo, seccion, carrera);
            },
            error: function(xhr, status, error) {
                // Restaurar botón
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
                console.error('Error:', error);
                alert('Error al generar el PDF: ' + error);
            }
        });
    });

    // Validación de fechas: fecha_hasta no puede ser menor que fecha_desde
    $('#fecha_desde, #fecha_hasta').change(function() {
        const fechaDesde = $('#fecha_desde').val();
        const fechaHasta = $('#fecha_hasta').val();
        
        if (fechaDesde && fechaHasta && fechaHasta < fechaDesde) {
            alert('La fecha "Hasta" no puede ser menor que la fecha "Desde"');
            $('#fecha_hasta').val('');
        }
    });
});

// Manejar clic en botón PDF
// Función reutilizable para abrir/descargar PDF de notas definitivas vía POST
function generarPdfNotasDefinitivasPost(docenteId, materiaId, periodoId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'generar_pdf_notas_definitivas.php';
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

$('.btn-pdf').click(function() {
    const docenteId = $(this).data('docente-id');
    const materiaId = $(this).data('materia-id');
    const periodoId = $(this).data('periodo-id');
    
    // Mostrar loading
    const $btn = $(this);
    const originalHtml = $btn.html();
    $btn.html('<i class="fas fa-spinner fa-spin"></i> Generando...');
    $btn.prop('disabled', true);
    
    // Generar PDF vía POST
    generarPdfNotasDefinitivasPost(docenteId, materiaId, periodoId);
    
    // Restaurar botón después de 3 segundos
    setTimeout(() => {
        $btn.html(originalHtml);
        $btn.prop('disabled', false);
    }, 3000);
});

// Función para limpiar la selección del profesor
function limpiarProfesor() {
    $('#buscar_profesor').val('');
    $('#profesor_id').val('');
    $('.mt-2').filter(function() {
        return $(this).find('.text-success').length > 0;
    }).remove();
}
</script>

<?php include("includes/footer.php"); ?>